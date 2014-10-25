<?php
/*

	Plugin Name: PixoPoint Google Analytics
	Plugin URI: http://pixopoint.com/products/google-analytics/
	Description: A WordPress plugin which allows you to add Google analytics to your site
	Author: PixoPoint Web Development / Ryan Hellyer
	Version: 0.3
	Author URI: http://pixopoint.com/

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/


/**
 * Define some constants
 * @since 0.1
 */
define( 'PIXOPOINT_GOOGLE_ANALYTICS_DIR', dirname( __FILE__ ) . '/' );
define( 'PIXOPOINT_GOOGLE_ANALYTICS_URL', WP_PLUGIN_URL . '/' . basename( PIXOPOINT_GOOGLE_ANALYTICS_DIR )  . '/' );
define( 'PIXOPOINT_GOOGLE_ANALYTICS_IMAGES_URL', PIXOPOINT_GOOGLE_ANALYTICS_URL . 'images/' );
define( 'PIXOPOINT_GOOGLE_ANALYTICS_AD', "\n<!-- PixoPoint Google Analytics v0.2 - http://pixopoint.com/products/google-analytics/ -->" );

/**
 * Grabs data from database
 * Choose from "network" or "site" values
 * @since 0.1
 */
function pixopoint_google_analytics_get_option( $option ) {

	// Santize and serve "site" code
	if ( 'site' == $option )
		$code = pixopoint_google_analytics_trackingcode_sanitizer( get_option( 'pixopoint_google_analytics' ) );
	// Santize and serve "network" code
	elseif ( 'network' == $option )
		$code = pixopoint_google_analytics_trackingcode_sanitizer( get_site_option( 'pixopoint_google_analytics_network' ) );
	elseif ( 'domain' == $option )
		$code = pixopoint_google_analytics_getdomain( get_site_option( 'pixopoint_google_analytics_domain' ) );
		//str_replace( 'http://', '', esc_url( get_site_option( 'domain' ) ) );
	else
		$code = $option;


	return $code;
}

/**
 * Get Domain from URL
 * @since 0.1
 */
function pixopoint_google_analytics_getdomain( $url ) {

	$url = esc_url( $url ); // Santise URL

	$nowww = ereg_replace('www\.','',$url);
	$domain = parse_url($nowww);
	if( !empty( $domain["host"] ) )
		return $domain["host"];
	else
		return $domain["path"];

}

/**
 * Sanitize code
 * I don't know of any standard way to sanitize analytics tracking codes, so used wp_kses() to strip out nasties, santize_url() to strip out random junk, then used strtoupper() to fix lower case caused by sanitize_url(). If you can come up with a more suitable way to do this, please lemme know :) In the mean time, this should hopefully do the trick okay.
 * @since 0.1
 */
function pixopoint_google_analytics_trackingcode_sanitizer( $code ) {
	$code = strtoupper(
		sanitize_title(
			wp_kses( $code, '' )
		)
	);
	return $code;
}

/**
 * Loading the plugin admin page or core
 * @since 0.1
 */
if ( is_admin() )
	require( 'admin_page.php' );
else
	require( 'core.php' );
