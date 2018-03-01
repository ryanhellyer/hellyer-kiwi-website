<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @since      1.0.0
 *
 * @package    pushpress-schedule
 * @subpackage pushpress-schedule/admin/partials
 */

$types = Pushpress_CalendarType::all();
$random_type = $types->data[0];



if ( isset( $_POST['pushpress-class-type'] ) ) {
	$class_type = esc_attr( $_POST['pushpress-class-type'] );
} else {
	$class_type = '';
}

if ( isset( $_POST['pushpress-calendar-type'] ) ) {
	$calendar_type = esc_attr( $_POST['pushpress-calendar-type'] );
} else {
	$calendar_type = '';
}

if ( isset( $_POST['pushpress-post-code'] ) ) {
	$post_code = esc_attr( $_POST['pushpress-post-code'] );
} else {
	$post_code = '';
}

if ( isset( $_POST['pushpress-coach'] ) ) {
	$coach = esc_attr( $_POST['pushpress-coach'] );
} else {
	$coach = '';
}

if ( isset( $_POST['pushpress-day'] ) ) {
	$day = esc_attr( $_POST['pushpress-day'] );
} else {
	$day = '';
}

if ( isset( $_POST['pushpress-week'] ) ) {
	$week = esc_attr( $_POST['pushpress-week'] );
} else {
	$week = '';
}

if ( isset( $_POST['pushpress-month'] ) ) {
	$month = esc_attr( $_POST['pushpress-month'] );
} else {
	$month = '';
}


?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1><?php esc_html_e( 'PushPress Schedule Admin', 'pushpress-schedule' ); ?></h1>
<p><?php esc_html_e( 'This plugin will generate and process shortcodes to allow you to display your schedule on your website', 'pushpress-schedule' ); ?>
<form method="POST" id="pushpress-schedule-shortcode-generator">
	<table>
		<tr>
			<td><label><?php esc_html_e( 'Class type', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-class-type" id="pushpress-class-type" value="<?php echo esc_attr( $class_type ); ?>" /></td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Calendar type', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-calendar-type" id="pushpress-calendar-type" value="<?php echo esc_attr( $calendar_type ); ?>" /></td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Post code', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-post-code" id="pushpress-post-code" value="<?php echo esc_attr( $post_code ); ?>" /></td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Coach', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-coach" id="pushpress-coach" value="<?php echo esc_attr( $coach ); ?>" /></td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Day', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-day" id="pushpress-day" value="<?php echo esc_attr( $day ); ?>" /></td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Week', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-week" id="pushpress-week" value="<?php echo esc_attr( $week ); ?>" /></td>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Month', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-month" id="pushpress-month" value="<?php echo esc_attr( $month ); ?>" /></td>
		</tr>
	</table>

	<input type="submit" id="pushpress-schedule-shortcode-generator-submit" class="button button-primary" value="<?php esc_attr_e( 'Submit', 'pushpress-schedule' ); ?>" />

</form>

<p style="margin: 40px 0;font-size:20px;font-family:monospace;" id="pushpress-schedule-shortcode">[pushpress_schedule]</p>

<h2><?php esc_html_e( 'Schedule', 'pushpress-schedule' ); ?></h2>
<?php

/**
 * Display whole schedule.
 *
 * Here are some code examples for how to display specific schedules.
 * 	echo pushpress_schedule_shortcode( array( 'id' => 'single_type_calendar', 'calendar_item_type' => $random_type->uuid ) );
 * 	echo pushpress_schedule_shortcode( array( 'id' => 'eventsonly_type_calendar', 'type' => 'event' ) );
 */
$args = array(
	'class_type'    => $class_type,
	'calendar_type' => $calendar_type,
	'post_code'     => $post_code,
	'coach'         => $coach,
	'day'           => $day,
	'week'          => $week,
	'month'         => $month,
);

echo pushpress_schedule_shortcode( $args );
