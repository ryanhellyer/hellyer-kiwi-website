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

		add_action( 'after_switch_theme', 'flush_rewrite_rules' );
		add_action( 'init',                array( $this, 'rewrites' ) );
		add_action( 'template_include',    array( $this, 'load_legal_notice_page' ) );

	}

	/**
	 * Adding new rewrite rule.
	 *
	 * @global $wp
	 * @global object $wp_rewrite WordPress rewrite object
	 */
	public function rewrites() {
		global $wp, $wp_rewrite;
		$wp->add_query_var( 'template' );
		add_rewrite_endpoint( 'legal-notice', EP_ROOT );
		$wp_rewrite->add_rule(
			'^/legal-notice/?$',
			'index.php?template=legal-notice',
			'bottom'
		);
		$wp_rewrite->flush_rules();
	}

	/**
	* Filters the page title.
	*
	* @return      string              The filtered title.
	*/
	public function title() {
		return 'Legal notice';
	}

	/**
	 * Loads the Legal Notice template.
	 *
	 * @global $wp
	 * @global object $wp_query WordPress query object
	 */
	public function load_legal_notice_page( $original_template ) {
		global $wp, $wp_query;

		$template = $wp->query_vars;
		if ( array_key_exists( 'legal-notice', $template ) ) {

			// Filter some bits
			add_filter( 'body_class',             array( $this, 'body_classes' ) );
			add_filter( 'pre_get_document_title', array( $this, 'title' ) );

			$wp_query->set( 'is_404', false );
			return get_stylesheet_directory() . '/legal-notice.php';
		}

		return $original_template;
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
