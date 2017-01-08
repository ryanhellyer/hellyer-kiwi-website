<?php

/**
 * Load useful functions.
 */
require( 'inc/functions.php' );

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param  string  $class  The class being instantiated
 */
function autoload_arousingaudio( $class ) {

	// Bail out if not loading a Media Manager class
	if ( 'ArousingAudio_' != substr( $class, 0, 14 ) ) {
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
spl_autoload_register( 'autoload_arousingaudio' );

new ArousingAudio_Setup;
new ArousingAudio_Audio;
new ArousingAudio_Ratings;
