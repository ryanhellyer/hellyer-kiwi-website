<?php

/**
 * Primary class used to load the theme.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Hellish Simplicity
 * @since Hellish Simplicity 1.5
 */
class SRC_Theme_Setup {

	/**
	 * Theme version number.
	 * 
	 * @var string
	 */
	const VERSION_NUMBER = '1.0';

	/**
	 * Theme name.
	 * 
	 * @var string
	 */
	const THEME_NAME = 'src';

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',               array( $this, 'init' ) );
		add_action( 'after_setup_theme',  array( $this, 'theme_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'after_switch_theme', array( $this, 'theme_activation' ) );

		// Add filters
		add_filter('private_title_format', array( $this, 'remove_private_title_format' ) );

	}

	/**
	 * Init.
	 */
	public function init() {

		register_nav_menus(
			array(
				'header'       => __( 'Header Menu' ),
				'social-links' => __( 'Social Links Menu' ),
				'footer'       => __( 'Footer Menu' ),
			)
		);

		register_sidebar( array(
			'name'          => __( 'Sidebar', 'src' ),
			'id'            => 'sidebar',
			'description' => __( 'Widgets in this area will be shown on all posts and pages.', 'src' ),
			'before_widget' => '',
			'after_widget'  => '',
			'before_title'  => '<h3>',
			'after_title'   => '</h3>',
		) );

	}

	/**
	 * Load stylesheets.
	 */
	public function stylesheets() {
		if ( ! is_admin() ) {
			wp_enqueue_style( self::THEME_NAME, get_stylesheet_directory_uri() . '/css/style.css', array(), self::VERSION_NUMBER );
			wp_enqueue_style( 'google-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800', array(), self::VERSION_NUMBER );
		}
	}

	/**
	 * Load script.
	 */
	public function script() {
		if ( ! is_admin() ) {
			wp_enqueue_script( self::THEME_NAME, get_template_directory_uri() . '/js/script.js', null, SELF::VERSION_NUMBER );
		}
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Add title tags
		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
//		add_image_size( self::THEME_NAME . '-excerpt-thumb', 250, 350 );
	}

	/**
	 * Removing the "Private: " text from private page/post titles.
	 */
	public function remove_private_title_format( $content ) {
		return '%s';
	}

	/**
	 ** Tasks to run on theme activation.
	 */
	public function theme_activation() {

		// Adding option so that we can configure what post/page appears home page featured post area when user is logged out
		add_option( 'src_featured_page', 1 );
		add_option( 'src_registration_thanks_page', 1 );

	}

}
new SRC_Theme_Setup;
