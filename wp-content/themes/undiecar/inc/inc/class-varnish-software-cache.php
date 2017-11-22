<?php

/**
 * Handles cache invalidation within the theme.
 *
 * @copyright Copyright (c), Varnish Software
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Varnish Software
 * @since Varnish Software 1.0
 */
class Varnish_Software_Cache {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'widget_display_callback', array( $this,'flush_cache_via_callback' ), 10, 3 );
		add_action( 'admin_init', array( $this, 'flush_cache_via_init' ) );
	}

	/**
	 * Flushing the cache.
	 *
	 * @param  anything  $data  Callback input
	 * @param  anything  $data  Unmodified output
	 */
	public function flush_cache_via_callback( $data ) {

		delete_transient( 'varnish_software_footer_widgets' );

		return $data;
	}

	/**
	 * Flushing the cache.
	 */
	public function flush_cache_via_init() {

		if ( isset( $_POST['action'] ) && 'save-widget' == $_POST['action'] ) {
			delete_transient( 'varnish_software_footer_widgets' );
		}

	}


}
