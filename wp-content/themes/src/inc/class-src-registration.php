<?php

/**
 * Modifies how the Simple Registration form plugin works.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC
 * @since SRC 1.0
 */
class SRC_Registration extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add filters
		add_filter( 'simple-registration-redirect', array( $this, 'filter_registration_redirect' ) );
		add_filter( 'simple-login-redirect', array( $this, 'filter_login_redirect' ) );

	}

	/**
	 * Modify the registration redirect URL.
	 *
	 * @param  string  $URL  The current URL to redirect to
	 * @return string  The modified redirect URL
	 */
	public function filter_registration_redirect( $url ) {

		$post_id = get_option( 'src_registration_thanks_page' );
		$url = get_permalink( $post_id );

		return $url;
	}

	/**
	 * Modify the login redirect URL.
	 *
	 * @param  string  $URL  The current URL to redirect to
	 * @return string  The modified redirect URL
	 */
	public function filter_login_redirect( $url ) {

		$url = esc_url( home_url( '/' . get_option( '_bbp_root_slug' ) . '/' ) );

		return $url;
	}

}
new SRC_Registration;
