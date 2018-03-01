<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    pushpress_coaches
 * @subpackage pushpress_coaches/public
 */
class Pushpress_Coaches_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $pushpress_coaches   The ID of this plugin.
	 */
	private $pushpress_coaches;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $pushpress_coaches       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $pushpress_coaches, $version ) {

		$this->pushpress_coaches = $pushpress_coaches;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in pushpress_coaches_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The pushpress_coaches_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->pushpress_coaches, plugin_dir_url( __FILE__ ) . 'css/pushpress-coaches-public.css', array(), $this->version, 'all' );		

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pushpress_Coaches_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pushpress_Coaches_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


		wp_enqueue_script( $this->pushpress_coaches, plugin_dir_url( __FILE__ ) . 'js/pushpress-coaches-public.js', array( 'jquery' ), $this->version, false );
		

	}

}
