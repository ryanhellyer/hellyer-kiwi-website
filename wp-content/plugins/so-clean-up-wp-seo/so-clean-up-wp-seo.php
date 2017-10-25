<?php
/**
 * Plugin Name: 		Hide SEO Bloat
 * Plugin URI:  		https://so-wp.com/plugin/hide-seo-bloat
 * Description:			Hide most of the bloat that the Yoast SEO plugin adds to your WordPress Dashboard
 * Version:     		3.4.1
 * Author:				SO WP
 * Author URI:  		https://so-wp.com
 * License:    			GPL-3.0+
 * License URI:			http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: 		/languages
 * Text Domain: 		so-clean-up-wp-seo
 * Network:     		true
 * GitHub Plugin URI:	https://github.com/senlin/so-clean-up-wp-seo
 * GitHub Branch:		develop
 */

// don't load the plugin file directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-so-clean-up-wp-seo.php' );
require_once( 'includes/class-so-clean-up-wp-seo-settings.php' );

// Load plugin libraries
require_once( 'admin/class-so-clean-up-wp-seo-admin-api.php' );

/**
 * Returns the main instance of CUWS to prevent the need to use globals.
 *
 * @since  v2.0.0
 * @return object CUWS
 */
function CUWS () {
	$instance = CUWS::instance( __FILE__, '3.4.1' );

	if ( null === $instance->settings ) {
		$instance->settings = CUWS_Settings::instance( $instance );
	}

	return $instance;
}

CUWS();
