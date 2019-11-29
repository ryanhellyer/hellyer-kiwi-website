<?php

/**
 * Adds a link to the settings page on the plugins page.
 */
class Auto_XML_Backup_Plugin_Link extends Auto_XML_Backup_Abstract {

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		$file = dirname( dirname( __FILE__ ) ) . '/auto-xml-backup.php';
		$plugin = plugin_basename( $file );
		add_filter( "plugin_action_links_$plugin", array( $this, 'add_settings_link' ) );
	}

	/**
	 * Add the settings page link to the plugins page.
	 *
	 * @param  array  $links  The existing plugins page links
	 * @return array  The modified plugins page links
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="' . esc_url( $this->get_settings_url() ) . '">' . __( 'Settings', 'auto-xml-backup' ) . '</a>';
		array_push( $links, $settings_link );
		return $links;
	}

}
