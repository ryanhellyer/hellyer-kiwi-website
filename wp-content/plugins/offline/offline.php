<?php
/*
Plugin Name: Offline
Plugin URI: https://geek.hellyer.kiwi/products/offline/
Description: Offline
Author: Ryan Hellyer
Version: 2.0
Author URI: https://geek.hellyer.kiwi/

Copyright (c) 2017 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


/**
 * Offline Cache class.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Offline Cache
 * @since Offline Cache 1.0
 */
class Offline_Cache {

	const CRON_TIME = 60;

	/**
	 * Class constructor.
	 */
	public function __construct() {

// For testing manifest generator
if( isset( $_GET['generate_manifest'] ) ) {
	add_action( 'init', array( $this, 'generate_manifest' ) );
}

		// Register (de)activation hooks
		$plugin_path = dirname( __FILE__ ) . '/offline.php';
		register_activation_hook(   $plugin_path, array( $this, 'activation' ) );
		register_deactivation_hook( $plugin_path, array( $this, 'deactivation' ) );

		// Add custom cron schedule
		add_filter( 'cron_schedules',    array( $this, 'cron_schedules' ) );
		add_action( 'offline_get_urls',  array( $this, 'generate_manifest' ) );

		// Setup manifest functionality
		add_action( 'template_redirect', array( $this, 'add_manifest_attribute' ) );			
		add_action( 'init',              array( $this, 'display_manifest' ) );

	}

	/**
	 * On activation, set a time, frequency and name of an action hook to be scheduled.
	 */
	public function activation() {
		$first_run_time = current_time ( 'timestamp' ) + self::CRON_TIME;
		wp_schedule_event( $first_run_time, 'offline', 'offline_get_urls' );
	}

	/**
	 * On deactivation, remove all functions from the scheduled action hook.
	 */
	public function deactivation() {
		wp_clear_scheduled_hook( 'offline_get_urls' );
	}

	/**
	 * Adds custom cron schedule.
	 *
	 * @param array   $schedules Cron schedule array
	 * @return array $schedules Amended cron schedule array
	 */
	public function cron_schedules( $schedules ) {

		$schedules['offline'] = array(
			'interval' => self::CRON_TIME,
			'display'  => 'Custom WP Cron time for Offline Cache plugin'
		);

		return $schedules;
	}

	/**
	 * Generate a new manifest.
	 */
	public function generate_manifest() {

		// Grab every single public post or page URL, plus the home page.
		$post_types = get_post_types( array( 'public' => true ) );
		$query = new WP_Query(
			array(
				'post_type'              => $post_types,
				'no_found_rows'          => true,  // Improves query performance
				'update_post_meta_cache' => false, // Improves query performance
				'update_post_term_cache' => false, // Improves query performance
				'posts_per_page'         => 100,   // Improves query performance
				'fields'                 => 'ids', // Improves query performance
			)
		);
		$found_pages = $processed_pages = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$pages_to_process[] = get_the_permalink();

			}
		}
		$pages_to_process[] = home_url( '/' );

		$count = 0;
		$found_urls = array();
		while ( 6 > $count ) {
			foreach ( $pages_to_process as $key => $page_url ) {

				$path = str_replace( home_url(), '', $page_url ); // remove home_url() from it, then check for dots, to confirm presence of files

				if (
					strpos( $page_url, '?' ) !== false
					||
					strpos( $path, '.' ) !== false
					||
					strpos( $page_url, '#' ) !== false
				) {
				} else {
					$found_urls = $this->get_urls_from_page( $page_url );
//echo 'Page URL: ' . $page_url . "\n";print_r( $found_urls );echo "\n";
				}

			}

			$pages_to_process = array_merge( $found_urls, $pages_to_process );
			$count++;
		}
		$pages_to_process = array_unique( $pages_to_process );

		// Purge pages (usually due to not returning 200 status)
		$pages_to_purge = wp_cache_get( 'pages_to_purge' );
		foreach ( $pages_to_process as $key => $page_url ) {
			foreach ( $pages_to_purge as $purge_key => $purge_page_url ) {
				if ( $purge_page_url == $page_url ) {
					unset( $pages_to_process[ $key ] );
				}
			}

		}
//print_r( $pages_to_process );echo "\n\n";

		// Declare this is a cache manifest
		$manifest_code = "CACHE MANIFEST\n";

		// Output each URL
		foreach( $pages_to_process as $url ) {
			$manifest_code .= esc_url( $url ) . "\n";
		}
		$manifest_code .= "\n";

		// Add time stamp
		$manifest_code .= '# Time stamp: ' . date( 'l jS \of F Y h:i:s A' );
//echo "\n\nManifest code:\n".$manifest_code;die;
		// Store the URLs for use in the manifest cache (stored for whole year, but gets wiped every time manifest is regenerated)
/*
$manifest_code = 'CACHE MANIFEST
http://ual1.360create.graphics/sample-page-2/
http://ual1.360create.graphics/wp-content/uploads/2016/04/ual-menu-logo-4.png
http://ual1.360create.graphics/
http://ual1.360create.graphics/contacts/
http://ual1.360create.graphics/wp-content/themes/ualmobile/style.css?ver=1.0
http://ual1.360create.graphics

# Time stamp: DYNAMIC';
*/

		set_transient( 'offline_cache', $manifest_code, YEAR_IN_SECONDS );

