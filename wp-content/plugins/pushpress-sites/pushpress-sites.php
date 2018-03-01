<?php
/*
 * Plugin Name: PushPress Sites
 * Plugin URI: https://pushpress.com
 * Description: Creates shortcodes from a gravity form.
 * Author: PushPress, Inc
 * Version: 1.0
 * Author URI: https://pushpress.com
 */

require( 'inc/pushpress_shortcode.php' );

add_action(
	'plugins_loaded',
	function() {

		if ( class_exists( 'PushPress_Connect' ) ) {

			// Load the plugin
			require( 'class-pushpress-sites.php' );
			new PushPress_Sites;

		} else {

			// If PushPress Connect plugin not loaded, then serve error message
			add_action( 'admin_notices', function() {
				echo '
				<div class="notice notice-error is-dismissible">
					<p>' . esc_html__( 'Error: The PushPress Connect plugin is required for the PushPress Sites plugin to work correctly.', 'pushpress-sites' ) . '</p>
				</div>';
			} );

		}

	}
);
