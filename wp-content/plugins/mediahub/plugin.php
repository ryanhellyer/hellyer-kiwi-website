<?php
/*
Plugin Name: MediaHub Content API
Plugin URI: http://forsitemedia.nl/
Description: Haalt content op uit MediaHub.
Author: ForSite Media, Daan Kortenbach
Version: 1.2
Author URI: http://forsitemedia.nl/
License: GPLv2
*/

/*  Copyright 2013  Daan Kortenbach  (email : daan@forsitemedia.nl)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/**
 * Initialize the plugin.
 */
class MediaHub_Init {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'localization' ), 5 );
		add_action( 'plugins_loaded', array( $this, 'plugin_init' ), 6 );
	}
	
	public function plugin_init() {

		/**
		 * Load the plugin.
		 * Users of version 2 or 3 of the API will default to the legacy version.
		 * Everyone else will automatically load the latest version. 
		 */
		$keys = get_option( 'mhca_api_key');
		if ( '' != $keys && ( strpos( $keys['mediahub_api_url'],'v3' ) !== false || strpos( $keys['mediahub_api_url'],'v2' ) !== false ) ) {
			require( 'legacy.php' );
		} else {
			require( 'v2/mediahub.php' );
		}
	}

	/*
	 * Setup localization for translations
	 */
	public function localization() {

		load_plugin_textdomain(
			'mediahub', // Unique identifier
			false, // Deprecated abs path
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Languages folder
		);

	}

}
new MediaHub_Init;
