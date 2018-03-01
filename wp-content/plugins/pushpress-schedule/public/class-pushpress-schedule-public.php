<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    pushpress_schedule
 * @subpackage pushpress_schedule/public
 */
class Pushpress_Schedule_Public {

	private $ajax_security;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $pushpress_schedule   The ID of this plugin.
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
	 * @param      string    $pushpress_schedule       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $pushpress_schedule, $version ) {

		$this->pushpress_schedule = $pushpress_schedule;
		$this->version = $version;

		$this->ajax_security = "eMu.J9aFhfi#R[:oWA@xp_~P@&BGQHD{c|64b2Wu@kvO1 L4Sh=x;J0<UeB1].`Y";
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
		 * defined in pushpress_schedule_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The pushpress_schedule_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->pushpress_schedule, plugin_dir_url( __FILE__ ) . 'css/pushpress-schedule-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'fullcalendar', plugin_dir_url( __FILE__ ) . 'css/fullcalendar/fullcalendar.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'pushpress-jquery-ui-dialog', plugin_dir_url( __FILE__ ) . 'css/jquery-ui.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		// this is in public
		wp_enqueue_script( 'moment-js', plugin_dir_url( __FILE__ ) . 'js/fullcalendar/lib/moment.min.js', array(), $this->version, false );
		wp_enqueue_script( 'fullcalendar', plugin_dir_url( __FILE__ ) . 'js/fullcalendar/fullcalendar.js', array( 'jquery', 'moment-js' ), $this->version, false );
		wp_enqueue_script( 'jquery-ui-dialog' );

		// Register the script
		wp_register_script( 'pushpress_schedule_public', plugin_dir_url( __FILE__ ) . 'js/pushpress-schedule-public.js', array('jquery'), '1.0.1', true );

		// Localize the script with new data
		$data_array = array(
			'ajaxurl' => admin_url( 'admin-ajax.php'),
			'security' => wp_create_nonce($this->ajax_security)
		);
		wp_localize_script( 'pushpress_schedule_public', 'MyAjax', $data_array );

		// Enqueued script with localized data.
		wp_enqueue_script( 'pushpress_schedule_public' );

	}

	public function get_schedule_callback() {

		check_ajax_referer( $this->ajax_security, 'security' );

		$callback = '';
		if ( isset( $_GET['callback'] ) ) {
			$callback = esc_html( $_GET['callback'] );
		}

		$args = array();

		$args['start_time'] = strtotime( 'midnight ' . $_POST['start'] );
		$args['end_time'] = strtotime( 'midnight ' . $_POST['end'] );

		//$cache_key = 'CALENDAR_' . $args['type'] . $args['calendar_type_id'] . $args['start_time'] . 
		
		$calendar_items = Pushpress_Calendar::all( $args );

		$events = array();

		foreach ( $calendar_items->data as $item )  { 

			$calendar_item = Pushpress_Calendar::retrieve( $item['uuid'] );

			// If class type (eg: CrossFit) set, then ignore others
			if (
				isset( $_POST['class_type'] )
				&&
				'' !== $_POST['class_type']
				&&
				$_POST['class_type'] !== $calendar_item->type['name']
			) {
				continue;
			}

			// If session type (eg: Class) set, then ignore others
			if (
				isset( $_POST['calendar_type'] )
				&&
				'' !== $_POST['calendar_type']
				&&
				$_POST['calendar_type'] !== $calendar_item->session_type['type']
			) {
				continue;
			}

			// If post-code (eg: 90254) set, then ignore others
			if (
				isset( $_POST['post_code'] )
				&&
				'' !== $_POST['post_code']
			) {

				$postal_codes = array();
				foreach ( $calendar_item->coach as $the_coach ) {
					$postal_codes[] = $the_coach->postal_code;
				}

				if ( ! in_array( $_POST['post_code'], $postal_codes ) ) {
					continue;
				}

			}

			// If coach (eg: McConachey) set, then ignore others
			if ( isset( $_POST['coach'] ) && '' !== $_POST['coach'] ) {

				$coaches = array();
				foreach ( $calendar_item->coach as $the_coach ) {
					$coaches[] = $the_coach->first_name;
					$coaches[] = $the_coach->last_name;
					$coaches[] = $the_coach->username;
				}

				if ( ! in_array( $_POST['coach'], $coaches ) ) {
					continue;
				}

			}

			// If date (eg: month is '02') set, then ignore others
			$start_timestamp = strtotime( $calendar_item->start_datetime );
			$start_day = date( 'd', $start_timestamp );
			$start_week = date( 'W', $start_timestamp );
			$start_month = date( 'm', $start_timestamp );

			if (
				( isset( $_POST['day'] ) && '' !== $_POST['day'] && $day !== $start_day )
				||
				( isset( $_POST['week'] ) && '' !== $_POST['week'] && $week !== $start_week )
				||
				( isset( $_POST['month'] ) && '' !== $_POST['month'] && $month !== $start_month )
			) {
				continue;
			}

			$recur_days = array_filter(explode( ',', trim( $item['recurring_day_of_week'] ) ) );
			$recur_0 = in_array( 0, $recur_days );
			$recur_1 = in_array( 1, $recur_days );
			$recur_2 = in_array( 2, $recur_days );
			$recur_3 = in_array( 3, $recur_days );
			$recur_4 = in_array( 4, $recur_days );
			$recur_5 = in_array( 5, $recur_days );
			$recur_6 = in_array( 6, $recur_days );

			$x = array(
				'uuid'               => $item['uuid'],
				'timezone'           => date( 'e', $item['start_timestamp'] ),
				'title'              => $item['title'],
				'start'              => date( 'c', $item['start_timestamp'] ),
				'end'                => date( 'c',$item['end_timestamp'] ),
				'textColor'          => '#ffffff',
				'backgroundColor'    => ( $item->type->color ) ?  $item->type->color : '#1a90d8',
				'borderColor'        => ( $item->type->color ) ?  $item->type->color : '#1a90d8',
				'source'             => $item['template_id'],
				'date'               => date( 'l jS F', $item['start_timestamp'] ),
				'time'               => date( 'ga', $item['start_timestamp'] ) . ' - ' . date( 'ga', $item['end_timestamp'] ), '9am - 10am',
				'class_type'         => $calendar_item->type['name'],
				'coach'              => $calendar_item->coach[0]->first_name . ' ' . $calendar_item->coach[0]->last_name,
				'description'       => $item['description'],
				'isRecurring'        => (int) $item['is_recurring'],
				'recurring_0'        => $recur_0,
				'recurring_1'        => $recur_1,
				'recurring_2'        => $recur_2,
				'recurring_3'        => $recur_3,
				'recurring_4'        => $recur_4,
				'recurring_5'        => $recur_5,
				'recurring_6'        => $recur_6,
				'allDay'             =>(bool) $item['is_all_day'],
				'CoachID'            =>(int) $item->coach[0]['id'],
				'CoachUUID'          => ( strlen( $item['coach_uuid'] ) ) ? $item['coach_uuid'] : '',
				'AssistantCoachID'   =>(int) $item->assistant_coach[0]['id'],
				'AssistantCoachUUID' => ( strlen( $item['assistant_coach_uuid'])) ? $item['assistant_coach_uuid'] : '',
				'LocationID'         => (int) $item['location_id'],
				'CalendarTypeID'     => (int) $item['calendar_type_id'],
				'Coach'              => $item->coach[0]->first_name . ' ' . $item->coach[0]->last_name,
				'Attendees'          => array()
			);
			$events[] = $x;
		}

		ob_start();
		// include pushpress_schedule_shortcode_folder() . '/schedule.php';
		// $output = ob_get_contents();
		//ob_end_clean();
		// return $output;
		echo $callback . '(';
		echo json_encode( $events );
		echo ');';

	  die(); // this is required to return a proper result
	}
	
}
