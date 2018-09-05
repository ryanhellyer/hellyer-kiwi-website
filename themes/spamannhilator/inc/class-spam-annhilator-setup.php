<?php

/**
 * Primary class used to load the theme.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Spam Annhilator theme
 * @since Spam Annhilator theme 1.0
 */
class Spam_Annhilator_Setup {

	/**
	 * Theme version number.
	 * 
	 * @var string
	 */
	private $version_number = '1.0';

	/**
	 * Theme name.
	 * 
	 * @var string
	 */
	private $theme_name = 'spamannhilator';

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
$this->version_number .= rand();
		// Add action hooks
		add_action( 'after_setup_theme',  array( $this, 'theme_setup' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'stylesheets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'wp',                 array( $this, 'force_404' ) );
		add_action( 'init',               array( $this, 'disable_gutenberg_junk' ) );
		add_action( 'template_redirect',  array( $this, 'redirect_logged_in_users' ) );
		add_action( 'init',               array( $this, 'disable_embed_script' ) );
		add_action( 'widgets_init',       array( $this, 'widgets_init' ) );

		// Add filters
		add_filter( 'the_content',          array( $this, 'the_content' ) );
		add_filter( 'private_title_format', array( $this, 'remove_private_title_format' ) );
		add_filter( 'simple_facebook_login_html', array( $this, 'privacy_policy_check' ) );
	}

	/**
	 * Disable Gutenberg junk.
	 */
	public function disable_gutenberg_junk() {
		remove_action( 'wp_enqueue_scripts', 'gutenberg_common_scripts_and_styles' );
	}

	/**
	 * Load stylesheets.
	 */
	public function stylesheets() {
		if ( ! is_admin() ) {
			wp_enqueue_style( $this->theme_name, get_stylesheet_directory_uri() . '/css/style.min.css', array(), $this->version_number );
			wp_enqueue_style( 'google-open-sans', 'https://fonts.googleapis.com/css?family=Open+Sans:400,800', array(), $this->version_number );
		}
	}

	/**
	 * Load script.
	 */
	public function script() {

		wp_enqueue_script(
			$this->theme_name . '-cookies',
			get_template_directory_uri() . '/js/cookies.js',
			array(),
			$this->version_number
		);

		wp_localize_script(
			$this->theme_name . '-cookies',
			'cookie_notice_text',
			array(
				sprintf(
					esc_html__( 'Please note that we use cookies on this website. For more information visit our %sprivacy page%s. %s', 'spamannhilator' ),
					'<a href="' . esc_url( home_url() . '/' . __( 'privacy-policy', 'spamannhilator' ) . '/' ) . '">',
					'</a>',
					'<span id="close-cookie-notice">&#10006;</span>'
				)
			)
		);

		wp_localize_script(
			$this->theme_name . '-cookies',
			'domain_name',
			esc_html( $_SERVER['HTTP_HOST'] )
		);

		// Scripts for logged in members only
		if (
			! is_admin()
			&&
			is_user_logged_in()
		) {

			wp_enqueue_script(
				'mustaches',
				get_template_directory_uri() . '/js/mustaches.min.js',
				array(),
				$this->version_number
			);

			wp_enqueue_script(
				$this->theme_name,
				get_template_directory_uri() . '/js/script.js',
				array( 'mustaches' ), // Used for templating
				$this->version_number
			);

			wp_localize_script( $this->theme_name, 'spamannhilator_nonce', array( wp_create_nonce( 'wp_rest' ) ) );
			wp_localize_script( $this->theme_name, 'spamannhilator_home_url', array( esc_url( home_url() ) ) );
			wp_localize_script( $this->theme_name, 'spamannhilator_user_id', array( absint( get_current_user_id() ) ) );
		}

	}

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 */
	public function theme_setup() {

		// Add title tags
		add_theme_support( 'title-tag' );

		// User new gallery code
		add_theme_support( 'html5', array( 'gallery', 'caption' ) );

	}

	/**
	 * Force 404 errors when on page types not present in the site.
	 */
	public function force_404() {
		global $wp_query;

		if (
			is_archive()
			||
			is_single()
			||
			is_search()
			||
			is_404()
		) {
			status_header( 404 );
			nocache_headers();
			include( get_query_template( 'index' ) );
			die();
		}

	}

	/**
	 * Redirecting logged in users to the members profile.
	 */
	public function redirect_logged_in_users() {

		// Only redirect if user is logged in, on front page and not an admin
		if (
			is_front_page()
			&&
			is_user_logged_in()
			&&
			! current_user_can( 'manage-options' )
		) {

			$author_id = get_current_user_id();
			$username = get_the_author_meta( 'user_login', $author_id );

			$url = home_url() . '/' . __( 'member', 'spamannhilator' ) . '/' . $username . '/';
			wp_redirect( esc_url( $url ), 302 );

		}

	}

	/**
	 * Disables the WP Embed script.
	 */
	public function disable_embed_script() {

		if ( ! is_admin() ) {
			wp_deregister_script( 'wp-embed' );
		}

	}

	/**
	* Register widgetized area and update sidebar with default widgets.
	*/
	public function widgets_init() {

		register_sidebar(
			array(
				'name'          => esc_html__( 'Footer', 'spam-annhilator' ),
				'id'            => 'footer',
				'before_widget' => '<aside class="widget">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3>',
				'after_title'   => '</h3>',
			)
		);

	}

	/**
	 * Adding a wrapper to the members page.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function the_content( $content ) {

		// Front page uses Gutenberg, with full width sections, so don't add wrap there
		if ( is_front_page() ) {
			return $content;
		}

		$content = '<div class="wrap">' . $content . '</div>';
		return $content;
	}

	/**
	 * Removing the "Private: " text from private page/post titles.
	 */
	public function remove_private_title_format( $content ) {
		return '%s';
	}

	/**
	 * Add privacy policy checkbox to login page.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function privacy_policy_check( $content ) {
		$string = '';

		// Bail out now if logged in
		if ( ! is_user_logged_in() ) {

			// Serve privacy policy with checkbox
			$privacy_page_id = get_option( 'spamannhilator_privacy_policy' );
			$string .= '<p>';
			$string .= '<input id="spamannhilator-privacy-policy-checkbox" type="checkbox" /> ';
			$string .= sprintf(
				esc_html__( 'I have read and agree to the %s', 'spamannhilator' ),
				'<a href="' . esc_url( get_permalink( $privacy_page_id ) ) . '">
					' . esc_html__( 'Privacy Policy', 'spamannhilator' ) . '
				</a>'
			);
			$string .= '</p>';

			// Using inline script because it makes for simpler code
			$string .= "
<script>
the_checkbox = document.getElementById( 'spamannhilator-privacy-policy-checkbox' );
console.log( the_checkbox );
the_checkbox.addEventListener(
	'change',
	function() {

		var facebook_login = document.getElementById( 'simple-facebook-login' );
		if (
			'button' === facebook_login.className
		) {
			facebook_login.className = 'button active';
		} else {
			facebook_login.className = 'button';
		}

	}
);
</script>";

		}

		$content = $string . $content;

		return $content;
	}

}
