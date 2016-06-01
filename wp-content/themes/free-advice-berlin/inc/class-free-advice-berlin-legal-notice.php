<?php

/**
 * Legal Notice page.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
class Free_Advice_Berlin_Legal_Notice {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$site_details = get_blog_details();
		add_filter( 'wp_title', array( $this, 'title' ), 10, 2 );

		if (
			'/legal-notice/' == str_replace( $site_details->path, '', $_SERVER['REQUEST_URI'] )
			||
			'legal-notice/' == str_replace( $site_details->path, '', $_SERVER['REQUEST_URI'] )
		) {
			add_action( 'wp', array( $this, 'load_legal_notice_page' ) );
		} elseif (
			'/legal-notice' == str_replace( $site_details->path, '', $_SERVER['REQUEST_URI'] )
			||
			'legal-notice' == str_replace( $site_details->path, '', $_SERVER['REQUEST_URI'] )
		) {
			add_action( 'wp', array( $this, 'redirect_to_slashed' ) );
		}

	}

	/**
	* Filters the page title.
	*
	* @param       string    $title    Default title text for current view.
	* @param       string    $sep      Optional separator.
	* @return      string              The filtered title.
	*/
	public function title( $title, $sep ) {
		return 'Legal notice';
	}

	/**
	 * Redirecting to trailing slashed page.
	 */
	public function redirect_to_slashed() {
		wp_redirect( home_url( 'legal-notice/' ), 302 );
		exit;
	}

	/**
	 * Loads the Legal Notice template.
	 */
	public function load_legal_notice_page() {
		status_header( 200 );
		add_filter( 'body_class', array( $this, 'body_classes' ) );
		include( get_query_template( 'legal-notice' ) );
		die();
	}

	/**
	 * Modifies the body class.
	 * Removes irrelevant 404 class.
	 * Adds .page class since this is a page.
	 *
	 * @param  array  $classes  The body classes
	 * @return array  The modified body classes
	 */
	public function body_classes( $classes ) {

		if ( ( $key = array_search( 'error404', $classes ) ) !== false ) {
			unset( $classes[$key] );
		}

		$classes[] = 'page';

		return $classes;
	}

}
