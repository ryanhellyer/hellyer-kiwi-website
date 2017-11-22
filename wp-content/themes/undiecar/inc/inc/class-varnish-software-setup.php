<?php

/**
 * Primary class used to load the Varnish Software theme.
 *
 * @copyright Copyright (c), Varnish Software
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Varnish Software
 * @since Varnish Software 1.0
 */
class Varnish_Software_Setup extends Varnish_Software_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 *
	 * @global  int  $content_width  Sets the media widths (unfortunately required as a global due to WordPress core requirements) 
	 */
	public function __construct() {

		global $content_width;
		$content_width = 680;

		// Add action hooks
		add_action( 'after_setup_theme',  array( $this, 'theme_setup' ) );
		add_action( 'widgets_init',       array( $this, 'widgets_init' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheet' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'init',               array( $this, 'register_menus' ) );
		add_action( 'after_setup_theme',  array( $this, 'woocommerce_support' ) );
		add_action( 'admin_init',         array( $this, 'admin_features' ) );

	}

	/**
	 * Load stylesheet.
	 */
	public function stylesheet() {
		if ( ! is_admin() ) {

 			/* make sure the Theme Integrator plugin is updated to match before changing these */
 			wp_enqueue_style( 'varnish-software', get_stylesheet_directory_uri() . '/css/style.css', array(), self::VERSION_NUMBER );
			wp_enqueue_style( 'varnish-google-fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:400,400italic,600,600italic,700,700italic', array(), self::VERSION_NUMBER );

		}
	}

	/**
	 * Load scripts.
	 */
	public function scripts() {

		if ( ! is_admin() ) {

			wp_enqueue_script(
				'varnish-script',
				get_stylesheet_directory_uri() . '/js/rewrite.js', /* make sure the Theme Integrator plugin is updated to match before changing this */
				array(),
				self::VERSION_NUMBER,
				true
			);

		}

	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {

		// Make theme available for translation
		load_theme_textdomain( 'varnish-software', get_template_directory() . '/languages' );

		// Add theme support
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );

	}

	/**
	 * Register widgetized area and update sidebar with default widgets.
	 */
	public function widgets_init() {
		register_sidebar(
			array(
				'name'          => 'Footer', 'varnish-software',
				'id'            => 'footer',

				'before_widget' => '
						<div class="%2$s" id="%1$s">
							<div class="region region-footer-first">',

				'after_widget'  => '
							</div>
						</div>
',
				'before_title'  => "\n\t\t\t\t\t\t\t\t<h1>",
				'after_title'   => "</h1>\n",
			)
		);

		register_sidebar(
			array(
				'name'          => 'After content', 'varnish-software',
				'id'            => 'after-loop',
				'before_widget' => '<div class="%2$s" id="%1$s">',
				'after_widget'  => '</div>',
				'before_title'  => "\n\t\t\t\t\t\t\t\t<h1>",
				'after_title'   => "</h1>\n",
			)
		);

	}

	/**
	 * Register menus.
	 * Intentionally not translated, to avoid excessive translation strings.
	 */
	public function register_menus() {

		$menus = array(
			'main-navigation'   => 'Main navigation',
			'top-navigation'    => 'Top navigation',
			'signup-block'      => 'Signup block',
			'start-trial'       => 'Start your trial',
			'footer'            => 'Footer',
		);

		foreach ( $menus as $menu_slug => $menu_name ) {
			register_nav_menu( $menu_slug, $menu_name );
		}

	}

	/**
	 * Add WooCommerce support.
	 */
	public function woocommerce_support() {
		add_theme_support( 'woocommerce' );
	}

	/**
	 * Features which only run in admin area.
	 */
	public function admin_features() {
		add_option( 'varnish-use-root-header-footer', 'no', null, false );
	}

}
