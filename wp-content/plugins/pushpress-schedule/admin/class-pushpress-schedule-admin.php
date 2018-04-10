<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    pushpress_schedule
 * @subpackage pushpress_schedule/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    pushpress_schedule
 * @subpackage pushpress_schedule/admin
 */
class PushPress_Schedule_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $pushpress_schedule    The ID of this plugin.
	 */
	private $pushpress_schedule;

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
	 * @param      string    $pushpress_schedule       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $pushpress_schedule, $version ) {

		$this->pushpress_schedule = $pushpress_schedule;
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
		 * defined in Pushpress_Schedule_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pushpress_Schedule_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class. 
		 */
		wp_enqueue_style( $this->pushpress_schedule, plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/pushpress-schedule-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fullcalendar-css', plugin_dir_url( dirname( __FILE__ ) ) . ' . public/css/fullcalendar/fullcalendar.min.css', array(), $this->version, 'all' );		

		wp_enqueue_style( $this->pushpress_schedule, plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/pushpress-schedule-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fullcalendar', plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/fullcalendar/fullcalendar.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'pushpress-jquery-ui-dialog', plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/jquery-ui.css', array(), $this->version, 'all' );
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
		 * defined in Pushpress_Schedule_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Pushpress_Schedule_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		
		wp_enqueue_script( 'pushpress_schedule_admin', plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/pushpress-schedule-admin.js', array( 'jquery' ), $this->version, false );

		// this is in public
		wp_enqueue_script( 'moment-js', plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/fullcalendar/lib/moment.min.js', array(), $this->version, false );

		wp_enqueue_script( 'fullcalendar', plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/fullcalendar/fullcalendar.js', array( 'jquery', 'moment-js' ), $this->version, false );

	}

	public static function index() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/pushpress-schedule-admin-display.php';
		
	}

}
