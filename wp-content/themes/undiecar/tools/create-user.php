<?php

// Bail out if not processing users now
if ( ! isset( $_GET['create_user'] ) ) {
	return;
}

		echo $password = md5( 'Ivano Spigariol'  . 'ryanhellyer' );die;


add_action( 'init', 'undiecar_create_user' );
function undiecar_create_user() {
	$new_drivers = $existing_drivers = array();

	$drivers = explode( ',', $_GET['create_user'] );

	foreach ( $drivers as $key => $driver_name ) {

		$password = md5( $driver_name  . 'ryanhellyer' );

		// should add display name in here, not just the username
		$driver_id = wp_insert_user(
			array(
				'user_login'   => sanitize_title( $driver_name  ),
				'display_name' => esc_html( $driver_name  ),
				'user_pass'    => $password,
			)
		);

		//On success
		if ( ! is_wp_error( $driver_id ) ) {
			$new_drivers[] = $driver_name;
		} else {
			$existing_drivers[] = $driver_name;
		}

	}

	echo "New drivers:\n";
	foreach ( $new_drivers as $key => $driver_name ) {
		echo $driver_name . ',';
	}
	echo "\n\n\nExisting drivers:\n";
	foreach ( $existing_drivers as $key => $driver_name ) {
		echo $driver_name . ',';
	}

	die;
}
