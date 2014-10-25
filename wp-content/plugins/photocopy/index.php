<?php
/*

Plugin Name: Photocopy
Plugin URI: http://geek.ryanhellyer.net/products/photocopy/
Description: Backs up your media instantly to Amazon S3
Author: Ryan Hellyer
Version: 1.0.1
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2012 Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


/*
 * Bail out if not in admin
 * This will prevent the plugin from loading outside the admin panel
 * Most of the time this will be the desired functionality, but if you find that
 * this is problematic for your situation (ie: you require front-end uploads to work) then
 * let me know and I'll make it hookable so you can force it load everywhere.
 *
 * @author Ryan Hellyer <ryan@pixopoint.com>
 * @since 1.0
 */
if ( ! is_admin() )
	return;

/*
 * Load required files
 *
 * @author Ryan Hellyer <ryan@pixopoint.com>
 * @since 1.0
 */
if ( ! class_exists( 'S3' ) ) {
	require( 'inc/S3-class.php' );
}
require( 'inc/media-to-s3-class.php' );
new Media_To_S3();
