<?php

/**
 * Fixes hard coded URLs which have been added to content, plugin or themes.
 * 
 * @copyright Copyright (c) 2018, Strattic
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Strattic_Fix_Hard_Coded_URLs extends Strattic_Core {

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {

		// Add filter
		add_filter( 'strattic_buffer', array( $this, 'string_replace' ) );

	}

	/**
	 * Replace the URLs with the corrected ones.
	 */
	public function string_replace( $html ) {

// Bypassing due to problems experienced in switching to "serverless" system
return $html;
		$cloudfront_domain = $this->get_domain( STRATTIC_CLOUDFRONT_URL );

		$replacements = array(
			0 => array(
				'search' => 'http://' . $cloudfront_domain,
				'replace' => home_url(),
			),
			1 => array(
				'search' => 'http://s' . $cloudfront_domain,
				'replace' => home_url(),
			),
		);

		foreach ( $replacements as $key => $replacement ) {
			$search = $replacement[ 'search' ];
			$replace = $replacement[ 'replace' ];

			$html = str_replace( $search, $replace, $html );
		}

		return $html;
	}

	/**
	 * Get the domain of a URL.
	 */
	private function get_domain( $url ) {
		$parse = parse_url( $url );
		return $parse[ 'host' ];
	}

}
