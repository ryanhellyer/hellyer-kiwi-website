<?php

/**
 * Primary class used to load the theme.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Cuckoo Nord
 * @since Cuckoo Nord 1.0
 */
class Cuckoo_Setup {

	/**
	 * Theme version number.
	 * 
	 * @var string
	 */
	const VERSION_NUMBER = '1.0';

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'init',               array( $this, 'post_type' ) );
		add_action( 'init',               array( $this, 'support' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheet' ) );
//		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'init',               array( $this, 'menus' ) );
		add_action( 'template_redirect',  array( $this, 'under_construction' ) );
		add_action( 'wp',                 array( $this, 'force_404' ) );

		add_filter( 'the_content',        array( $this, 'gallery_content' ) );

		add_post_type_support( 'page', 'excerpt' );
		add_post_type_support( 'page', 'front-end-editor' ); // For the WP Front End Editor plugin https://wordpress.org/plugins/wp-front-end-editor/
	}

	public function post_type() {

		$args = array(
			'public'    => true,
			'label'     => esc_html( 'Gallery', '' ),
			'supports'  => array( 'title', 'editor', 'thumbnail' ),
			'menu_icon' => 'dashicons-format-gallery',
		);
		register_post_type( 'gallery', $args );

	}

	/**
	 * Add/remove theme/post-type supports.
	 */
	public function support() {
		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption' ) );
		add_theme_support( 'post-thumbnails' );
		remove_post_type_support( 'post', 'trackbacks' );
		add_image_size( 'cuckoo-archive', 640, 640, true );
	}

	/**
	 * Load stylesheet.
	 */
	public function stylesheet() {
		if ( ! is_admin() ) {
			wp_enqueue_style( 'style', get_stylesheet_directory_uri() . '/css/style.min.css', array(), self::VERSION_NUMBER );
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
	 * Add navigation menu support.
	 */
	public function menus() {

		register_nav_menus(
			array(
				'language' => esc_html__( 'Language Selector Menu', 'cuckoo' ),
			)
		);

		register_nav_menus(
			array(
				'header' => esc_html__( 'Header Menu', 'cuckoo' ),
			)
		);

	}

	/**
	 * Show gallery page content.
	 */
	public function gallery_content( $content ) {

		// Bail out now if not on front page
		if ( ! is_front_page() ) {
			return $content;
		}


		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'gallery',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		if ( $query->have_posts() ) {

			$content .= '<ul id="gallery">';

			while ( $query->have_posts() ) {
				$query->the_post();

				$content .= '<li><a href="' . esc_url( get_permalink() ) . '">' . 
				get_the_post_thumbnail( get_the_id(), 'post-thumbnail' ) .
				get_the_title() . 
				'</a></li>';

			}

			$content .= '</ul>';

		}

		return $content;
	}

	/**
	 * Under construction.
	 */
	public function under_construction() {

		// Bail out now if not on front page
		if ( is_user_logged_in() ) {
			return;
		}

		echo '<style>
		body {
			margin: 0;
			padding: 0;
			background: #fff;
		}
		h1 {
			font-family: sans-serif;
			font-size: 80px;
			color: #222;
			text-align: center;
			margin-top: 200px;
		}
		</style>
		<h1>Coming Soon!</h1>';

		die;
	}

	/**
	 * Force 404 on some paget types.
	 */
	public function force_404() {
		global $wp_query;

		if ( is_archive() ) {
			status_header( 404 );
			nocache_headers();
			include( get_query_template( '404' ) );

			die();
		}

	}

}
