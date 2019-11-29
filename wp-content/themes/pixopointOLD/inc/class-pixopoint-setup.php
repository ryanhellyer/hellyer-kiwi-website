<?php

/**
 * Primary class used to load the theme
 *
 * Some of the code used here was adapted from the Underscores theme from Automattic.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class PixoPoint_Setup {

	/**
	 * Constructor
	 * Add methods to appropriate hooks and filters
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function __construct() {
		add_action( 'after_setup_theme',     array( $this, 'theme_setup' ) );
		add_action( 'widgets_init',          array( $this, 'widgets_init' ) );
		add_filter( 'pixopoint_header_name', array( $this, 'header_name' ) );
		add_action( 'wp_enqueue_scripts',    array( $this, 'stylesheets' ) );
	}

	/**
	 * Load stylesheets
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function stylesheets() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/style.less' );
		} else {
			// you can also use .less files as mce editor style sheets
			add_editor_style( 'editor-style.less' );
		}
	}

	/**
	 * Load stylesheets
	 * 
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function header_name() {
		return '
			<h1><a href="http://ryanhellyer.net">Ryan<span>Hellyer</span><small>.net</small></a></h1>';
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function theme_setup() {
	
		// Make theme available for translation
		load_theme_textdomain( 'pixopoint', get_template_directory() . '/languages' );
	
		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );
	
		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
	
		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'primary' => __( 'Primary Menu', 'pixopoint' ),
			)
		);
	}

	/**
	 * Register widgetized area and update sidebar with default widgets
	 *
	 * @since 1.0
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 */
	public function widgets_init() {
		register_sidebar(
			array(
				'name'          => __( 'Sidebar', 'pixopoint' ),
				'id'            => 'sidebar',
				'before_widget' => '<aside id="%1$s" class="%2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h1 class="widget-title">',
				'after_title'   => '</h1>',
			)
		);
	}

}
