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

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',              array( $this, 'init' ) );
		add_action( 'cmb2_admin_init',   array( $this, 'events_metaboxes' ) );
		add_action( 'template_redirect', array( $this, 'set_event_data' ) );

		add_filter( 'the_content',            array( $this, 'add_extra_content' ) );
		add_filter( 'src_featured_image_url', array( $this, 'filter_featured_image_url' ) );
		add_filter( 'src_featured_title',     array( $this, 'filtered_featured_title' ) );
		add_filter( 'the_content',            array( $this, 'add_results' ), 9 );

		// iRacing results uploader
		add_action( 'add_meta_boxes', array( $this, 'results_upload_metabox' ) );
		add_action( 'save_post',      array( $this, 'results_upload_save' ), 10, 2 );
		add_action( 'post_edit_form_tag', array( $this, 'update_form_enctype' ) );

	}

	/**
	 * When on event, use tracks featured image.
	 *
	 * @string  string  $image_url  The featured image URL
	 * @return  string  The modified image URL
	 */
	public function filter_featured_image_url( $image_url ) {

		if ( 'event' === get_post_type() ) {
			$image_url = get_the_post_thumbnail_url( $this->event['current_round']['track'], 'full' );
		}

		return $image_url;
	}

	/**
	 * When on event, use tracks featured title.
	 *
	 * @string  string  $title   The featured title
	 * @return  string  The modified featured title
	 */
	public function filtered_featured_title( $title ) {

		if ( 'event' === get_post_type() ) {
			$title = __( 'Round', 'src' ) . ' ' . $this->event['round_number'] . ': ' . $this->event['season_name'];
			$title .= "\n";
			$title .= $this->event['current_round']['track_name'];
		}

		return $title;
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
				'label'              => __( 'Events', 'src' ),
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
			'name' => esc_html__( 'Season', 'src' ),
			'id'         => 'season',
			'type'       => 'select',
			'options_cb' => 'src_get_seasons',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Track', 'src' ),
			'id'         => 'track',
			'type'       => 'select',
			'options_cb' => 'src_get_tracks',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Practice(s) Length', 'src' ),
			'id'         => 'practise_length',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Qualifying Format', 'src' ),
			'id'         => 'qualifying_format',
			'type'       => 'select',
			'options_cb' => array( $this, 'qualifying_formats' ),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Qualifying Grid', 'src' ),
			'id'         => 'qualifying_grid',
			'type'       => 'select',
			'options_cb' => array( $this, 'qualifying_grid' ),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Race(s) Length', 'src' ),
			'id'         => 'race_length',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Date', 'src' ),
			'id'   => 'date',
			'type' => 'text_date_timestamp',
		) );

		foreach ( $this->event_types() as $name => $desc ) {

			$cmb->add_field( array(
				'name' => esc_html( $name ) . ' time',
				'desc' => esc_html( $desc ) . ' time',
				'id'   => $slug . '_' . sanitize_title( $name ) . '_timestamp',
				'type' => 'text_time',
				'time_format' => 'H:i', // Set to 24hr format
			) );

		}

	}

	public function qualifying_formats() {
		return array(
			'5min' => esc_html__( '15 min shared track', 'src' ),
			'15min' => esc_html__( '15 min shared track', 'src' ),
			'1lap' => esc_html__( 'Two lap solo', 'src' ),
			'2lap' => esc_html__( 'Two lap solo', 'src' ),
			'4lap' => esc_html__( 'Two lap solo', 'src' ),
		);
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
		}

		// Get all events from that season
		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',

			'meta_key'               => 'season',
			'meta_value'             => $season_id,

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
$content = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx';
		if ( 'event' !== get_post_type() ) {
			return $content;
		}

		$date = date( get_option( 'date_format' ), $this->event['current_round']['date'] );

		$track_logo = $this->event['current_round']['track_logo'];
		$track_logo_image = wp_get_attachment_image_src( $track_logo, 'src-three' );
		if ( isset( $track_logo_image[0] ) ) {
			$track_logo_image_url = $track_logo_image[0];
		}

		$track_url = get_permalink( $this->event['current_round']['track'] );

		$html = '
		<div id="sidebar">
