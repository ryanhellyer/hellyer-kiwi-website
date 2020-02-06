<?php

/**
 * Messages.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Messages extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post', array( $this, 'meta_boxes_save' ), 10, 2 );
		add_action( 'template_redirect', array( $this, 'show_image' ) );

		add_shortcode( 'b', array( $this, 'shortcode_b' ) );
		add_shortcode( 'u', array( $this, 'shortcode_u' ) );
		add_shortcode( 'url', array( $this, 'shortcode_url' ) );
		add_shortcode( 'img', array( $this, 'shortcode_img' ) );
		add_shortcode( 'hide', array( $this, 'shortcode_hide' ) );
		add_shortcode( 'thumbnail', array( $this, 'shortcode_thumbnail' ) );
//		add_filter( 'the_content', array( $this, 'shortcode_fudging' ) );
	}

	/**
	 * Since shortcodes don't perfectly match BB Code, we fudge the syntax a little.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function shortcode_fudging( $content ) {

		$content = get_post_meta( get_the_ID(), '_message', true );
		$content = str_replace( '[url=', '[url temp=', $content );

		return $content;
	}

	/**
	 * Converting messages on frontend to textarea.
	 * Allows for easy copy/paste of code
	 */
	public function html_version() {

		if ( ! isset( $_GET[ 'post' ] ) ) {
			return;
		}

		$message_id = $_GET[ 'post' ];
		$content = get_post_meta( $message_id, '_message', true );

		$html = do_shortcode( $content );

		echo '<h2>' . esc_html__( 'HTML version', 'undiecar' ) . '</h2>';
		echo '<textarea style="height:300px;width:100%;">' . $html . '</textarea>';

	}

	public function shortcode_b( $args = null, $content ) {
		return '<strong>' . $content . '</strong>';
	}

	public function shortcode_u( $args = null, $content ) {
		return '<u>' . $content . '</u>';
	}

	public function shortcode_url( $args, $content = null ) {

		if ( isset( $args['temp'] ) ) {
			$url = $args['temp'];
		} else {
			$url = $content;
		}

		return '<a href="' . esc_url( $url ) . '">' . $content . '</a>';
	}

	public function shortcode_img( $args = null, $url ) {
		return '<img src="' . esc_url( $url ) . '" />';
	}

	public function shortcode_hide( $args = null, $content ) {
		return '';
	}

	public function shortcode_thumbnail() {
		$post_id   = absint( $_GET['post'] );
		$image_url = get_permalink( $post_id );
		$image_url = add_query_arg( 'driver', '[NAME]', $image_url );

		return '[img]' . esc_html( $image_url ) /*Don't use esc_url() due to it removing shortcodes */ . '[/img]';
	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'message',
			array(

				'publicly_queryable' => false,
				'show_in_nav_menus' => false,
				'show_in_menu' => true,

				'exclude_from_search' => true,
				'show_ui' => true,

				'publicly_queryable' => true,
				'label'              => esc_html__( 'Messages', 'undiecar' ),
				'supports'           => array( 'title', 'thumbnail' ),
				'menu_icon'          => 'dashicons-flag',
			)
		);

		register_post_type(
			'message-chunk',
			array(
				'public'             => true,
				'publicly_queryable' => false,
				'label'              => esc_html__( 'Message chunks', 'undiecar' ),
				'supports'           => array( 'title' ),
				'show_in_menu'           => 'edit.php?post_type=message',
			)
		);

	}

	/**
	 * Add admin metaboxes.
	 */
	public function add_metaboxes() {

		add_meta_box(
			'message', // ID
			__( 'Message', 'undiecar' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			array( 'message', 'message-chunk' ), // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'core'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

		add_meta_box(
			'message_output', // ID
			__( 'Message output', 'undiecar' ), // Title
			array(
				$this,
				'html_version', // Callback to method to display HTML
			),
			array( 'message', 'message-chunk' ), // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'core'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

		add_meta_box(
			'message_recipients', // ID
			__( 'Message recipients', 'undiecar' ), // Title
			array(
				$this,
				'recipients', // Callback to method to display HTML
			),
			array( 'message', 'message-chunk' ), // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'core'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

	}

	/**
	 * Output the message meta box.
	 */
	public function meta_box() {

		$html = '
			<p>
				<textarea style="width:100%;min-height:300px;" name="_message" id="_message">' . esc_textarea( get_post_meta( get_the_ID(), '_message', true ) ) . '</textarea>
			</p>

			<input type="hidden" id="message-nonce" name="message-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">';


		if ( 'message-chunk' === get_post_type() ) {
			//
		} else {

			$buttons = '<p style="font-size:14px;font-family:monospace">';

			$query = new WP_Query( array(
				'posts_per_page'         => 100,
				'post_type'              => 'message-chunk',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			) );
			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {
					$query->the_post();

					$buttons .= '[' . esc_html( sanitize_title( get_the_title( get_the_ID() ) ) ) . '] &nbsp; ';

				}

				wp_reset_postdata();
			}
			$buttons .= '</p>';

			$html = $buttons . $html;
		}

		echo $html;
	}

	/**
	 * Display the recipients in a metabox.
	 */
	public function recipients() {

		if ( isset( $_GET['post'] ) ) {
			$log   = get_post_meta( $_GET['post'], 'log', true );
			$log   = array_unique( $log );

			echo '<ul>';
			foreach ( $log as $key => $driver_name ) {
				echo '<li><a href="' . esc_url( home_url( '/member/' . sanitize_title( $driver_name ) . '/' ) ) . '">' . esc_html( $driver_name ) . '</a></li>';
			}
			echo '</ul>';

		} else {
			echo 'coming later ...';
		}

	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['_message'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['message-nonce'], __FILE__ ) ) {
				return;
			}

			// Sanitize and store the data
			$_message = wp_kses_post( $_POST['_message'] );
			update_post_meta( $post_id, '_message', $_message );
		}

	}

	/**
	 * Show featured image.
	 * Used for tracking views on messages read.
	 */
	public function show_image() {

		// Bail out if not on a message post.
		if ( 'message' !== get_post_type() || ! isset( $_GET['driver'] ) ) {
			return;
		}

		// Load main loop.
		if ( have_posts() ) {
			while ( have_posts() ) {
				the_post();

				$image_key = 'message_' . get_the_ID() . '_image';
				if ( false === ( $image_contents = get_transient( $image_key ) ) ) {
					$image       = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
					$src         = $image[0];
					$uploads_dir = wp_upload_dir();
					$file        = str_replace( $uploads_dir['url'], $uploads_dir['path'], $src );

					$image_contents = file_get_contents( $file );

					set_transient( $image_key, $image_contents, HOUR_IN_SECONDS );
				}

				// Update log meta.
				$log   = get_post_meta( get_the_ID(), 'log', true );
				$log[] = $_GET['driver'];
				$log   = array_unique( $log );
				update_post_meta( get_the_ID(), 'log', $log );

				// Output the file.
				header( 'Content-Type: image/jpg' );
				header( 'Content-Length: ' . (string) ( filesize( $file ) ) );
				echo $image_contents;
				die;
			}

		} else {
			require( get_template_directory() . '/404.php' );
		}

	}

}
