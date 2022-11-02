<?php

if ( ! isset( $_GET['autounsubrscribe'] ) ) {
	return;
}


// Get all events from that season
$query = new WP_Query( array(
	'posts_per_page'         => 12,
	'post_type'              => 'message',
	'post_status'            => 'publish',
	'no_found_rows'          => true,
	'update_post_meta_cache' => false,
	'update_post_term_cache' => false,
) );
$drivers_who_opened_message = array();
if ( $query->have_posts() ) {
	while ( $query->have_posts() ) {
		$query->the_post();

		$log  = get_post_meta( get_the_ID(), 'log', true );

		$count = 0;
		if ( is_array( $log ) ) {
			$count = count( $log );

			foreach ( $log as $driver_name => $x ) {
				$drivers_who_opened_message[] = $driver_name;
			}

		}

		echo esc_html( get_the_title() . ' (#' . get_the_ID() . ')' ) . ': ' . esc_html( $count ) . " people read reminder message\n";
	}
}
$drivers_who_opened_message = array_unique( $drivers_who_opened_message );
$drivers_who_opened_message = array_values( $drivers_who_opened_message );

function undiecar_check_if_opened_message( $driver_to_check, $drivers_who_opened_message ) {
	foreach ( $drivers_who_opened_message as $driver ) {
		if ( $driver_to_check === $driver ) {
			return true;
		}
	}

	return false;
}

$drivers = get_users( array( 'number' => 2000 ) );
$disabled = array();
foreach ( $drivers as $key => $driver ) {
	$driver_id   = $driver->ID;
	$driver_name = $driver->data->display_name;

	$time_three_months_ago = time() - ( 3 * MONTH_IN_SECONDS );
	$registration_time     = $driver->data->user_registered;

	if ( $registration_time < $time_three_months_ago ) { // Don't bother if user registered less than three months ago.

		if (
			false === undiecar_check_if_opened_message( $driver_name, $drivers_who_opened_message ) // If haven't opened message reently ... 
			&&
			'no' !== get_user_meta( $driver_id, 'receive_notifications', true ) // Don't bother if notifications turned off anyway.
			&&
			'yes' !== get_user_meta( $driver_id, 'receive_less_notifications', true ) // DOn't bother if already showing less notifications.

		) {

			update_user_meta( $driver_id, 'receive_less_notifications', 'yes' );
			$disabled[] = $driver_name;
		}

	}
}

echo "\n\n";
print_r( $disabled );

die;
