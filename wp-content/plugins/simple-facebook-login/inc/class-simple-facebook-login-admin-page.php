<?php
/*
Plugin Name: Simple Facebook Login
Plugin URI: https://geek.hellyer.kiwi/products/simple-facebook-login/
Description: A simple implementation of a Facebook login system without any extra junk
Author: Ryan Hellyer
Version: 1.0
Author URI: https://geek.hellyer.kiwi/

Copyright (c) 2018 Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


/**
 * Simple Facebook Login class.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Simple_Facebook_Login_Admin_Page {

	const SLUG = 'simple-facebook-login';

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );

	}


	/**
	 * Init plugin options to white list our options.
	 */
	public function register_setting() {

		register_setting(
			self::SLUG,
			'simple-facebook-login-app-id',
			array( $this, 'sanitize' )
		);

		register_setting(
			self::SLUG,
			'simple-facebook-login-app-secret',
			array( $this, 'sanitize' )
		);

	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {

		add_options_page(
			esc_html__( 'Simple Facebook Login', 'simple-facebook-login' ), // Page title
			esc_html__( 'Facebook Login', 'simple-facebook-login' ),        // Menu title
			'manage_options',                                               // Capability required
			self::SLUG,                                                     // The URL slug
			array( $this, 'admin_page' )                                    // Displays the admin page
		);

	}

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Simple Facebook Login', 'simple-facebook-login' ); ?></h1>

			<form method="post" action="options.php">

				<table class="form-table">
					<tr>
						<th>
							<label for="simple-facebook-login-app-id"><?php _e( 'Facebook App ID', 'simple-facebook-login' ); ?></label>
						</th>
						<td>
							<input type="text" id="simple-facebook-login-app-id" name="simple-facebook-login-app-id" value="<?php echo esc_attr( get_option( 'simple-facebook-login-app-id' ) ); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<label for="simple-facebook-login-app-secret"><?php _e( 'Facebook App secret', 'simple-facebook-login' ); ?></label>
						</th>
						<td>
							<input type="text" id="simple-facebook-login-app-secret" name="simple-facebook-login-app-secret" value="<?php echo esc_attr( get_option( 'simple-facebook-login-app-secret' ) ); ?>" />
						</td>
					</tr>
				</table>

				<?php settings_fields( self::SLUG ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'simple-facebook-login' ); ?>" />
				</p>
			</form>
		</div><?php
	}

	/**
	 * Sanitize the data being saved.
	 *
	 * @param   string   $input   The input string
	 * @return  array             The sanitized string
	 */
	public function sanitize( $input ) {
		$output = sanitize_title( $input );
		return $output;
	}

}
