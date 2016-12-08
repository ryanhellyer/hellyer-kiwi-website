<?php
/**
 * Plugin Name: Image Teleporter Modified by Ryan
 * Plugin URI: http://www.BlueMedicineLabs.com/
 * Description: This plugin waves a magic wand and turns images that are hosted elsewhere (like in your Flickr account or on another website) into images that are now in your Media Library. The code on your page is automatically updated so that your site now uses the version of the images that are in your Media Library instead.
 * Version: 1.0
 * Author: Blue Medicine Labs and Ryan Hellyer
 * Author URI: http://www.BlueMedicineLabs.com/
 * License: GPL2
 */

/*  Copyright 2013  Blue Medicine Labs  (email : us@bluemedicinelabs.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( is_admin() ) {
	require( 'init-plugin.php' );
}
