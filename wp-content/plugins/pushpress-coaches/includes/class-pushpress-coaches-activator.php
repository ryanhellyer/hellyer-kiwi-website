<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    pushpress_coaches
 * @subpackage pushpress_coaches/includes
 */

class Pushpress_Coaches_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
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
