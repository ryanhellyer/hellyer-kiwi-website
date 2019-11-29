<?php

/**
 * Primary class used to load the theme
 *
 * Some of the code used here was adapted from the Underscores theme from Automattic.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Hellish Simplicity
 * @since Hellish Simplicity 1.5
 */
class Hellish_Setup {

	/**
	 * Constructor
	 * Add methods to appropriate hooks and filters
	 */
	public function __construct() {
		global $content_width;
		$content_width = 680;

		add_action( 'after_setup_theme',                    array( $this, 'theme_setup' ) );
		add_action( 'widgets_init',                         array( $this, 'widgets_init' ) );
		add_action( 'wp_enqueue_scripts',                   array( $this, 'stylesheet' ) );
	}

	/**
	 * Load stylesheet
	 */
	public function stylesheet() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.css' );
		}
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {
	
		// Make theme available for translation
		load_theme_textdomain( 'hellish', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		add_theme_support( 'title-tag' );

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'excerpt-thumb', 150, 150, true );

		// Register navigation menu
		register_nav_menus(
			array(
				'primary' => 'Primary'
			)
		);
	}

	/**
	 * Register widgetized area and update sidebar with default widgets
	 */
	public function widgets_init() {
		register_sidebar(
			array(
				'name'          => __( 'Sidebar', 'hellish' ),
				'id'            => 'sidebar',
				'before_widget' => '<div class="widget">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Footer', 'hellish' ),
				'id'            => 'footer',
				'before_widget' => '<div><div>',
				'after_widget'  => '</div></div>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);
	}

}
new Hellish_Setup;
