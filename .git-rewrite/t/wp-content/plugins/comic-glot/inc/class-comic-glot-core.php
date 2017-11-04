<?php

/**
 * The core class.
 * Provides tools used across all other classes.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Comic Glot
 * @since Comic Glot 1.0
 */
class Comic_Glot_Core {

	/**
	 * Constructor
	 * Add methods to appropriate hooks and filters
	 */
	public function __construct() {

		add_action( 'init',               array( $this, 'register_post_type' ) );
		add_action( 'plugins_loaded',     array( $this, 'text_domain' ) );

	}

	/**
	 * Register the post-type
	 */
	public function register_post_type() {
		$args = array(
			'public'             => true,
			'label'              => __( 'Comic', 'comic-glot' ),
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'comic' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'comments' ),
		);
		register_post_type( 'comic', $args );
	}

	/**
	 * Register the text domain
	 */
	public function text_domain() {

		// Make theme available for translation
		load_plugin_textdomain( 'comic-glot', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages' ); 

	}

	/**
	 * Get the available languages.
	 * 
	 * @return  array  The available languages
	 */
	protected function get_available_languages() {

		// Get the available languages
		require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		$translations = wp_get_available_translations();
		$translations['en_US'] = array(
			'language'    => 'en_US',
			'native_name' => 'English (USA)',
			'iso'         => array(
				1 => 'en',
				2 => 'eng',
				3 => 'eng',
			),
		);

		$available_languages = get_available_languages();

		// Add USA English by default
		$languages = array();
		$languages[] = array(
			'language'    => 'en_US',
			'native_name' => 'English',
			'lang'        => 'en',
		);

		// Add extra languages
		foreach ( $available_languages as $locale ) {
			if ( isset( $translations[ $locale ] ) ) {
				$translation = $translations[ $locale ];
				$languages[] = array(
					'language'    => $translation['language'],
					'native_name' => $translation['native_name'],
					'lang'        => $translation['iso'][1],
				);

				// Remove installed language from available translations.
				unset( $translations[ $locale ] );
			}
		}

		return $languages;
	}

	/**
	 * Get the currently available languages
	 * 
	 * @return  array  The currently availble languages
	 */
	public function get_current_languages() {

		// Get the post ID (need to try query var first due to CMB framework)
		if ( isset( $_GET['post'] ) ) {
			$post_id = absint( $_GET['post'] );
		} elseif( isset( $_POST['post_ID'] ) ) {
			$post_id = absint( $_POST['post_ID'] );
		} else {
			$post_id = get_the_ID();
		}

		// Get the available languages from DB
		$_comic_languages = get_post_meta( $post_id, '_comic_languages', true );
		if ( '' == $_comic_languages ) {
			$_comic_languages = array();
		}

		return $_comic_languages;
	}

	/**
	 * Get the language name from the language slug
	 *
	 * @param   string  $slug  The language slug
	 * @return  string  The language name
	 */
	protected function get_language_name_from_slug( $slug ) {

		// Get the available languages
		require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		$translations = wp_get_available_translations();
		$translations['en_US'] = array(
			'language'    => 'en_US',
			'native_name' => 'English (USA)',
			'iso'         => array(
				1 => 'en',
				2 => 'eng',
				3 => 'eng',
			),
		);

		foreach( $translations as $language => $language_info ) {
			if ( $language == $slug ) {
				return $language_info['native_name'];
			}
		}

		return false;
	}

	/**
	 * Get the language slug from the language iso value.
	 *
	 * @param   string  $iso    The language iso
	 * @return  string  The language slug value
	 */
	protected function get_language_slug_from_iso( $iso ) {

		// Get the available languages
		require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		$translations = wp_get_available_translations();
		$translations['en_US'] = array(
			'language'    => 'en_US',
			'native_name' => 'English (USA)',
			'iso'         => array(
				1 => 'en',
				2 => 'eng',
				3 => 'eng',
			),
		);

		// Get all possible slugs
		$slugs = array();
		foreach( $translations as $language => $language_info ) {

			if (
				( isset( $language_info['iso'][0] ) && $iso == $language_info['iso'][0] )
				||
				( isset( $language_info['iso'][1] ) && $iso == $language_info['iso'][1] )
				||
				( isset( $language_info['iso'][2] ) && $iso == $language_info['iso'][2] )
			) {
				$slugs[] = $language_info['language'];
			}
		}

		// We may have unavailable slugs, so lets ignore those (plus, we only want one value, not multiples)
		$slug = false;
		foreach( $slugs as $slug ) {
			if ( in_array( $slug, $this->get_current_languages() ) ) {
				return $slug;
			}
		}

		return $slug;
	}

	/**
	 * Get the language iso value from the language slug
	 *
	 * @param   string  $slug    The language slug
	 * @return  string  The language iso value
	 */
	protected function get_language_iso_from_slug( $slug ) {

		// Get the available languages
		require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
		$translations = wp_get_available_translations();
		$translations['en_US'] = array(
			'language'    => 'en_US',
			'native_name' => 'English (USA)',
			'iso'         => array(
				1 => 'en',
				2 => 'eng',
				3 => 'eng',
			),
		);

		foreach( $translations as $language => $language_info ) {
			if ( $language_info['language'] == $slug ) {

				foreach( array( 0, 1, 2, 3 ) as $key ) {
					if ( isset( $language_info['iso'][$key] ) ) {
						return $language_info['iso'][$key];
					}
				}

			}
		}

		return false;
	}

	/**
	 * Is iso one of the currently available ones?
	 *
	 * @param   string  $iso    The language iso value
	 * @return  bool    true if it is current available, false if not
	 */
	protected function is_iso( $iso ) {

		foreach( $this->get_current_languages() as $slug ) {
			$the_iso = $this->get_language_iso_from_slug( $slug );
			if ( $the_iso == $iso ) {
				return true;
			}
		}

		return false;
	}

}
