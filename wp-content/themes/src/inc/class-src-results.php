<?php

/**
 * Results.
 * Temporary system until final version is ready.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Results extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_shortcode( 'src-driver-standings',   array( $this, 'driver_standings' ) );
		add_shortcode( 'src-amdriver-standings', array( $this, 'amdriver_standings' ) );
		add_shortcode( 'src-team-standings',     array( $this, 'team_standings' ) );

		add_shortcode( 'src-schedule',           array( $this, 'schedule' ) );
	}

	/**
	 * Driver standings.
	 */
	public function driver_standings( $args ) {

		// Set Season
		if ( isset( $args['season'] ) ) {
			$season = $args['season'];
		} else {
			return 'No season set';
		}

		$order = array(
			'Username',
			'Nationality',
			'Car',
			'Team',
			'Class',
			'Pts',
		);

		$drivers = src_get_drivers( $season );

		$content = '';
		if ( is_array( $drivers ) ) {
			$content .= '<table>';

			$content .= '<thead><tr>';
			$content .= '<th>' . esc_html__( 'Pos', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Name', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Num', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Nationality', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Car', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Team', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Class', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Pts', 'src' ) . '</th>';
			$content .= '</tr></thead>';

			$count = 0;
			foreach ( $drivers as $row_number => $driver ) {
				$count++;
				$content .= '<tr>';

				$content .= '<td>' . esc_html( $count ) . '</td>';

				foreach( $order as $column ) {

					foreach ( $this->driver_data_order() as $key => $col ) {

						if ( $column === $col ) {

							$content .= '<td>';

							if ( 2 === $key ) {
								$content .= src_get_nationality( $username );
							} else if ( 'Username' == $col ) {
								$username = $driver[$key];
								$name = src_get_display_name_from_username( $username );
								$url = src_get_memberurl_from_username( $username );

								if ( false !== $url ) {
									$content .= '<a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a>';
								} else {
									$content .= $name;
								}

							} else {
								$content .= esc_html( $driver[$key] );
							}
							$content .= '</td>';

						}

					}
				}

				$content .= '<td>' . esc_html( src_get_driver_points( $season, $username ) ) . '</td>';

				$content .= '</tr>';
			}
			$content .= '</table>';
		}

		return $content;

	}

	/**
	 * AM Driver standings.
	 */
	public function amdriver_standings( $args ) {

		// Set Season
		if ( isset( $args['season'] ) ) {
			$season = $args['season'];
		} else {
			return 'No season set';
		}

		$order = array(
			'Username',
			'Nationality',
			'Car',
			'Team',
			'Class',
			'Pts',
		);

		$drivers = src_get_events( $season );

		$content = '';
		if ( is_array( $drivers ) ) {
			$content .= '<table>';

			$content .= '<thead><tr>';
			$content .= '<th>' . esc_html__( 'Pos', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Name', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Num', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Nationality', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Car', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Team', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Class', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Pts', 'src' ) . '</th>';
			$content .= '</tr></thead>';

			$count = 0;
			foreach ( $drivers as $row_number => $driver ) {

				// Ignore PRO drivers
				if ( 'PRO' === $driver[5] ) {
					continue;
				}

				$count++;
				$content .= '<tr>';

				$content .= '<td>' . esc_html( $count ) . '</td>';

				foreach( $order as $column ) {

					foreach ( $this->driver_data_order() as $key => $col ) {

						if ( $column === $col ) {

							$content .= '<td>';
							if ( 2 === $key ) {
								$content .= src_get_nationality( $username );
							} else if ( 'Username' == $col ) {
								$username = $driver[$key];
								$name = src_get_display_name_from_username( $username );
								$url = src_get_memberurl_from_username( $username );

								if ( false !== $url ) {
									$content .= '<a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a>';
								} else {
									$content .= $name;
								}

							} else {
								$content .= esc_html( $driver[$key] );
							}
							$content .= '</td>';

						}

					}
				}

				$content .= '<td>' . esc_html( src_get_driver_ampoints( $season, $username ) ) . '</td>';

				$content .= '</tr>';
			}
			$content .= '</table>';
		}

		return $content;

	}

	/**
	 * Team standings.
	 */
	public function team_standings( $args ) {

		// Set Season
		if ( isset( $args['season'] ) ) {
			$season_slug = $args['season'];
		} else {
			return 'No season set';
		}

		// Get order into array

		$order = array(
			'Team',
			'Pts',
		);

		$teams = src_get_teams( $season_slug );

		$content = '';
		if ( is_array( $teams ) ) {
			$content .= '<table>';

			$content .= '<thead><tr>';
			$content .= '<th>' . esc_html__( 'Pos', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Team', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Pts', 'src' ) . '</th>';
			$content .= '</tr></thead>';

			$count = 0;
			foreach ( $teams as $team => $points ) {
				$count++;

				$content .= '<tr>';
				$content .= '<td>' . esc_html( $count ) . '</td>';
				$content .= '<td>' . esc_html( $team ) . '</td>';
				$content .= '<td>' . esc_html( $points ) . '</td>';
				$content .= '</tr>';

			}
			$content .= '</table>';
		}

		return $content;
	}

	/**
	 * Schedule.
	 */
	public function schedule( $args ) {

		// Set Season
		if ( isset( $args['season'] ) ) {
			$season_slug = $args['season'];
		} else {
			return 'No season set';
		}

		// Get order into array

		$order = array(
			'Team',
			'Pts',
		);

		$events = src_get_events( $season_slug );

		$content = '';
		if ( is_array( $events ) ) {
			$content .= '<table>';

			$content .= '<thead><tr>';
			$content .= '<th>' . esc_html__( 'Num', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Event', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'FP 1', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'FP 2', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Qualifying', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Race 1', 'src' ) . '</th>';
			$content .= '<th>' . esc_html__( 'Race 2', 'src' ) . '</th>';
			$content .= '<th></th>';
			$content .= '</tr></thead>';

			$count = 0;
			foreach ( $events as $key => $event ) {
				$count++;

				if ( isset( $event['name'] ) ) {
					$name = $event['name'];
				} else if ( isset( $event['track_name'] ) ) {
					$name = $event['track_name'];
				}

				if ( isset( $event['description'] ) ) {
					$description = $event['description'];
				}

				if ( isset( $event['event_fp1_timestamp'] ) && '' !== $event['event_fp1_timestamp'] ) {
					$date_fp1   = date( get_option( 'date_format' ), $event['event_fp1_timestamp'] );
					$time_fp1   = date( get_option( 'time_format' ), $event['event_fp1_timestamp'] );
				} else {
					$time_fp1 = $date_fp1 = '';
				}

				if ( isset( $event['event_fp2_timestamp'] ) && '' !== $event['event_fp2_timestamp'] ) {
					$date_fp2   = date( get_option( 'date_format' ), $event['event_fp2_timestamp'] );
					$time_fp2   = date( get_option( 'time_format' ), $event['event_fp2_timestamp'] );
				} else {
					$time_fp2 = $date_fp2 = '';
				}

				if ( isset( $event['event_qualifying_timestamp'] ) && '' !== $event['event_qualifying_timestamp'] ) {
					$date_qual  = date( get_option( 'date_format' ), $event['event_qualifying_timestamp'] );
					$time_qual  = date( get_option( 'time_format' ), $event['event_qualifying_timestamp'] );
				} else {
					$time_qual = $date_qual = '';
				}

				if ( isset( $event['event_race-1_timestamp'] ) && '' !== $event['event_race-1_timestamp'] ) {
					$date_race1 = date( get_option( 'date_format' ), $event['event_race-1_timestamp'] );
					$time_race1 = date( get_option( 'time_format' ), $event['event_race-1_timestamp'] );
				} else {
					$time_race1 = $date_race1 = '';
				}

				if ( isset( $event['event_race-2_timestamp'] ) && '' !== $event['event_race-2_timestamp'] ) {
					$date_race2 = date( get_option( 'date_format' ), $event['event_race-2_timestamp'] );
					$time_race2 = date( get_option( 'time_format' ), $event['event_race-2_timestamp'] );
				} else {
					$time_race2 = $date_race2 = '';
				}

				$content .= '<tr>';
				$content .= '<td>' . esc_html( $count ) . '</td>';
				$content .= '<td>' . esc_html( $name );

				if ( isset( $description ) ) {
					$content .= ' <span>' . esc_html( $description ) . '</span>';
				}

				$content .= '</td>';

				$content .= '<td>' . esc_html( $time_fp1 ) .   ' <span>' . esc_html( $date_fp1 ) .   '</span></td>';
				$content .= '<td>' . esc_html( $time_fp2 ) .   ' <span>' . esc_html( $date_fp2 ) .   '</span></td>';
				$content .= '<td>' . esc_html( $time_qual ) .  ' <span>' . esc_html( $date_qual ) .  '</span></td>';
				$content .= '<td>' . esc_html( $time_race1 ) . ' <span>' . esc_html( $date_race1 ) . '</span></td>';
				$content .= '<td>' . esc_html( $time_race2 ) . ' <span>' . esc_html( $date_race2 ) . '</span></td>';

				if ( isset( $event['event_race-2_timestamp'] ) && ( $event['event_race-2_timestamp'] < time() ) ) {
					$content .= '<td><span class="tick-mark"></div></td>';
				} else {
					$content .= '<td></td>';
				}

				$content .= '</tr>';

			}
			$content .= '</table>';
		}

		return $content;
	}

	function sort_callback( $a, $b ) {
		return $a[ $this->orderby ] - $b[ $this->orderby ];
	}

	function sort_callback_reverse( $a, $b ) {
		return $b[ $this->orderby ] - $a[ $this->orderby ];
	}

}
