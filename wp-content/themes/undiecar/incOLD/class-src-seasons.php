<?php

/**
 * Seasons.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Seasons extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',            array( $this, 'init' ) );

		add_filter( 'the_content',     array( $this, 'schedule' ) );
		add_filter( 'the_content',     array( $this, 'drivers' ) );
		add_filter( 'the_content',     array( $this, 'championships' ), 8 );
		add_filter( 'the_content',     array( $this, 'gallery' ), 12 );

//		add_filter( 'the_content',     array( $this, 'teams_championship' ), 9 );
		add_filter( 'the_content',     array( $this, 'permanently_store_results' ), 100 );

		add_action( 'cmb2_admin_init', array( $this, 'seasons_metaboxes' ) );
		add_action( 'add_meta_boxes',  array( $this, 'permanently_store_results_metabox' ) );
		add_action( 'save_post',       array( $this, 'permanently_store_results_save' ), 10, 2 );

		// Add shortcode
		add_shortcode( 'src-schedule',   array( $this, 'schedule_shortcode' ) );

		// Add filters
		add_filter( 'the_content', array( $this, 'add_points_info_to_content' ) );

	}

	public function permanently_store_results( $content ) {

		wp_reset_query(); // Crude hack, should probably be run at the location the problem occured.

		if (
			1 !== get_the_ID()
			&&
			'store' === get_post_meta( get_the_ID(), '_permanently_store_results', true )
		) {

			if ( ! defined( 'UNDIECAR_PERMANENTLY_STORE_RESULTS' ) ) { // Avoids wp_update_post looping

				define( 'UNDIECAR_PERMANENTLY_STORE_RESULTS', true );
				wp_update_post(
					array(
						'ID'           => get_the_ID(),
						'post_content' => $content,
					)
				);
			}

			update_post_meta( get_the_ID(), '_permanently_store_results', 'stored' );
		}

		return $content;
	}

	public function championships( $content ) {

		// Bail out now if meant to be using stored results
		if ( 'stored' === get_post_meta( get_the_ID(), '_permanently_store_results', true ) ) {
			return $content;
		}

		$content = $this->championship( $content, false, 100, esc_html__( 'Championship', 'undiecar' ) );

		return $content;
	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'season',
			array(
				'public'       => true,
				'label'        => __( 'Season', 'src' ),
				'supports'     => array( 'thumbnail', 'title', 'editor' ),
				'show_in_menu' => 'edit.php?post_type=event',
			)
		);

	}

	public function schedule_shortcode( $content ) {
		return $this->schedule( $content, 'shortcode' );
	}

	/**
	 * Output the schedule as a table.
	 *
	 * @param  string  $content    The post content
	 * @return string  The modified post content
	 */
	public function schedule( $content, $opt = null ) {

		// Bail out now if meant to be using stored results
		if ( 'stored' === get_post_meta( get_the_ID(), '_permanently_store_results', true ) ) {
			return $content;
		}

		$args = array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		);

		// If on a season, then only use that seasons schedule
		if ( 'season' === get_post_type() ) {
			$args['meta_key']    = 'season';
			$args['meta_value'] = get_the_ID();
		} else if ( 'shortcode' === $opt ) {
			// In shortcode, so show all events
		} else {
			return $content;
		}

		$columns = array(
			'Num'        => true,
			'Event'      => true,
			'FP1'        => false,
//			'FP2'        => false,
			'Qualifying' => false,
			'Race 1'     => false,
			'Race 2'     => false,
			'Race 3'     => false,
		);

		// Get all events from that season
		$query = new WP_Query( $args );
		$events = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$date = get_post_meta( get_the_ID(), 'date', true );

				$formatted_time = get_post_meta( get_the_ID(), 'qualifying_time', true );

				$date_formatted = date( 'Y-m-d', $date ) . ' ' . $formatted_time;
				$time_stamp = strtotime( $date_formatted );

				if ( $time_stamp < time() ) {
					$events[$date]['past'] = true;
				}

				$events[$date]['id'] = get_the_ID();
				$events[$date]['track']      = get_post_meta( get_the_ID(), 'track', true );

				$events[$date]['title'] = get_the_title();

				foreach ( $this->event_types() as $name => $desc ) {

					// Legacy time
					$time = get_post_meta( get_the_ID(), 'event_' . sanitize_title( $name ) . '_timestamp', true ); //legacy

					if ( '' !== $time ) {

						if ( 'FP1' === $name ) {
							$columns['FP1'] = true;
						} else if ( 'Qualifying' === $name ) {
							$columns['Qualifying'] = true;
						} else if ( 'Race 1' === $name ) {
							$columns['Race 1'] = true;
						} else if ( 'Race 2' === $name ) {
							$columns['Race 2'] = true;
						} else if ( 'Race 3' === $name ) {
							$columns['Race 3'] = true;
						}

						$events[$date][sanitize_title( $name ) . '_timestamp'] = get_post_meta( get_the_ID(), 'event_' . sanitize_title( $name ) . '_timestamp', true );
					}

					// Get the time
					$time = get_post_meta( get_the_ID(), str_replace( '-', '_', sanitize_title( $name ) ) . '_time', true );
					if ( '' !== $time ) {

						if ( 'FP1' === $name ) {
							$columns['FP1'] = true;
						} else if ( 'Qualifying' === $name ) {
							$columns['Qualifying'] = true;

/*** SHOULD USE THE NEW SYSTEM WHICH ALLOWS FOR UNLIMITED RACES ****/
						} else if ( 'Race 1' === $name ) {
							$columns['Race 1'] = true;
						} else if ( 'Race 2' === $name ) {
							$columns['Race 2'] = true;
						} else if ( 'Race 3' === $name ) {
							$columns['Race 3'] = true;
						}

						$events[$date][sanitize_title( $name ) . '_time'] = get_post_meta( get_the_ID(), str_replace( '-', '_', sanitize_title( $name ) ) . '_time', true );
					}

				}

			}

		}

		ksort( $events );

		$html = '<h3>' . esc_html__( 'Schedule', 'src' ) . '</h3>';
		$html .= '<table class="some-list" id="src-schedule">';

		// Create the THEAD
		$html .= '<thead><tr>';
		foreach ( $columns as $label => $column ) {

			// Only load the columns being used
			if ( true === $column ) {

				// Shortening stuff for mobile
				if ( 'Qualifying' === $label ) {
					$label = 'Qual';
				} else if ( 'Num' === $label ) {
					$label = '#';
				}

				$html .= '<th class="' . esc_attr( sanitize_title( 'col-' . $label ) ) . '">' . esc_html( $label ) . '</th>';
			}

		}
		$html .= '</tr></thead>';

		$html .= '<tbody>';
		$count = 0;
		foreach ( $events as $date => $event ) {
			$count++;
			$formatted_date = ' GMT <span>' . esc_html( date( 'l', $date ) ) . '<br />' . esc_html( date( get_option( 'date_format' ), $date ) ) . '</span>';

			$html .= '<tr>';

			$past_class = '';
			if ( isset( $events[$date]['past'] ) && true === $events[$date]['past'] ) {
				$past_class = ' past-event';
			}

			// Only load the columns being used
			foreach ( $columns as $label => $column ) {

				if ( true === $column ) {

					$text = '';
					if ( 'Num' == $label ) {
						$text = $count;
					} else if ( 'FP1' == $label ) {

						if ( isset( $event['fp1_timestamp'] ) ) {
							$text = esc_html( $event['fp1_timestamp'] ) . $formatted_date; // legacy
						}

						if ( isset( $event['fp1_time'] ) ) {
							$text = esc_html( $event['fp1_time'] ) . $formatted_date;
						}

					} else if ( 'Event' == $label ) {
						if ( isset( $event['track'] ) ) {
							$text = '<a href="' . esc_url( get_permalink( $event['id'] ) ) . '">' . esc_html( get_the_title( $event['id'] ) ) . '</a>';
						}
					} if ( 'Qualifying' == $label ) {
						if ( isset( $event['qualifying_timestamp'] ) ) {
							$text = esc_html( $event['qualifying_timestamp'] ) . $formatted_date; // legacy
						}
						if ( isset( $event['qualifying_time'] ) ) {
							$text = esc_html( $event['qualifying_time'] ) . $formatted_date;
						}
					} if ( 'Race 1' == $label ) {
						if ( isset( $event['race-1_timestamp'] ) ) {
							$text = esc_html( $event['race-1_timestamp'] ) . $formatted_date; // legacy
						}
						if ( isset( $event['race-1_time'] ) ) {
							$text = esc_html( $event['race-1_time'] ) . $formatted_date;
						}
					} if ( 'Race 2' == $label ) {
						if ( isset( $event['race-2_timestamp'] ) ) {
							$text = esc_html( $event['race-2_timestamp'] ) . $formatted_date; // legacy
						}
						if ( isset( $event['race-2_time'] ) ) {
							$text = esc_html( $event['race-2_time'] ) . $formatted_date;
						}
					} if ( 'Race 3' == $label ) {
						if ( isset( $event['race-3_timestamp'] ) ) {
							$text = esc_html( $event['race-3_timestamp'] ) . $formatted_date; // legacy
						}
						if ( isset( $event['race-3_time'] ) ) {
							$text = esc_html( $event['race-3_time'] ) . $formatted_date;
						}
					}

					$html .= '<td class="' . esc_attr( sanitize_title( 'col-' . $label ) . $past_class ) . '">' . $text /* do not escape */ . '</td>';

				}

			}

			$html .= '</tr>';

		}
		$html .= '</tbody>';

		$html .= '</table>';

		wp_reset_query();

		$content .= $html;

		return $content;
	}

	public function drivers( $content ) {

		if ( 'season' !== get_post_type() ) {
			return $content;
		}

		// Bail out now if meant to be using stored results
		if ( 'stored' === get_post_meta( get_the_ID(), '_permanently_store_results', true ) ) {
			return $content;
		}

		return $content;
	}

	public function seasons_metaboxes() {
		$slug = 'season';

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Season', 'src' ),
			'object_types' => array( 'season', ),
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Label', 'src' ),
			'description'=> esc_html__( 'Used as a tagline or to indicater the broad title of the championship, perhaps to group a series of seasons together', 'src' ),
			'id'         => 'label',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Division 1', 'src' ),
			'description'=> esc_html__( 'Set the average road/oval iRating required for entry into division 1. Leave blank for no separate divisions.', 'src' ),
			'id'         => 'division_1_cutoff',
			'type'       => 'text',
			'default' => '',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Drop scores', 'src' ),
			'description'=> esc_html__( 'Set the number of events which competitors can drop their results for.', 'src' ),
			'id'         => 'drop_scores',
			'type'       => 'text',
			'default' => '',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
		) );


		$cmb->add_field( array(
			'name'       => esc_html__( 'Bonus point for pole position', 'src' ),
			'id'         => 'bonus_point_pole',
			'type'       => 'checkbox',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Bonus point for fastest lap', 'src' ),
			'id'         => 'bonus_point_fastest_lap',
			'type'       => 'checkbox',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Bonus point for most laps led', 'src' ),
			'id'         => 'bonus_point_most_laps_led',
			'type'       => 'checkbox',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Points for each position', 'src' ),
			'desc'       => esc_html__( 'Numbers only', 'src' ),
			'id'         => 'points_positions',
			'type'       => 'text',
			'repeatable' => true,
			'attributes' => array(
				'type'    => 'number',
				'pattern' => '\d*',
			),

		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Fixed setups?', 'src' ),
			'id'         => 'fixed_setup',
			'type'       => 'checkbox',
		) );

	}

	/**
	 * Store results for ever.
	 */
	public function permanently_store_results_metabox() {
		add_meta_box(
			'permanently-store-results', // ID
			__( 'Permanently store results', 'src' ), // Title
			array(
				$this,
				'permanently_store_results_html', // Callback to method to display HTML
			),
			array( 'season' ), // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	public function permanently_store_results_html() {

		if ( 'season' !== get_post_type() ) {
			return;
		}

		$selected = get_post_meta( get_the_ID(), '_permanently_store_results', true );

		echo $selected;
		echo '
		<p>
			<label for="permanently-store-results">' . esc_html__( 'Use permanently stored results? Intended for stashing results for historical purposes after season has ended.', 'src' ) . '</label>
			<select type="checkbox" id="permanently-store-results" name="permanently-store-results">';

		$options = array(
			'fresh' => 'Fresh',
			'store' => 'Store permanently next page load',
			'stored' => 'Results already stored permanently',
		);
		foreach ( $options as $value => $label ) {
			echo '<option ' . selected( $selected, $value, true ) . ' value="' . esc_attr( $value ) . '">' . esc_html( $label ) . '</option>';
		}


		echo ';
			</select>
		</p>
		<input type="hidden" id="permanently-store-results-nonce" name="permanently-store-results-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">';

	}

	/**
	 * Save results upload save.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function permanently_store_results_save( $post_id, $post ) {

		if ( ! isset( $_POST['permanently-store-results-nonce'] ) ) {
			return $post_id;
		}

		// Do nonce security check
		if ( ! wp_verify_nonce( $_POST['permanently-store-results-nonce'], __FILE__ ) ) {
			return $post_id;
		}

		/*
		 * This is a bit of a hack, as we're loading the championship table and 
		 * just triggering it to stash the results mid-table then bailing out.
		 */

		if ( 'store' === $_POST['permanently-store-results'] ) {
			update_post_meta( $post_id, '_permanently_store_results', 'store' );
		} else if ( 'stored' === $_POST['permanently-store-results'] ) {
			update_post_meta( $post_id, '_permanently_store_results', 'stored' );
		} else {
			delete_post_meta( $post_id, '_permanently_store_results' );
		}

	}

	/**
	 * Add points information to the post content.
	 *
	 * @param  string  $content   The post content
	 * @return string  The modified post content
	 */
	public function add_points_info_to_content( $content ) {
		$html = '';

		// Bail out now if meant to be using stored results
		if ( 'stored' === get_post_meta( get_the_ID(), '_permanently_store_results', true ) ) {
			return $content;
		}

		if ( 'season' !== get_post_type() ) {
			return $content;
		}

		$points_positions = get_post_meta( get_the_ID(), 'points_positions', true );
		if ( '' !== $points_positions ) {
			$html .= '<h3>' . esc_html( 'Points system', 'src' ) . '</h3>';
			$html .= '<p>' . esc_html__( 'At the conclusion of each race, the top finishers will score points towards the drivers championship, according to the following scale:', 'src' ) . '</p>';

			/**
			 * Load number formatters.
			 *
			 * uncomment extension=php_intl.dll in php.ini FPM
			 * sudo apt-get install php7.0-intl
			 * sudo service php7.0-fpm restart
			 */
			$ordinal_number = new NumberFormatter( 'en', NumberFormatter::ORDINAL );

			$html .= '<p>';
			$position = 1;
			foreach ( $points_positions as $key => $points ) {

				$html .= $ordinal_number->format( $position ) . ': ' . $points . ' ' . esc_html( 'points', 'src' ) . '<br />';

				$position++;
			}
			$html .= '</p>';

		}
/*
		// If we have any bonus points, then add those
		$bonus_point_pole          = get_post_meta( get_the_ID(), 'bonus_point_pole', true );
		$bonus_point_fastest_lap   = get_post_meta( get_the_ID(), 'bonus_point_fastest_lap', true );
		$bonus_point_best_crash    = get_post_meta( get_the_ID(), 'bonus_point_best_crash', true );
		$bonus_point_most_laps_led = get_post_meta( get_the_ID(), 'bonus_point_most_laps_led', true );

		$bonuses = array();
		if ( '' !== $bonus_point_pole ) {
			$bonuses[] = esc_html__( 'pole position', 'src' );
		}
		if ( '' !== $bonus_point_fastest_lap ) {
			$bonuses[] = esc_html__( 'fastest lap in each event', 'src' );
		}
		if ( '' !== $bonus_point_most_laps_led ) {
			$bonuses[] = esc_html__( 'most laps led in each race', 'src' );
		}
		if ( '' !== $bonus_point_best_crash ) {
			$bonuses[] = esc_html__( 'most spectacular crash in each race', 'src' );
		}

		$bonuses[] = esc_html__( 'least incidents in each event (must not be more than one lap down on the leader of their class)', 'src' );

		if ( 0 < count( $bonuses ) ) {

			$number = esc_html__( 'Bonus', 'src' );
			if ( 1 === $bonuses ) {
				$number = esc_html__( 'A', 'src' );
			}

			$suffix = '';
			if ( 1 < $bonuses ) {
				$suffix = 's';
			}

			$text = '';
			foreach ( $bonuses as $key => $thing ) {

				if ( $key === ( count( $bonuses ) - 1 ) ) {
					$text .= ' ' . esc_html__( 'and', 'src' ) . ' ';
				} else if ( 0 !== $key ) {
					$text .= ', ';
				}
				$text .= $thing;
			}

			$text = sprintf(
				esc_html__( '%s point%s will be awarded for %s.', 'src' ),
				$number,
				$suffix,
				$text
			);
			$html .= '<p>' . esc_html( $text ) . '</p>';
//			$html .= '<p>' . esc_html__( 'Bonus points are awarded per event, not per class.', 'undiecar' ) . '</p>';

		}
*/
		return $content . $html;
	}

	/**
	 * Add all pictures from the current season as a gallery.
	 */
	public function gallery( $content ) {
		global $post;

		// Bail out now if not on a season.
		if (
			'season' !== get_post_type()
			||
			'stored' === get_post_meta( get_the_ID(), '_permanently_store_results', true )
		) {
			return $content;
		}

		$slug = $post->post_name;
		$content .= do_shortcode( '[undiecar_season_gallery season="' . esc_attr( $slug ) . '" title="' . esc_html__( 'Gallery', 'undiecar' ) . '"]' );

		return $content;
	}

}
