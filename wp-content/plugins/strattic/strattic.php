<?php

/*
Plugin Name: Strattic
Plugin URI:  https://www.strattic.com
Description:

Author: Strattic
Author URI: https://www.strattic.com/

Copyright 2018 Strattic

*/

/**
 * Load Composer files.
 */
$composer_path = __DIR__ . '/vendor/autoload.php';
if ( ! file_exists ( $composer_path ) ) {
	$composer_path = '/var/strattic/plugin/vendor/autoload.php';
}
require( $composer_path );

/**
 * Set environment.
 * This generates the $_ENV global variable.
 */
$env = null;
if ( defined( 'STRATTIC_ENV' ) ) {
	$env = '.env.' . STRATTIC_ENV;
}
//echo $env;die;
$dotenv = new Dotenv\Dotenv( __DIR__, $env );
$dotenv->load();

//print_r( $_ENV );die;

if ( isset( $_ENV[ 'STRATTIC_WORDPRESS_USER_AGENT' ] ) && ! defined( 'STRATTIC_WORDPRESS_USER_AGENT' ) ) {
	define( 'STRATTIC_WORDPRESS_USER_AGENT', $_ENV[ 'STRATTIC_WORDPRESS_USER_AGENT' ] );
}

/**
 * Set constants.
 */
if ( ! defined( 'STRATTIC_PLUGIN_URL' ) ) {
	define( 'STRATTIC_PLUGIN_URL', esc_url( $_ENV[ 'BASE_FRONT_URL' ] ) );
}
if ( ! defined( 'STRATTIC_ASSETS' ) ) {
	define( 'STRATTIC_ASSETS', STRATTIC_PLUGIN_URL . '/plugin/' );
}
define( 'STRATTIC_VERSION', '2.1.8' );
define( 'STRATTIC_API_URL', esc_url( $_ENV[ 'BASE_API_URL' ] . '/' ) );
if ( ! defined( 'STRATTIC_PLUGIN_API_URL' ) ) {
	define( 'STRATTIC_PLUGIN_API_URL', esc_url( $_ENV[ 'BASE_PLUGIN_API_URL' ] . '/' ) );
}
define( 'STRATTIC_FORM_SUBMISSION_ENDPOINT', STRATTIC_API_URL . 'forms/formprocessor' );


// If we're behind a proxy server and using HTTPS, we need to alert Wordpress of that fact
// see also http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}


/**
 * Set default file permissions.
 * This is required due to WordPress not setting permissions that match our platform by default.
 */
if ( ! defined( 'FS_CHMOD_DIR' ) ) {
	define( 'FS_CHMOD_DIR', ( 0775 & ~ umask() ) );
}
if ( ! defined( 'FS_CHMOD_FILE' ) ) {
	define( 'FS_CHMOD_FILE', ( 0664 & ~ umask() ) );
}

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param  string  $class  The class being instantiated
 */
function autoload_strattic( $class ) {

	// Bail out if not loading the correct class
	if ( 'Strattic' != substr( $class, 0, 8 ) ) {
		return;
	}

	// Convert from the class name, to the classes file name
	$file_data = strtolower( $class );
	$file_data = str_replace( '_', '-', $file_data );
	$file_name = 'class-' . $file_data . '.php';

	// Get the classes file path
	$dir = dirname( __FILE__ );
	$path = $dir . '/inc/' . $file_name;

	// Include the class (spl_autoload_register will automatically instantiate it for us)
	if ( file_exists( $path ) ) {
		require( $path );
	}
}
spl_autoload_register( 'autoload_strattic' );


new Strattic_Buffer;
new Strattic_URLs;
new Strattic_Stats;
//new Strattic_Anti_Spam;
//new Strattic_Strip_Double_Slashes;
new Strattic_Fix_Hard_Coded_URLs;
new Strattic_Discover_Links;
new Strattic_String_Replace;
new Strattic_404;
new Strattic_Form_Processing;
new Strattic_Languages;
new Strattic_Disable_Features;
new Strattic_Minification;
new Strattic_Logout;
new Strattic_Posts;
new Strattic_Beaver_Builder_CSS;
new Strattic_Admin;

if ( is_admin() ) {
	new Strattic_Extra_Links;
	new Strattic_AJAX;
	new Strattic_Warn_About_Caching_Plugin;
	new Strattic_Remove_Leftover_Users;
}

/**
 * Load external plugins.
 * These are either third party plugins, or plugins we intend to release for use outside of the Strattic environment.
 */
if (
	(
		'on' === get_option( 'strattic-minify-css' )
		||
		'on' === get_option( 'strattic-minify-js' )
	)
	&&
	$GLOBALS['pagenow'] !== 'wp-login.php'
) {
	require( 'plugins/minit/minit.php' );
	require( 'plugins/minit-pro/minit-pro.php' );
//	require( 'plugins/lazy-load/lazy-load.php' );
}
require( 'plugins/strattic-search/strattic-search.php' );
require( 'plugins/strattic-search-pro/strattic-search-pro.php' );
require( 'plugins/strattic-helper/strattic-helper.php' );
