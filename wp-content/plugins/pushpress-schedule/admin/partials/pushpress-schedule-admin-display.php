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

$coaches = Pushpress_Customer::all(
	array(
		'is_staff' => 1,
		'active'   => 1,
		'deleted'  => 0,
		'order_by' => 'first_name'
	)
);
$possible_coaches = array();
foreach ( $coaches->data as $key => $coach ) {
	$first_name = $coach->first_name;
	$last_name  = $coach->last_name;

	$possible_coaches[] = $first_name . ' ' . $last_name;
}


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

if ( isset( $_POST['pushpress-length'] ) ) {
	$length = esc_attr( $_POST['pushpress-length'] );
} else {
	$length = 'Month';
}


?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<h1><?php esc_html_e( 'PushPress Schedule Admin', 'pushpress-schedule' ); ?></h1>
<p><?php esc_html_e( 'This plugin will generate and process shortcodes to allow you to display your schedule on your website', 'pushpress-schedule' ); ?>
<form method="POST" id="pushpress-schedule-shortcode-generator">
	<table>
		<tr>
			<td><label><?php esc_html_e( 'Class type', 'pushpress-schedule' ); ?></label></td>
			<td><select name="pushpress-class-type" id="pushpress-class-type">
				<option <?php selected( $class_type, '' ); ?> value=""><?php esc_html_e( 'Default', 'pushpress-schedule' ); ?></option>
				<option <?php selected( $class_type, 'Class' ); ?> value="Class"><?php esc_html_e( 'Class', 'pushpress-schedule' ); ?></option>
				<option <?php selected( $class_type, 'Event' ); ?> value="Event"><?php esc_html_e( 'Event', 'pushpress-schedule' ); ?></option>
			</select>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Calendar type', 'pushpress-schedule' ); ?></label></td>
			<td><select type="text" name="pushpress-calendar-type" id="pushpress-calendar-type"><?php

			echo '<option ' . selected( '', $calendar_type, false ) . ' value="">' . esc_html__( 'Default', 'pushpress-schedule' ) . '</option>';
			foreach( $types->data as $x => $data ) {
				$possible_calendar_type = $data->name;
				echo '<option ' . selected( $possible_calendar_type, $calendar_type, false ) . ' value="' . esc_attr( $possible_calendar_type ) . '">' . esc_html( $possible_calendar_type ) . '</option>';
			}

			?>
			</select></td>
		</tr>
		<!--
		<tr>
			<td><label><?php esc_html_e( 'Post code', 'pushpress-schedule' ); ?></label></td>
			<td><input type="text" name="pushpress-post-code" id="pushpress-post-code" value="<?php echo esc_attr( $post_code ); ?>" /></td>
		</tr>
		-->
		<tr>
			<td><label><?php esc_html_e( 'Coach', 'pushpress-schedule' ); ?></label></td>
			<td><select type="text" name="pushpress-coach" id="pushpress-coach"><?php

			echo '<option ' . selected( '', $coach, false ) . ' value="">' . esc_html__( 'Default', 'pushpress-schedule' ) . '</option>';
			foreach( $possible_coaches as $x => $coach_name ) {
				echo '<option ' . selected( $coach_name, $coach, false ) . ' value="' . esc_attr( $coach_name ) . '">' . esc_html( $coach_name ) . '</option>';
			}

			?>
			</select>
		</tr>
		<tr>
			<td><label><?php esc_html_e( 'Length', 'pushpress-schedule' ); ?></label></td>
			<td><select name="pushpress-length" id="pushpress-length">
				<option <?php selected( $length, '' ); ?> value=""><?php esc_html_e( 'Default', 'pushpress-schedule' ); ?></option>
				<option <?php selected( $length, 'day' ); ?> value="day"><?php esc_html_e( 'Day', 'pushpress-schedule' ); ?></option>
				<option <?php selected( $length, 'week' ); ?> value="week"><?php esc_html_e( 'Week', 'pushpress-schedule' ); ?></option>
				<option <?php selected( $length, 'month' ); ?> value="month"><?php esc_html_e( 'Month', 'pushpress-schedule' ); ?></option>
			</select></td>
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
	'length'        => $length,
);

echo pushpress_schedule_shortcode( $args );
