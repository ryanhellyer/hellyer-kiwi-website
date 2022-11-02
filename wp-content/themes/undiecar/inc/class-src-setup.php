<?php

/**
 * Primary class used to load the theme.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Undycar
 * @since Undycar 1.0
 */
class SRC_Theme_Setup extends SRC_Core {

	/**
	 * Theme version number.
	 * 
	 * @var string
	 */
	const VERSION_NUMBER = '1.1';

	/**
	 * Theme name.
	 * 
	 * @var string
	 */
	const THEME_NAME = 'undiecar';

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'after_switch_theme', array( $this, 'set_options' ) );
		add_action( 'init',               array( $this, 'init' ) );
		add_action( 'after_setup_theme',  array( $this, 'theme_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'wp',                 array( $this, 'force_404' ) );

		// Add shortcodes
		add_shortcode( 'src-driver',      array( $this, 'driver_block_shortcode' ) );

		// Add filters
		add_filter( 'private_title_format', array( $this, 'remove_private_title_format' ) );

	}

	/**
	 * Set options.
	 */
	public function set_options() {
		add_option( 'medium_crop', '1' );
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

		add_image_size( 'src-featured', 1920, 300, true );
		add_image_size( 'src-logo', 300, 150, true );
		add_image_size( 'src-four', 480, 240, true );
		add_image_size( 'src-three', 580, 305, true );

	}

	/**
	 * Load stylesheets.
	 */
	public function stylesheets() {
		if ( ! is_admin() ) {
			wp_enqueue_style( self::THEME_NAME, get_stylesheet_directory_uri() . '/css/style.min.css', array(), self::VERSION_NUMBER );
			wp_enqueue_style( 'google-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800', array(), self::VERSION_NUMBER );
		}
	}

	/**
	 * Load script.
	 */
	public function script() {
		if ( ! is_admin() ) {
			wp_enqueue_script( self::THEME_NAME, get_template_directory_uri() . '/js/script.js', null, SELF::VERSION_NUMBER );
			wp_enqueue_script( self::THEME_NAME . '-tabber', get_template_directory_uri() . '/js/tabber.js', null, SELF::VERSION_NUMBER );
			wp_localize_script( self::THEME_NAME, 'undiecar_home_url', home_url()	);
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

		// User new gallery code
		add_theme_support( 'html5', array( 'gallery', 'caption' ) );

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

	public function force_404() {

		global $wp_query;
		if ( is_archive() ) {
			status_header( 404 );
			nocache_headers();
			include( get_query_template( '404' ) );
		die();
		}
	}

	/**
	 * 
	 */
	public function driver_block_shortcode( $args, $content ) {

		if ( ! isset( $args['username'] ) ) {
			return $content;
		}

		$driver = get_user_by( 'login', $args['username'] );
		$driver_id = absint( $driver->ID );

		$content .= $this->get_driver_block( $driver_id );

		return $content;
	}

}
new SRC_Theme_Setup;
