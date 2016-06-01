<?php

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param  string  $class  The class being instantiated
 */
function autoload_free_advice_berlin( $class ) {

	// Bail out if not loading a Media Manager class
	if ( 'Free_Advice_Berlin_' != substr( $class, 0, 19 ) ) {
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
spl_autoload_register( 'autoload_free_advice_berlin' );

new Free_Advice_Berlin_Admin;
new Free_Advice_Berlin_Setup;
new Free_Advice_Berlin_Ratings;
new Free_Advice_Berlin_Related_Group_Posts;
new Free_Advice_Berlin_Legal_Notice;
new Free_Advice_Berlin_Facebook;
new Free_Advice_Berlin_Show;

function wpdocs_filter_wp_title( $title, $sep ) {
	return 'abcdefghijklmnopqrstuvwxyz';
}
add_filter( 'wp_title', 'wpdocs_filter_wp_title', 10, 2 );
