<?php

/*
 * Modified WordPress API Shortcode - modified for use on geek.hellyer.kiwi
 *
 * Add [wpapi] shortcode to call WordPress.org API for plugins and themes.
 *
 * @package    	WPAS
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com> and Ryan Hellyer <ryanhellyer@gmail.com>
 * @copyright   Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/wp-api-shortcode
 * @license   	http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2007 - 2013  Pereira Pulido Nuno Ricardo  (email : pereira@nunoapps.com)
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not,
 * write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @wordpress-plugin
 * Plugin Name:       	WordPress API Shortcode
 * Plugin URI:        	http://nunoapps.com/plugins/wp-api-shortcode
 * Description:       	Add <code>[wpapi]</code> shortcode to call WordPress.org API for plugins and themes.
 * Version:           	1.0.0
 * Author:            	Pereira Pulido Nuno Ricardo
 * Author URI:        	http://namaless.com
 * Text Domain:       	freelancer
 * License:      		GPL-2.0+
 * License URI:       	http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       	/languages
 * GitHub Plugin URI: 	https://github.com/nunoapps/wp-api-shortcode
 */

class WP_API_Shortcode {

	/**
	 * Construct
	 *
	 * @access 	public
	 * @since 	0.0.1
	 * @return 	void
	 */
	public function __construct() {

		// Create empty global object.
		global $wpas;
		$wpas = new stdClass;

		// Assign plugin uri, path and basename to global object.
		$wpas->plugin_uri 	= trailingslashit( plugin_dir_url( __FILE__ ) );
		$wpas->plugin_dir 	= trailingslashit( plugin_dir_path( __FILE__ ) );
		$wpas->plugin_base 	= basename( plugin_dir_path( __FILE__ ) );

		// Hook the translations.
		add_action( 'plugins_loaded', array( $this, 'i18n' ) );

		// Hook include files.
		add_action( 'plugins_loaded', array( $this, 'includes' ) );

	}

	/**
	 * Load translations.
	 *
	 * @access 	public
	 * @since 	0.0.1
	 * @return 	void
	 */
	public function i18n() {
		load_plugin_textdomain( 'wpas', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Include files.
	 *
	 * @access 	public
	 * @since 	0.0.1
	 * @return 	void
	 */
	public function includes() {

		// Load frontend and backend files.
		require( 'classes/class-wordpress-api.php' );
		require( 'includes/shortcodes.php' );
		require( 'includes/functions.php' );

	}

}

// Init the plugin.
new WP_API_Shortcode();
