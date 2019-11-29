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
class Comic_Glot_Setup extends Comic_Glot_Core {

	public $script_urls;
	public $css_urls;

	/**
	 * Constructor
	 * Add methods to appropriate hooks and filters
	 */
	public function __construct() {

		parent::__construct();

		// Add script URL's
		$this->script_urls = array(
			includes_url( '/js/jquery/jquery.js' ),
			plugin_dir_url( dirname( __FILE__ ) ) . 'scripts/swipe.js',
			plugin_dir_url( dirname( __FILE__ ) ) . 'scripts/swipe-init.js',
		);

		// Add CSS URL's
		$this->css_urls = array(
			plugin_dir_url( dirname( __FILE__ ) ) . 'style.css',
		);

		add_action( 'comic_glot_head',    array( $this, 'stylesheet' ) );
		add_action( 'comic_glot_footer',  array( $this, 'scripts' ) );
		add_action( 'the_content',        array( $this, 'override_content' ) );
		add_action( 'init',               array( $this, 'rewrites' ) );
		add_action( 'query_vars',         array( $this, 'rewrite_query_vars' ) );
		add_action( 'template_redirect',  array( $this, 'comic_template' ), 11 );
	}

	/**
	 * Load the comic template
	 */
	public function comic_template() {

		// If a password is required, then serve regular template
		if ( post_password_required() ) {
			return;
		}

		// Only load if on comic template
		if ( 'comic' == get_post_type() ) {
			require( dirname( dirname( __FILE__ ) ) . '/comic-template.php' );
			exit;
		}
	}

	/**
	 * Rewrite the query variable global
	 * 
	 * @param  array  $query_vars  The query vars
	 * @return array  The modified query vars
	 */
	public function rewrite_query_vars( $query_vars ) {
		$query_vars['lang'] = 'lang';
		return $query_vars;
	}

	/*
	 * Add rewrite rules
	 */
	public function rewrites() {
		add_rewrite_rule( 'comic/([^/]+)/([^/]+)/?$', 'index.php?comic=$matches[1]&lang=$matches[2]', 'top' );
	}

	/**
	 * Override the content
	 * 
	 * @param  string   $content   The post content
	 * @return string   The modified post content
	 */
	public function override_content( $content ) {

		// If not on comic, then bail out now
		if ( 'comic' != get_post_type() ) {
			return $content;
		}

		// If password required, then bail out now so that regular theme template is used
		if ( post_password_required() ) {
			return $content;
		}

		$content = '';
		$frames = get_post_meta( get_the_ID(), '_frames', true );

		// Get isos of currently used languages
		$isos = get_query_var( 'lang' );
		if ( '' != $isos ) {
			$isos = explode( '-', $isos );
		} else {
			$current_languages = $this->get_current_languages();
			foreach( $current_languages as $key => $slug ) {
				$isos[] = $this->get_language_iso_from_slug( $slug );
			}
		}

		// Check that iso values selected are actually being used (or even exist)
		foreach( $isos as $key => $iso ) {

			// If a non-legit iso value is found, then serve error
			if ( ! $this->is_iso( $iso ) ) {
				$content = '<div><span>' . __( 'Error: 404 page not found.', 'comic-glot' ) . '</span></div>';
				return $content;
			}
		}

		// Loop through each frame
		foreach( $frames as $frame ) {
			$content .= '
		<div>';

			// Load the frame for each language
			foreach( $isos as $key => $iso ) {
				$slug = $this->get_language_slug_from_iso( $iso );

				if ( isset( $frame[$slug . '_id'] ) ) {
					$attachment_id = absint( $frame[$slug . '_id'] );

					if ( is_int( $attachment_id ) ) {
						$url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
					}

					$content.= '
			<span lang="' . esc_attr( $slug )  . '">
				<img src="' . esc_url( $url ) . '" />
				<br />
			</span>';

				}
			}

			$content .= '
		</div>';
		}

		// Add the back button
		$content .= '
		<div>
			<span>
				<strong><a class="button back" href="' . esc_url( home_url() ) . '">' . __( 'Back to home', 'comic-glot' ) . '</a></strong>
			</span>
		</div>';

		return $content;
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
	 * Get the comic images
	 * 
	 * @return [type] [description]
	 */
	public function get_comic_images( $post_id ) {
		$frames = get_post_meta( $post_id, '_frames', true );

		// Loop through each frame
		foreach( $frames as $frame ) {

			// Load the frame for each language
			foreach( $this->langs as $lang => $lang_info ) {
				if ( isset( $frame[$lang . '_id'] ) ) {
					$attachment_id = absint( $frame[$lang . '_id'] );
					if ( is_int( $attachment_id ) && 0 != $attachment_id ) {
						$url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
						$urls [] = esc_url( $url );
					}
				}
			}

		}

		return $urls;
	}


}
new Comic_Glot_Setup;
