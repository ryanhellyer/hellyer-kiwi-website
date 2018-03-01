<?php


function pushpress_schedule_shortcode_folder() { 
	return plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes';
}

function pushpress_schedule_shortcode( $atts = array() ) {

	$args = array();

	//month / week / day
	if ( ! isset( $atts['view_by'] ) ) { 
		$args['view_by'] = 'month';
	}

	if ( ! isset( $atts['view_by'] ) ) { 
		$args['view_by'] = 'month';
	}



	if ( ! isset( $atts['id'] ) ) { 
		$atts['id'] = 'pushpress-calendar';
	}

	if ( isset( $atts['type'] ) && '' !== $atts['type'] ) {
		$args['type'] = $atts['type'];
	}
	if ( isset( $atts['class_type'] ) && '' !== $atts['class_type'] ) {
		$args['class_type'] = $atts['class_type'];
	}
	if ( isset( $atts['calendar_type'] ) && '' !== $atts['calendar_type'] ) {
		$args['calendar_type'] = $atts['calendar_type'];
	}
	if ( isset( $atts['post_code'] ) && '' !== $atts['post_code'] ) {
		$args['post_code'] = $atts['post_code'];
	}
	if ( isset( $atts['coach'] ) && '' !== $atts['coach'] ) {
		$args['coach'] = $atts['coach'];
	}
	if ( isset( $atts['month'] ) && '' !== $atts['month'] ) {
		$args['month'] = $atts['month'];
	}
	if ( isset( $atts['week'] ) && '' !== $atts['week'] ) {
		$args['week'] = $atts['week'];
	}
	if ( isset( $atts['day'] ) && '' !== $atts['day'] ) {
		$args['day'] = $atts['day'];
	}

	$args['start_time'] = strtotime( 'midnight last sunday' );
	$args['end_time'] = strtotime( 'midnight sunday' );

	//$cache_key = "CALENDAR_" . $args['type'] . $args['calendar_type_id'] . $args['start_time'] . 
	
	//$calendar_items = Pushpress_Calendar::all($args);        

	$events = array();

	/*
	foreach ($calendar_items->data as $item) { 

		$recur_days = array_filter(explode(",", trim($item['recurring_day_of_week'])));
		$recur_0 = in_array(0, $recur_days);
		$recur_1 = in_array(1, $recur_days);
		$recur_2 = in_array(2, $recur_days);
		$recur_3 = in_array(3, $recur_days);
		$recur_4 = in_array(4, $recur_days);
		$recur_5 = in_array(5, $recur_days);
		$recur_6 = in_array(6, $recur_days);

		$x = array(
			"uuid" => $item['uuid'],
			"timezone" => date("e", $item['start_timestamp']),
			"title"=>$item['title'],
			"start"=> date("c", $item['start_timestamp']),
			"end"=> date("c",$item['end_timestamp']),
			"textColor" => "#ffffff",
			"backgroundColor" => ($item->type->color) ?  $item->type->color : "#1a90d8",
			"borderColor" => ($item->type->color) ?  $item->type->color : "#1a90d8",
			"source"=>$item['template_id'],
			"description"=>$item['description'],
			"isRecurring" => (int)$item['is_recurring'],
			"recurring_0" => $recur_0,
			"recurring_1" => $recur_1,
			"recurring_2" => $recur_2,
			"recurring_3" => $recur_3,
			"recurring_4" => $recur_4,
			"recurring_5" => $recur_5,
			"recurring_6" => $recur_6,
			"allDay"=>(bool)$item['is_all_day'],
			"CoachID"=>(int)$item['coach_id'],
			"CoachUUID" => (strlen($item['coach_uuid'])) ? $item['coach_uuid'] : '',
			"AssistantCoachID"=>(int)$item['assistant_coach_id'],
			"AssistantCoachUUID" => (strlen($item['assistant_coach_uuid'])) ? $item['assistant_coach_uuid'] : '',
			"LocationID" => (int) $item['location_id'],
			"CalendarTypeID" => (int) $item['calendar_type_id'],
			"Coach" => $item['first_name'] . ' ' . $item['last_name'],

			"Attendees" => array()
		);
		$events[] = $x;     
	}
	*/

	ob_start();
	include pushpress_schedule_shortcode_folder() . '/schedule.php';
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}
add_shortcode( 'pushpress-schedule', 'pushpress_schedule_shortcode' );
