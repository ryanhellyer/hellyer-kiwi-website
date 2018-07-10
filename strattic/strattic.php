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

define( 'STRATTIC_VERSION', '1.2' );

/**
 * CHANGES:
 * 		relabelled copy/paste code from example plugin
 *		moved menu item to top - this mirrors how WP Engine and others work
 *		made translatable
 *		used WordPress default button CSS class
 *		added Strattic icon to menu
 *		made strings in JS translatable
 *		changed request to stage1.php file so that it will work if the wp-content folder is moved
 *		converted JS function names to ones less likely to clash with other plugin/theme code
 *		removed reference to non-existent #myDiv element in JS
 *		
 * FEATURE ADDITIONS:
 *		now handles double slashes in URLs (does not handle them when present within script tags)
 *
 * TODO:
 *		Add SMTP support https://wordpress.org/plugins/wp-ses/
 *		Where possible, move files to /usr/local/bin
 *		Perhaps provide help link
 *		Create search index
 * 
 * wp maintenance mode ... 
 * I'll add a note to myself to disable updates until after the publish process has completed, and to block publishing if updates are currently underway.
 * We should also implement a real Cron task (assuming it isn't setup up like this already) to trigger WP Cron, rather than relying on page loads, as they're not going to behave as intended when there is so little traffic on the staging site.
 */

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

new Strattic_Admin;
new Strattic_Buffer;
new Strattic_API;
new Strattic_Admin_Links;
new Strattic_Discover_Links;
new Strattic_Search;
new Strattic_Anti_Spam;
new Strattic_Strip_Double_Slashes;
new Strattic_String_Replace;