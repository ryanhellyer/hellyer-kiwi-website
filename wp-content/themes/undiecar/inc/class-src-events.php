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
	 * @param  array $mime_types   The allowed mime types.
	 * @return array modified mime types
	 */
	public function allow_setup_uploads( $mime_types ) {
		$mime_types['sto']  = 'application/octet-stream';
		$mime_types['json'] = 'application/json';
		$mime_types['zip']  = 'application/zip';

		return $mime_types;
	}

	/**
	 * When on event, use tracks featured image.
	 *
	 * @string  string  $image_url  The featured image URL
	 * @return  string  The modified image URL
	 */
	public function filter_featured_image_url( $image_url ) {

		if ( 'event' === get_post_type() && ! has_post_thumbnail() ) {
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
				'label'              => esc_html__( 'Events', 'undiecar' ),
				'supports'           => array( 'title', 'editor', 'thumbnail' ),
				'menu_icon'          => 'dashicons-flag',
			)
		);

	}

	/**
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function events_metaboxes() {
		$slug = 'event';

		$event_id = null;
		if ( isset( $_GET[ 'post' ] ) ) {
			$event_id = $_GET[ 'post' ];
		} else if ( isset( $_POST[ 'post_ID' ] ) ) {
			$event_id = $_POST[ 'post_ID' ];
		}

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Event Information', 'undiecar' ),
			'object_types' => array( 'event', ),
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Season', 'undiecar' ),
			'id'         => 'season',
			'type'       => 'select',
			'options_cb' => 'src_get_seasons',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Track', 'undiecar' ),
			'id'         => 'track',
			'type'       => 'select',
			'options_cb' => 'src_get_tracks',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Time of day', 'undiecar' ),
			'id'         => 'time_of_day',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Date', 'undiecar' ),
			'id'   => 'date',
			'type' => 'text_date_timestamp',
		) );

		$cmb->add_field( array(
			'name' => 'Free practice time',
			'id'   => 'fp1_time',
			'type' => 'text_time',
			'time_format' => 'H:i', // Set to 24hr format
		) );

		$cmb->add_field( array(
			'name' => 'Free practice length',
			'id'   => 'fp1_length',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => 'Qualifying time',
			'id'   => 'qualifying_time',
			'type' => 'text_time',
			'time_format' => 'H:i', // Set to 24hr format
		) );

		$cmb->add_field( array(
			'name' => 'Qualifying format',
			'id'   => 'qualifying_format',
			'type' => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Number of races', 'undiecar' ),
			'id'   => 'number_of_races',
			'type' => 'text',
			'default' => '1',
			'attributes' => array(
				'type' => 'number',
				'pattern' => '\d*',
			),
			'sanitization_cb' => 'absint',
			'escape_cb'       => 'absint',
		) );

		$number_of_races = get_post_meta( $event_id, 'number_of_races', true );
		$number_of_races = absint( $number_of_races );
		$number = 1;
		while ( $number <= $number_of_races ) {
			if ( 1 !== $number_of_races ) {
				$name = __( 'Race #', 'undiecar' ) . $number;
			} else {
				$name = __( 'Race', 'undiecar' );
			}

			$cmb->add_field( array(
				'name' =>  'Race #' . $number . ' time',
				'desc'   => 'race_' . $number . '_time',
				'id'   => 'race_' . $number . '_time',
				'type' => 'text_time',
				'time_format' => 'H:i', // Set to 24hr format
			) );

			$cmb->add_field( array(
				'name' => 'Race #' . $number . ' length',
				'desc'   => 'race_' . $number . '_length',
				'id' => 'race_' . $number . '_length',
				'type' => 'text',
			) );

			$cmb->add_field( array(
				'name' => 'Race #' . $number . ' points mulitiplier',
				'desc'   => 'race_' . $number . '_points_multiplier',
				'id' => 'race_' . $number . '_points_multiplier',
				'type' => 'text',
				'default' => '1.0',
				'attributes' => array(
					'type' => 'number',
					'pattern' => '\d*',
				),
			) );

			$number++;
		}

		// Setup files for each car
		$query = new WP_Query( array(
			'post_type'      => 'car',
			'posts_per_page' => 100
		) );
		if ( $query->have_posts() ) {

			$cars = array();
			$count = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				if ( 'on' === get_post_meta( $event_id, 'car-' . get_the_ID(), true ) ) {
					$count++;

					$cmb->add_field( array(
						'name'        => sprintf( esc_html__( 'Setup file for %s', 'undiecar' ), get_the_title( get_the_ID() ) ),
						'description' => esc_html__( 'Please note that due to a technical glitch, you may need to upload the file via the media upload section, then just select it here. Leave blank if open setups allowed.', 'undiecar' ),
						'id'          => 'setup_file_' . get_the_ID(),
						'type'        => 'file',
					) );

					$cmb->add_field( array(
						'name'        => sprintf( esc_html__( 'Setup default for %s', 'undiecar' ), get_the_title( get_the_ID() ) ),
						'description' => esc_html__( 'This is just a text fallback for when no fixed setup is available.', 'undiecar' ),
						'id'          => 'setup_default_' . get_the_ID(),
						'type'        => 'text',
					) );

					$cmb->add_field( array(
						'name' => esc_html__( 'Fuel amount', 'undiecar' ),
						'id'         => 'fuel_amount_' . get_the_ID(),
						'type'       => 'text',
					) );

				}

			}

			wp_reset_postdata();
		}

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
			'normal'   => esc_html__( 'Normal', 'undiecar' ),
			'reversed' => esc_html__( 'Reversed', 'undiecar' ),
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
			'posts_per_page'         => 1000,
			'post_type'              => 'event',

			'meta_key'               => 'season',
			'meta_value'             => $season_id,
			'post_status'            => 'publish',
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

		$next_round = $previous_round = $current_round = false;

		$round_number = '';
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

		$date = get_post_meta( get_the_ID(), 'date', true );
		if ( '' !== $date ) {
			$date = date( get_option( 'date_format' ), $date );
		}

		// Add track logo
		$track_logo = $this->event['current_round']['track_logo'];
		$track_logo_image = wp_get_attachment_image_src( $track_logo, 'src-three' );
		if ( isset( $track_logo_image[0] ) ) {
			$track_logo_image_url = $track_logo_image[0];
		}

		$track_url = get_permalink( $this->event['current_round']['track'] );

		$sidebar_html = '
		<div id="sidebar">
';

		// Sidebar track logo
		if ( isset( $track_logo_image_url ) ) {
			$sidebar_html .= '
			<a href="' . esc_url( $track_url ) . '">
				<img style="width:100%;" src="' . esc_url( $track_logo_image_url ) . '" />
			</a>';
		}

		// Sidebar date
		$date_timestamp = get_post_meta( get_the_ID(), 'date', true );
		if ( '' !== $date_timestamp ) {
			$date = date( get_option( 'date_format' ), $date_timestamp );
			$sidebar_html .= '
			<p>
				<strong>' . esc_html( $date ) . '</strong>
			</p>';
		}

		// Sidebar event info
		$sidebar_html .= '
			<p>';
		$season_id = get_post_meta( get_the_ID(), 'season', true );
		if ( '' !== $season_id ) {
			$sidebar_html .= '
				<strong>' . esc_html( get_the_title( $season_id ) ) . '</strong>';
		}

		$sidebar_html .= '
			</p>';

		// Sidebar practice times
		$time = get_post_meta( get_the_ID(), 'fp1_time', true );
		if ( '' !== $time ) {
			$sidebar_html .= '
			<p>
				<strong>Free Practice</strong>
				<br />
				Start time: ' . esc_html( $time ) . ' GMT';

			$length = get_post_meta( get_the_ID(), 'fp1_length', true );
			if ( '' !== $length ) {
				$sidebar_html .= '
				<br />
				Length: ' . esc_html( $length );
			}

			$sidebar_html .= '
			</p>';
		}

		// Sidebar qualifying times
		$time = get_post_meta( get_the_ID(), 'qualifying_time', true );
		if ( '' !== $time ) {
			$sidebar_html .= '
			<p>
				<strong>Qualifying</strong>
				<br />
				Start time: ' . esc_html( $time ) . ' GMT';

			$format = get_post_meta( get_the_ID(), 'qualifying_format', true );
			if ( '' !== $format ) {
				$sidebar_html .= '
				<br />
				Format: ' . esc_html( $format );
			}

			$sidebar_html .= '
			</p>';
		}

		// Sidebar race times
		$number_of_races = get_post_meta( get_the_ID(), 'number_of_races', true );
		$number = 1;
		while ( $number <= $number_of_races ) {

			if ( '1' === $number_of_races ) {
				$title = 'Race';
			} else {
				$title = 'Race #' . $number;
			}

			$sidebar_html .= '
				<p>
					<strong>' . esc_html( $title ) . '</strong>
					<br />';

			$time = get_post_meta( get_the_ID(), 'race_' . $number . '_time', true );
			if ( '' !== $time ) {

				$sidebar_html .= '
				Start time: ' . esc_html( $time ) . ' GMT
				<br />';
			}

			$length = get_post_meta( get_the_ID(), 'race_' . $number . '_length', true );
			if ( '' !== $length ) {

				$sidebar_html .= '
				Length: ' . esc_html( $length );
			}

			$sidebar_html .= '
			</p>';

			$number++;
		}

		// sidebar game time
		$game_time_of_day = get_post_meta( get_the_ID(), 'time_of_day', true );
		if ( '' !== $game_time_of_day ) {
			$sidebar_html .= '
			<p>
				<strong>Game time: </strong>
				<br />
				' . esc_html( $game_time_of_day ) . '
			</p>';
		}

		$sidebar_html .= '
		</div>';


		/**
		 * Generate event description.
		 */

		// Count up how many races there are
		$race_count = 0;

		if ( '' !== get_post_meta( get_the_ID(), 'race_1_time', true ) ) {
			$race_count++;
		}
		if ( '' !== get_post_meta( get_the_ID(), 'race_2_time', true ) ) {
			$race_count++;
		}
		if ( '' !== get_post_meta( get_the_ID(), 'race_3_time', true ) ) {
			$race_count++;
		}
		$suffix = '';
		$qualifying_grid = '';
		if ( 1 < $race_count ) {
			$suffix = 's';

			// Add text for reversed grid races.
			if ( 'reversed' === get_post_meta( get_the_ID(), 'qualifying_grid', true ) ) {
				$qualifying_grid = ' ' . esc_html__( 'The grid for race two will be reversed.', 'undiecar' );
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

		$formatted_number = str_replace( __( 'one', 'undiecar' ), __( 'the', 'undiecar' ), $number->format( $race_count ) );
		$q_time = get_post_meta( get_the_ID(), 'event_qualifying_timestamp', true ); // legacy
		if ( '' === $q_time ) {
			$q_time = get_post_meta( get_the_ID(), 'qualifying_time', true );
		}

		// Work out past/future strings
		$date_timestamp = get_post_meta( get_the_ID(), 'date', true );

		$time = strtotime( date( 'Y-m-d', $date_timestamp ) . ' ' . $q_time ) + HOUR_IN_SECONDS * 2;

		if ( time() < $time ) {
			$will_be = esc_html__( 'will be', 'undiecar' );
			$begins = esc_html__( 'begins', 'undiecar' );
		} else {
			$will_be = esc_html__( 'was', 'undiecar' );
			$begins = esc_html__( 'began', 'undiecar' );
		}

		if ( __( 'Special Events', 'undiecar' ) === get_the_title( $this->event['season_id'] ) ) {
			$html .= wpautop(
				sprintf(
					__( 'This event %s held on %s %s at the %s long <a href="%s">%s</a> track in %s. Qualifying %s at %s GMT, followed by %s %s race%s.%s', 'undiecar' ),
					$will_be,
					esc_html( date( 'l', $this->event['current_round']['date'] ) ), // Day of week
					esc_html( $date ),
					esc_html( get_post_meta( $this->event['current_round']['track'], 'track_length', true ) ) . ' km',
					esc_url( $track_url ),
					esc_html( $this->event['current_round']['track_name'] ),
					//'', // Removed as was repetitive after already mentioning track type in track name sometimes esc_html( $this->event['current_round']['track_type'] ),
					esc_html( src_get_countries()[ $this->event['current_round']['track_country'] ] ),
					$begins,
					esc_html( $q_time ),
					$formatted_number,
					esc_html( get_post_meta( get_the_ID(), 'race_length', true ) ),
					$suffix,
					$qualifying_grid
				)
			);
		} else {

			$current_track = '';
			$countries = src_get_countries();
			if ( isset( $countries[ $this->event['current_round']['track'] ] ) ) {
				$current_track = $countries[ $this->event['current_round']['track'] ];
			}

			$current_track_country = '';
			if ( isset( $this->event['current_round']['track_country'] ) ) {
				$current_track_country = $this->event['current_round']['track_country'];
			}

			$html .= wpautop(
				sprintf(
					__( 'Round %s of %s in <a href="%s">%s</a> of the Undiecar Championship %s held on %s %s at the %s long <a href="%s">%s</a> %s track in %s. Qualifying %s at %s GMT, followed by %s %s race%s.%s', 'undiecar' ),
					esc_html( $this->event['round_number'] ),
					esc_html( $this->event['number_of_rounds_in_season'] ),
					esc_url( get_permalink( $this->event['season_id'] ) ),
					esc_html( get_the_title( $season_id ) ),
					$will_be,
					esc_html( date( 'l', $this->event['current_round']['date'] ) ), // Day of week
					esc_html( $date ),
					get_post_meta( $this->event['current_round']['track'], 'track_length', true ) . ' km',
					esc_url( $track_url ),
					esc_html( $this->event['current_round']['track_name'] ),
					'', // Removed as was repetitive after already mentioning track type in track name sometimes esc_html( $this->event['current_round']['track_type'] ),
					esc_html( $current_track_country ),
					$begins,
					esc_html( $q_time ),
					$formatted_number,
					esc_html( get_post_meta( get_the_ID(), 'race_length', true ) ),
					$suffix,
					$qualifying_grid
				)
			);
		}

		// Add track map
		$track_map = $this->event['current_round']['track_map'];
		$track_map_image = wp_get_attachment_image_src( $track_map, 'large' );
		$map_html = '';
		if ( isset( $track_map_image[0] ) ) {
			$track_map_image_url = $track_map_image[0];

			$map_html = '
			<p>&nbsp;</p><!-- crude spacing hack -->
			<img class="event-image" src="' . $track_map_image_url . '" />
			';
		}

		// Next/Previous race navigation buttons
		$nav_html = '<div id="next-prev-buttons">';
		if ( isset( $this->event['previous_round'] ) && false !==  $this->event['previous_round'] ) {
			$url = get_permalink( $this->event['previous_round']['id'] );
			$nav_html .= '<a href="' . esc_url( $url ) . '" class="button alignleft">&laquo; ' . esc_html__( 'Last race', 'undiecar' ) . '</a>';
		}

		if ( isset( $this->event['next_round'] ) && false !== $this->event['next_round'] ) {
			$url = get_permalink( $this->event['next_round']['id'] );
			$nav_html .= '<a href="' . esc_url( $url ) . '" class="button alignright">' . esc_html__( 'Next race', 'undiecar' ) . '&raquo;</a>';
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

				$url = home_url() . '/' . __( 'member', 'undiecar' ) . '/' . sanitize_title( $driver ) . '/';
				$least_incidents_text .= '<a href="' . esc_url( $url ) . '">' . esc_html( $driver ) . '</a>';
			}
		}

		$bonus_points = '';
		if (
			'' !== $least_incidents_text
			||
			'' !== get_post_meta( get_the_ID(), '_pole_position', true )
			||
			'' !== get_post_meta( get_the_ID(), '_fastest_lap', true )
		) {
/*
REMOVED BECAUSE THEY ONLY APPLY TO THE FIRST RACE (I THINK)
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
				Pole position: <a href="' . esc_url( home_url() . '/' . __( 'member', 'undiecar' ) . '/' . sanitize_title( $name ) . '/' ) . '">' . esc_html( $name ) . '</a>
				<br />';
			}

			if ( '' !== get_post_meta( get_the_ID(), '_fastest_lap', true ) ) {
				$name = get_post_meta( get_the_ID(), '_fastest_lap', true );
				$bonus_points .= '
				Fastest lap: <a href="' . esc_url( home_url() . '/' . __( 'member', 'undiecar' ) . '/' . sanitize_title( $name ) . '/' ) . '">' . esc_html( $name ) . '</a>';
			}

			$bonus_points .= '
			</p>';
*/
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

		// Available cars
		$content = '';
		if ( 0 === $count ) {
			// No cars, so bail now
			return;
		} else if ( 1 === $count ) {
			$content .= '<h3>' . esc_html__( 'Allowed car', 'undiecar' ) . '</h3>';
			$car_id = $cars[0];

			$content .= '<p>';
			$content .= '<strong><a href="' . esc_url( get_the_permalink( $car_id ) ) . '">' . esc_html( get_the_title( $car_id ) ) . '</a></strong>';

			$setup = get_post_meta( $event_id, 'setup_default_' . $car_id, true );
			$setup_file = get_post_meta( $event_id, 'setup_file_' . $car_id, true );
			if ( '' !== $setup_file ) {
				$setup = '<a href="' . esc_url( $setup_file ) . '">' . esc_html( 'Download fixed setup', 'undiecar' ) . '</a>';
			}
			$content .= '<br />'.  $setup;

			$fuel_amount = get_post_meta( $event_id, 'fuel_amount_' . $car_id, true );
			if ( '' !== $fuel_amount ) {
				$content .= '<br />' . sprintf( esc_html( 'Fuel limited to %s' ), esc_html( $fuel_amount ) );
			}
			$content .= '</p>';

		} else {
			$content .= '<h3>' . esc_html__( 'Allowed cars', 'undiecar' ) . '</h3>';
			$content .= '<p>' . esc_html__( 'This is a multi-car event. Drivers may choose one of the following cars.', 'undiecar' ) . '</p>';
			$content .= '<ol>';

			foreach ( $cars as $car_id ) {

				$setup = get_post_meta( $event_id, 'setup_default_' . $car_id, true );
				$setup_file = get_post_meta( $event_id, 'setup_file_' . $car_id, true );
				if ( '' !== $setup_file ) {
					$setup = '<a href="' . esc_url( $setup_file ) . '">' . esc_html( 'Download fixed setup', 'undiecar' ) . '</a>';
				}
				$setup = '<br />'.  $setup;

				$fuel_amount = get_post_meta( $event_id, 'fuel_amount_' . $car_id, true );
				if ( '' !== $fuel_amount ) {
					$fuel_amount = '<br />' . sprintf( esc_html( 'Fuel limited to %s' ), esc_html( $fuel_amount ) );
				}

				$content .= '<li><a href="' . esc_url( get_the_permalink( $car_id ) ) . '">' . esc_html( get_the_title( $car_id ) ) . '</a> ' . $setup . $fuel_amount . '</li>';
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
			__( 'Upload iRacing results', 'undiecar' ), // Title
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
			<label for="result-qual-file">' . esc_html__( 'Qualifying results', 'undiecar' ) . '</label>
			<input type="file" id="result-qual-file" name="result-qual-file" />
		</p>
		<p>
			<label for="result-1-file">' . esc_html__( 'Race 1 results', 'undiecar' ) . '</label>
			<input type="file" id="result-1-file" name="result-1-file" />
		</p>
		<p>
			<label for="result-2-file">' . esc_html__( 'Race 2 results', 'undiecar' ) . '</label>
			<input type="file" id="result-2-file" name="result-2-file" />
		</p>
		<p>
			<label for="result-3-file">' . esc_html__( 'Race 3 results', 'undiecar' ) . '</label>
			<input type="file" id="result-3-file" name="result-3-file" />
		</p>
		<input type="hidden" id="result-nonce" name="result-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">
		<p>';
*/

		echo '<style>.undiecar-info {color:#999;font-family:monospace;font-size:9px;line-height:9px;width:100%;height:100px;}</style>';


		$event_id = null;
		if ( isset( $_GET[ 'post' ] ) ) {
			$event_id = $_GET[ 'post' ];
		} else if ( isset( $_POST[ 'post_ID' ] ) ) {
			$event_id = $_POST[ 'post_ID' ];
		}
		$number_of_races = get_post_meta( $event_id, 'number_of_races', true );
		$number_of_races = absint( $number_of_races );
		$number = 1;
		while ( $number <= $number_of_races ) {

			if ( 1 === $number ) {
				echo '<p style="word-wrap: break-word;">' . sprintf( esc_html( 'Copy/paste the contents of the page at %s to upload event results.', 'undiecar' ), '<a href="http://members.iracing.com/membersite/member/GetSubsessionResults?subsessionID=XXX">http://members.iracing.com/membersite/member/GetSubsessionResults?subsessionID=XXX</a>' ) . '</p>';
			}

			echo '
			<p>
				<label for="' . esc_attr( 'result-' . $number ) . '">' . sprintf( esc_html__( 'Race %s results', 'undiecar' ), absint( $number ) ) . '</label>
				<textarea id="' . esc_attr( 'result-' . $number ) . '" name="' . esc_attr( 'result-' . $number ) . '"></textarea>
			</p>';

			$number++;
		}

		echo '
		<input type="hidden" id="result-nonce" name="result-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">

		<p>';
		$number_of_races = get_post_meta( $event_id, 'number_of_races', true );
		$number_of_races = absint( $number_of_races );
		$number = 1;
		while ( $number <= $number_of_races ) {
			echo '
			<textarea class="undiecar-info">' . 
				print_r(
					json_decode( get_post_meta( get_the_ID(), '_results_' . $number, true ), true ),
					true
				) . 
			'</textarea>';

			$number++;
		}
		echo '</p>';


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

		$number_of_races = get_post_meta( $post_id, 'number_of_races', true );
		$number_of_races = absint( $number_of_races );
		$race_number = 0;
		while ( $race_number <= $number_of_races ) {
			$race_number++;

			// Bail out if no result found
			if ( ! isset( $_POST[ 'result-' . $race_number ] ) || '' === $_POST[ 'result-' . $race_number ] ) {
				continue;
			}

			// Get data from results
			$results = stripslashes( $_POST[ 'result-' . $race_number ] );
			$results = json_decode( $results );

			$drivers = array();
			foreach ( $results->rows as $key => $row ) {
				$driver_name = urldecode( $row->displayname );
				$driver_name = str_replace( '+', ' ', $driver_name );
				$car_name = str_replace( '+', ' ', $row->ccName );

				if ( 'RACE' === $row->simsesname ) {
					$start_pos = absint( $row->startpos ) + 1;
					$drivers[ $driver_name ]['start_pos'] = absint( $start_pos );
				}

				$drivers[ $driver_name ]['name']      = esc_html( $driver_name );
				$drivers[ $driver_name ]['car']       = esc_html( $car_name );

				if ( 'QUALIFY' === $row->simsesname ) {
					$qual_time = $this->get_formatted_time_from_iracing( $row->bestquallaptime );

					$drivers[ $driver_name ]['qual_time'] = esc_html( $qual_time );
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

						// Updating car number.
						$current_number = get_user_meta( $user_id, 'car_number', true );
						if ( '' === $current_number || '0' === $current_number ) {
							update_user_meta( $user_id, 'car_number', $car_no );
						}

						// Updating suit design.
						$stored_design = get_user_meta( $user_id, 'suit_design', true );
						$new_design    = esc_html( $row->suit_pattern . ',' . $row->suit_color1 . ',' . $row->suit_color2 . ',' . $row->suit_color3 );
						if ( $new_design !== $stored_design ) {
							update_user_meta( $user_id, 'suit_design', $new_design );
						}

						// Updating car design.
						$stored_design = get_user_meta( $user_id, 'car_design', true );
						$new_design    = esc_html( $row->car_pattern . ',' . $row->car_color1 . ',' . $row->car_color2 . ',' . $row->car_color3 );
						if ( $new_design !== $stored_design ) {
							update_user_meta( $user_id, 'car_design', $new_design );
						}

						// Updating helmet design.
						$stored_design = get_user_meta( $user_id, 'helmet_design', true );
						$new_design    = esc_html( $row->helm_pattern . ',' . $row->helm_color1 . ',' . $row->helm_color2 . ',' . $row->helm_color3 );
						if ( $new_design !== $stored_design ) {
							update_user_meta( $user_id, 'helmet_design', $new_design );
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
					if ( isset( $drivers[ $driver_name ] ) && is_array( $drivers[ $driver_name ] ) ) {
						$drivers[ $driver_name ] = array_merge( $drivers[ $driver_name ], $x );
					} else {
						$drivers[ $driver_name ] = $x; // Not sure why this would be required, coz it's missing the data from above in this scenario ...
					}

				}

			}

			$results = array();
			foreach ( $drivers as $key => $driver ) {
				$new_key = $driver[ 'position' ] - 1;
				$results[ $new_key ] = $driver;
			}

			ksort( $results );
			$results = json_encode( $results, JSON_UNESCAPED_UNICODE );
			update_post_meta( $post_id, '_results_' . $race_number, $results );
		}

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

		$number_of_races = get_post_meta( get_the_ID(), 'number_of_races', true );
		$number_of_races = absint( $number_of_races );
		$race_number = 0;
		while ( $race_number <= $number_of_races ) {
			$race_number++;

			$results = get_post_meta( get_the_ID(), '_results_' . $race_number, true );		

			if ( '' === $results ) {
				continue;
			}

			$results = json_decode( $results, true );
			if ( empty( $results ) ) {
				continue;
			}

			if ( 1 !== $number_of_races ) {
				$title =__( 'Results table - Race #' . $race_number, 'undiecar' );
			} else {
				$title =__( 'Results table', 'undiecar' );
			}

			$html .= '<h3 class="table-heading">' . esc_html( $title ) . '</h3>';
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

			$columns_to_keep = array();
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

			$html .= '<th>' . esc_html__( 'Pos', 'undiecar' ) . '</th>';			
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
				$time_of_day  = get_post_meta( get_the_ID(), 'race_1_time', true );
				$time = strtotime( date( 'Y-m-d', $date ) . ' ' . $time_of_day ) + HOUR_IN_SECONDS * 2;

				if ( time() < $time ) {
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
			'posts_per_page'         => 1000,
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
				$time_of_day  = get_post_meta( get_the_ID(), 'race_1_time', true );
				$time = strtotime( date( 'Y-m-d', $date ) . ' ' . $time_of_day ) + HOUR_IN_SECONDS * 2 ;

				if ( time() > $time ) {
					$events[ $time ] = get_the_ID();
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

		$exploded = explode( ':', $time );
		$count = count( $exploded ) - 1;
		if ( isset( $exploded[ $count ] ) ) {
			$decimal = round( $exploded[ $count ], 2 );
			$exploded[$count] = $decimal;
		}
		$time = implode( ':', $exploded );

		if ( $time < 0 ) {
			return '';
		}

		return $time;
	}

	/**
	 * iRacing time formatter.
	 * iRacing uses odd time format in 1/10000th seconds
	 */
	protected function get_formatted_time_from_iracing( $time ) {

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
