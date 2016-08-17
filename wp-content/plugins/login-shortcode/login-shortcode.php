<?php
/*
Plugin Name: Login Form
Plugin URI: https://geek.hellyer.kiwi/plugins/
Description: Login Form. Code ripped almost verbatim from Justin Tadlock http://justintadlock.com/archives/2011/08/30/adding-a-login-form-to-a-page
Version: 1.0
Author: Ryan Hellyer
Author URI: https://geek.hellyer.kiwi/
License: GPL2

------------------------------------------------------------------------
Copyright Ryan Hellyer / Justin Tadlock

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



// Use it like this ... [login-form label_username="Enter Username" label_password="Enter Password"]


add_action( 'init', 'login_form_add_shortcodes' );

function login_form_add_shortcodes() {
	add_shortcode( 'login-form', 'login_form_shortcode' );
}

function login_form_shortcode( $attr ) {

	if ( is_user_logged_in() ) {
		echo '<meta http-equiv="refresh" content="0; url=' . esc_url( home_url() ) . '">';
	}

	/* Set up some defaults. */
	$defaults = array(
		'label_username' => 'Username',
		'label_password' => 'Password'
	);

	/* Merge the user input arguments with the defaults. */
	$attr = shortcode_atts( $defaults, $attr );

	/* Set 'echo' to 'false' because we want it to always return instead of print for shortcodes. */
	$attr['echo'] = false;

	return wp_login_form( $attr );
}
