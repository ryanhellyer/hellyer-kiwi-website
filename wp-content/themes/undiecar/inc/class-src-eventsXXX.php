<?php

/**
 * Events.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Events extends SRC_Core {

	public $event;
	public $stored = false;
	public $store_now = false;

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',              array( $this, 'init' ) );
		add_action( 'cmb2_admin_init',   array( $this, 'events_metaboxes' ) );
		add_action( 'template_redirect', array( $this, 'set_event_data' ) );

		add_filter( 'the_content',            array( $this, 'store_or_not_store' ), 1 );
		add_filter( 'the_content',            array( $this, 'store_result_permanently' ), 100 );

		add_filter( 'the_content',            array( $this, 'add_extra_content' ) );
		add_filter( 'src_featured_image_url', array( $this, 'filter_featured_image_url' ) );
		add_filter( 'upload_mimes',           array( $this, 'allow_setup_uploads' ) );
		add_filter( 'the_content',            array( $this, 'image_gallery' ) );

		// iRacing results uploader
		add_action( 'add_meta_boxes',     array( $this, 'results_upload_metabox' ) );
		add_action( 'save_post',          array( $this, 'results_upload_save' ), 10, 2 );
		//add_action( 'post_edit_form_tag', array( $this, 'update_form_enctype' ) );

		// Shortcodes
		add_shortcode( 'undiecar_future_events', array( $this, 'future_events' ) );
		add_shortcode( 'undiecar_previous_events', array( $this, 'previous_events' ) );
	}

	public function store_or_not_store( $content ) {

		if ( 'event' !== get_post_type() ) {
			return $content;
		}

		$season_id = get_post_meta( get_the_ID(), 'season', true );
		if ( 'stored' === get_post_meta( $season_id, '_permanently_store_results', true ) ) {

			if ( '1' === get_post_meta( get_the_ID(), '_permanently_stored_results', true ) ) {
				$this->stored = true;
			} else {
				$this->store_now = true;
			}

		}

		return $content;
	}

	public function store_result_permanently( $content ) {

		if (
			true === $this->store_now
			&&
			! defined( 'UNDIECAR_PERMANENTLY_STORE_RESULTS' ) // Avoids wp_update_post looping
		) {

			define( 'UNDIECAR_PERMANENTLY_STORE_RESULTS', true );
			wp_update_post(
				array(
					'ID'           => get_the_ID(),
					'post_content' => $content,
				)
			);

			update_post_meta( get_the_ID(), '_permanently_stored_results', true );
		}

		return $content;
	}

	/**
	 * Allow setup file uploads.
	 *
	 * @param  array   $mime_types   The allowed mime types
	 * @return array  modified mime types
	 */
	public function allow_setup_uploads( $mime_types ){
		$mime_types['sto'] = 'application/octet-stream';

		return $mime_types;
	}

	/**
	 * When on event, use tracks featured image.
	 *
	 * @string  string  $image_url  The featured image URL
	 * @return  string  The modified image URL
	 */
	public function filter_featured_image_url( $image_url ) {

		if ( 'event' === get_post_type() ) {
			$image_url = get_the_post_thumbnail_url( $this->event['current_round']['track'], 'src-featured' );
		}

		return $image_url;
	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'event',
			array(
				'public'             => true,
				'publicly_queryable' => true,
				'label'              => esc_html__( 'Events', 'src' ),
				'supports'           => array( 'title', 'editor' ),
				'menu_icon'          => 'dashicons-flag',
			)
		);

	}

	/**
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function events_metaboxes() {
		$slug = 'event';

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Event Information', 'src' ),
			'object_types' => array( 'event', ),
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Season', 'src' ),
			'id'         => 'season',
			'type'       => 'select',
			'options_cb' => 'src_get_seasons',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Track', 'src' ),
			'id'         => 'track',
			'type'       => 'select',
			'options_cb' => 'src_get_tracks',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Time of day', 'src' ),
			'id'         => 'time_of_day',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Fuel amount', 'src' ),
			'id'         => 'fuel_amount',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Qualifying Format', 'src' ),
			'id'         => 'qualifying_format',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Date', 'src' ),
			'id'   => 'date',
			'type' => 'text_date_timestamp',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Number of races', 'src' ),
			'id'   => 'number_of_races',
			'type' => 'number',
		) );

		foreach ( $this->event_types() as $name => $desc ) {

			$cmb->add_field( array(
				'name' => esc_html( $name ) . ' time',
				'desc' => esc_html( $desc ) . ' time',
				'id'   => $slug . '_' . sanitize_title( $name ) . '_timestamp',
				'type' => 'text_time',
				'time_format' => 'H:i', // Set to 24hr format
			) );

			$cmb->add_field( array(
				'name' => esc_html( $name ) . ' length',
				'desc' => esc_html( $desc ) . ' length',
				'id'   => $slug . '_' . sanitize_title( $name ) . '_length',
				'type' => 'text',
			) );

			if ( 
				'Race 1' === $name
				||
				'Race 2' === $name
			) {

				$cmb->add_field( array(
					'name' => esc_html__( $name . ' points multiplier', 'src' ),
					'id'         => $slug . '_' . sanitize_title( $name ) . '_points_multiplier',
					'type'       => 'text',
					'default' => '1.0',
				) );
			}

		}

		$cmb->add_field( array(
			'name'        => esc_html__( 'Setup file', 'src' ),
			'description' => esc_html__( 'Please note that due to a technical glitch, you may need to upload the file via the media upload section, then just select it here.', 'src' ),
			'id'          => 'setup_file',
			'type'        => 'file',
		) );

		$cmb->add_field( array(
			'name'        => esc_html__( 'Setup default', 'src' ),
			'description' => esc_html__( 'Please note that due to a technical glitch, you may need to upload the file via the media upload section, then just select it here.', 'src' ),
			'id'          => 'setup_file',
			'type'        => 'file',
		) );

	}

	public function get_events_drivers_array( $event_id ) {

		$season_id = get_post_meta( $event_id, 'season', true );
		$query = new WP_Query( array(
			'p'                      => $season_id,
			'posts_per_page'         => 1,
			'post_type'              => 'season',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$drivers_array = array(
			'' => 'None',
		);
		if ( $query->have_posts() ) {

			while ( $query->have_posts() ) {
				$query->the_post();
				$season_slug = basename( get_permalink() );
				$drivers = $this->get_seasons_drivers( $season_slug );
			}

			foreach ( $drivers as $driver_key => $driver_id ) {

				if ( $season_slug === get_user_meta( $driver_id, 'season', true ) ) {
					$driver = get_userdata( $driver_id );
					if ( isset( $driver->data->display_name ) && '' !== $driver->data->display_name ) {
						$driver_name = $driver->data->display_name;
						$drivers_array[$driver_name] = $driver_name;
					}
				}

			}

			wp_reset_postdata();

		}

		return $drivers_array;
	}


	public function qualifying_grid() {
		return array(
			'normal'   => esc_html__( 'Normal', 'src' ),
			'reversed' => esc_html__( 'Reversed', 'src' ),
		);
	}

	/**
	 * Set event data.
	 */
	public function set_event_data() {

		if ( 'event' !== get_post_type() ) {
			return;
		}

		// Which season?
		$season_id = get_post_meta( get_the_ID(), 'season', true );
		$query = new WP_Query( array(
			'p'                      => $season_id,
			'posts_per_page'         => 1,
			'post_type'              => 'season',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$season_name = get_the_title();

			}

			wp_reset_postdata();
		}

		// Get all events from that season
		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',

			'meta_key'               => 'season',
			'meta_value'             => $season_id,
			'post_status' => 'any',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$events = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$date  = get_post_meta( get_the_ID(), 'date', true );
				$track = get_post_meta( get_the_ID(), 'track', true );
				$events[$date] = array(
					'id'          => get_the_ID(),
					'date'        => $date,
					'title'       => get_the_title(),
					'track'       => $track,
				);

				foreach ( $this->event_types() as $name => $desc ) {

					$time = get_post_meta( get_the_ID(), 'event_' . sanitize_title( $name ) . '_timestamp', true );
					if ( '' !== $time ) {
						$events[$date][sanitize_title( $name ) . '_timestamp'] = $time;
					}

				}

			}

			wp_reset_postdata();
		}
		wp_reset_query();

		// Sort events into date order
		ksort( $events );

		// Convert array keys to consecutive integers
		$new_events = array();
		$count = 0;
		foreach ( $events as $date => $event ) {
			$new_events[$count] = $event;
			$count++;
		}
		if ( 0 === $count ) {
			return;
		}

		$number_of_rounds_in_season = count( $new_events );

		foreach ( $new_events as $key => $event ) {

			// If on current event ... 
			if ( get_the_ID() === $event['id'] ) {
				$round_number = $key + 1;
				$current_round = $event;

				// Get previous round
				if ( 0 < $key ) {
					$previous_round = $new_events[$key - 1];
				} else {
					$previous_round = false;
				}

				// Get next round
				if ( $key < $number_of_rounds_in_season ) {
					if ( isset( $new_events[$key + 1] ) ) {
						$next_round = $new_events[$key + 1];
					} else {
						$next_round = false;
					}
				} else {
					$next_round = false;
				}

			}

		}

		// Set as class variable so that it can be used to filter via other methods
		$this->event['season_name']                = $season_name;
		$this->event['number_of_rounds_in_season'] = $number_of_rounds_in_season;
		$this->event['round_number']               = $round_number;
		$this->event['season_id']                  = $season_id;
		$this->event['season_name']                = $season_name;

		$this->event['previous_round']             = $previous_round;
		$this->event['next_round']                 = $next_round;
		$this->event['current_round']              = $current_round;

		// Add track information for previous, current and next rounds
		foreach ( array( 'previous_round', 'next_round', 'current_round' ) as $key => $x ) {
			if ( ! isset( $this->event[$x]['track'] ) ) {
				continue;
			}

			$query = new WP_Query( array(
				'p'                      => $this->event[$x]['track'],
				'post_type'              => 'track',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			) );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$this->event[$x]['track_name']    = get_the_title();
					$this->event[$x]['track_map']     = get_post_meta( get_the_ID(), 'map_id', true );
					$this->event[$x]['track_logo']    = get_post_meta( get_the_ID(), 'logo_id', true );
					$this->event[$x]['track_country'] = get_post_meta( get_the_ID(), 'country', true );
					$this->event[$x]['track_type']    = get_post_meta( get_the_ID(), 'track_type', true );
					$this->event[$x]['image1']        = get_post_meta( get_the_ID(), 'image1_id', true );
					$this->event[$x]['image2']        = get_post_meta( get_the_ID(), 'image2_id', true );
					$this->event[$x]['image3']        = get_post_meta( get_the_ID(), 'image3_id', true );
					$this->event[$x]['image4']        = get_post_meta( get_the_ID(), 'image4_id', true );
				}

			}

			wp_reset_postdata();
		}

		wp_reset_query();

	}

	/**
	 * Adding extra content.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function add_extra_content( $content ) {

		// Bail out now if result already stored
		if ( true === $this->stored ) {
			return $content;
		}

		if ( 'event' !== get_post_type() ) {
			return $content;
		}

		$legacy_date = date( get_option( 'date_format' ), $this->event['current_round']['date'] );
		if ( '' !== $legacy_date ) {
			update_post_meta( get_the_ID(), 'event_race-1_date', $legacy_date );
		}

		$date = get_post_meta( get_the_ID(), 'event_race-1_date', true );

		$track_logo = $this->event['current_round']['track_logo'];
		$track_logo_image = wp_get_attachment_image_src( $track_logo, 'src-three' );
		if ( isset( $track_logo_image[0] ) ) {
			$track_logo_image_url = $track_logo_image[0];
		}

		$track_url = get_permalink( $this->event['current_round']['track'] );

		$sidebar_html = '
		<div id="sidebar">
';

		if ( isset( $track_logo_image_url ) ) {
			$sidebar_html .= '
			<a href="' . esc_url( $track_url ) . '">
				<img style="width:100%;" src="' . esc_url( $track_logo_image_url ) . '" />
			</a>';
		}

		$sidebar_html .= '
			<p>
				<strong>' . esc_html( $date ) . '</strong>
			</p>';

		foreach ( $this->event_types() as $name => $desc ) {
			$sidebar_html .= '<p>';
			$meta_key = 'event_' . sanitize_title( $name ) . '_timestamp';
			$time = get_post_meta( get_the_ID(), $meta_key, true );
			if ( '' !== $time ) {

				$slug = strtolower( sanitize_title( $name ) );

				$extra_session_info = '';
				$length = '';
				if ( 'Qualifying' === $name ) {
					$length = get_post_meta( get_the_ID(), 'qualifying_format', true );
				} else if ( 'FP1' === $name ) {
					$length = get_post_meta( get_the_ID(), 'fp1_length', true );
				} else if ( 'FP2' === $name ) {
					$length = get_post_meta( get_the_ID(), 'fp2_length', true );
				} else if ( 'FP1' === $name ) {
					$length = get_post_meta( get_the_ID(), 'fp2_length', true );
				} else if ( 'Race 1' === $name ) {
					$length = get_post_meta( get_the_ID(), 'event_race-1_length', true  );
				} else if ( 'Race 2' === $name ) {
					$length = get_post_meta( get_the_ID(), 'event_race-2_length', true  );
				} else if ( 'Race 3' === $name ) {
					$length = get_post_meta( get_the_ID(), 'event_race-3_length', true  );
				}

				if ( 'Race 2' === $name ) {

					// Supporting legacy meta key - can be removed later as auto converts to new system
					if ( 'reversed' === get_post_meta( get_the_ID(), 'qualifying_grid', true ) ) {
						$grid = get_post_meta( get_the_ID(), 'qualifying_grid', true );
						update_post_meta( get_the_ID(), 'event_race-2_grid', $grid );
						delete_post_meta( get_the_ID(), 'qualifying_grid' );
					}

					
					if ( 'reversed' === get_post_meta( get_the_ID(), 'event_' . $slug . '_grid', true ) ) {
						$extra_session_info .= 'Reversed grid';
					}
				}


				$sidebar_html .= '<strong>' . esc_html( $desc ) . '</strong><br />Start time: ' . esc_html( $time ) . ' GMT';

				$session_date = get_post_meta( get_the_ID(), 'event_' . $slug . '_date', true );
				if ( '' !== $session_date && is_numeric( $session_date ) ) {
					$sidebar_html .= '<br />Date: ' . esc_html( date( 'Y-m-d', $session_date ) );
				}

				if ( '' !== $length ) {
					$sidebar_html .= '<br />Length: ' . esc_html( $length );
				}
				$sidebar_html .= '<br />' . $extra_session_info;
			}
			$sidebar_html .= '</p>';
		}

		$setup_file_id = get_post_meta( get_the_ID(), 'setup_file_id', true );
		if ( '' !== $setup_file_id ) {
			$setup_file = wp_get_attachment_url( $setup_file_id );
			$sidebar_html .= '<p><a href="' . esc_url( $setup_file ) . '">Download fixed setup</a></p>';
		}

		$season_id = get_post_meta( get_the_ID(), 'season', true );
		$sidebar_html .= '
		</div>';


		/**
		 * Generate event description.
		 */

		// Count up how many races there are
		$race_count = 0;
		if ( '' !== get_post_meta( get_the_ID(), 'event_race-1_timestamp', true ) ) {
			$race_count++;
		}
		if ( '' !== get_post_meta( get_the_ID(), 'event_race-2_timestamp', true ) ) {
			$race_count++;
		}
		if ( '' !== get_post_meta( get_the_ID(), 'event_race-3_timestamp', true ) ) {
			$race_count++;
		}
		$suffix = '';
		$qualifying_grid = '';
		if ( 1 < $race_count ) {
			$suffix = 's';

			// Add text for reversed grid races.
			if ( 'reversed' === get_post_meta( get_the_ID(), 'qualifying_grid', true ) ) {
				$qualifying_grid = ' ' . esc_html__( 'The grid for race two will be reversed.', 'src' );
			}

		}

		/**
		 * Load number formatter.
		 *
		 * uncomment extension=php_intl.dll in php.ini FPM
		 * sudo apt-get install php7.0-intl
		 * sudo service php7.0-fpm restart
		 */
		$number = new NumberFormatter( 'en', NumberFormatter::SPELLOUT );

		// Output event description
		$html = '';

		if ( __( 'Special Events', 'undiecar' ) === get_the_title( $this->event['season_id'] ) ) {
			$html .= wpautop(
				sprintf(
					__( 'This event will be held on %s %s at the %s long <a href="%s">%s</a> %s track in %s. Qualifying begins at %s GMT, followed by %s %s race%s.%s', 'undiecar' ),
					esc_html( date( 'l', $this->event['current_round']['date'] ) ), // Day of week
					esc_html( $date ),
					esc_html( get_post_meta( $this->event['current_round']['track'], 'track_length', true ) ) . ' km',
					esc_url( $track_url ),
					esc_html( $this->event['current_round']['track_name'] ),
					'', // Removed as was repetitive after already mentioning track type in track name sometimes esc_html( $this->event['current_round']['track_type'] ),
					esc_html( src_get_countries()[ $this->event['current_round']['track_country'] ] ),
					esc_html( get_post_meta( get_the_ID(), 'event_qualifying_timestamp', true ) ),
					$number->format( $race_count ),
					esc_html( get_post_meta( get_the_ID(), 'race_length', true ) ),
					$suffix,
					$qualifying_grid
				)
			);
		} else {
			$html .= wpautop(
				sprintf(
					__( 'Round %s of %s in <a href="%s">%s</a> of the Undiecar Championship will be held on %s %s at the %s long <a href="%s">%s</a> %s track in %s. Qualifying begins at %s GMT, followed by %s %s race%s.%s', 'undiecar' ),
					esc_html( $this->event['round_number'] ),
					esc_html( $this->event['number_of_rounds_in_season'] ),
					esc_url( get_permalink( $this->event['season_id'] ) ),
				 	esc_html( get_the_title( $season_id ) ),
					esc_html( date( 'l', $this->event['current_round']['date'] ) ), // Day of week
					esc_html( $date ),
					esc_html( get_post_meta( $this->event['current_round']['track'], 'track_length', true ) ) . ' km',
					esc_url( $track_url ),
					esc_html( $this->event['current_round']['track_name'] ),
					'', // Removed as was repetitive after already mentioning track type in track name sometimes esc_html( $this->event['current_round']['track_type'] ),
					esc_html( src_get_countries()[ $this->event['current_round']['track_country'] ] ),
					esc_html( get_post_meta( get_the_ID(), 'event_qualifying_timestamp', true ) ),
					$number->format( $race_count ),
					esc_html( get_post_meta( get_the_ID(), 'race_length', true ) ),
					$suffix,
					$qualifying_grid
				)
			);
		}

		// Add track map
		$track_map = $this->event['current_round']['track_map'];
		$track_map_image = wp_get_attachment_image_src( $track_map, 'large' );
		if ( isset( $track_map_image[0] ) ) {
			$track_map_image_url = $track_map_image[0];
		}
		$map_html = '
		<p>&nbsp;</p><!-- crude spacing hack -->
		<img class="event-image" src="' . $track_map_image_url . '" />
		';

		// Next/Previous race navigation buttons
		$nav_html = '<div id="next-prev-buttons">';
		if ( isset( $this->event['previous_round'] ) && false !==  $this->event['previous_round'] ) {
			$url = get_permalink( $this->event['previous_round']['id'] );
			$nav_html .= '<a href="' . esc_url( $url ) . '" class="button alignleft">&laquo; ' . esc_html__( 'Last race', 'src' ) . '</a>';
		}

		if ( isset( $this->event['next_round'] ) && false !== $this->event['next_round'] ) {
			$url = get_permalink( $this->event['next_round']['id'] );
			$nav_html .= '<a href="' . esc_url( $url ) . '" class="button alignright">' . esc_html__( 'Next race', 'src' ) . '&raquo;</a>';
		}
		$nav_html .= '</div>';

		$least_incidents = get_post_meta( get_the_ID(), '_least_incidents', true );
		$least_incidents_text = '';
		if ( is_array( $least_incidents ) ) {
			foreach ( $least_incidents as $key => $driver ) {

				if ( '' !== $least_incidents_text ) {
					$least_incidents_text .= ', ';
				} else {
					$least_incidents_text = '';
				}

				$url = home_url() . '/' . __( 'member', 'src' ) . '/' . sanitize_title( $driver ) . '/';
				$least_incidents_text .= '<a href="' . esc_url( $url ) . '">' . esc_html( $driver ) . '</a>';
			}
		}

