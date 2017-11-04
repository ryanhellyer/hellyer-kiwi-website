<?php
/*
Plugin Name: Frontend Upload Audio
Plugin URI: https://geek.hellyer.kiwi/plugins/
Description: Frontend Upload Audio
Version: 1.0
Author: Ryan Hellyer
Author URI: https://geek.hellyer.kiwi/

*/


/**
 * Add a custom audio upload meta box.
 * Based on code from the Unique Headers plugin ... https://geek.hellyer.kiwi/plugins/unique-headers/
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Frontend_Upload_Audio {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'init',               array( $this, 'set_file_name' ) );
		add_filter( 'sanitize_file_name', array( $this, 'change_file_name' ), 10 );
		add_action( 'init',               array( $this, 'process_form' ), 40 );
		add_shortcode( 'upload_audio',    array( $this, 'form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_shortcode( 'upload_success',  array( $this, 'success' ) );

	}

	/**
	 * Setting file name as definition.
	 * Set early on, as the $_POST gets modified/removed later in execution.
	 */
	public function set_file_name() {

		if ( isset( $_POST[ 'audio-title' ] ) ) {
			$name = sanitize_title( $_POST[ 'audio-title' ] );
			define( 'AROUSINGAUDIO_FILE_NAME', $name );
		}

	}

	/**
	 * Changing the file name to match the title entered by the file submitter.
	 */
	public function change_file_name( $original_filename ) {

		// Bail out now if file name not set, as we are not on the upload form
		if ( ! defined( 'AROUSINGAUDIO_FILE_NAME' ) ) {
			return $original_filename;
		}


		$upload_dir = wp_upload_dir();
		$ext = '.mp3';

		$file_name = AROUSINGAUDIO_FILE_NAME . $ext;

		return $file_name;
	}

	/**
	 * Processing form submissions.
	 */
	public function process_form() {

		// Bail out now if file not being uploaded
		if ( ! isset( $_FILES[ 'audio_upload' ] ) ) {
			return;
		}

		$audio_id = $this->create_audio_post();
		$file = $this->media_upload( $audio_id );
		$attachment_id = $this->attach_file_to_post( $file, $audio_id );

		if ( is_numeric( $attachment_id ) ) {
			wp_redirect( home_url( '/success/?id=' . absint( $attachment_id ) ), 302 );
			exit;
		}

	}

	/**
	 * Uploading the content into WordPress.
	 */
	public function media_upload( $audio_id ) {

		// Load WordPress file handling tools
		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			require_once( ABSPATH . 'wp-admin/includes/image.php' );
		}

		$files = $_FILES[ 'audio_upload' ];
        $uploadedfile = array(
            'name'     => $_FILES[ 'audio_upload' ][ 'name' ][ 0 ],
            'type'     => $_FILES[ 'audio_upload' ][ 'type' ][ 0 ],
            'tmp_name' => $_FILES[ 'audio_upload' ][ 'tmp_name' ][ 0 ],
            'error'    => $_FILES[ 'audio_upload' ][ 'error' ][ 0 ],
            'size'     => $_FILES[ 'audio_upload' ][ 'size' ][ 0 ]
        );

		$upload_overrides = array(
			'test_form' => false
		);

		$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

		if ( $movefile && ! isset( $movefile['error'] ) ) {
			//file_put_contents( '/home/ryan/nginx/arousingaudio.com/public_html/wp-content/plugins/frontend-upload-audio/temp.txt', print_r( $movefile, true ) );
		} else {
			//define( 'AUDIO_UPLOAD_FAILURE', true );
			file_put_contents( '/home/ryan/nginx/arousingaudio.com/public_html/wp-content/plugins/frontend-upload-audio/temp.txt', 'ERROR: ' . print_r( $uploadedfile, true ) );
		}

		return $movefile;
	}

	/**
	 * Attach file to post.
	 */
	public function attach_file_to_post( $file, $parent_post_id ) {
//print_r( $file );die;
		$filename = $file[ 'file' ];


///print_r( $_POST );die;
//$upload_dir = wp_upload_dir();
//$movefile[ 'file' ] = $upload_dir[ 'basedir' ] . sanitize_title( $_POST[ 'audio-title' ] ) . '.mp3';

		$name = explode( '.', basename( $filename ) );
		$name = $name[0];
//echo $name."\n\n";
//$name = sanitize_title( $_POST[ 'audio-title' ] );
		$filetype = wp_check_filetype( basename( $filename ), null );
		$wp_upload_dir = wp_upload_dir();
		$attachment = array(
			'guid'           => esc_html( $wp_upload_dir['url'] . '/' . basename( $name ) ), 
			'post_mime_type' => $filetype['type'],
			'post_title'     => esc_html( $name ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		$attachment_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

//echo $name."\n\n";
//print_r( $attachment );echo "\n\n";echo $attachment_id;die;
		return $attachment_id;
	}

	/**
	 * Create an audio post.
	 */
	public function create_audio_post() {

		$title = '';
		if ( isset( $_POST[ 'audio-title' ] ) ) {
			$title = $_POST[ 'audio-title' ];
		}

		$description = '';
		if ( isset( $_POST[ 'audio-description' ] ) ) {
			$description = $_POST[ 'audio-description' ];
		}

		$tagline = '';
		if ( isset( $_POST[ 'audio-tagline' ] ) ) {
			$tagline = $_POST[ 'audio-tagline' ];
		}

		$genres = array();
		if ( isset( $_POST[ 'audio-genre' ] ) ) {
			$genres = $_POST[ 'audio-genre' ];
		}

		$args = array(
			'post_title'    => esc_html( $title ),
			'post_name'     => sanitize_title( $title ),
			'post_content'  => wp_kses_post( $description ),
			'post_excerpt'  => wp_kses_post( $tagline ),
			'post_status'   => get_option( 'arousingaudio_status' ),
			'post_author'   => 1,
            'post_type' => 'audio',
		);

		foreach ( $genres as $key => $genre ) {
			if ( '' != $genre ) {
				$args[ 'tax_input' ][ 'genre' ][] = $genre;
			}
		}

		$audio_id = wp_insert_post( $args );

		return $audio_id;
	}

	/**
	 * The forms HTML.
	 */
	public function form() {

		$content = '';
		if ( defined( 'AUDIO_UPLOAD_FAILURE' ) ) {
			$content .= AUDIO_UPLOAD_FAILURE . __( 'The form submission failed.', 'frontend-upload-audio' );
		}

		require( dirname( __FILE__ ) . '/uploader.php' );

		return $content;
	}

	/**
	 * The success shortcode.
	 */
	public function success() {

		if ( isset( $_GET[ 'id' ] ) ) {
			$id = absint( $_GET[ 'id' ] );
			$url = get_permalink( $id );
		}

		if ( ! isset( $_GET[ 'id' ] ) || '' == $url ) {
			$content = '<p>Er, that is not supposed to happen :/ Either no ID was set or an incorrect one was provided.</p>';
		} else {

			$content = '<p>' . __( 'Thank you for uploading your content to Arousing Audio :)', 'frontend-upload-audio' ) . '</p>';

			if ( 'draft' == get_option( 'arousingaudio_status' ) ) {
				$content = '<p>' . sprintf( __( 'Your upload will be available at %s once approved.', 'frontend-upload-audio' ), '<a href="' . esc_url( $url ) . '">' . esc_url( $url ) . '</a>' ) . '</p>';
			} else {
				$content = '<p>' . sprintf( __( 'Your upload can be found at %s.', 'frontend-upload-audio' ), '<a href="' . esc_url( $url ) . '">' . esc_url( $url ) . '</a>' ) . '</p>';
			}

		}

		return $content;
	}

	/**
	 * Adding required scripts.
	 */
	public function scripts() {

		wp_enqueue_script(
			'frontend-upload-audio',
			plugin_dir_url( __FILE__ ) . 'uploader.js',
			array(),
			'1.0',
			true
		);

	}

}
new Frontend_Upload_Audio( $args );
