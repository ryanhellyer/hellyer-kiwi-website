<?php

/**
 * Applies specific templates for specific themes.
 *
 * @copyright Copyright (c), Strattic / Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Strattic Search
 * @since Strattic Search 2.3.34
 */
class Strattic_Search_Themes {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'strattic_search_page_data', array( $this, 'get_page_data' ) );
		add_filter( 'strattic_active_pages', function() {
			return $this->get_theme_data( 'active_pages' );
		} );
	}

	/**
	 * Get the immediate search templates.
	 *
	 * @return array $templates The templates.
	 */
	public function get_page_data() {

		// Store data required immediately when the page loads inline (everything else can be stored in the main index).
		$selectors = $this->get_theme_data( 'content_areas' );
		foreach ( $selectors as $selector ) {
			$data = array(
				'templates'       => $this->get_templates(),
				'name_attr'       => $this->get_theme_data( 'name_attr' ),
				'body_classes'    => $this->get_theme_data( 'body_classes' ),
				'active_pages'    => $this->get_theme_data( 'active_pages' ),
				'hide_content'    => $this->get_theme_data( 'hide_content' ),
				'current_date'    => date( 'Y-m-d_G' ), // Only used for auto-expiring the JSON cache.
			);
		}

		return $data;
	}

	/**
	 * If available template.
	 *
	 * @access private
	 * @return bool true if template exists.
	 */
	private function if_available_template() {
		$path = dirname( __FILE__ ) . '/themes/' . basename( get_template_directory() ) . '/info.json';
		if ( file_exists( $path ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Get theme info.
	 *
	 * @access private
	 * @param string $key The key for the item to return.
	 * @return mixed The info to return.
	 */
	private function get_theme_data( $key ) {

		// If logged in, then use logged in specific body classes.
		if ( is_user_logged_in() && 'body_classes' === $key ) {
			$key = 'body_classes_logged_in';
		}

		if ( '_template' === substr( $key, -9 ) ) {
			return $this->get_theme_template( $key );
		} else {
			return $this->get_theme_info( $key );
		}
	}

	/**
	 * Get the templates.
	 *
	 * @access private
	 * @return mixed The info to return.
	 */
	private function get_templates() {
		$templates = array();
		$selectors = $this->get_theme_data( 'content_areas' );
		foreach ( $selectors as $selector ) {
			$templates[ $selector ] = array(
				'loading'                => $this->get_theme_template( $selector . '/loading-template' ),
				'single_result_template' => $this->get_theme_template( $selector . '/single-result-template' ),
				'multi_results_template' => $this->get_theme_template( $selector . '/multi-results-template' ),
				'no_results_template'    => $this->get_theme_template( $selector . '/no-results-template' ),
				'result_template'        => $this->get_theme_template( $selector . '/result-template' ),
			);
		}

		return $templates;
	}

	/**
	 * Get a theme template.
	 *
	 * @access private
	 * @param string $key The key for the item to return.
	 * @return mixed The info to return.
	 */
	private function get_theme_template( $key ) {
		$plugin_dir    = dirname( dirname( __FILE__ ) );
		$template_name = substr( $key, 0, -9 );
		$path          = $plugin_dir . '/themes/' . basename( get_template_directory() ) . '/' . $template_name . '.tpl';

		if ( ! file_exists( $path ) ) {
			$path = $plugin_dir . '/themes/strattic-default/#strattic-search-results/' . basename( $template_name ) . '.tpl';
		}

		$template = file_get_contents( $path );

		return $template;
	}

	/**
	 * Get a bit of theme info.
	 *
	 * @access private
	 * @param string $key The key for the item to return.
	 * @return mixed The info to return.
	 */
	private function get_theme_info( $key ) {
		$plugin_dir = dirname( dirname( __FILE__ ) );
		$path       = $plugin_dir . '/themes/' . basename( get_template_directory() ) . '/info.json';

		if ( ! file_exists( $path ) ) {
			$path = $plugin_dir . '/themes/strattic-default/info.json';
		}
		$json = file_get_contents( $path );
		$info = json_decode( $json, true );

		$value = null;
		if ( isset( $info[ $key ] ) ) {
			$value = $info[ $key ];
		}

		return $value;
	}

}
