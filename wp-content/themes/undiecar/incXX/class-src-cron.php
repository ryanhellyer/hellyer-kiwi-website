<?php

/**
 * WP Cron tasks.
 */
class SRC_Cron extends SRC_Core {

	/**
	 * Class constructor
	 */
	public function __construct() {
//add_action( 'init', array( $this, 'download_iracing_members_files' ), 1 );
//add_action( 'init', array( $this, 'convert_iracing_members_file_to_json' ), 1 );
		add_action( 'after_switch_theme', array( $this, 'schedule_crons' ) );
	}

	/**
	 * On activation, set a time, frequency and name of an action hook to be scheduled.
	 */
	public function schedule_cron() {
		wp_schedule_event( time(), 'daily', 'download_iracing_members_files' );
		wp_schedule_event( time(), 'daily', 'convert_iracing_members_file_to_json' );
	}

	/**
	 * Convert raw iRacing member file to JSON format.
	 */
	public function download_iracing_members_files() {
		$dir = wp_upload_dir();

		$oval_stats = file_get_contents( 'https://s3.amazonaws.com/ir-data-now/csv/Oval_driver_stats.csv' );
		$oval_file_path = $dir['basedir'] . '/Oval_driver_stats.csv';
		file_put_contents( $oval_file_path, $oval_stats );
		unset( $oval_stats );

		$road_stats = file_get_contents( 'https://s3.amazonaws.com/ir-data-now/csv/Road_driver_stats.csv' );
		$road_file_path = $dir['basedir'] . '/Road_driver_stats.csv';
		file_put_contents( $road_file_path, $road_stats );
		unset( $road_stats );
	}

	/**
	 * Convert raw iRacing member file to JSON format.
	 */
	public function convert_iracing_members_file_to_json() {
		$dir = wp_upload_dir();

		// Get raw file paths
		$oval_file_path = $dir['basedir'] . '/Oval_driver_stats.csv';
		$road_file_path = $dir['basedir'] . '/Road_driver_stats.csv';

		// Get file contents
		$stats['oval'] = file_get_contents( $oval_file_path );
		$stats['road'] = file_get_contents( $road_file_path );

		// Loop through each type of racing individually
		$new_stats = array();
		foreach ( $stats as $type => $x ) {

			$stats[$type] = explode( "\n", $stats[$type] );

			// Loop through each drivers stats individually
			unset( $stats[$type][0] );
			$stat_count = count( $stats[$type] );
			//$stat_count = 2;
			foreach ( $stats[$type] as $key => $values ) {
				$values = str_replace( '"', '', $values );

				$values = explode( ',', $values );

				// Get drivers name for array key
				$drivers_name = esc_html( $values[0] );

				// Creating individual drivers data array
				$data = array();

				if ( isset( $values[1] ) ) {
					$data['custid'] = $values[1];
				}
				if ( isset( $values[2] ) ) {
					$data['location'] = $values[2];
				}
				if ( isset( $values[12] ) ) {
					$data[$type . '_avg_inc'] = $values[12];
				}
				if ( isset( $values[13] ) ) {
					$data[$type . '_license'] = substr( $values[13], 0, 1 );
				}
				if ( isset( $values[14] ) ) {
					$data[$type . '_irating'] = $values[14];
				}

				// Sanitizing inputs
				foreach ( $data as $x => $d ) {
					$x = mb_strimwidth ( esc_html( $x ), 0 , 100 );
					$d = mb_strimwidth ( esc_html( $d ), 0 , 30 );
					$data[$x] = $d;
				}

				// Seems to prevent it running out of memory, not sure why
				if ( $key > $stat_count ) {
					continue;
				}

				// Create array with alphabetical key
				$new_stats[$type][$drivers_name] = $data;

			}

			unset( $stats[$type] );

		}

		$new_stats = array_replace_recursive( $new_stats['oval'], $new_stats['road'] );

		file_put_contents( $dir['basedir'] . '/iracing-members.json', json_encode( $new_stats, JSON_UNESCAPED_UNICODE ) );
		unset( $new_stats );
	}

}
