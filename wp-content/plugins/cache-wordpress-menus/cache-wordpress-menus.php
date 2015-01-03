<?php
/*
Plugin Name: Cache WordPress Menus
Plugin URI: http://geek.ryanhellyer.net/products/manifest-cache/
Description: Caches WordPress nav menus
Author: Ryan Hellyer
Version: 1.0
Author URI: https://geek.hellyer.kiwi/

Copyright (c) 2015 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


/**
 * Cache WordPress menus.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Cache_WordPress_Menus {

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Create unique transient key for this page
		$this->transient = 'nav-' . md5( $_SERVER['REQUEST_URI'] . var_export( $args, true ) );

		// Filter the menu output
		add_filter( 'wp_nav_menu',     array( $this, 'set_cached_menu' ), 10, 2 );
		add_filter( 'pre_wp_nav_menu', array( $this, 'get_cached_menu' ), 10, 2 );
	}

	/**
	 * Set the menu cache.
	 * 
	 * @param  string   $nav_menu   The nav menu content
	 * @param  array    $args       The menu arguments
	 * @return string   The cached menu
	 */
	public function set_cached_menu( $nav_menu, $args ) {

		set_transient( $this->transient, $nav_menu, 30 );

		return $nav_menu;
	}

	/**
	 * Get the cached menu
	 * 
	 * @param  bool    $dep   Deprecated variable
	 * @param  array   $args  The menu arguments
	 * @return string  The cached menu
	 */
	public function get_cached_menu( $dep = null, $args ) {

		// Return the cached menu if possible
		if ( false === ( $menu = get_transient( $this->transient ) ) ) {
			return null;
		} else {
			return $menu;
		}

	}

}
new Cache_WordPress_Menus();