/*
		// If cars specified, then share information about them
		$query = new WP_Query( array(
			'post_type'      => 'car',
			'posts_per_page' => 100
		) );
		$event_id = get_the_ID();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				if ( '' !== get_post_meta( $event_id, 'car-' . get_the_ID(), true ) ) {
					$bla[] = get_the_ID();
				}

			}
			wp_reset_postdata();
		}
*/

		$bonus_points = '';
		if (
			'' !== $least_incidents_text
			||
			'' !== get_post_meta( get_the_ID(), '_pole_position', true )
			||
			'' !== get_post_meta( get_the_ID(), '_fastest_lap', true )
		) {

			$bonus_points = '
			<h3>Bonus points</h3>
			<p>';
			if ( '' !== $least_incidents_text ) {
				$bonus_points .= '
				Least incidents: ' . wp_kses_post( $least_incidents_text ) . '
				<br />';
			}

			if ( '' !== get_post_meta( get_the_ID(), '_pole_position', true ) ) {
				$name = get_post_meta( get_the_ID(), '_pole_position', true );
				$bonus_points .= '
				Pole position: <a href="' . esc_url( home_url() . '/' . __( 'member', 'src' ) . '/' . sanitize_title( $name ) . '/' ) . '">' . esc_html( $name ) . '</a>
				<br />';
			}

			if ( '' !== get_post_meta( get_the_ID(), '_fastest_lap', true ) ) {
				$name = get_post_meta( get_the_ID(), '_fastest_lap', true );
				$bonus_points .= '
				Fastest lap: <a href="' . esc_url( home_url() . '/' . __( 'member', 'src' ) . '/' . sanitize_title( $name ) . '/' ) . '">' . esc_html( $name ) . '</a>';
			}

			$bonus_points .= '
			</p>';
		}

		$content = '<div id="base-content">' . $html . $content . $this->get_car_list() . $bonus_points . '</div>' . $sidebar_html . $this->add_results() . $map_html . $nav_html;

		return $content;
	}

	/**
	 * Create list of allowed cars.
	 */
	private function get_car_list() {

		$query = new WP_Query( array(
			'post_type'      => 'car',
			'posts_per_page' => 100
		) );

		$event_id = get_the_ID();
		if ( $query->have_posts() ) {

			$cars = array();
			$count = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				if ( 'on' === get_post_meta( $event_id, 'car-' . get_the_ID(), true ) ) {
					$count++;

					$cars[] = get_the_ID();

				}

			}

			wp_reset_postdata();
		}

		$content = '';
		if ( 0 === $count ) {
			// No cars, so bail now
			return;
		} else if ( 1 === $count ) {
			$content .= '<h3>' . esc_html__( 'Allowed car', 'src' ) . '</h3>';
			$car_id = $cars[0];
			$content .= '<strong><a href="' . esc_url( get_the_permalink( $car_id ) ) . '">' . esc_html( get_the_title( $car_id ) ) . '</a></strong>';
		} else {
			$content .= '<h3>' . esc_html__( 'Allowed cars', 'src' ) . '</h3>';
			$content .= '<p>' . esc_html__( 'This is a multi-class event. Drivers may choose one of the following cars.', 'src' ) . '</p>';
			$content .= '<ol>';

			foreach ( $cars as $car_id ) {
				$content .= '<li><a href="' . esc_url( get_the_permalink( $car_id ) ) . '">' . esc_html( get_the_title( $car_id ) ) . '</a></li>';
			}

			$content .= '</ol>';
		}


		return $content;
	}

	/**
	 * Add results upload metabox.
	 */
	public function results_upload_metabox() {

		add_meta_box(
			'iracing-results-uploader', // ID
			__( 'Upload iRacing results', 'src' ), // Title
			array(
				$this,
				'results_upload_metabox_html', // Callback to method to display HTML
			),
			array( 'event', 'post' ), // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

	}

	/**
	 * Results upload HTML.
	 */
	public function results_upload_metabox_html() {

		if ( 'event' !== get_post_type() ) {
			return;
		}
/*
		echo '
		<p>
			<label for="result-qual-file">' . esc_html__( 'Qualifying results', 'src' ) . '</label>
			<input type="file" id="result-qual-file" name="result-qual-file" />
		</p>
		<p>
			<label for="result-1-file">' . esc_html__( 'Race 1 results', 'src' ) . '</label>
			<input type="file" id="result-1-file" name="result-1-file" />
		</p>
		<p>
			<label for="result-2-file">' . esc_html__( 'Race 2 results', 'src' ) . '</label>
			<input type="file" id="result-2-file" name="result-2-file" />
		</p>
		<p>
			<label for="result-3-file">' . esc_html__( 'Race 3 results', 'src' ) . '</label>
			<input type="file" id="result-3-file" name="result-3-file" />
		</p>
		<input type="hidden" id="result-nonce" name="result-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">
		<p>';
*/

		echo '
		<p>
			http://members.iracing.com/membersite/member/GetSubsessionResults?subsessionID=XXX
		</p>';

		echo '
		<p>
			<label for="result-1">' . esc_html__( 'Race 1 results', 'src' ) . '</label>
			<textarea id="result-1" name="result-1"></textarea>
		</p>
		<p>
			<label for="result-2-file">' . esc_html__( 'Race 2 results', 'src' ) . '</label>
			<input type="file" id="result-2-file" name="result-2-file" />
		</p>
		<p>
			<label for="result-3-file">' . esc_html__( 'Race 3 results', 'src' ) . '</label>
			<input type="file" id="result-3-file" name="result-3-file" />
		</p>
		<input type="hidden" id="result-nonce" name="result-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">
		<p>';

		foreach ( array( 1, 2, 3 ) as $key => $race_number ) {
			echo '
			<textarea style="font-family:monospace;font-size:9px;line-height:9px;width:100%;height:100px;">' . 
				print_r(
					json_decode( get_post_meta( get_the_ID(), '_results_' . $race_number, true ), true ),
					true
				) . 
			'</textarea>';
		}

			echo '
			<textarea style="font-family:monospace;font-size:9px;line-height:9px;width:100%;height:100px;">' . 
				print_r(
					json_decode( get_post_meta( get_the_ID(), '_event_info', true ), true ),
					true
				) . 
			'</textarea>';
		echo '

		</p>';


		$least_incidents = get_post_meta( get_the_ID(), '_least_incidents', true );
		$least_incidents_text = '';
		if ( is_array( $least_incidents ) ) {
			foreach ( $least_incidents as $key => $driver ) {
				if ( '' !== $least_incidents_text ) {
					$least_incidents_text .= ', ';
				} else {
					$least_incidents_text = '';
				}

				$least_incidents_text .= $driver;
			}
		}

		echo '
		<p>
			Least incidents: ' . esc_html( $least_incidents_text ) . '
		</p>
		<p>
			Pole position: ' . esc_html( get_post_meta( get_the_ID(), '_pole_position', true ) ) . '
		</p>
		<p>
			Fastest lap: ' . esc_html( get_post_meta( get_the_ID(), '_fastest_lap', true ) ) . '
		</p>';

	}

	/**
	 * Save results upload save.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function results_upload_save( $post_id, $post ) {

		if ( ! isset( $_POST['result-nonce'] ) ) {
			return $post_id;
		}

		// Do nonce security check
		if ( ! wp_verify_nonce( $_POST['result-nonce'], __FILE__ ) ) {
			return $post_id;
		}


		// Get data from results
		$results = stripslashes( $_POST[ 'result-1' ] );
		$results = json_decode( $results );

		foreach ( $results->rows as $key => $row ) {
			$driver_name = urldecode( $row->displayname );
			$driver_name = str_replace( '+', ' ', $driver_name );

			if ( 'QUALIFY' === $row->simsesname ) {

				$car_name = str_replace( '+', ' ', $row->ccName );
				$start_pos = absint( $row->finishpos ) + 1;
				$qual_time = $this->get_formatted_time_from_iracing( $row->bestquallaptime );

				$drivers[ $driver_name ] = array(
					'name'          => esc_html( $driver_name ),
					'car'           => esc_html( $car_name ),
					'start_pos'     => absint( $start_pos ),
					'qual_time'     => esc_html( $qual_time ),
				);

			}

			if ( 'RACE' === $row->simsesname ) {

				$finish_position  = absint( $row->pos ) + 1;
				$car_no           = $row->carnum;
				$avg_lap_time     = $this->get_formatted_time_from_iracing( $row->avglap );
				$fastest_lap_time = $this->get_formatted_time_from_iracing( $row->bestlaptime );
				$fastest_lap      = $row->bestlapnum;
				$interval         = $this->get_formatted_time_from_iracing( $row->interval );
				$reason_out       = $row->reasonout;
				$laps_led         = $row->lapslead;
				$laps_completed   = $row->lapscomplete;
				$incidents        = $row->incidents;

				// Check if the user has a number, and if not, give them the one they used in this race
				$args= array(
					'search' => $driver_name,
					'search_fields' => array( 'display_name' )
				);
				$user = new WP_User_Query( $args );
				if ( isset( $user->results[0]->ID ) ) {
					$user_id = absint( $user->results[0]->ID );
					$current_number = get_user_meta( $user_id, 'car_number', true );
					if ( '' === $current_number || '0' === $current_number ) {
						update_user_meta( $user_id, 'car_number', $car_no );
					}
				}


				$x = array(
					'car_no'           => absint( $car_no ),
					'position'         => absint( $finish_position ),
					'avg_lap_time'     => esc_html( $avg_lap_time ),
					'fastest_lap_time' => esc_html( $fastest_lap_time ),
					'fastest_lap'      => esc_html( $fastest_lap ),
					'interval'         => esc_html( $interval ),
					'reason_out'       => esc_html( $reason_out ),
					'laps_led'         => absint( $laps_led ),
					'laps_completed'   => absint( $laps_completed ),
					'incidents'        => absint( $incidents ),
				);
				$drivers[ $driver_name ] = array_merge( $x, $drivers[ $driver_name ] );

			}

		}
		$results = array();
		foreach ( $drivers as $key => $driver ) {
			$new_key = $driver[ 'position' ] - 1;
			$results[ $new_key ] = $driver;
		}

		ksort( $results );

		$results = json_encode( $results, JSON_UNESCAPED_UNICODE );
		update_post_meta( $post_id, '_results_1', $results );

		// Store event info
		$event_info = array(
			'session_id' => $results->sessionid,
			'number_of_lead_changes' => $results->nleadchanges,
			'weather_wind_dir' => $results->weather_wind_dir,
			'weather_fog_density' => $results->weather_fog_density,
			'ncautions' => $results->ncautions,
			'eventlapscomplete' => $results->eventlapscomplete,
			'weather_type' => $results->weather_type,
			'rubber_level_race' => $results->rubberlevel_practice,
			'leave_marbles' => $results->leavemarbles,
			'weather_rh' => $results->weather_rh,
			'weather_wind_speed_value' => $results->weather_wind_speed_value,
			'weather_temperature_value' => $results->weather_temp_value,
			'time_of_day' => $results->timeofday,
			'event_strength_of_field' => $results->eventstrengthoffield,
			'weather_skies' => $results->weather_skies,
		);
		foreach ( $event_info as $key => $x ) {
			$event_info[ $key ] = esc_html( $x );
		}
		$event_info = json_encode( $event_info, JSON_UNESCAPED_UNICODE );
		update_post_meta( $post_id, '_event_info', $event_info );
	}

	/**
	 * Get columns to keep in results.
	 *
	 * @param  bool  $original_key  true if keeping original keys
	 * @return  array  The columns to be kept
	 */
	public function get_columns_to_keep( $keep_keys = false ) {
		if ( false === $keep_keys ) {
			$count = 0;
			foreach ( $columns_to_keep as $key => $value ) {
				$new_columns_to_keep[$count] = $value;
				$count++;
			}
			return $new_columns_to_keep;
		}

		return $columns_to_keep;
	}

	/**
	 * Update the form enctype.
	 */
	public function update_form_enctype() {

		if ( 'event' === get_post_type() ) {
			echo ' enctype="multipart/form-data"';
		}

	}

	/**
	 * Add results to events pages.
	 *
	 * @return string  The modified page content
	 */
	public function add_results() {
		$html = '';

		foreach ( array( 1, 2, 3 ) as $key => $race_number ) {

			$results = get_post_meta( get_the_ID(), '_results_' . $race_number, true );		

			if ( '' === $results ) {
				continue;
			}

			$results = json_decode( $results, true );
			if ( empty( $results ) ) {
				continue;
			}

			$html .= '<h3 class="table-heading">' . esc_html__( 'Results table - Race #' . $race_number, 'src' ) . '</h3>';
			$html .= '<table class="some-list">';

			$html .= '<thead><tr>';

			// Check if we have multiple car types
			foreach ( $results as $key => $result ) {

				foreach ( $results as $key2 => $result2 ) {
					if ( isset( $result['car'] ) && $result['car'] !== $result2['car'] ) {
						$multiple_car_types = true;
					}
				}

			}

			$columns_to_keep[] = 'Name';
			$columns_to_keep[] = 'Start';
			$columns_to_keep[] = 'Car #';

			if ( isset( $multiple_car_types ) ) {
				$columns_to_keep[] = 'Car';
			}

			$columns_to_keep[] = 'Out';
			$columns_to_keep[] = 'Interval';
			$columns_to_keep[] = 'Laps led';
			$columns_to_keep[] = 'Qual';
			$columns_to_keep[] = 'Avg lap';
			$columns_to_keep[] = 'Fastest lap';
			$columns_to_keep[] = 'fastest lap';
			$columns_to_keep[] = 'laps compl';
			$columns_to_keep[] = 'Inc';

			$html .= '<th>' . esc_html__( 'Pos', 'src' ) . '</th>';			
			foreach ( $columns_to_keep as $key => $label ) {
				$html .= '<th>' . esc_html( $label ) . '</th>';			
			}

			$html .= '</thead>';
			$html .= '<tbody>';

			foreach ( $results as $key => $result ) {

				// Initially didn't use zero as starting position
				if ( isset( $results[ 0 ] ) ) {
					$position = $results[ $key][ 'position' ];
				} else {
					$position = $key;
				}

				$html .= '<tr>';
				$html .= '<td>' . esc_html( $position ) . '</td>';

				$driver_names = '';
				$names = explode( '|', $result['name'] );
				if ( is_array( $names ) ) {
					foreach ( $names as $name ) {

						$driver_slug = sanitize_title( $name );
						$link_start = $link_end = '';
						if ( username_exists( $driver_slug ) ) {
							$link_start = '<a href="' . esc_url( home_url() . '/member/' . $driver_slug ) . '/">';
							$link_end = '</a>';
						}

						$driver_names .= $link_start . esc_html( $name ) . $link_end . '<br />';
					}
				} else {

					$name = $names;
					$driver_slug = sanitize_title( $name );
					$link_start = $link_end = '';
					if ( username_exists( $driver_slug ) ) {
						$link_start = '<a href="' . esc_url( home_url() . '/member/' . $driver_slug ) . '/">';
						$link_end = '</a>';
					}

					$driver_names .= $link_start . esc_html( $name ) . $link_end;
				}

				$html .= '<td>' . $driver_names . '</td>';
				$html .= '<td>' . esc_html( $result['start_pos'] ) . '</td>';
				$html .= '<td>' . esc_html( $result['car_no'] ) . '</td>';

				if ( isset( $multiple_car_types ) ) {
					$html .= '<td>' . esc_html( $result['car'] ) . '</td>';
				}

				if ( isset( $result['out'] ) ) {
					$reason_out = $result['out'];
				} else {
					$reason_out = $result['reason_out'];
				}
				$html .= '<td>' . esc_html( $reason_out ) . '</td>';

				$interval = $result['interval'];
				if ( '-1' === $interval ) {
					$laps = $results[ 0 ][ 'laps_completed' ] - $result[ 'laps_completed' ];
					$interval = '-' . $laps . 'L';
				} else {
					$interval = $this->get_simplified_time( $interval );
				}
				$html .= '<td>' . esc_html( $interval ) . '</td>';

				$html .= '<td>' . esc_html( $result['laps_led'] ) . '</td>';

				if ( isset( $result['qual_time'] ) ) {
					$qual = $result['qual_time'];
				} else if ( isset( $result['qual_result'] ) ) {
					$qual = $result['qual_result'];
				} else {
					$qual = '';
				}
				$html .= '<td>' . esc_html( $this->get_simplified_time( $qual ) ) . '</td>';

				$html .= '<td>' . esc_html( $this->get_simplified_time( $result['avg_lap_time'] ) ) . '</td>';
				$html .= '<td>' . esc_html( $this->get_simplified_time( $result['fastest_lap_time'] ) ) . '</td>';

				$fastest_lap = $result['fastest_lap'];
				if ( '-1' === $fastest_lap ) {
					$fastest_lap = '';
				}
				$html .= '<td>' . esc_html( $fastest_lap ) . '</td>';
				if ( isset( $result['laps-completed'] ) ) {
					$laps_completed = $result['laps-completed'];
				} else {
					$laps_completed = $result['laps_completed'];
				}
				$html .= '<td>' . esc_html( $laps_completed ) . '</td>';
				$html .= '<td>' . esc_html( $result['incidents'] ) . '</td>';
/*
				foreach ( $result as $k => $cell ) {

					// Shove qualifying result into main results
					if ( 'qual_time' === $k ) {
						$cell = '';
						$qual_results = get_post_meta( get_the_ID(), '_results_qual', true );		
						$qual_results = json_decode( $qual_results, true );

						if ( is_array( $qual_results ) ) {

							foreach ( $qual_results as $q_key => $q_value ) {

								if ( $q_value['name'] === $result['name'] ) {
									$cell = $q_value['qual_time'];
								}

							}
						}

					}

					// Link the drivers name
					$link_start = $link_end = '';
					if ( 'name' === $k ) {
						$driver_slug = sanitize_title( $cell );
						if ( username_exists( $driver_slug ) ) {
							$link_start = '<a href="' . esc_url( home_url() . '/member/' . $driver_slug ) . '/">';
							$link_end = '</a>';
						}
					}

					$html .= '<td>' . $link_start . esc_html( $cell ) . $link_end . '</td>';
				}
*/
				$html .= "</tr>\n";
			}

			$html .= '</tbody>';

			$html .= '</table>';
		}

		return $html;
	}

	/**
	 * Add an image gallery.
	 *
	 * @param  string  $content  The page content
	 * @return string  The modified page content
	 */
	public function image_gallery( $content ) {

		if ( 'event' !== get_post_type() ) {
			return $content;
		}

		// Show image gallery
		$images = get_attached_media( 'image', get_the_ID() );

		if ( is_array( $images ) ) {
			$image_ids = '';
			foreach ( $images as $key => $image ) {
				$image_id = $image->ID;
				$image_ids .= absint( $image_id ) . ',';
			}
			$content .= do_shortcode( '[gallery size="src-four" ids="' . esc_attr( $image_ids ) . '"]' );
		}

		return $content;
	}

	public function future_events() {

		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$events = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$date  = get_post_meta( get_the_ID(), 'date', true );

				if ( time() < $date ) {
					$events[$date] = get_the_ID();
				}

			}

			wp_reset_postdata();
		}
		ksort( $events );

		return $this->get_events_table( $events );
	}

	public function previous_events() {

		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$events = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$date  = get_post_meta( get_the_ID(), 'date', true );

				if ( time() > $date ) {
					$events[$date] = get_the_ID();
				}

			}

			wp_reset_postdata();
		}
		krsort( $events );

		return $this->get_events_table( $events, true );
	}

	private function get_events_table( $events, $reverse_numbers = false ) {

		$content = '
		<table class="some-list">
			<thead>
				<tr>
					<th>#</th>
					<th>' . esc_html__( 'Date', 'undiecar' ) . '</th>
					<th>' . esc_html__( 'Event', 'undiecar' ) . '</th>
				</tr>
			</thead>
			<tbody>';

		if ( true === $reverse_numbers ) {
			$count = count( $events );
		} else {
			$count = 1;
		}
		foreach ( $events as $data => $event_id ) {

			$date  = get_post_meta( $event_id, 'date', true );

			$content .= '
				<tr>
					<td>' . esc_html( $count ) . '</td>
					<td>' . esc_html( date( 'l', $date ) ) . '<br />' . esc_html( date( get_option( 'date_format' ), $date ) ) . '</td>
					<td><a href="' . esc_url( get_permalink( $event_id ) ) . '">' . esc_html( get_the_title( $event_id ) ) . '</a></td>
				</tr>';

			if ( true === $reverse_numbers ) {
				$count = $count - 1;
			} else {
				$count++;
			}
		}

		$content .= '
			</tbody>
		</table>
		';

		return $content;
	}

	/**
	 * Simplifying time output.
	 */
	private function get_simplified_time( $time ) {
		// Strip hours off if zero
		if ( '00:' === substr( $time, 0, 3 ) ) {
			$time = substr( $time, 3 );
		}

		// Strip minutes off if zero
		if ( '00:' === substr( $time, 0, 3 ) ) {
			$time = substr( $time, 3 );
		}

		// Strip seconds off if zero
		if ( '00:' === substr( $time, 0, 3 ) ) {
			$time = substr( $time, 3 );
		}

		// Strip zero off front
		if (
			'0' === substr( $time, 0, 1 )
			&&
			'0.' != substr( $time, 0, 2 ) 
		) {
			$time = substr( $time, 1 );
		}

		if ( '00.0' == $time ) {
			$time = '';
		}

		$exploded = explode( '.', $time );
		if ( isset( $exploded[ 1 ] ) ) {
			$x = $exploded[ 1 ] / 100;
			$milliseconds = round( $x );
			$time = $exploded[ 0 ] . '.' . $milliseconds;
		}

		if ( $time < 0 ) {
			return '';
		}

		return $time;
	}

	/**
	 * iRacing time formatter.
	 * iRacing uses odd time format in 1/10000th seconds
	 */
	private function get_formatted_time_from_iracing( $time ) {

		// If negative, then it's because it's recording a person as a lap down, so just send it straight back
		if ( $time < 0 ) {
			return $time;
		}

		$time_in_seconds = $time / 10000;

		$milliseconds = str_replace( '0.', '', ( $time_in_seconds - (int) $time_in_seconds ) );
		$milliseconds = substr( $milliseconds, 0, 4 );

		$formatted_time = gmdate( 'H:i:s', $time_in_seconds ) . '.' . $milliseconds;

		return $formatted_time;
	}

}