<?php

/**
 * Fired during plugin activation
 *
 * @since      1.0.0
 *
 * @package    pushpress_lead_form
 * @subpackage pushpress_lead_form/includes
 */

class Pushpress_Lead_Form_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		// Require parent plugin
		if ( ! is_plugin_active( 'pushpress-connect/pushpress-connect.php' ) and current_user_can( 'activate_plugins' ) ) {
			// Stop activation redirect and show error
			wp_die( 'Sorry, but this plugin requires the PushPress Connect Plugin to be installed and active. <br><a href="' . esc_url( admin_url( 'plugins.php' ) ) . '">&laquo; Return to Plugins</a>' );
		}

	}

}
