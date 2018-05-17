<?php
/**
 * Initilisation of the theme.
 *
 * @package Spam Annhilator theme
 * @since Spam Annhilator theme 1.0
 */

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param  string  $class  The class being instantiated
 */
function autoload_spamannhilator( $class ) {

	// Bail out if not loading the correct class
	if ( 'Spam_Annhilator' != substr( $class, 0, 15 ) ) {
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
spl_autoload_register( 'autoload_spamannhilator' );

new Spam_Annhilator_Checker;
new Spam_Annhilator_Setup;
new Spam_Annhilator_Members;
new Spam_Annhilator_Destroyer;
