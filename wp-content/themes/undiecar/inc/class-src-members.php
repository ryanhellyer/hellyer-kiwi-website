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
		add_action( 'init',                array( $this, 'unsubscribe' ) );
		add_action( 'init',                array( $this, 'save' ) );
		add_filter( 'init',                array( $this, 'member_template' ), 99 );
		add_filter( 'get_avatar',          array( $this, 'avatar_filter' ) , 1 , 5 );
		add_filter( 'get_avatar_url',      array( $this, 'custom_avatar' ), 1 , 5 );
		add_shortcode( 'undiecar_drivers', array( $this, 'display_driver_table' ) );
	}

	public function unsubscribe() {

		if ( '/member/' === substr( $_SERVER['REQUEST_URI'], 0, 8 ) ) {

			$chunks      = explode( '/', $_SERVER['REQUEST_URI'] );
			$driver_slug = $chunks[2];
			$query_var   = str_replace( '?', '', $chunks[3] );
			$decoded     = base64_decode( $query_var );
			$options     = explode( '|', $decoded );
			$driver_name = $options[0];
			$option      = $options[1];

			if ( 'receive_notifications' === $option ) {
				$member = get_user_by( 'login', $driver_slug );
				if ( is_object( $member ) ) {
					$member_id = $member->ID;
					update_user_meta( $member_id, 'receive_notifications', 'no' );
				}

				wp_safe_redirect( home_url() . '/unsubscribed/', 302 );
				die;
			}

			if ( 'receive_less_notifications' === $option ) {
				$member = get_user_by( 'login', $driver_slug );
				if ( is_object( $member ) ) {
					$member_id = $member->ID;
					update_user_meta( $member_id, 'receive_less_notifications', 'yes' );
				}

				wp_safe_redirect( home_url() . '/less-notifications/', 302 );
				die;
			}

		}

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
			(
				absint( $_POST['member-id'] ) === get_current_user_id()
				||
				is_super_admin()
			)
		) {

			$member_id = absint( $_POST['member-id'] );

			/**
			 * Handle file uploads.
			 */
			require_once ( ABSPATH . 'wp-admin/includes/file.php' );
			require_once ( ABSPATH . 'wp-admin/includes/image.php' );

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

				// Resize the attachments
				$attach_data = wp_generate_attachment_metadata( $attachment_id, $file_name );
				wp_update_attachment_metadata( $attachment_id, $attach_data );

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
					'sanitize' => 'wp_kses_post',
				),
				'car-number'         => array(
					'meta_key' => 'car_number',
					'sanitize' => 'digits',
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
				'season'             => array(
					'meta_key' => 'season',
					'sanitize' => 'wp_kses_limited',
				),
			);

			// Don't let regular users set their season
			if ( is_super_admin() ) {
				$user_meta['team'] = array(
					'meta_key' => 'team',
					'sanitize' => 'wp_kses_limited',
				);
				$user_meta['note'] = array(
					'meta_key' => 'note',
					'sanitize' => 'wp_kses_limited',
				);
				$user_meta['invited'] = array(
					'meta_key' => 'invited',
					'sanitize' => 'wp_kses_limited',
				);
				$user_meta['former_champion'] = array(
					'meta_key' => 'former_champion',
					'sanitize' => 'wp_kses_limited',
				);
			}

			foreach ( $user_meta as $field_key => $x ) {

				unset( $value );
				if ( isset( $_POST[$field_key] ) ) {

					if ( 'wp_kses_limited' === $x['sanitize'] ) {

						if ( is_array( $_POST[$field_key] ) ) {

							foreach ( $_POST[$field_key] as $a => $b ) {
								$value[$a] = wp_kses_post( $b );
								$value[$a] = strip_tags( $value[$a] );
								$value[$a] = substr( $value[$a], 0, 30 );
							}

						} else {
							$value = wp_kses_post( $_POST[$field_key] );
							$value = strip_tags( $value );
							$value = substr( $value, 0, 30 );
						}

					} else if ( 'wp_kses_post' === $x['sanitize'] ) {
						$value = wp_kses_post( $_POST[$field_key] );
						$value = substr( $value, 0, 3000 );
					} else if ( 'digits' === $x['sanitize'] ) {

						if ( ctype_digit( $_POST[$field_key] ) ) {
							$value = esc_html( $_POST[$field_key] );
						} else {
							$value = absint( $_POST[$field_key] );
						}

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

			// Save driver to each season
			if ( isset( $_POST['multi-season'] ) ) {
				$multi_season = $_POST['multi-season'];
				foreach ( $multi_season as $season_id => $x ) {

					$drivers = get_post_meta( $season_id, 'drivers', true );
					if ( ! is_array( $drivers ) || ! in_array( $member_id, $drivers ) ) {
						$drivers[] = $member_id;
						update_post_meta( $season_id, 'drivers', $drivers );
					}

				}
			}

			// Process checkbox
			if ( isset( $_POST['receive-notifications'] ) ) {
				$receive_notifications = 'yes';
			} else {
				$receive_notifications = 'no';
			}
			update_user_meta( $member_id, 'receive_notifications', $receive_notifications );

			// Process checkbox
			if ( isset( $_POST['receive-less-notifications'] ) ) {
				$receive_less_notifications = 'yes';
			} else {
				$receive_less_notifications = 'no';
			}
			update_user_meta( $member_id, 'receive_less_notifications', $receive_less_notifications );

			// Set the password
			if ( isset( $_POST['password'] ) && '' !== $_POST['password'] ) {
				$password = $_POST['password'];

				wp_update_user(
					array(
						'ID'        => $member_id,
						'user_pass' => $password
					)
				);

				update_user_meta( $member_id, 'password_set', true );

				$member_info = get_userdata( $member_id );
				$username = sanitize_title( $member_info->data->display_name );

				$credentials = array();
				$credentials['user_login']    = $username;
				$credentials['user_password'] = $password;
				$credentials['remember']      = true;

				$user = wp_signon( $credentials, false );
				if ( is_wp_error( $user ) ) {
					wp_die( 'uh oh! Something went wrong. Please private message "Ryan Hellyer" on iRacing and let him know that error #248 occurred.' );
				} else {}

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
		$member_path = str_replace( $_SERVER['HTTP_HOST'], '', $member_path );
		$member_path = $member_path . '/member/';

		// If path isn't even in the REQUEST_URI, then we aint on a members page
		if ( strpos( $_SERVER['REQUEST_URI'], $member_path ) !== false ) {
			//
		} else {
			return $template;
		}

		$member_slug = str_replace( $member_path, '', $_SERVER['REQUEST_URI'] );
		$member_slug = str_replace( '/', '', $member_slug );
		$member_slug = str_replace( '%20', '-', $member_slug );

		// Redirect if name not quite correct
		if (
			sanitize_title( $member_slug ) !== $member_slug
			||
			$_SERVER['REQUEST_URI'] === $member_path . sanitize_title( $member_slug )
		) {
			wp_redirect(
				$member_path . sanitize_title( $member_slug ) . '/',
				301
			);
		}

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
		$member_id = $src_member->ID;

		// Look for user submitted URL
		$header_image_id = get_user_meta( $member_id, 'header_image', true );
		$header_image = wp_get_attachment_image_src( $header_image_id, 'src-featured' );
		if ( isset( $header_image[0] ) && '' !== $header_image[0] ) {
			return $header_image[0];
		}

		// Try to fall back to gallery image
		$images = get_user_meta( $member_id, 'images', true );
		if ( is_array( $images ) ) {
			krsort( $images );
			foreach( $images as $key => $image_id ) {
				$image = wp_get_attachment_image_src( $image_id, 'src-featured' );
				if ( isset( $image[0] ) ) {
					return $image[0];
				}
			}
		}

		return $image_url;
	}

	public function display_driver_table( $args, $content ) {

		$drivers = array();
		if ( isset( $args['season'] ) ) {
			$season = $args['season'];

			$season_post = get_page_by_path( $season, 'array', 'season' );

			if ( ! isset( $season_post->ID ) ) {
				return $content;
			}

			$season_id = $season_post->ID;
			$driver_names = get_post_meta( $season_id, 'drivers', true  );
			$driver_names = explode( "\n", $driver_names );

			foreach ( $driver_names as $key => $driver_name ) {
				$driver_slug = sanitize_title( $driver_name );
				$driver_object = get_user_by( 'login', $driver_slug );

				if ( isset( $driver_object->ID ) ) {
					$driver_id = $driver_object->ID;

					if ( 'no' !== get_user_meta( $driver_id, 'receive_notifications', true ) ) {
						$drivers_to_notify[ $driver_slug ] = $driver_id;
					}

					if ( isset( $driver_id  ) ) {
						$drivers[ $driver_slug ] = $driver_id ;
					}

				} else {
					$errors[] = $driver_name;
				}

			}

		} else {

			// No season set, so lets just dump them all out
			$all_drivers = get_users( array( 'number' => 2000 ) );
			foreach ( $all_drivers as $driver ) {
				$driver_slug = $driver->data->user_login;
				$driver_id = $driver->ID;

				if ( 'no' !== get_user_meta( $driver_id, 'receive_notifications', true ) ) {

					$drivers_to_notify[ $driver_slug ] = $driver_id;
					if ( 'yes' !== get_user_meta( $driver_id, 'receive_less_notifications', true ) ) {
						$drivers_to_rarely_notify[ $driver_slug ] = $driver_id;
					}
				}

				$drivers[ $driver_slug ] = $driver_id;
			}

		}

		ksort( $drivers );

		$content .= '
		<table class="some-list" id="src-schedule">
			<thead>
				<tr>
					<th class="col">#</th>
					<th class="col-event">Driver Name</th>
					<th class="col-event">Number</th>
				</tr>
			</thead>
			<tbody>';

		$count = 0;
		foreach ( $drivers as $key => $driver_id ) {
			$count++;

			$driver = get_userdata( $driver_id );
			$driver_name = $driver->display_name;

			if ( 'banned' != get_user_meta( $driver_id, 'season', true ) ) {
				$content .= '
				<tr>
					<td>' . esc_html( $count ) . '</td>
					<td><a href="' . esc_url( home_url() . '/member/' . sanitize_title( $driver_name ) . '/' ) . '">' . esc_html( $driver_name ) . '</a></td>
					<td>' . esc_html( get_user_meta( $driver_id, 'car_number', true ) ) . '</td>
				</tr>';
			}

		}

		$content .= '
			</tbody>
		</table>
		';

		if ( is_super_admin() ) {

//$drivers_to_rarely_notify
			$drivers_list = $this->drivers_to_notify( $drivers_to_notify );
			$number = count( explode( ',', $drivers_list ) );
			$content .= '<h3>Drivers to message. Total count ' . esc_html( $number ) . '</h3>';
			$content .= '<textarea style="font-family:monospace;font-size:12px;margin:20px 0;height:100px;">';
			$content .= $drivers_list;
			$content .= '</textarea>';

			$drivers_list = $this->drivers_to_notify( $drivers_to_rarely_notify );
			$number = count( explode( ',', $drivers_list ) );
			$content .= '<h3>Drivers to regularly message. Total count ' . esc_html( $number ) . '</h3>';
			$content .= '<textarea style="font-family:monospace;font-size:12px;margin:20px 0;height:100px;">';
			$content .= $drivers_list;
			$content .= '</textarea>';

			if ( isset( $errors ) ) {

				$content .= '<h3>The following drivers are listed as being members, but are not registered on the website</h3>';
				$content .= '<p>';

				foreach ( $errors as $key => $driver_name )  {
					$content .= $driver_name . '<br />';
				}

				$content .= '</p>';
			}

		}

		return $content;
	}

	function custom_avatar( $avatar_url, $member_id ) {

		if ( '' !== get_user_meta( $member_id, 'avatar', true ) ) {
			$attachment_id = get_user_meta( $member_id, 'avatar', true );
			$image = wp_get_attachment_image_src( $attachment_id, 'thumb' );
			return $image[0];
		}

		return $avatar_url;
	}

	public function drivers_to_notify( $drivers ) {
		// Get drivers to message.
		$messages = 'Iberia,Brazil';
		$drivers_list = '';

		foreach ( $drivers as $driver_slug => $driver_id )  {

			$remove_clubs = explode( ',', $messages );
			foreach ( $remove_clubs as $club ) {
				if ( $club === get_user_meta( $driver_id, 'club', true ) ) {
					$remove = true;
				}
			}

			if (
				(
					'banned' != get_user_meta( $driver_id, 'season', true )
					&&
					! isset( $remove )
					&&
					'someunwanteddriver' !== $driver_id
					&&
					'someunwanteddriver' !== $driver_id
				)
				||
				'josu-solaguren' === $driver_slug
				||
				'kleber-bottaro-moura' === $driver_slug

			) {
				$driver = get_userdata( $driver_id );
				$driver_name = $driver->display_name;
				$drivers_list .= $driver_name . ',';
			}

			unset( $remove );

		}

		return $drivers_list;
	}

}
