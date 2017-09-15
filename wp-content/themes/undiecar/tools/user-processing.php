<?php

// ******** update_user_meta( $member_id, 'receive_extra_communication', $receive_extra_communication ); HANDLE EMAIL COMMUNICATION CHECK

// ******** SHOULD PROCESS STUFF HERE WITH $this->get_seasons_drivers()


// Only allow for super admins
if ( ! is_super_admin() ) {
	return;
}

// Bail out if not processing users now
if ( ! isset( $_GET['user_processing'] ) ) {
	return;
}






if ( 'process' === $_GET['user_processing'] ) {
	return;

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;
		echo $driver_id . "\n";

		if ( 'Ryan' !== $driver->data->display_name ) {
//			$password = md5( $driver->data->display_name );
//			wp_set_password( $password, $driver_id );
//			echo $driver_id . "\n";
		}


		$season = get_user_meta( $driver_id, 'season', true );
		if ( '2' !== $season && strpos( $driver->user_email, '@me.com' ) === false ) {
			update_user_meta( $driver_id, 'season', 'reserve' );
		}

		//if ( strpos( $driver->user_email, '@me.com' ) !== false ) {
		//	update_user_meta( $driver_id, 'season', '1' );
		//}

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

// https://undiecar.com/?user_processing=special
if ( 'special' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		// Ignore season 1 drivers who haven't set their password (means they never intended to register for the site)
		if (
			'banned' !== get_user_meta( $driver_id, 'season' )
			&&
			'2' !== get_user_meta( $driver_id, 'season' )
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

if ( 'update_iracing_info' === $_GET['user_processing'] ) {

	$dir = wp_upload_dir();

	$stats = file_get_contents( $dir['basedir'] . '/iracing-members.json' );
	$stats = json_decode( $stats, true );

	// If user exists in iRacing, then return their stats, otherwise return false

	$meta_keys = array(
		'oval_irating',
		'oval_license',
		'oval_avg_inc',
		'road_irating',
		'road_license',
		'road_avg_inc',
		'custid',
	);


	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;
		$display_name = $driver->data->display_name;

		if ( isset( $stats[$display_name] ) ) {

			print_r( $stats[$display_name] );
			foreach ( $meta_keys as $key => $meta_key ) {

				if ( isset( $stats[$display_name][$meta_key] ) ) {
					update_user_meta( $driver_id, $meta_key, $stats[$display_name][$meta_key] );
				}

			}

		}

	}

	die( 'All iRacing meta data updated :)' );
}

if ( 'list_by_road_irating' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;
		$irating = get_user_meta( $driver_id, 'road_irating', true );

		if ( '1' !== get_user_meta( $driver_id, 'season', true ) ) {
			$stats[$irating] = $driver->data->display_name;
		}

	}

	ksort( $stats );
	foreach ( $stats as $irating => $driver_name ) {
		echo $driver_name . ': ' . $irating . "\n";
	}

	die( "\n\n".'Done :)' );
}


die( "\n\nDONE" );