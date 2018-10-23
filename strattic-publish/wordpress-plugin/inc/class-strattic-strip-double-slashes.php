<?php

/**
 * Strip Double Slashes.
 * URLs with double slashes work fine with Apache or Nginx, but totally fail in S3. This plugin fixes any URL declarations with this format to ensure that they continue working even when converted to static files on S3.
 * 
 * @copyright Copyright (c), Strattic
 */
class Strattic_Strip_Double_Slashes {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_filter( 'strattic_buffer', array( $this, 'strip_double_slashes_from_dom' ) );

	}

	/**
	 * Strip double slashes from the DOM.
	 *
	 * Does not work within JS code. Could potentially modify all URLs between <script> tags as solution.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @access private
	 * @param  string  $html  The HTML to be modified
	 * @return string  The modified HTML code
	 */
	public function strip_double_slashes_from_dom( $html ) {

		// Implement DOMDocument for parsing the URLs
		$doc = new \DOMDocument(); // if this triggers a fatal error of "Uncaught Error: Class 'DOMDocument'", then install php-xml (apt-get install php-xml)
		libxml_use_internal_errors( true ); // Disable error display since most sites have HTML errors
		$doc->loadHTML( $html );
		libxml_use_internal_errors( false );


		// Iterate through each tag
		foreach ( array( 'script', 'link', 'img' ) as $tag ) {

			// Iterate through each node list of this tag
			$nodes = $doc->getElementsByTagName( $tag );
			foreach( $nodes as $node ) {

				// Loop through each attribute
				foreach ( array( 'href', 'src' ) as $attribute ) {

					$attr =  $node->getAttribute( $attribute );
					$start_of_attr = mb_substr( $attr, 0, strlen( home_url() ) ); // removing root URL
					if ( $start_of_attr === home_url() ) {

						$attr_chunk = str_replace( home_url(), '', $attr ); // Strip root URL from attribute value
						$new_attr = home_url() . str_replace( '//', '/', $attr_chunk ); // Reform URL without double slashes

						// Rewrite the URL in the attribute
						$node->setAttribute( $attribute, $new_attr );

					}

				}

			}

		}

		return $doc->saveHTML();
	}

}
