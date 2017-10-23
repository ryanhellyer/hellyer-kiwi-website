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





class Undiecar_Update_iRacing_Info extends SRC_Core {

	public function __construct() {

		if ( ! isset( $_GET['start'] ) ) {
			return;
		}

		if ( 'update_iracing_info' === $_GET['user_processing'] ) {

			$drivers = get_users();
			$x =  $_GET['start'];
			$end = $_GET['start'] + 10;
			while( $x <= $end ) {

				$driver = $drivers[$x];
				$driver_id = $driver->ID;

				$name = $driver->data->display_name;
				$driver_data = $this->iracing_member_info( $name );
				echo $name;

				// Add some meta keys
				$meta_keys = array(
					'oval_avg_inc',
					'oval_license',
					'oval_irating',
					'road_avg_inc',
					'road_license',
					'road_irating',
					'custid',
				);
				foreach ( $meta_keys as $meta_key ) {
					if ( isset( $driver_data[$meta_key] ) ) {
						update_user_meta(
							$driver_id,
							esc_html( $meta_key ),
							esc_html( $driver_data[$meta_key] )
						);
					}
				}

				$x++;
			}

			echo '<meta http-equiv="refresh" content="0;URL=\'' . 
				esc_url(
					home_url() . '/?user_processing=update_iracing_info&start=' . ( $end + 1 )
				)
			. '\'" />';

		}

	}

}
new Undiecar_Update_iRacing_Info;





if ( 'remove' === $_GET['user_processing'] ) {
	add_action( 'init', 'undiecar_remove_drivers' );
	function undiecar_remove_drivers() {
		require_once( ABSPATH . 'wp-admin/includes/user.php' );
		require_once( ABSPATH . 'wp-admin/includes/ms.php' );
		$drivers = array(/*
			'Henry Bennett',
			'Austin Espitee',
			'Richard Tam',
			'Andrey Efimenko',
			'Martin Kober',
			'Sergio Morresi',
			'Pebst Augusta',
			'Bill Gallacher Jr',
			'Carlos López',
			'Jeffrey Oakley',
			'Carl Barrick',
			'Vinicius Marega',
			'Daniel Wright4',*/
		);
		$all_drivers = get_users();

		foreach ( $drivers as $key => $display_name ) {

			foreach ( $all_drivers as $driver ) {
				$driver_id = $driver->ID;
				if ( $display_name === $driver->data->display_name ) {
					wp_delete_user( $driver_id );
					wpmu_delete_user( $driver_id );
					echo $display_name . "\n";
				}
			}
		}
	}
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

if ( 'season_3' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		if ( '3' === get_user_meta( $driver_id, 'season', true ) ) {
			echo $driver->data->display_name . ',';
		}

	}

	die;
}

/**
 * Get all drivers eligible for special races.
 *
 * Only special people ... http://dev-hellyer.kiwi/undycar/?user_processing=special
 *
 * All special eligible people ... http://dev-hellyer.kiwi/undycar/?user_processing=special&include_season=3
 */
if (
	'special' === $_GET['user_processing']
) {

	$count = 0;
	$drivers = get_users( array( 'number' => 1000 ) );
print_r( $drivers );die;
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		$include_season = 'randomstring';
		if ( isset( $_GET['include_season'] ) ) {
			$include_season = $_GET['include_season'];
		}


		if (
			'special' === get_user_meta( $driver_id, 'season', true )
			||
			'reserve' === get_user_meta( $driver_id, 'season', true )
			||
			'2' === get_user_meta( $driver_id, 'season', true )
			||
			$include_season === get_user_meta( $driver_id, 'season', true )
		) {

			if ( 'banned' !== get_user_meta( $driver_id, 'season', true ) ) {
				echo $driver->data->display_name . ',';
				$count++;
			}
		}

	}

	echo "\nTotal count: " . $count;
	die;
}

if ( 'reserves' === $_GET['user_processing'] ) {

	$drivers = get_users();
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		if (
			'reserve' === get_user_meta( $driver_id, 'season', true )
		) {
			echo $driver->data->display_name . ',';
		}

	}

}

if ( 'update_iracing_infoXXXXXXXXXXXXXXX' === $_GET['user_processing'] ) {

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
			$stats[$irating]['name'] = $driver->data->display_name;
			$stats[$irating]['registered_date'] = get_userdata( $driver_id )->user_registered;
		}

	}

	ksort( $stats );
	foreach ( $stats as $irating => $driver_data ) {
		echo $driver_data['name'] . ': ' . $irating . '       - ' . $driver_data['registered_date'] . "\n";
	}

	die( "\n\n".'Done :)' );
}

