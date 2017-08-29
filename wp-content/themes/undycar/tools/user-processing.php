<?php

//echo md5( 'Ty Majeski' );die;

//update_user_meta( 586, 'season', '2' );

//echo md5( 'Evan Fitzgerald' );die;
//203752a9c3f0adbc77103a5516821858

// Bail out if not processing users now
if ( ! isset( $_GET['user_processing'] ) ) {
	return;
}



if ( 'process' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		if ( 'Ryan' !== $driver->data->display_name ) {
			$password = md5( $driver->data->display_name );
			wp_set_password( $password, $driver_id );
			echo $driver_id . "\n";
		}

		update_user_meta( $driver_id, 'season', 'special' );

		if ( strpos( $driver->user_email, '@me.com' ) !== false ) {
			update_user_meta( $driver_id, 'season', '1' );
		}

		foreach ( undycar_get_season_2_drivers() as $key => $driver_name ) {

			if ( $driver->data->display_name === $driver_name ) {
				update_user_meta( $driver_id, 'season', '2' );
			}

		}

	}
}

if ( 'season_2' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		if ( '2' === get_user_meta( $driver_id, 'season', true ) ) {
			echo $driver->data->display_name . ',';
		}

	}

}

if ( 'special' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		// Ignore season 1 drivers who haven't set their password (means they never intended to register for the site)
		if (
			'1' !== get_user_meta( $driver_id, 'password_set' )
			&&
			'1' === get_user_meta( $driver_id, 'season', true )
		) {
			continue;
		}

		echo $driver->data->display_name . ',';

	}

}

if ( 'reserves' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		// Ignore season 1 drivers who haven't set their password (means they never intended to register for the site)
		if (
			'1' !== get_user_meta( $driver_id, 'password_set' )
			&&
			( '1' === get_user_meta( $driver_id, 'season', true ) || '2' === get_user_meta( $driver_id, 'season', true ) )
		) {
			continue;
		}

		echo $driver->data->display_name . ',';

	}

}

die();


function undycar_get_season_2_drivers() {

	$drivers = 'Evan Fitzgerald
Ryan Hellyer
Kaue Gomes
Tim Williams
Patrick Hingston
M B Dickey2
Cristian Otarola
Lucas Stinziano2
Anthony Cothran
Craig P Kasper
Craig Shepherd
Kevin McCarthy
Patrick Langley
Adam Crapser
JW Miller
Andre Heidstra
Stephane Parent
Claudius Wied
Ramon Regalado
Steven Roberts4
Egoitz Elorz
Dirk Rommeswinkel
A J Burton
Daniel Wright4
Casey Drake
Marco A Pereira
Philippe Tortue
Art Seeger
Victor H. Santana
Richard Browell
Stuart John
Joshua S Lee
Said Gonzalez
Nikolay Ladushkin
Kleber Bottaro Moura
Javier Perez M.
Neil A. Jackson	8';

	$drivers = explode( "\n", $drivers );

	return $drivers;
}
