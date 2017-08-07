<?php

/**
 * WP Cron tasks.
 */
class SRC_Cron extends SRC_Core {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_filter( 'cron_schedules', array( $this, 'every_minute' ) );
		add_action( 'after_switch_theme', array( $this, 'schedule_cron' ) );
		add_action( 'src_per_minute_event', array( $this, 'create_draft_race_report' ) );

if(isset($_GET['test']) && 'create'===$_GET['test']){add_action('template_redirect',array($this,'create_draft_race_report'));}
if(isset($_GET['test']) && 'process'===$_GET['test']){add_action('template_redirect',array($this,'process_draft_race_reports'));}
	}

	/**
	 * Add once per minute interval to wp schedules
	 *
	 * @param   array  $interval   The schedule interval settings
	 * @return  array  $interval   The schedule interval settings
	 */
	public function every_minute( $interval ) {

		$interval['once_per_minute'] = array(
			'interval' => 60, // 60 seconds
			'display'  => __( 'Once per minute', 'src' ),
		);

		return $interval;
	}

	/**
	 * On activation, set a time, frequency and name of an action hook to be scheduled.
	 */
	public function schedule_cron() {
		wp_schedule_event( time(), 'once_per_minute', 'src_per_minute_event' );
	}

	public function get_post_by_post_name( $slug = '', $post_type = '' ) {
		//Make sure that we have values set for $slug and $post_type
		if ( ! $slug || ! $post_type ) {
			return false;
		}

		// We will not sanitize the input as get_page_by_path() will handle that
		$post_object = get_page_by_path( $slug, OBJECT, $post_type );

		if ( ! $post_object ) {
		 return false;
		}

		return $post_object;
	}

	/**
	 * Create a draft race report.
	 * Reports are generated in separate WP Cron task, as they may stack one on top of another.
	 */
	public function create_draft_race_report() {

		$most_recent_event = src_get_most_recent_event();

		// Bail out if no recent event set
		if ( ! isset( $most_recent_event['name'] ) || ! isset( $most_recent_event['season'] ) ) {
			return;
		}

		// 
		$post_title = sprintf( __( 'Report: %s. Season %s.', 'src' ), $most_recent_event['name'], $most_recent_event['season'] );
		$post_name = sanitize_title( sprintf( __( 'report-%s-%s', 'src' ), $most_recent_event['season'], $most_recent_event['name'] ) );

		// If no report post found for this, then generate a new one
		$report_post = $this->get_post_by_post_name( $post_name, 'post' );

		if ( false === $report_post ) {

			// Insert the post into the database
			$args = array(
				'post_title'    => wp_strip_all_tags( wp_kses_post( $post_title ) ),
				'post_name'     => $post_name,
				'post_status'   => 'draft',
				'post_author'   => get_option( 'gertrudes_id' ),
			);
			$post_id = wp_insert_post( $args );
			update_post_meta( $post_id, 'src-report', 'processing' );
			update_post_meta( $post_id, 'src-name', $most_recent_event['name'] );
			update_post_meta( $post_id, 'src-season', $most_recent_event['season'] );

		}

	}

	/**
	 * Process draft race reports.
	 */
	public function process_draft_race_reports() {

		$args = array(
			'post_type'              => 'post',
			'posts_per_page'         => 10,
			'post_status'            => array( 'any' ),
			'meta_query' => array(
				array(
					'key'     => 'src-report',
					'value'   => array( 'processing' ),
					'compare' => 'IN',
				),
			),
			'no_found_rows'          => true,  // useful when pagination is not needed.
			'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids',
		);

		$posts = new WP_Query( $args );
		if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) {
				$posts->the_post();

				$name = get_post_meta( get_the_ID(), 'src-name', true );
				$season_slug = get_post_meta( get_the_ID(), 'src-season', true );

				// Get events 
				$events = src_get_events( $season_slug );

				// Create arrays for old events and all events that have been held
				foreach ( $events as $key => $event ) {

					if ( isset( $event['event_race-3_timestamp'] ) && is_numeric( $event['event_race-3_timestamp'] ) ) {
						$time_stamp = $event['event_race-3_timestamp'];
						$ordered_events[$time_stamp] = $event;
					}

					if ( isset( $event['event_race-2_timestamp'] ) && is_numeric( $event['event_race-2_timestamp'] ) ) {
						$time_stamp = $event['event_race-2_timestamp'];
						$ordered_events[$time_stamp] = $event;
					}

					if ( isset( $event['event_race-1_timestamp'] ) && is_numeric( $event['event_race-1_timestamp'] ) ) {
						$time_stamp = $event['event_race-1_timestamp'];
						$ordered_events[$time_stamp] = $event;
					}

				}
				ksort( $ordered_events );
				$prev_events = $all_held_events = array();
				$reached = false;
				$total_rounds_held_so_far = $total_races_held_so_far = 0;
				foreach ( $ordered_events as $key => $event ) {

					if ( ( $name === $event['name'] || $name === $event['track_name'] ) ) {
						$all_held_events[$key] = $event;
						$reached = true;
						$total_races_held_so_far++;

					} else if ( true !== $reached ) {
						$prev_events[$key] = $all_held_events[$key] = $event;
						$total_races_held_so_far++;
					}

					if (
						$name === $event['name']
						||
						$name === $event['track_name']
						||
						true !== $reached
					) {

						//
						if ( ! isset( $held_round[ $event['name'] ] ) ) {
							$total_rounds_held_so_far++;
						}

						$held_round[ $event['name'] ] = true;

					}

				}

/*
				foreach ( $all_held_events as $key => $event ) {
print_r( $event );echo "\n............\n";

// Setting total rounds held so far
$total_rounds_held_so_far++;

// Setting total races held so far
foreach ( array( 1, 2, 3 ) as $x ) {
	if ( isset( $event['event_race-' . $x . '_timestamp'] ) && '' !== $event['event_race-' . $x . '_timestamp'] ) {
		$total_races_held_so_far++;
//		echo $event['track_name'] . ' ... ' . $total_rounds_held_so_far . ' - ' . $total_races_held_so_far . ': ' . print_r( $event['event_race-' . $x . '_timestamp'], true ) . "\n.............\n";
	}
}

				}
die;
*/


				// Get how many races at last round, and round before last
				$rounds['current'] = array_values( array_slice( $all_held_events, -1 ) ) [0];
				$rounds['last'] = array_values( array_slice( $prev_events, -1 ) ) [0];

				foreach ( $rounds as $key => $round ) {

					$count = 1;
					foreach ( array( 1, 2, 3 ) as $x ) {

						if ( isset( $round['event_race-' . $x . '_timestamp'] ) && '' !== $round['event_race-' . $x . '_timestamp'] ) {
							$race_counts[$key] = $count;
							$count++;
						}
					}

				}

				// Get points as of last round and current round (so we can compare them later)
				$results_as_of_last_round = $results_current = array();
				$last_round_points_total = $current_points_total = 0;
				$raw_results = src_get_results( $season_slug );
				$results = array();
				foreach ( $raw_results as $driver => $driver_data ) {
					$last_round_points_total = $current_points_total = 0;

					foreach ( $driver_data as $x => $points ) {

						if ( $x <= count( $prev_events ) ) {
							$last_round_points_total = $last_round_points_total + $points;
							if ( 0 !== $last_round_points_total ) {
								$results_as_of_last_round[$driver] = $last_round_points_total;
							}
						}

						if ( $x <= count( $all_held_events ) ) {
							$current_points_total = $current_points_total + $points;
							if ( 0 !== $current_points_total ) {
								$results_current[$driver] = $current_points_total;
							}

							// List how many DNF's the driver has had so far this season
							if ( 'DNF' === $points ) {
								$results[$driver]['total_dnfs'] = 1;
								if ( isset( $results[$driver]['total_dnfs'] ) ) {
									$results[$driver]['total_dnfs'] = $results[$driver]['total_dnfs'] + 1;
								}
							}

						}

//print_r( $race_counts ); echo "\n\n";
//$x = array_slice( $driver_data, - 2, 2, true );
//print_r( $x );
//die;



$results_from_current_race = array_slice( $driver_data, - $race_counts['current'], $race_counts['current'], true );
$results[$driver]['current-results'] = 'WRONG';//$results_from_current_race;
// Need to convert points into race positions

					}

				}
				arsort( $results_as_of_last_round );
				arsort( $results_current );

				// Get previous event
				$sliced = array_slice($all_held_events, -1 );
				$current_event = array_pop( $sliced );

				// Get current event
				$sliced = array_slice($prev_events, -1 );
				$prev_event = array_pop( $sliced );

				// 
				$current_position = 0;
				foreach ( $results_current as $driver => $points ) {
					$current_position++;

					// Set total points
					$results[$driver]['points'] = $points;

					// Set total previous points
					if ( isset( $results_as_of_last_round[$driver] ) ) {
						$results[$driver]['previous_points'] = $results_as_of_last_round[$driver];
					} else {
						$results[$driver]['previous_points'] = 0;
					}

					// Set points change
					if ( isset( $results_as_of_last_round[$driver] ) ) {
						$results[$driver]['points_change'] = $points - $results_as_of_last_round[$driver];
					} else {
						$results[$driver]['points_change'] = $points;
					}

					// Set position change
					$last_position = 0;
					$results[$driver]['position_change'] = 'first-points-of-season';
					foreach ( $results_as_of_last_round as $driver2 => $points_last_round ) {
						$last_position++;

						if ( $driver === $driver2 ) {
							$position_change = $last_position - $current_position;
							$results[$driver]['position_change'] = $position_change;
						}

					}

					// Previous points difference to driver ahead
					foreach ( $results_as_of_last_round as $driver2 => $points2 ) {

						if ( $driver2 === $driver ) {
							break;
						}

						$points3 = $points2;
					}
					if ( isset( $points_diff_to_driver_ahead ) && $points3 !== $points2 ) {
						$results[$driver]['prev_points_diff_to_driver_ahead'] = $points3 - $points2;
					}

					// Points difference to driver ahead
					foreach ( $results_current as $driver2 => $points2 ) {

						if ( $driver2 === $driver ) {
							break;
						}

						$points_diff_to_driver_ahead = $points2 - $points;
					}
					if ( isset( $points_diff_to_driver_ahead ) ) {
						$results[$driver]['points_diff_to_driver_ahead'] = $points_diff_to_driver_ahead;
					}

					// Set change in points diff to driver ahead
					if ( isset( $results[$driver]['prev_points_diff_to_driver_ahead'] ) && isset( $results[$driver]['points_diff_to_driver_ahead'] ) ) {
						$results[$driver]['change_in_points_to_driver_ahead'] = $results[$driver]['points_diff_to_driver_ahead'] - $results[$driver]['prev_points_diff_to_driver_ahead'];
					}

					// Number of races at current event
					$number_of_races_at_current_event = 0;
					if ( '' !== $current_event['event_race-1_timestamp'] ) {
						$number_of_races_at_current_event++;
					}
					if ( '' !== $current_event['event_race-2_timestamp'] ) {
						$number_of_races_at_current_event++;
					}
					if ( '' !== $current_event['event_race-3_timestamp'] ) {
						$number_of_races_at_current_event++;
					}

					// Number of races at previous event
					$number_of_races_at_prev_event = 0;
					if ( '' !== $prev_event['event_race-1_timestamp'] ) {
						$number_of_races_at_prev_event++;
					}
					if ( '' !== $prev_event['event_race-2_timestamp'] ) {
						$number_of_races_at_prev_event++;
					}
					if ( '' !== $prev_event['event_race-3_timestamp'] ) {
						$number_of_races_at_prev_event++;
					}

				}

echo 'Total rounds held so far: ' . $total_rounds_held_so_far . "\n";
echo 'Total races held so far: ' . $total_races_held_so_far . "\n\n";

echo 'How many races in current and previous round: ' . "\n";
print_r( $race_counts );
echo "\n\n";

echo "Needed:
Position in race 1
Position in race 2
Position in race 3

Position in race 1 at last event
Position in race 2 at last event
Position in race 3 at last event
";

print_r( $results );
echo "\n___________________\nPoints from before most recent round:\n";
print_r( $results_as_of_last_round );
echo "\n___________________\nPoints from most recent round:\n";
print_r( $results_current );
die;

				//function src_get_interesting_info_from_round();

				// Get four random drivers to send emails to

				// Add post content

				// 

			}
		}

	}

}
