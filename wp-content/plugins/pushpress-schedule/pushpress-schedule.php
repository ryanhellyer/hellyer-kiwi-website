<?php

/**
 * Plugin Name:       PushPress Schedule
 * Plugin URI:        
 * Description:       Displays your PushPress Calendar on your website.
 * Version:           1.0.0
 * Author:            PushPress, Inc.
 * Author URI:        http://sites.pushpress.com/
 * Text Domain:       pushpress_schedule
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

add_action(
	'plugins_loaded',
	function() {

		if ( class_exists( 'PushPress_Connect' ) ) {

			define( 'PUSHPRESS_SCHEDULE_URL', plugins_url( '' , __FILE__ ) );
			define( 'PUSHPRESS_SCHEDULE_DIR', dirname( __FILE__ ) );

			// Load the plugin
			require( plugin_dir_path( __FILE__ ) . 'includes/class-pushpress-schedule.php' );

			$plugin = new Pushpress_Schedule();
			$plugin->run();

			// Add admin menu
			function pushpress_schedule_admin_menu() {
				add_submenu_page( 'pushpress', esc_html__( 'PushPress Schedule', 'pushpress-schedule' ), esc_html__( 'Schedule', 'pushpress-schedule' ), 'manage-options', 'pushpress-schedule', array( 'PushPress_Schedule_Admin', 'index' ) );
			}
			add_action('admin_menu', 'pushpress_schedule_admin_menu');


		} else {

			// If PushPress Connect plugin not loaded, then serve error message
			add_action( 'admin_notices', function() {
				echo '
				<div class="notice notice-error is-dismissible">
					<p>' . esc_html__( 'Error: The PushPress Connect plugin is required for the PushPress Schedule plugin to work correctly.', 'pushpress-sites' ) . '</p>
				</div>';
			} );

		}

	}
);
