<?php

// ******** SHOULD PROCESS STUFF HERE WITH $this->get_seasons_drivers()


// Only allow for super admins
if ( ! is_super_admin() ) {
	return;
}

// Bail out if not processing users now
if ( ! isset( $_GET['user_processing'] ) ) {
	return;
}



/**
 * Update information from iRacing, including iRating.
 */
class Undiecar_Update_iRacing_Info extends SRC_Core {

	public function __construct() {

		if ( ! isset( $_GET['start'] ) ) {
			return;
		}

		if ( 'update_iracing_info' === $_GET['user_processing'] ) {

			$drivers = get_users( array( 'number' => 1000 ) );
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


/*

if ( 'remove' === $_GET['user_processing'] ) {
	add_action( 'init', 'undiecar_remove_drivers' );
	function undiecar_remove_drivers() {
		require_once( ABSPATH . 'wp-admin/includes/user.php' );
		require_once( ABSPATH . 'wp-admin/includes/ms.php' );
		$drivers = array(
			'zzz',
			'xxx',
		);
		$all_drivers = get_users( array( 'number' => 1000 ) );

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

if ( 'season_1' === $_GET['user_processing'] ) {
	require_once( ABSPATH . 'wp-admin/includes/user.php' );
	require_once( ABSPATH . 'wp-admin/includes/ms.php' );

	$drivers = get_users( array( 'number' => 1000 ) );
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		if ( '1' === get_user_meta( $driver_id, 'season', true ) ) {
			$user = get_user_by( 'ID', $driver_id );
			wp_delete_user( $driver_id );
			wpmu_delete_user( $driver_id );
		}
	}
	die('done');
}
*/

if ( 'season_4' === $_GET['user_processing'] ) {

	$drivers = get_users( array( 'number' => 1000 ) );
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		if (
			'4' === get_user_meta( $driver_id, 'season', true )
			&&
			'no' !== get_user_meta( $driver_id, 'receive_notifications', true )
		) {
			echo $driver->data->display_name . ',';
		}

	}

	die;
}

if ( 'season_5' === $_GET['user_processing'] ) {

	$drivers = get_users(
		array(
			'number'     => 1000,
			'meta_key'   => 'season',
			'meta_value' => '5',
		)
	);
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		if (
			'no' !== get_user_meta( $driver_id, 'receive_notifications', true )
		) {
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
if ( 'special' === $_GET['user_processing'] ) {

	$count = 0;
	$drivers = get_users(
		array(
			'number' => 1000,
/*
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key'     => 'season',
					'value'   => 'special',
					'compare' => '=',
				),
				array(
		'relation' => 'AND',
							array(
						'key' => 'season',
						'value' => 'reserve',
						'compare' => '=',
					),
					array(
						'key'     => 'receive_notifications',
						'value'   => 'yes',
						'compare' => '=',
					),
				),
			)
*/
		)
	);
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->data->ID;

		if (
			(
				'special' === get_user_meta( $driver_id, 'season', true )
				||
				'reserve' === get_user_meta( $driver_id, 'season', true )
				||
				'' == get_user_meta( $driver_id, 'season', true )
			)
			&&
			'no' !== get_user_meta( $driver_id, 'receive_notifications', true )
		) {
			echo $driver->data->display_name . ',';
			$count++;
		}
	}

	echo "\nTotal count: " . $count;
	die;
}


if (
	'list_by_irating' === $_GET['user_processing']
	||
	'list_by_irating_reserves' === $_GET['user_processing']
) {

	$args['number'] = 1000;

	// Only show reserves
	if ( 'list_by_irating_reserves' === $_GET['user_processing'] ) {
		$args['meta_key'] = 'season';
		$args['meta_value'] = 'reserve';
	}

	$drivers = get_users( $args );

	foreach ( $drivers as $driver ) {
		$driver_id = $driver->ID;

		$road_irating = get_user_meta( $driver_id, 'road_irating', true );
		$oval_irating = get_user_meta( $driver_id, 'oval_irating', true );
		$road_license = get_user_meta( $driver_id, 'road_license', true );
		$oval_license = get_user_meta( $driver_id, 'oval_license', true );
		$total_irating = $road_irating + $oval_irating;

		$stats[$total_irating]['id'] = $driver_id;
		$stats[$total_irating]['name'] = $driver->data->display_name;
		$stats[$total_irating]['road_irating'] = $road_irating;
		$stats[$total_irating]['oval_irating'] = $oval_irating;
		$stats[$total_irating]['road_license'] = $road_license;
		$stats[$total_irating]['oval_license'] = $oval_license;
		$stats[$total_irating]['registered_date'] = get_userdata( $driver_id )->user_registered;

	}

	ksort( $stats );
	foreach ( $stats as $total_irating => $driver_data ) {
		echo $driver_data['name'] . "\n";
		echo '	road iRating = ' . $driver_data['road_irating'] . "\n";
		echo '	oval iRating = ' . $driver_data['oval_irating'] . "\n";
		echo '	road license = ' . $driver_data['road_license'] . "\n";
		echo '	oval license = ' . $driver_data['road_license'] . "\n";
		echo '	registration = ' . $driver_data['registered_date'] . "\n";
		echo "\n\n";
	}

	die( "\n\n".'Done :)' );
}


//		$checked = get_user_meta( $member_id, 'receive_extra_communication', true );

















/**
 * List all drivers with customer ID.
 */
if ( 'customer_ids' === $_GET['user_processing'] ) {

	$count = 0;
	$drivers = get_users(
		array(
			'number' => 1000,
		)
	);
	$missed = $custids = '';
	foreach ( $drivers as $driver ) {
		$driver_id = $driver->data->ID;

		if (
			'no' !== get_user_meta( $driver_id, 'receive_notifications', true )
			&&
			'' !== get_user_meta( $driver_id, 'custid', true )
		) {

			$custids .= get_user_meta( $driver_id, 'custid', true ) . ',';

			$count++;
		} else if (
			'no' !== get_user_meta( $driver_id, 'receive_notifications', true )
			&&
			'' !== get_user_meta( $driver_id, 'custid', true )
		) {
			$missed .= $driver->data->display_name . "\n";
		}

	}

	echo $custids . "\n\n\MISSED:\n\n" . $missed;

	echo "\nTotal count: " . $count;
	die;
}
