<?php
/*

Plugin Name: SEO Whacker
Plugin URI: https://geek.hellyer.kiwi/plugins/seo-whacker/
Description: Removes features from the WordPress SEO plugin which are often unrequired
Author: Ryan Hellyer
Version: 1.4
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


/* 
 * Disable plugin updates
 *
 * @param array  $r   Response header
 * @param string $url The update URL
 * @since 1.0.1
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
function seowhacker_hidden_plugin( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) )
		return $r; // Not a plugin update request. Bail immediately.
	$plugins = unserialize( $r['body']['plugins'] );
	unset( $plugins->plugins[ plugin_basename( __FILE__ ) ] );
	unset( $plugins->active[ array_search( plugin_basename( __FILE__ ), $plugins->active ) ] );
	$r['body']['plugins'] = serialize( $plugins );
	return $r;
}
add_filter( 'http_request_args', 'seowhacker_hidden_plugin', 5, 2 );

/*
 * Remove tooltips and tracking option
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
function seowhacker_tooltips() {
	return; // It stopped working

	if ( ! is_admin() )
		return;

	$options = get_option( 'wpseo' );
//print_r( $options );die;
	$new_options['yoast_tracking'] = 'off';
	$new_options['disableadvanced_meta'] = true;
	$new_options['tracking_popup_done'] = true;
	$new_options['ignore_tour'] = true;
	if ( $options != $new_options ) {
		update_option( 'wpseo', $new_options );
	}

}
seowhacker_tooltips();

/*
 * Remove admin menu options for non-super admins and multisite
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
function seowhacker_remove_menus() {
	if ( is_super_admin() || ! is_multisite() )
		return;

	remove_menu_page( 'wpseo_dashboard' );
}
add_action( 'admin_menu', 'seowhacker_remove_menus', 999 );

/*
 * Removing admin bar junk from view
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.1
 */
function seowhacker_admin_bar() {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wpseo-menu');
}
add_action( 'wp_before_admin_bar_render', 'seowhacker_admin_bar' );

/*
 * Removes unneeded sections in the post edit screen
 * Removes the advanced and page analysis sections
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.1
 */
function seowhacker_remove_blocks() {
	?>
	<style>
	#wpseo-debug-info,
	.wpseo-metabox-tabs-div .advanced,
	.wpseo_tablink,
	.wpseo-metabox-tabs-div li.general,
	#linkdex {
		display: none;
	}
	</style><?php
}
add_action( 'admin_head', 'seowhacker_remove_blocks' );



/*
 * Removes unneeded sections in the post edit screen
 * Removes the advanced and page analysis sections
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.1
 */
function seowhacker_remove_menuitems( $submenu_pages ) {
	foreach( $submenu_pages as $key => $value ) {
		if ( 'wpseo_bulk-title-editor' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_bulk-description-editor' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_import' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_files' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_internal-links' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_rss' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_licenses' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_permalinks' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( 'wpseo_social' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
		if ( '' == $value[4] ) {
			unset( $submenu_pages[$key] );
		}
	}
	return $submenu_pages;
}
add_filter( 'wpseo_submenu_pages', 'seowhacker_remove_menuitems' );

/**
 * Remove columns.
 * Code is derived from code on this page http://tidyrepo.com/wordpress-seo-by-yoast/
 *
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.4
 * @return  bool False
 */
function seowhacker_remove_columns() {
	return false;
}
add_filter( 'wpseo_use_page_analysis', 'seowhacker_remove_columns' );

