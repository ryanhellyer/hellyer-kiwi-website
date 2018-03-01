<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    pushpress_schedule
 * @subpackage pushpress_schedule/includes
 */

class PushPress_Schedule_Activator {

	/**
	 * Class constructor.
	 */
	public static function activate() {

		if (
			! is_plugin_active( 'pushpress-connect/pushpress-connect.php' )
			&&
			current_user_can( 'activate_plugins' )
		) {

			// Stop activation redirect and show error
			wp_die('Sorry, but this plugin requires the PushPress Connect Plugin to be installed and active. <br><a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">&laquo; Return to Plugins</a>');

		}

	}

}
register_activation_hook( dirname( dirname( __FILE__ ) ) . '/pushpress-schedule.php', 'activate_pushpress_schedule' );
