<?php

/**
 * Primary class used to load the theme.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
class Free_Advice_Berlin_Setup {

	/**
	 * Theme version number.
	 * 
	 * @var string
	 */
	const VERSION_NUMBER = '1.0.4';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init',                        array( $this, 'support' ) );
		add_action( 'after_setup_theme',           array( $this, 'remove_admin_bar' ) );
		add_action( 'after_setup_theme',           array( $this, 'theme_setup' ) );
		add_action( 'wp_enqueue_scripts',          array( $this, 'stylesheet' ) );
		add_action( 'wp_enqueue_scripts',          array( $this, 'script' ) );
		add_filter( 'comment_form_default_fields', array( $this, 'comment_fields' ) );
		add_action( 'wp',                          array( $this, 'force_404' ), 15 );
		add_post_type_support( 'page', 'excerpt' );
		add_post_type_support( 'page', 'front-end-editor' ); // For the WP Front End Editor plugin https://wordpress.org/plugins/wp-front-end-editor/
	}

	/**
	 * Add and remove post-type supports.
	 */
	public function support() {
		add_post_type_support( 'page', 'excerpt' );
		remove_post_type_support( 'post', 'trackbacks' );
	}

	/**
	 * Force a 404 page on specific templates.
	 */
	public function force_404() {
		global $wp_query;

		if ( ! is_page() && ! is_front_page() && ! is_admin() && ! is_search() ) {
			status_header( 404 );
			nocache_headers();
			include( get_query_template( '404' ) );
			die();
		}
	}

	/**
	 * Hiding the admin bar.
	 */
	public function remove_admin_bar() {
		show_admin_bar( false );
	}

	/**
	 * Load stylesheet.
	 */
	public function stylesheet() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/css/style.css', array(), self::VERSION_NUMBER );
		}
	}

	/**
	 * Load script.
	 */
	public function script() {
		if ( ! is_admin() ) {
			wp_enqueue_script( 'free-advice-berlin', get_template_directory_uri() . '/scripts/theme.js', array(), self::VERSION_NUMBER, true );
		}
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {
		add_theme_support( 'title-tag' );
	}

	/**
	 * Modify which comments fields are used.
	 *
	 * @param  array  $fields  The comment fields
	 * @return array Modified comment fields
	 */
	public function comment_fields( $fields ) {

		if ( isset( $fields['url'] ) ) {
			unset($fields['url']);
		}

		if ( isset( $fields['email'] ) ) {
			unset($fields['email']);
		}

		return $fields;
	}

}
