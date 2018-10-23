<?php
/*
Plugin Name: Strattic
Plugin URI:  http://strattic.com
Description: 

Author: Strattic
Author URI: https://strattic.com/

Copyright 2018 Strattic

*/


require( 'strattic-config.php' );


if ( ! defined( 'STRATTIC_ASSETS' ) ) {
	define( 'STRATTIC_ASSETS', content_url( 'mu-plugins/strattic/' ) );
}

define( 'STRATTIC_VERSION', '2.0' );
define( 'STRATTIC_ALERT_EMAIL', 'ryan@strattic.com' );
define( 'STRATTIC_FORM_SUBMISSION_ENDPOINT', 'https://api.strattic.com/' );


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
	require( $path );
}
spl_autoload_register( 'autoload_strattic' );

new Strattic_Buffer;
new Strattic_API;
new Strattic_Discover_Links;
//new Strattic_Anti_Spam;
//new Strattic_Strip_Double_Slashes;
new Strattic_Fix_Hard_Coded_URLs;
new Strattic_String_Replace;
//new Strattic_Form_Processing;
new Strattic_404;
new Strattic_Keep_Alive;

if ( is_admin() ) {
	new Strattic_Admin;
	new Strattic_Admin_Links;
	new Strattic_Warn_About_Caching_Plugin;
	new Strattic_Remove_Leftover_Users;
	new Strattic_Settings;
}
