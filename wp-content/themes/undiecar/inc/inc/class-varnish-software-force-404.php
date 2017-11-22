<?php

/**
 * Forces 404 page to load when no page content present.
 *
 * @copyright Copyright (c), Varnish Software
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Varnish Software
 * @since Varnish Software 1.0
 */
class Varnish_Software_Force_404 {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'template_redirect' ), 9 );
	}

	/**
	 * Load the English language version of the page if appropriate.
	 *
	 * @global  object  $wp_query  The main WordPress query object
	 */
	public function template_redirect() {
		global $wp_query;

		if ( defined( 'VARNISH_ENGLISH_DEFAULT' ) || isset( $_GET['fl_builder'] ) ) {
			return;
		}

		// Load the English language version if no content present and English language page found
		if ( isset( $wp_query->post->post_content ) && '' == $wp_query->post->post_content ) {
			$wp_query->set_404();
			$wp_query->max_num_pages = 0;
		}

	}
}
