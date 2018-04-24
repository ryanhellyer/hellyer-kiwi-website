<?php
/*
Plugin Name: Simple Facebook Login
Plugin URI: https://geek.hellyer.kiwi/products/simple-facebook-login/
Description: A simple implementation of a Facebook login system without any extra junk
Author: Ryan Hellyer
Version: 1.0
Author URI: https://geek.hellyer.kiwi/

Copyright (c) 2018 Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/

/**
 * Autoload the classes.
 * Includes the classes, and automatically instantiates them via spl_autoload_register().
 *
 * @param  string  $class  The class being instantiated
 */
function autoload_simple_facebook_login( $class ) {

	// Bail out if not loading a relevant class
	if ( 'Simple_Facebook_Login_' != substr( $class, 0, 22 ) ) {
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
spl_autoload_register( 'autoload_simple_facebook_login' );


new Simple_Facebook_Login_Admin_Page;

$simple_facebook_login = new Simple_Facebook_Login_Init;

add_action( 'template_redirect', 'simple_facebook_login', 5 );
/**
 * Process login.
 * Needs to be fired earlier than shortcode to ensure member is logged in before page beings loading.
 * Variables need to be passed from processing to the HTML output.
 *
 * @global object  $simple_facebook_login  Used to ensure that login processing and shortcode are handled separately
 */
function simple_facebook_login() {
	global $simple_facebook_login;

	return $simple_facebook_login->init();
}

add_shortcode( 'simple_facebook_login', 'simple_facebook_login_html');
/**
 * Outputs HTML to the shortcode.
 *
 * @global object  $simple_facebook_login  Used to ensure that login processing and shortcode are handled separately
 */
function simple_facebook_login_html() {
	global $simple_facebook_login;

	return $simple_facebook_login->get_html();

}
