<?php

/**
 * Strattic buffer
 * 
 * @copyright Copyright (c), Strattic
 * @since 1.1
 */
class Strattic_Buffer {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'template_redirect', array( $this, 'template_redirect' ) );

	}

	/*
	 * Starting page buffer.
	 */
	public function template_redirect() {

		if (
			! is_single()
			&&
			! is_post_type_archive()
			&&
			! is_page()
			&&
			! is_archive()
			&&
			! is_404()
			&&
			! is_attachment()
			&&
			! is_front_page()
			&&
			! is_search()
		) {
			return;
		}

		ob_start( array( $this, 'ob' ) );
	}

	/*
	 * Rewriting URLs once buffer ends.
	 *
	 * @param   string  The pages HTML
	 * @return  string  The filtered page output
	 */
	public function ob( $html ) {

		$html = $this->strattic_strip_double_slashes_from_dom( $html );

		return $html;
	}

	/**
	 * Strip double slashes from the DOM.
	 *
	 * Does not work within JS code. Could potentially modify all URLs between <script> tags as solution.
	 *
	 * @access private
	 * @param  string  $html  The HTML to be modified
	 * @return string  The modified HTML code
	 */
	private function strattic_strip_double_slashes_from_dom( $html ) {

		// Implement DOMDocument for parsing the URLs
		$doc = new \DOMDocument();
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