';

		if ( isset( $track_logo_image_url ) ) {
			$html .= '
			<a href="' . esc_url( $track_url ) . '">
				<img style="width:100%;" src="' . esc_url( $track_logo_image_url ) . '" />
			</a>';
		}

		$html .= '
			<p>
				<strong>' . esc_html( $date ) . '</strong>
			</p>';

		foreach ( $this->event_types() as $name => $desc ) {
			$html .= '<p>';
			$meta_key = 'event_' . sanitize_title( $name ) . '_timestamp';
			$time = get_post_meta( get_the_ID(), $meta_key, true );
			if ( '' !== $time ) {

				$extra_session_info = '';
				$length = '';
				if ( 'Qualifying' === $name ) {
					$qualf = get_post_meta( get_the_ID(), 'qualifying_format', true );
					if ( '' !== $qualf ) {
						$length = $this->qualifying_formats()[$qualf];
					}
				} else if ( 'Practice' === $name ) {
					$length = get_post_meta( get_the_ID(), 'practise_length', true );
				} else {
					$length = get_post_meta( get_the_ID(), 'race_length', true  );

					if ( 'Race 2' === $name ) {
						if ( 'reversed' === get_post_meta( get_the_ID(), 'qualifying_grid', true ) ) {
							$extra_session_info .= 'Reversed grid';
						}
					}

				}

				$html .= '<strong>' . esc_html( $desc ) . '</strong><br />Start time: ' . esc_html( $time ) . ' GMT';
				if ( '' !== $length ) {
					$html .= '<br />Length: ' . esc_html( $length );
				}
				$html .= '<br />' . $extra_session_info;
			}
			$html .= '</p>';
		}

		$season_id = get_post_meta( get_the_ID(), 'season', true );
		$html .= '
		</div>';

		// If no content is present, then lets auto-generate it ... 
		if ( '' === $content ) {

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
			if ( 0 < $race_count ) {
				$suffix = 's';

				// Add text for reversed grid races.
				$qualifying_grid = '';
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
			$content .= wpautop(
				sprintf(
					'Round %s of %s in <a href="%s">%s</a> of the <a href="https://undycar.com/">Undycar Series</a> will be held on %s at the %s long <a href="%s">%s</a> %s track in %s. Qualifying begins at %s, followed by %s %s race%s.%s',
					esc_html( $this->event['round_number'] ),
					esc_html( $this->event['number_of_rounds_in_season'] ),
					esc_html( get_permalink( $this->event['season_id'] ) ),
				 	esc_html( get_the_title( $season_id ) ),
					esc_html( $date ),
					esc_html( get_post_meta( get_the_ID(), 'race_length', true ) ),
					esc_url( $track_url ),
					esc_html( $this->event['current_round']['track_name'] ),
					esc_html( $this->event['current_round']['track_type'] ),
					esc_html( src_get_countries()[ $this->event['current_round']['track_country'] ] ),
					esc_html( get_post_meta( get_the_ID(), 'event_qualifying_timestamp', true ) ),
					$number->format( $race_count ),
					esc_html( get_post_meta( get_the_ID(), 'race_length', true ) ),
					$suffix,
					$qualifying_grid
				)
			);

		}

		$content = $html . $content;



		$track_map = $this->event['current_round']['track_map'];
		$track_map_image = wp_get_attachment_image_src( $track_map, 'large' );
		if ( isset( $track_map_image[0] ) ) {
			$track_map_image_url = $track_map_image[0];
		}

		$content .= '
		<img src="' . $track_map_image_url . '" />
		';


		// Next/Previous race navigation buttons
		$content .= '<div id="next-prev-buttons">';
		if ( isset( $this->event['previous_round'] ) && false !==  $this->event['previous_round'] ) {
			$url = get_permalink( $this->event['previous_round']['id'] );
			$content .= '<a href="' . esc_url( $url ) . '" class="button alignleft">&laquo; ' . esc_html__( 'Last race', 'src' ) . '</a>';
		}

		if ( isset( $this->event['next_round'] ) && false !== $this->event['next_round'] ) {
			$url = get_permalink( $this->event['next_round']['id'] );
			$content .= '<a href="' . esc_url( $url ) . '" class="button alignright">' . esc_html__( 'Next race', 'src' ) . '&raquo;</a>';
		}
		$content .= '</div>';

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
		echo '
		<p>
			<input type="file" name="result-file" />
			<input type="hidden" id="result-nonce" name="result-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">
		</p>
		<p>
			' . print_r(
					get_post_meta( get_the_ID(), json_decode( '_results' ), true ),
					true
				) . '

		</p>';
	}

	/**
	 * Save results upload save.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function results_upload_save( $post_id, $post ) {


		// Only save if correct post data sent
		if ( isset( $_FILES['result-file']['tmp_name'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['result-nonce'], __FILE__ ) ) {
				return;
			}

			// Get the file and split it into rows
			$temp_file =  $_FILES['result-file']['tmp_name'];
			$csv = file_get_contents( $temp_file );
			$rows = explode( "\n", $csv );


			$column_labels = $rows[3];

			unset( $rows[0] );
			unset( $rows[1] );
			unset( $rows[2] );
			unset( $rows[3] );

			$columns_to_keep = array(
				7 => 'name',
				8 => 'start_pos',
				9 => 'car_no',
				11 => 'out',
				12 => 'interval',
				13 => 'laps_led',
				14 => 'qual_time',
				15 => 'avg_lap_time',
				16 => 'fastest_lap_time',
				17 => 'fastest_lap',
				18 => 'laps-completed',
				19 => 'incidents',
			);

			$results = array();
			foreach ( $rows as $key => $row ) {
				$row = str_replace( '"', '', $row );

				$driver_result = array();
				$row_array = explode( ',', $row );
				foreach ( $row_array as $column_number => $cell ) {

					if ( isset( $columns_to_keep[$column_number] ) ) {

						$column_name = $columns_to_keep[$column_number];
						$results[ $row_array[0] ][$column_name] = utf8_encode( $cell );

					}

				}

			}

			$results = json_encode( $results );
			update_post_meta( $post_id, '_results', $results );
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
	 * @param  string  $content  The page content
	 * @return string  The modified page content
	 */
	public function add_results( $content ) {
		$html = '';

		$results = get_post_meta( get_the_ID(), '_results', true );		

		if ( '' === $results ) {
			return $content;
		}

		$results = json_decode( $results, true );
		if ( empty( $results ) ) {
			return $content;
		}


		$html .= '<h3>' . esc_html__( 'Results table', 'src' ) . '</h3>';
		$html .= '<table>';

		$html .= '<thead><tr>';


		$columns_to_keep = array(
			'Name',
			'Start',
			'Car',
			'Out',
			'Interval',
			'Laps led',
			'Qual',
			'Avg lap',
			'Fastest lap',
			'fastest lap',
			'laps compl',
			'Inc',
		);

		$html .= '<th>' . esc_html__( 'Pos', 'src' ) . '</th>';			
		foreach ( $columns_to_keep as $key => $label ) {
			$html .= '<th>' . esc_html( $label ) . '</th>';			
		}

		$html .= '</thead>';
		$html .= '<tbody>';

		foreach ( $results as $key => $result ) {
			$html .= '<tr>';
			$html .= '<td>' . esc_html( $key ) . '</td>';

			foreach ( $result as $k => $cell ) {
				$html .= '<td>' . esc_html( $cell ) . '</td>';
			}

			$html .= '</tr>';
		}

		$html .= '</tbody>';

		$html .= '</table>';

		return $content . $html;
	}

}
