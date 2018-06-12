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

		add_action( 'template_redirect', array( $this, 'process_url' ), 1 );

	}


	/**
	 * Process current URL.
	 */
	public function process_url() {

		// Get current URL
		$url = 'http';
		if ( is_ssl() ) {
			$url . 's';
		}
		$url .= '://';
		$url .= $_SERVER[ 'HTTP_HOST' ];
		$url .= $_SERVER[ 'REQUEST_URI' ];

		// Get path (needs to strip home_url() so that it can handle sub-folder sites)
		$path = str_replace( home_url(), '', $url );
echo '<!-- ' . $url . ' ___' . $path . ' -->';

		// Check if it's present in a list of paths already
		$options = array(
			'paths',
			'manual-links',
			'discovered-links',
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

		update_option( 'strattic-discovered-links', $paths, 'no' );

	}

}
