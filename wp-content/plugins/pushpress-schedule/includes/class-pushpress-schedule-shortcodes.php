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
	if ( isset( $atts['length'] ) && '' !== $atts['length'] ) {
		$args['length'] = $atts['length'];
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

	ob_start();
	include pushpress_schedule_shortcode_folder() . '/schedule.php';
	$output = ob_get_contents();
	ob_end_clean();

	return $output;
}
add_shortcode( 'pushpress-schedule', 'pushpress_schedule_shortcode' );
