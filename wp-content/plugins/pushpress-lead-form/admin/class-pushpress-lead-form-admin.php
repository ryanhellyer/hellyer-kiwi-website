<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    pushpress_lead_form
 * @subpackage pushpress_lead_form/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    pushpress_lead_form
 * @subpackage pushpress_lead_form/admin
 */
class Pushpress_Lead_Form_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $pushpress_lead_form    The ID of this plugin.
	 */
	private $pushpress_lead_form;

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
	 * @param      string    $pushpress_lead_form       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $pushpress_lead_form, $version ) {

		$this->pushpress_lead_form = $pushpress_lead_form;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pushpress_Lead_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pushpress_Lead_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class. 
		 */
		wp_enqueue_style( $this->pushpress_lead_form, plugin_dir_url( __FILE__ ) . 'css/pushpress-lead-form-admin.css', array(), $this->version, 'all' );		
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Pushpress_Lead_Form_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pushpress_Lead_Form_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script( 'pushpress_lead_form_admin', plugin_dir_url( __FILE__ ) . 'js/pushpress-lead-form-admin.js', array( 'jquery' ), $this->version, false );


	}

	public static function index() { 
		if ( PUSHPRESS_INTEGRATED ) {
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/pushpress-lead-form-admin-display.php';
		}
		else { 
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/pushpress-not-integrated.php';
		}
		
	}

}
