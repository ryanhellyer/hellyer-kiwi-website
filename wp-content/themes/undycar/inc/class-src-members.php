<?php

/**
 * Member profiles.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Members extends SRC_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'init',       array( $this, 'save' ) );
		add_filter( 'init',       array( $this, 'member_template' ), 99 );
		add_filter( 'get_avatar', array( $this, 'avatar_filter' ) , 1 , 5 );
	}

	/**
	 * Filter the avatar URL.
	 * Replaces Gravatar with the member uploaded one where possible.
	 */
	public function avatar_filter( $avatar, $member_id, $size, $default, $alt ) {
		$user = false;

		$avatar_id = get_user_meta( $member_id, 'avatar', true );
		if ( '' !== $avatar_id ) {
			$avatar_data = wp_get_attachment_image_src( $avatar_id, 'medium' );

			if ( isset( $avatar_data[0] ) && '' !== $avatar_data[0] ) {
				$avatar_url = $avatar_data[0];
				$avatar = '<img alt="' . esc_attr( $alt ) . '" src="' . esc_url( $avatar_url ) . '" class="avatar" />';
			}

		}

		return $avatar;
	}

	public function save() {

		if (
			isset( $_POST['src_nonce'] )
			&&
			wp_verify_nonce( $_POST['src_nonce'], 'src_nonce' )
			&&
			isset( $_POST['member-id'] )
			&&
			is_user_logged_in()
			&&
			( is_super_admin() || $_POST['member-id'] === get_current_user_id() )
		) {

			$member_id = absint( $_POST['member-id'] );

			/**
			 * Handle file uploads.
			 */
			require_once ( ABSPATH . 'wp-admin/includes/file.php' );

			foreach ( $_FILES as $key => $file ) {

				if ( '' === $file['name'] ) {
					continue;
				}

				$overrides = array( 'test_form' => false);
				$result = wp_handle_upload( $file, $overrides );
				$file_name = $result['file'];

				$member_info = get_userdata( $member_id );

				$filetype = wp_check_filetype( basename( $result['file'] ), null );
				$wp_upload_dir = wp_upload_dir();
				$attachment = array(
					'guid'           => $wp_upload_dir['url'] . '/' . basename( $file_name ), 
					'post_mime_type' => $filetype['type'],
					'post_title'     => wp_kses_post( $member_info->display_name ) . ' avatar',
					'post_content'   => '',
					'post_status'    => 'inherit'
				);

				$attachment_id = wp_insert_attachment( $attachment, $file_name, get_the_ID() );
				$old_attachment_id = get_user_meta( $member_id, $key, true );
				wp_delete_attachment( $old_attachment_id );
				update_user_meta( $member_id, $key, $attachment_id );
			}

			/**
			 * Handle normal field inputs.
			 */
			$user_meta = array(
				'location'           => array(
					'meta_key' => 'location',
					'sanitize' => 'wp_kses_limited',
				),
				'nationality'           => array(
					'meta_key' => 'nationality',
					'sanitize' => 'wp_kses_limited',
				),
				'description'        => array(
					'meta_key' => 'description',
					'sanitize' => 'wp_kses_limited',
				),
				'car-number'         => array(
					'meta_key' => 'car_number',
					'sanitize' => 'number',
				),
				'racing-experience'  => array(
					'meta_key' => 'racing_experience',
					'sanitize' => 'wp_kses_post',
				),
				'first-racing-games' => array(
					'meta_key' => 'first_racing_games',
					'sanitize' => 'wp_kses_post',
				),
				'twitter'            => array(
					'meta_key' => 'twitter',
					'sanitize' => 'esc_url',
				),
				'facebook'           => array(
					'meta_key' => 'facebook',
					'sanitize' => 'esc_url',
				),
				'youtube'            => array(
					'meta_key' => 'youtube',
					'sanitize' => 'esc_url',
				),
			);

			foreach ( $user_meta as $field_key => $x ) {

				unset( $value );
				if ( isset( $_POST[$field_key] ) ) {

					if ( 'wp_kses_limited' === $x['sanitize'] ) {
						$value = wp_kses_post( $_POST[$field_key] );
						$value = strip_tags( $value );
						$value = substr( $value, 0, 30 );
					} else if ( 'wp_kses_post' === $x['sanitize'] ) {
						$value = wp_kses_post( $_POST[$field_key] );
						$value = substr( $value, 0, 3000 );
					} else if ( 'number' === $x['sanitize'] ) {
						$value = absint( $_POST[$field_key] );
					} else if ( 'esc_url' === $x['sanitize'] ) {
						$value = esc_url( $_POST[$field_key] );
					}

					if ( isset( $value ) ) {
						update_user_meta( $member_id, $x['meta_key'], $value );
					}

				}

			}

		} else if ( isset( $_POST['src_nonce'] ) ) {
			wp_die( '<strong>Error:</strong> Form could not be processed due to a nonce error. You should never have seen this error. Please contact an admin and let them know this occurred and what you were doing when it happened.' );
		}

	}

	/**
	 * Set member template.
	 *
	 * @param  string  $template  The template being used
	 * @global object  $user  the current users object
	 * @return string  The new template
	 */
	public function member_template( $template ) {
		global $src_member;

		$member_path = str_replace( 'http://', '', home_url() );
		$member_path = str_replace( 'https://', '', $member_path );
		$member_path = str_replace( $_SERVER['SERVER_NAME'], '', $member_path );
		$member_path = $member_path . '/member/';
echo "\n\n\n".$_SERVER['REQUEST_URI'] . "\n--------\n" . $member_path
		// If path isn't even in the REQUEST_URI, then we aint on a members page
		if ( strpos( $_SERVER['REQUEST_URI'], $member_path ) !== false ) {
			//
		} else {
			return $template;
		}

		$member_slug = str_replace( $member_path, '', $_SERVER['REQUEST_URI'] );
		$member_slug = str_replace( '/', '', $member_slug );
		$src_member = get_user_by( 'login', $member_slug );

		if ( is_object( $src_member ) ) {
			 /**
			 * Prevent WordPress from returning a 404 status
			 */
			global $wp_query;
			$wp_query->is_404 = false;

			add_filter( 'src_featured_image_url', array( $this, 'filter_featured_image_url' ) );

			require( get_template_directory() . '/member.php' );exit;
			//$new_template = locate_template( array( 'member.php' ) );
			//return $new_template;
		}

		return $template;
	}

	/**
	 * Use members featured image.
	 *
	 * @param   string  $image_url  The featured image URL
	 * @global  object  $src_member The current page members object
	 * @return  string  The modified image URL
	 */
	public function filter_featured_image_url( $image_url ) {
		global $src_member;

		$header_image_id = get_user_meta( $src_member->ID, 'header_image', true );
		$header_image = wp_get_attachment_image_src( $header_image_id, 'full' );
		if ( isset( $header_image[0] ) && '' !== $header_image[0] ) {
			return $header_image[0];
		}

		return $image_url;
	}

}
