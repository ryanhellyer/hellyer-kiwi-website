<?php

/**
 * Discover new links.
 * When a user visits a page, we check if it's been found before and if not, we stash it.
 * 
 * @copyright Copyright (c) 2018, Strattic
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Strattic_Discover_Links {

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {

//		add_action( 'template_redirect', array( $this, 'process_url' ), 1 );

	}

	/**
	 * Process current URL.
	 */
	public function process_url() {

		// Get current URL
		$url = 'http';
		if ( is_ssl() ) {
			$url .= 's';
		}
		$url .= '://';
		$url .= $_SERVER[ 'HTTP_HOST' ];
		$url .= $_SERVER[ 'REQUEST_URI' ];

		// Get path (needs to strip home_url() so that it can handle sub-folder sites)
		$path = str_replace( home_url(), '', $url );

		// Strip query vars
		if ( strpos( $path, '?' ) !== false ) {
			$path = explode( '?', $path );
			$path = $path[ 0 ];
		}

		// Check if we should ignore this URL
		if ( true === $this->ignore( $path ) ) {
			return;
		}

		// Check if it's present in a list of paths already
		$options = array(
			'paths',
			'extra-links',
		);
		foreach ( $options as $option ) {

			$paths = get_option( 'strattic-' . $option );
			if ( is_array( $paths ) && in_array( $path, $paths ) ) {
				return; // bail out as path has been found already
			}

		}

		// Yay! We discovered a new path!
		if ( ! is_array( $paths ) ) {
			$paths = array();
		}

		array_push( $paths, $path );

		// Remove redundant discovered linksattic-discovered-links' );
		$api_paths = get_option( 'strattic-paths' );
		foreach ( $paths as $key => $path ) {

			if ( is_array( $api_paths ) && in_array( $path, $api_paths ) ) {
				unset( $paths[ $key ] );
			}

		}

		update_option( 'strattic-extra-links', $paths, 'no' );
	}

	/**
	 * @param  string  $path  The URL path
	 * @return bool    true if should be ignored
	 */
	private function ignore( $path ) {

		// Don't discovered wp-admin URLs
		if ( get_admin_url() === substr( home_url() . $path, 0, strlen( get_admin_url() ) ) ) {
			return true;
		}

		// Ignore 404's
		if ( is_404() ) {
			return true;
		}

		// Ignore Strattic internal pages
		if (
			'/strattic-api/' === $path
			||
			'/strattic-urls/' === $path
			||
			'/strattic-authentication-send/' === $path
		) {
			return true;
		}

		return false;
	}

}
