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

				/*
				if ( 'Renzo A. Olivieri' === $_GET['user_processing'] ) {
					$driver_id = 1777;
					$driver_data = $this->iracing_member_info( 'Renzo A. Olivieri' );
					echo $name . ', ';

					// Add some meta keys
					$meta_keys = array(
						'club',
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

					die('done');
				}
				*/


		if ( ! isset( $_GET['start'] ) ) {
			return;
		}

		if ( 'update_iracing_info' === $_GET['user_processing'] ) {
			set_time_limit( 600 );

			// Cache the drivers list coz it takes stupid long to access them all
			$key = 'undiecar_drivers_temp_list';
			if ( false === ( $drivers = get_transient( $key ) ) ) {
				$drivers = get_users( array( 'number' => 2000 ) );
				set_transient( $key, $drivers, HOUR_IN_SECONDS );
			}

			$x   =  $_GET['start'];
			$end = $_GET['start'] + 1;
			while( $x <= $end ) {
				$driver    = $drivers[$x];
				$driver_id = $driver->ID;

				$x++;

				$name = $driver->data->display_name;

				$driver_data = $this->iracing_member_info( $name );

				echo $name . ', ';
				echo '<br />custid: ' . $driver_data['custid'] . '<br />';
				// Add some meta keys
				$meta_keys = array(
					'club',
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

			}

			echo '<meta http-equiv="refresh" content="0;URL=\'' . 
				esc_url(
					home_url() . '/?user_processing=update_iracing_info&start=' . ( $end + 1 )
				)
			. '\'" />';
			echo '<br><br><strong>Redirecting</strong>';
			die;

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
		$all_drivers = get_users( array( 'number' => 2000 ) );

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

	$drivers = get_users( array( 'number' => 2000 ) );
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


if (
	'list_by_irating' === $_GET['user_processing']
	||
	'list_by_irating_reserves' === $_GET['user_processing']
) {

	$args['number'] = 2000;

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


/**
 * List all drivers with customer ID.
 */
if ( 'customer_ids' === $_GET['user_processing'] ) {

	$count = $count_missed = 0;
	$drivers = get_users(
		array(
			'number' => 2000,
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
$leagues['undie_lights'] = true;
update_user_meta( $driver_id, '_add_to_league', $leagues ) . ',';

			$count++;
		} else if (
			'no' !== get_user_meta( $driver_id, 'receive_notifications', true )
			&&
			'' === get_user_meta( $driver_id, 'custid', true )
		) {
			$missed .= $driver->data->display_name . "\n";

			$count_missed++;
		}

	}

	echo $custids . "\n\n\MISSED:\n\n" . $missed;

	echo "\nTotal count: " . $count . "\n";
	echo "\nMiss count: " . $count_missed;
	die;
}



// https://undiecar.com/?user_processing=requested&season=undiecar&compare=6
if ( 'requested' === $_GET['user_processing'] ) {

	$compare = get_posts(
		array(
			'name' => $_GET['compare'],
			'post_type' => 'season',
		)
	);
	$id = $compare[0]->ID;

	$compare_drivers = get_post_meta( $id, 'drivers', true );
	$compare_drivers = explode( "\n", $compare_drivers );

	// no idea why this is necessary, but strange extra character is present at end of names
	foreach ( $compare_drivers as $x => $compare_driver ) {
		$compare_drivers[$x] = substr( $compare_driver, 0, -1 );
	}

	$drivers = get_users(
		array(
			'number' => 2000,
//			'meta_key'     => 'season',
//			'meta_value'   => $_GET['season'],

		)
	);

	foreach ( $drivers as $driver ) {
		$name = $driver->display_name;

		$seasons = get_user_meta( $driver->ID, 'season', true );
		if ( is_array( $seasons ) ) {
			foreach ( $seasons as $season => $x ) {

				if ( $_GET['season'] === $season ) {

					if ( ! in_array( $name, $compare_drivers ) ) {
						echo $name . ',';
					}

				}

			}
		}

	}

	die;
}
