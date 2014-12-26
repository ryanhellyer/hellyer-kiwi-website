<?php

/**
 * Primary class used to load the theme
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Comic Glot
 * @since Comic Glot 1.0
 */
class Comic_Glot_Setup {

	public $script_urls;
	public $css_urls;

	/**
	 * Constructor
	 * Add methods to appropriate hooks and filters
	 */
	public function __construct() {

		// Add script URL's
		$this->script_urls = array(
			includes_url( '/js/jquery/jquery.js' ),
//			includes_url( '/js/jquery-migrate.min.js' ),
			get_stylesheet_directory_uri() . '/scripts/swipe.js',
			get_stylesheet_directory_uri() . '/scripts/swipe-init.js',
		);

		// Add CSS URL's
		$this->css_urls = array(
			get_stylesheet_directory_uri() . '/style.css',
		);

		if ( isset( $_GET['manifest'] ) ) {
			$this->manifest();
		}

		add_action( 'after_setup_theme',  array( $this, 'theme_setup' ) );
		add_action( 'wp_head',            array( $this, 'stylesheet' ) );
		add_action( 'wp_footer',          array( $this, 'scripts' ) );
		add_action( 'the_content',        array( $this, 'override_content' ) );
	}

	public function override_content() {
		$urls = self:: get_attached_images( $post_id );

		foreach( $urls as $url ) {
			echo '
		<div><img src="' . esc_url( $url ) . '" /></div>';
		}
	}

	public function stylesheet() {
		foreach( $this->css_urls as $url ) {
			$url = $this->convert_url_to_https( $url );
			echo '<link rel="stylesheet" href="' . esc_url( $url ) . '" type="text/css" media="all" />';
		}
	}

	public function scripts() {
		foreach( $this->script_urls as $url ) {
			$url = $this->convert_url_to_https( $url );
			echo '<script src="' . esc_url( $url ) . '"></script>';

		}
	}

	/**
	 * Convert URLs to https where required
	 * @param    string   $url    The URL
	 * @return   string   $url    The URL after conversion to http/https
	 */
	public function convert_url_to_https( $url ) {

		// Convert URLs to SSL if needed
		if ( is_ssl() ) {
			$url = str_replace( 'http://', 'https://', $url );
		} else {
			$url = str_replace( 'https://', 'http://', $url );
		}

		return $url;
	}


	/**
	 * Get the posts attached images
	 * 
	 * @param    int  $post_id   The post ID
	 * @return   array  The array of URLs
	 */
	static public function get_attached_images( $post_id ) {

		// Add images
		$images = get_attached_media( 'image', $post_id );
		foreach( $images as $key => $image ) {
			$attachment_id = $image->ID;
			$url = wp_get_attachment_image_src( $attachment_id, 'full' );
			$url = $url[0];
			$url = self::convert_url_to_https( $url );
			$urls[] = $url;
		}

		return $urls;
	}

	/**
	 * Add the manifest file
	 */
	public function manifest() {
		$post_id = absint( $_GET['manifest'] );

		// Add the page header
		header( 'Content-Type: text/cache-manifest' );

		// Declare this is a cache manifest
		echo "CACHE MANIFEST\n";

		$urls = self:: get_attached_images( $post_id );

		// Add script and CSS URLs in
		$urls = array_merge( $this->script_urls, $urls );
		$urls = array_merge( $this->css_urls, $urls );

		// Output each URL
		foreach( $urls as $url ) {
			$url = $this->convert_url_to_https( $url );
			echo esc_url( $url ) . "\n";
		}

		// Output the current page URL
		echo get_permalink( $post_id ) . "\n";

		// Add string
		$time = absint( time() / 30 );
		echo '#' . $time;
		exit;
	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {

		// Make theme available for translation
		load_theme_textdomain( 'comic-glot', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head
		add_theme_support( 'automatic-feed-links' );

		// Enable support for Post Thumbnails
		add_theme_support( 'post-thumbnails' );
		add_image_size( 'excerpt-thumb', 250, 350 );
	}

}
new Comic_Glot_Setup;
