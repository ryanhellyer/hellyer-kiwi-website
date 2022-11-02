<?php

/**
 * Adds support for the default FuseJS search engine to Strattic Search.
 *
 * @copyright Copyright (c), Strattic / Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@strattic.com>
 * @package Strattic Search
 * @since Strattic Search 2.3.37
 */
class Strattic_Search_Engine extends Strattic_Search_Core {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'strattic_search_index', array( $this, 'add_fuse_js_settings_to_index' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * Add the FuseJS settings to the JSON index.
	 *
	 * @param array $index The search index.
	 * @return array The modified search index.
	 */
	public function add_fuse_js_settings_to_index( $index ) {
		$index['fuse_js_options'] = array(
			'includeScore'      => true,
			'shouldSort'        => true,
			'findAllMatches'    => true,
			'threshold'         => 0.25,
			'distance'          => 10000, // Checks the first 10,000 characters of the post content.
			'useExtendedSearch' => false,
			'keys'              => array(
				'title',
				'content',
			),
			//  phpcs:ignore 'isCaseSensitive' => false,
			//  phpcs:ignore 'includeMatches' => false,
			//  phpcs:ignore 'minMatchCharLength' => 1,
			//  phpcs:ignore 'location' => 0,
			//  phpcs:ignore 'ignoreLocation' => false,
			//  phpcs:ignore 'ignoreFieldNorm' => false,
		);

		return $index;
	}

	/**
	 * Add scripts.
	 */
	public function scripts() {
		$plugin_dir = plugin_dir_url( dirname( __FILE__ ) );

		wp_enqueue_script( 'strattic-search-config', $plugin_dir . 'js/fuse.min.js', array(), self::VERSION_NUMBER, true );
		wp_enqueue_script( 'strattic-search-fusejs', $plugin_dir . 'js/strattic-search-fusejs.js', array( 'strattic-search-config' ), self::VERSION_NUMBER, true );
	}

}
