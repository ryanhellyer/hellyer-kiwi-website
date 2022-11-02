<?php
/*
Plugin Name: Strattic Search
Plugin URI: https://www.strattic.com/
Description: Strattic Search
Version: 2.3.41
Author: Strattic / Ryan Hellyer
Author URI: https://www.strattic.com/
Text Domain: strattic-search
License: GPL2

------------------------------------------------------------------------
Copyright Strattic / Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/
//return;

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param string $class The class being instantiated.
 */
function autoload_strattic_search( $class ) {

	// Bail out if not loading a Media Manager class
	$prefix = 'Strattic_Search_';
	if ( $prefix != substr( $class, 0, strlen( $prefix ) ) ) {
		return;
	}

	// Handle engine class separately.
	if ( 'Strattic_Search_Engine' === $class ) {
		return;
	}

	// Convert from the class name, to the classes file name
	$file_data = strtolower( $class );
	$file_data = str_replace( '_', '-', $file_data );
	$file_name = 'class-' . $file_data . '.php';

	// Get the classes file path.
	$dir = dirname( __FILE__ );
	$path = $dir . '/inc/' . $file_name;

	// Include the class (spl_autoload_register will automatically instantiate it for us).
	require( $path );
}
spl_autoload_register( 'autoload_strattic_search' );

new Strattic_Search_Core();
new Strattic_Search_Themes();