echo "\n\n..........\n".$manifest_code;

		exit;
	}

	/**
	 * Get URLs from a page.
	 *
	 * @param  string  $page_url  The page URL to get URLs from
	 * @return array   The URLs found in the page.
	 */
	public function get_urls_from_page( $page_url ) {
/*
$url = 'http://ual1.360create.graphics/facilities-coordinators/';
$response = wp_remote_get( $url );
print_r( $response );
echo $response[ 'body' ];
die;
*/
		$key = md5( $page_url );
		$found_pages = wp_cache_get( $key );
		if ( false === $found_pages ) {

			$response = wp_remote_get( $page_url );
			$found_pages = $pages_to_purge = array();
			if (
				isset( $response[ 'body' ] )
				&&
				isset( $response['response']['code'] )
				&&
				'200' == $response['response']['code']
			) {
//echo '<textarea>';print_r( $response[ 'body' ] );echo '</textarea>';
				$urls_to_cache[] = get_the_permalink();
				$found_urls = $this->get_urls_to_cache( $response[ 'body' ] );
				$found_pages = array_merge( $urls_to_cache, $found_urls );

			} else {

				// Page did not return 200 response, therefore need to purge it
				$pages_to_purge[] = $page_url;

			}

			$data = wp_cache_get( 'pages_to_purge' );
			if ( is_array( $data ) ) {
				$pages_to_purge = array_merge( $pages_to_purge, $data );
			}
			wp_cache_set( 'pages_to_purge', $pages_to_purge, null, MINUTE_IN_SECONDS );
			wp_cache_set( $key, $found_pages, null, MINUTE_IN_SECONDS );
		}

		return $found_pages;
	}

	/**
	 * Display the cache manifest file.
	 * 
	 * @param string $content
	 * @return string
	 */
	public function display_manifest() {

		// If not meant to load manifest, then bail out now
		if ( ! isset( $_GET['manifest'] ) ) {
			return;
		}

		// If cache is empty, then bail out now
		if ( '' == get_transient( 'offline_cache' ) ) {
			return 'Error: No manifest found.';
		}

		// Output the page
		header( 'Content-Type: text/cache-manifest' );
		echo get_transient( 'offline_cache' );

		exit;

	}

	/**
	 * Buffer page to add manifest attribute to HTML tag.
	 */
	public function add_manifest_attribute() {
		ob_start( array( $this, 'manifest_attribute_callback' ) );
	}

	/**
	 * Callback for output buffer.
	 * Adds manifest attribute to HTML tag.
	 * 
	 * @param   string   $content
	 * @return  string
	 */
	public function manifest_attribute_callback( $content ) {

		// Add Manifest attribute
		$url = home_url() . '?manifest';
		$content = str_replace( '<html ', '<html manifest="' . esc_url( $url ) . '" ', $content );

		return $content;
	}

	/**
	 * Get URLs to cache.
	 *
	 * @uses DOMDocument;
	 * @param  string   $content     The content
	 * @access private
	 * @return array    $urls_to_cache
	 */
	private function get_urls_to_cache( $content ) {

		// Obtain all URLs which need cached
		$doc = new DOMDocument();
		@$doc->loadHTML( $content ); // This throws non-stop errors if you give it bad HTML. Due to this problem, I decided to suppress all of these errors. If you can guarantee that only good HTML is sent to the parser, then you could remove the "@" symbol, but most sites do have a sloppy HTML, so this is kinda necessary unfortunately.
		$xpath = new DOMXpath( $doc );

		$tags = array(
			'link'   => 'href',
			'img'    => 'src',
			'script' => 'src',
			'img'    => 'src',
			'a'      => 'href',
		);

		// Loop through each type of HTML tag
		$urls_to_cache = array();
		foreach( $tags as $tag => $attribute ) {

			$nodes = $xpath->query( '//' . $tag );

			foreach( $nodes as $node ) {

				// Some irrelevant things may as well be ignored
				if (
					'https://api.w.org/' != $node->getAttribute( 'rel' )
					&&
					'alternate' != $node->getAttribute( 'rel' )
					&&
					'pingback' != $node->getAttribute( 'rel' )
					&&
					'profile' != $node->getAttribute( 'rel' )
					&&
					'application/rsd+xml' != $node->getAttribute( 'type' )
					&&
					'application/rss+xml' != $node->getAttribute( 'type' )
					&&
					'application/wlwmanifest+xml' != $node->getAttribute( 'type' )
					&&
					'wlwmanifest' != $node->getAttribute( 'rel' )
					&&
					'shortlink' != $node->getAttribute( 'rel' )
					&&
					'canonical' != $node->getAttribute( 'rel' )
					&&
					'.php' != substr( $node->getAttribute( 'href' ), -4 ) // Ignore .php files
					&&
					'/feed/' != substr( $node->getAttribute( 'href' ), -6 ) // Ignore feed links
				) {

					$url = $node->getAttribute( $attribute );
					$pos = strpos( $url, home_url() );
					if ( $pos === false ) {
						// string needle NOT found in haystack
					} else {
						// string needle found in haystack
						$urls_to_cache[] = $url;
					}
				}
			}
		}

		// Apply filter - can be used by plugins for adding extra features to filter
		$urls_to_cache = apply_filters( 'offline_cache', $urls_to_cache );

		return $urls_to_cache;
	}

}
new Offline_Cache;
