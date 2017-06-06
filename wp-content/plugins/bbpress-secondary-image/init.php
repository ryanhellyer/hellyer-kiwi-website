<?php
/**
 * Plugin Name: bbPress secondary image
 * Description: Adds second bbPress profile image. Based on avatar plugin by Jared Atchison.
 * Version:     1.0
 * Author:      Ryan Hellyer / Jared Atchison
 * Author URI:  http://jaredatchison.com
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WP Forms. If not, see <http://www.gnu.org/licenses/>.
 */

class bbPress_Secondary_Image {

	/**
	 * User ID
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $user_id_being_edited;

	/**
	 * Initialize all the things
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Text domain
		$this->load_textdomain();

		// Actions
		add_action( 'show_user_profile',         array( $this, 'edit_user_profile'        )        );
		add_action( 'edit_user_profile',         array( $this, 'edit_user_profile'        )        );
		add_action( 'personal_options_update',   array( $this, 'edit_user_profile_update' )        );
		add_action( 'edit_user_profile_update',  array( $this, 'edit_user_profile_update' )        );
		add_action( 'bbp_user_edit_after_about', array( $this, 'bbpress_user_profile'     )        );
	}

	/**
	 * Loads the plugin language files.
	 *
	 * @since 1.0.1
	 */
	public function load_textdomain() {
		$domain = 'bbpress-secondary-image';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Form to display on the user profile edit screen
	 *
	 * @since 1.0.0
	 * @param object $profileuser
	 * @return
	 */
	public function edit_user_profile( $profileuser ) {

		// bbPress will try to auto-add this to user profiles - don't let it.
		// Instead we hook our own proper function that displays cleaner.
		if ( function_exists( 'is_bbpress') && is_bbpress() ) {
			return;
		}

		?>

		<h3><?php _e( 'Secondary Image', 'bbpress-secondary-image' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="bbpress-secondary-image"><?php _e( 'Upload Secondary Image', 'bbpress-secondary-image' ); ?></label></th>
				<td style="width: 50px;" valign="top">
					<?php
					$secondary_image = get_user_meta( $profileuser->ID, 'bbpress_secondary_image', true );
					if ( isset( $secondary_image['full'] ) ) {
						$url = $secondary_image['full'];
						echo '<img src="' . esc_url( $url ) . '" width="100" />';
					}
					?>
				</td>
				<td>
				<?php

				// Nonce security ftw
				wp_nonce_field( 'bbpress_secondary_image_nonce', '_bbpress_secondary_image_nonce', false );
				
				// File upload input
				echo '<input type="file" name="bbpress-secondary-image" id="bbpress-secondary-image" /><br />';

				if ( empty( $profileuser->bbpress_secondary_image ) ) {
					echo '<span class="description">' . __( 'No local avatar is set. Use the upload field to add a local avatar.', 'bbpress-secondary-image' ) . '</span>';
				} else {
					echo '<input type="checkbox" name="bbpress-secondary-image-erase" value="1" /> ' . __( 'Delete local avatar', 'bbpress-secondary-image' ) . '<br />';
					echo '<span class="description">' . __( 'Replace the local avatar by uploading a new avatar, or erase the local avatar (falling back to a gravatar) by checking the delete option.', 'bbpress-secondary-image' ) . '</span>';
				}

				?>
				</td>
			</tr>
		</table>
		<script type="text/javascript">var form = document.getElementById('your-profile');form.encoding = 'multipart/form-data';form.setAttribute('enctype', 'multipart/form-data');</script>
		<?php
	}

	/**
	 * Update the user's avatar setting
	 *
	 * @since 1.0.0
	 * @param int $user_id
	 */
	public function edit_user_profile_update( $user_id ) {

		// Check for nonce otherwise bail
		if ( ! isset( $_POST['_bbpress_secondary_image_nonce'] ) || ! wp_verify_nonce( $_POST['_bbpress_secondary_image_nonce'], 'bbpress_secondary_image_nonce' ) ) {
			return;
		}

		if ( ! empty( $_FILES['bbpress-secondary-image']['name'] ) ) {

			// Allowed file extensions/types
			$mimes = array(
				'jpg|jpeg|jpe' => 'image/jpeg',
				'gif'          => 'image/gif',
				'png'          => 'image/png',
			);

			// Front end support - shortcode, bbPress, etc
			if ( ! function_exists( 'wp_handle_upload' ) ) {
				require_once ABSPATH . 'wp-admin/includes/file.php';
			}

			// Delete old images if successful
			$this->avatar_delete( $user_id );

			// Need to be more secure since low privelege users can upload
			if ( strstr( $_FILES['bbpress-secondary-image']['name'], '.php' ) ) {
				wp_die( 'For security reasons, the extension ".php" cannot be in your file name.' );
			}

			// Make user_id known to unique_filename_callback function
			$this->user_id_being_edited = $user_id; 
			$avatar = wp_handle_upload( $_FILES['bbpress-secondary-image'], array( 'mimes' => $mimes, 'test_form' => false, 'unique_filename_callback' => array( $this, 'unique_filename_callback' ) ) );

			// Handle failures
			if ( empty( $avatar['file'] ) ) {  
				switch ( $avatar['error'] ) {
				case 'File type does not meet security guidelines. Try another.' :
					add_action( 'user_profile_update_errors', create_function( '$a', '$a->add("avatar_error",__("Please upload a valid image file for the avatar.","bbpress-secondary-image"));' ) );
					break;
				default :
					add_action( 'user_profile_update_errors', create_function( '$a', '$a->add("avatar_error","<strong>".__("There was an error uploading the avatar:","bbpress-secondary-image")."</strong> ' . esc_attr( $avatar['error'] ) . '");' ) );
				}
				return;
			}

			// Save user information (overwriting previous)
			update_user_meta( $user_id, 'bbpress_secondary_image', array( 'full' => $avatar['url'] ) );

		} elseif ( ! empty( $_POST['bbpress-secondary-image-erase'] ) ) {
			// Nuke the current avatar
			$this->avatar_delete( $user_id );
		}

//echo 'xxx';die;
	}

	/**
	 * Form to display on the bbPress user profile edit screen
	 *
	 * @since 1.0.0
	 */
	public function bbpress_user_profile() {

		if ( ! bbp_is_user_home_edit() ) {
			return;
		}

		$user_id     = get_current_user_id();
		$profileuser = get_userdata( $user_id );

		echo '<div>';
			echo '<label for="bbpress-secondary-image">' . __( 'Secondary Image', 'bbpress-secondary-image' ) . '</label>';
 			echo '<fieldset class="bbp-form avatar">';

				$secondary_image = get_user_meta( $profileuser->ID, 'bbpress_secondary_image', true );
//echo 'xxxx';
				if ( isset( $secondary_image['full'] ) ) {
					$url = $secondary_image['full'];
					echo '<img src="' . esc_url( $url ) . '" width="100" />';
				}

				// Nonce security ftw
				wp_nonce_field( 'bbpress_secondary_image_nonce', '_bbpress_secondary_image_nonce', false );
				
				// File upload input
				echo '<br /><input type="file" name="bbpress-secondary-image" id="bbpress-secondary-image" /><br />';

				if ( empty( $profileuser->bbpress_secondary_image ) ) {
					echo '<span class="description" style="margin-left:0;">' . __( 'No local avatar is set. Use the upload field to add a local avatar.', 'bbpress-secondary-image' ) . '</span>';
				}

			echo '</fieldset>';
		echo '</div>';
		?>
		<script type="text/javascript">var form = document.getElementById('bbp-your-profile');form.encoding = 'multipart/form-data';form.setAttribute('enctype', 'multipart/form-data');</script>
		<?php
	}

	/**
	 * Delete avatars based on user_id
	 *
	 * @since 1.0.0
	 * @param int $user_id
	 */
	public function avatar_delete( $user_id ) {
		$old_avatars = get_user_meta( $user_id, 'bbpress_secondary_image', true );
		$upload_path = wp_upload_dir();

		if ( is_array( $old_avatars ) ) {
			foreach ( $old_avatars as $old_avatar ) {
				$old_avatar_path = str_replace( $upload_path['baseurl'], $upload_path['basedir'], $old_avatar );
				@unlink( $old_avatar_path );
			}
		}

		delete_user_meta( $user_id, 'bbpress_secondary_image' );
	}

	/**
	 * File names are magic
	 *
	 * @since 1.0.0
	 * @param string $dir
	 * @param string $name
	 * @param string $ext
	 * @return string
	 */
	public function unique_filename_callback( $dir, $name, $ext ) {
		$user = get_user_by( 'id', (int) $this->user_id_being_edited );
		$name = $base_name = sanitize_file_name( $user->display_name . '_avatar' );
		$number = 1;

		while ( file_exists( $dir . "/$name$ext" ) ) {
			$name = $base_name . '_' . $number;
			$number++;
		}

		return $name . $ext;
	}
}
new bbPress_Secondary_Image;
