<?php

/**
 * Available Cars.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Available_Cars extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		add_shortcode( 'src-available-cars',   array( $this, 'available_cars_shortcode' ) );

		// Add action hooks
		add_action( 'init',            array( $this, 'init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'events_metaboxes' ) );
		add_action( 'add_meta_boxes',  array( $this, 'add_metaboxes' ) );
		add_action( 'save_post',       array( $this, 'meta_boxes_save' ), 10, 2 );

	}

	public function available_cars_shortcode() {

$season_slug = 3;

		// Form array of available car information
		$available_cars = array();
		foreach ( src_get_available_cars( $season_slug ) as $key => $car ) {
			$available_cars[$key]['manufacturer'] = $car['manufacturer'];
			$available_cars[$key]['model']        = $car['model'];
			$available_cars[$key]['available'] = 2;

			foreach ( src_get_drivers( $season_slug ) as $x => $driver ) {

				if ( $car['manufacturer'] . ' ' . $car['model'] === $driver[3] ) {
					$available_cars[$key]['taken_by'] = $driver[4];
					$available_cars[$key]['available'] = src_how_many_spots_left_in_team( $season_slug, $driver[4] );
				}

			}

		}

		$content = '<table>
		<tr>
			<th></th>
			<th>Manufacturer</th>
			<th>Model</th>
			<th>Remaining</th>
		</tr>';
		$count = 0;
		foreach ( $available_cars as $key => $car ) {
			$count++;

			$strike = '';

			if ( 0 === $car['available'] ) {
				$strike = '<s>';
			}

			$content .= '<tr>';

			$content .= '<td>' . $strike . absint( $count ) . $strike . '</td>';
			$content .= '<td>' . $strike . esc_html( $car['manufacturer'] ) . $strike . '</td>';
			$content .= '<td>' . $strike . esc_html( $car['model'] ) . $strike . '</td>';

			$content .= '<td>' . $strike . esc_html( $car['available'] ) . $strike . '</td>';

			$content .= '</tr>';
		}

		$content .= '</table>';

		return $content;
	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'cars',
			array(
				'public' => false,
				'label'  => __( 'Cars', 'src' ),
				'supports' => array( 'title' )
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
			'title'        => esc_html__( 'Events', 'src' ),
			'object_types' => array( 'season', ),
		) );

		$group_field_id = $cmb->add_field( array(
			'id'          => $slug,
			'type'        => 'group',
			'description' => esc_html__( 'Create all the events here. When only one event is specified, it will be listed as a special event on the website.', 'src' ),
			'options'     => array(
				'group_title'   => esc_html__( 'Event {#}', 'src' ), // {#} gets replaced by row number
				'add_button'    => esc_html__( 'Add Another Event', 'src' ),
				'remove_button' => esc_html__( 'Remove Event', 'src' ),
				'sortable'      => true, // beta
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'Track Name', 'src' ),
			'id'   => 'track_name',
			'type' => 'text',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'Track Country', 'src' ),
			'id'         => 'country',
			'type'       => 'select',
			'options_cb' => 'src_get_countries',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'Event Name', 'src' ),
			'description' => esc_html__( 'Usually in the format "Round 3: Laguna Seca"', 'src' ),
			'id'   => 'name',
			'type' => 'text',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'Event Description', 'src' ),
			'description' => esc_html__( 'List the length of races, and any other relevant information specific to this event.', 'src' ),
			'id'   => 'description',
			'type' => 'textarea_small',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'Event Image', 'src' ),
			'description' => esc_html__( 'This will most likely be a an image of the track.', 'src' ),
			'id'   => 'image',
			'type' => 'file',
		) );

		foreach ( $this->event_types() as $name => $desc ) {

			$cmb->add_group_field( $group_field_id, array(
				'name' => esc_html( $name ) . ' date/time',
				'desc' => esc_html( $desc ) . ' date/time',
				'id'   => $slug . '_' . sanitize_title( $name ) . '_timestamp',
				'type' => 'text_datetime_timestamp',
				'time_format' => 'H:i', // Set to 24hr format
			) );

		}

	}

	/**
	 * Add admin metaboxes. 
	 */
	public function add_metaboxes() {

		$meta_boxes = array(
			'drivers'    => __( 'Drivers', 'src' ),
			'results'    => __( 'Results Overall', 'src' ),
			'results_am' => __( 'Results AM', 'src' ),
			'weight_penalties' => __( 'Weight Penalties', 'src' ),
		);

		foreach ( $meta_boxes as $id => $title ) {
			add_meta_box(
				$id, // ID
				$title, // Title
				array(
					$this,
					$id . '_meta_box', // Callback to method to display HTML
				),
				'season', // Post type
				'normal', // Context, choose between 'normal', 'advanced', or 'side'
				'low'  // Position, choose between 'high', 'core', 'default' or 'low'
			);
		}

	}

	/**
	 * Output the example meta box.
	 */
	public function drivers_meta_box() {

		?>

		<input type="hidden" id="seasons-drivers" name="seasons-drivers" value="" />
		<input type="hidden" id="seasons-nonce" name="seasons-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>" />
		<style>
		#drivers-wrapper {
			width: 100%;
			overflow-x: scroll;
		}

		#drivers-table {
			border-spacing: 0;
			border-collapse: separate;
		}

		#drivers-table tr.first-row td {
			font-weight: bold;
		}

		#drivers-table {
			width: 100%;
			border-top: 1px solid #ddd;
			border-left: 1px solid #ddd;
		}

		#drivers-table th,
		#drivers-table td {
			text-align: left;
			border-right: 1px solid #ddd;
			border-bottom: 1px solid #ddd;
			padding: 4px 4px;
		}

		#drivers-table td span {
			display: block;
			min-height: 1rem;
		}

		.pro-class {
			background: #aaffaa;
		}

		.am-class {
			background: #ffffaa;
		}

		</style>
		<div id="drivers-wrapper">

			<table id="drivers-table">

				<tr>
					<?php /* Important - don't leave spaces, as it goofs up the saving process in deleting them */ ?>
					<th>Username</th><th>Number</th><th>Note</th><th>Car</th><th>Team</th><th>Class</th>
				</tr><?php

			$data = get_post_meta( get_the_ID(), '_seasons_drivers', true );
/*

$csv = file_get_contents( dirname( __FILE__ ) . '/3-drivers.csv' );
$rows = explode( "\n", $csv );
$data = array();
foreach ( $rows as $key => $row ) {

	$row_exploded = explode( ',', $row );

	$name = $row_exploded[1];
	$number = $row_exploded[0];
	$row_exploded[0] = $name;
	$row_exploded[1] = $number;

	$data[$key] = $row_exploded;
}
unset( $data[0] );
*/
//update_post_meta( get_the_ID(), '_seasons_drivers', $data );

			if ( is_array( $data ) ) {

				foreach ( $data as $row_number => $row ) {

					// Style AM class drivers differently
					$class = 'pro-class';
					if ( isset( $row[5] ) && 'AM' === trim( $row[5] ) ) {
						$class = 'am-class';
					}

					echo '<tr class="' . esc_attr( $class ) . '">';
					if ( isset( $row[0] ) ) {
						echo '<td><span>' . esc_html( trim( $row[0] ) ) . '</span></td>';
					}
					if ( isset( $row[1] ) ) {
						echo '<td><span>' . esc_html( trim( $row[1] ) ) . '</span></td>';
					}
					if ( isset( $row[2] ) ) {
						echo '<td><span>' . esc_html( trim( $row[2] ) ) . '</span></td>';
					}
					if ( isset( $row[3] ) ) {
						echo '<td><span>' . esc_html( trim( $row[3] ) ) . '</span></td>';
					}
					if ( isset( $row[4] ) ) {
						echo '<td><span>' . esc_html( trim( $row[4] ) ) . '</span></td>';
					}
					if ( isset( $row[5] ) ) {
						echo '<td><span>' . esc_html( trim( $row[5] ) ) . '</span></td>';
					}
					echo '</tr>';

				}

			}
			echo '<tr><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td></tr>';
			echo '<tr><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td></tr>';
			echo '<tr><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td></tr>';
			echo '<tr><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td></tr>';
			echo '<tr><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td><td><span></span></td></tr>';

			?>
			</table>
		</div>
		<script>

		(function () {

			window.addEventListener(
				'load',
				function (){

					// Making the drivers table cells editable.
					var drivers_table = document.getElementById("drivers-table").getElementsByTagName('span');
					for(i = 0; i < drivers_table.length; i++) {
						drivers_table[i].contentEditable = "true";
					}

					// Making the results table cells editable.
					var results_table = document.getElementById("results-table").getElementsByTagName('span');
					for(i = 0; i < results_table.length; i++) {
						results_table[i].contentEditable = "true";
					}

					// Making the results table cells editable.
					var resultsam_table = document.getElementById("resultsam-table").getElementsByTagName('span');
					for(i = 0; i < resultsam_table.length; i++) {
						resultsam_table[i].contentEditable = "true";
					}

					// Making the weights penalty table cells editable.
					var weight_penalties_table = document.getElementById("weight-penalties-table").getElementsByTagName('span');
					for(i = 0; i < weight_penalties_table.length; i++) {
						weight_penalties_table[i].contentEditable = "true";
					}

				}
			);

			/**
			 * Handle clicks.
			 */
			window.addEventListener(
				'click',
				function (e){

					if ( 'publish' === e.target.id ) {

						// Drivers table
						var drivers_table = document.getElementById("drivers-table");
						var seasons_drivers = document.getElementById("seasons-drivers");
						seasons_drivers.value = drivers_table.innerHTML;

						// Results table
						var results_table = document.getElementById("results-table");
						var seasons_results = document.getElementById("seasons-results");
						seasons_results.value = results_table.innerHTML;

						// Results AM table
						var resultsam_table = document.getElementById("resultsam-table");
						var seasons_resultsam = document.getElementById("seasons-resultsam");
						seasons_resultsam.value = resultsam_table.innerHTML;

						// Weights penalties table
						var weight_penalties_table = document.getElementById("weight-penalties-table");
						var seasons_weight_penalties = document.getElementById("seasons-weight-penalties");
						seasons_weight_penalties.value = weight_penalties_table.innerHTML;

					}

				}
			);
		})();

		</script><?php
	}

	/**
	 * Output the results meta box.
	 */
	public function results_meta_box() {
		?>
		<input type="hidden" id="seasons-results" name="seasons-results" value="" />

		<style>
		#results-wrapper {
			width: 100%;
			overflow-x: scroll;
		}

		#results-table {
			border-spacing: 0;
			border-collapse: separate;
		}

		#results-table tr.first-row td {
			font-weight: bold;
		}

		#results-table {
			width: 100%;
			border-top: 1px solid #ddd;
			border-left: 1px solid #ddd;
		}

		#results-table th,
		#results-table td {
			text-align: left;
			border-right: 1px solid #ddd;
			border-bottom: 1px solid #ddd;
			padding: 4px 4px;
		}

		#results-table td span {
			display: block;
			min-height: 1rem;
		}

		</style>

		<div id="results-wrapper">

			<table id="results-table">

				<tr>
					<?php /* Important - don't leave spaces, as it goofs up the saving process in deleting them */ ?>
					<th>Username</th><?php

					$events = get_post_meta( get_the_ID(), 'event', true );
					$event_counter = 0;
					if ( is_array( $events ) ) {
						foreach ( $events as $key => $event ) {

							$track_initials = strtoupper( substr( $event['track_name'], 0, 3 ) );

							if ( isset( $event['event_race-1_timestamp'] ) && '' !== $event['event_race-1_timestamp'] ) {
								echo '<th>' . esc_html( $track_initials ) . '1</th>';
								$event_counter++;
							}
							if ( isset( $event['event_race-2_timestamp'] ) && '' !== $event['event_race-2_timestamp'] ) {
								echo '<th>' . esc_html( $track_initials ) . '2</th>';
								$event_counter++;
							}
							if ( isset( $event['event_race-3_timestamp'] ) && '' !== $event['event_race-3_timestamp'] ) {
								echo '<th>' . esc_html( $track_initials ) . '3</th>';
								$event_counter++;
							}

						}

					}
					?>

				</tr><?php

			$drivers = get_post_meta( get_the_ID(), '_seasons_drivers', true );
			$results = get_post_meta( get_the_ID(), '_seasons_results', true );

			if ( is_array( $drivers ) ) {

				foreach ( $drivers as $row_number => $row ) {
					$username = $row[0];

					// Style AM class drivers differently
					$class = 'pro-class';
					if ( isset( $row[5] ) && 'AM' === trim( $row[5] ) ) {
						$class = 'am-class';
					}

					echo '<tr class="' . esc_attr( $class ) . '">';
					if ( isset( $username ) ) {
						echo '<td><span>' . esc_html( trim( $username ) ) . '</span></td>';
					}
					// Iterate through the events
					for ( $x = 1; $x <= $event_counter; $x++ ) {

						if ( isset( $results[$username][$x] ) ) {
							$race = $results[$username][$x];
						} else {
							$race = '';
						}

						echo '<td><span>' . esc_html( $race ) . '</span></td>';
					}

					echo '</tr>';

				}

			}

			?>
			</table>
		</div><?php
	}

	/**
	 * Output the results AM meta box.
	 */
	public function results_am_meta_box() {
		?>
		<input type="hidden" id="seasons-resultsam" name="seasons-resultsam" value="" />

		<style>
		#resultsam-wrapper {
			width: 100%;
			overflow-x: scroll;
		}

		#resultsam-table {
			border-spacing: 0;
			border-collapse: separate;
		}

		#resultsam-table tr.first-row td {
			font-weight: bold;
		}

		#resultsam-table {
			width: 100%;
			border-top: 1px solid #ddd;
			border-left: 1px solid #ddd;
		}

		#resultsam-table th,
		#resultsam-table td {
			text-align: left;
			border-right: 1px solid #ddd;
			border-bottom: 1px solid #ddd;
			padding: 4px 4px;
		}

		#resultsam-table td span {
			display: block;
			min-height: 1rem;
		}

		</style>

		<div id="resultsam-wrapper">

			<table id="resultsam-table">

				<tr>
					<?php /* Important - don't leave spaces, as it goofs up the saving process in deleting them */ ?>
					<th>Username</th><?php

					$events = get_post_meta( get_the_ID(), 'event', true );
					$event_counter = 0;
					if ( is_array( $events ) ) {
						foreach ( $events as $key => $event ) {

							$track_initials = strtoupper( substr( $event['track_name'], 0, 3 ) );

							if ( isset( $event['event_race-1_timestamp'] ) && '' !== $event['event_race-1_timestamp'] ) {
								echo '<th>' . esc_html( $track_initials ) . '1</th>';
								$event_counter++;
							}
							if ( isset( $event['event_race-2_timestamp'] ) && '' !== $event['event_race-2_timestamp'] ) {
								echo '<th>' . esc_html( $track_initials ) . '2</th>';
								$event_counter++;
							}
							if ( isset( $event['event_race-3_timestamp'] ) && '' !== $event['event_race-3_timestamp'] ) {
								echo '<th>' . esc_html( $track_initials ) . '3</th>';
								$event_counter++;
							}

						}

					}
					?>

				</tr><?php

			$drivers = get_post_meta( get_the_ID(), '_seasons_drivers', true );
			$resultsam = get_post_meta( get_the_ID(), '_seasons_resultsam', true );

			if ( is_array( $drivers ) ) {

				foreach ( $drivers as $row_number => $row ) {
					$username = $row[0];

					// Style AM class drivers differently
					$class = 'pro-class';
					if ( isset( $row[5] ) && 'AM' === trim( $row[5] ) ) {
						$class = 'am-class';
					}

					echo '<tr class="' . esc_attr( $class ) . '">';
					if ( isset( $username ) ) {
						echo '<td><span>' . esc_html( trim( $username ) ) . '</span></td>';
					}
					// Iterate through the events
					for ( $x = 1; $x <= $event_counter; $x++ ) {

						if ( isset( $resultsam[$username] ) ) {
							$race = $resultsam[$username][$x];
						} else {
							$race = '';
						}

						echo '<td><span>' . esc_html( $race ) . '</span></td>';
					}

					echo '</tr>';

				}

			}

			?>
			</table>
		</div><?php
	}

	/**
	 * Output the results AM meta box.
	 */
	public function weight_penalties_meta_box() {
		?>
		<input type="hidden" id="seasons-weight-penalties" name="seasons-weight-penalties" value="" />

		<style>
		#weight-penalties-wrapper {
			width: 100%;
			overflow-x: scroll;
		}

		#weight-penalties-table {
			border-spacing: 0;
			border-collapse: separate;
		}

		#weight-penalties-table tr.first-row td {
			font-weight: bold;
		}

		#weight-penalties-table {
			width: 100%;
			border-top: 1px solid #ddd;
			border-left: 1px solid #ddd;
		}

		#weight-penalties-table th,
		#weight-penalties-table td {
			text-align: left;
			border-right: 1px solid #ddd;
			border-bottom: 1px solid #ddd;
			padding: 4px 4px;
		}

		#weight-penalties-table td span {
			display: block;
			min-height: 1rem;
		}

		</style>

		<div id="weight-penalties-wrapper">

			<table id="weight-penalties-table">

				<tr>
					<?php /* Important - don't leave spaces, as it goofs up the saving process in deleting them */ ?>
					<th>Username</th><?php

					$events = get_post_meta( get_the_ID(), 'event', true );
					$event_counter = 0;
					if ( is_array( $events ) ) {
						foreach ( $events as $key => $event ) {

							$track_initials = strtoupper( substr( $event['track_name'], 0, 3 ) );

							echo '<th>' . esc_html( $track_initials ) . '</th>';
							$event_counter++;

						}

					}
					?>

				</tr><?php

			$drivers = get_post_meta( get_the_ID(), '_seasons_drivers', true );
			$weight_penalties = get_post_meta( get_the_ID(), '_seasons_weight_penalties', true );

			if ( is_array( $drivers ) ) {

				foreach ( $drivers as $row_number => $row ) {
					$username = $row[0];

					// Style AM class drivers differently
					$class = 'pro-class';
					if ( isset( $row[5] ) && 'AM' === trim( $row[5] ) ) {
						$class = 'am-class';
					}

					echo '<tr class="' . esc_attr( $class ) . '">';
					if ( isset( $username ) ) {
						echo '<td><span>' . esc_html( trim( $username ) ) . '</span></td>';
					}
					// Iterate through the events
					for ( $x = 1; $x <= $event_counter; $x++ ) {

						if ( isset( $weight_penalties[$username] ) ) {
							$race = $weight_penalties[$username][$x];
						} else {
							$race = '';
						}

						echo '<td><span>' . esc_html( $race ) . '</span></td>';
					}

					echo '</tr>';

				}

			}

			?>
			</table>
		</div><?php
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['seasons-drivers'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['seasons-nonce'], __FILE__ ) ) {
				return;
			}

			// Sanitize and store the data
			$string = $_POST['seasons-drivers'];
			$string = str_replace( '</td><td', '</td>,<td', $string );
			$string = str_replace( '</th><th', '</th>,<th', $string );
			$string = str_replace( '</tr><tr', "</tr>\n<tr", $string );

			$string = strip_tags( $string );
			$string = trim( $string );
			$string = wp_kses_post( $string );

			// Convert into array and delete any blank rows
			$rows = explode( "\n", $string );
			$data = array();
			foreach ( $rows as $row_number => $row ) {
				$row_exploded = explode( ',', $row );
				if ( '' !== trim( str_replace( ',', '', $row ) ) ) {
					$data[$row_number] = $row_exploded;
				}
			}

			// First line of data is just from the heading
			unset($data[0]);

			update_post_meta( $post_id, '_seasons_drivers', $data );
		}

		// Only save if correct post data sent
		foreach ( array( 'results','resultsam', 'weight-penalties' ) as $class ) {

			if ( isset( $_POST['seasons-' . $class] ) ) {

				$string = $_POST['seasons-' . $class];
				$string = str_replace( '</td><td', '</td>,<td', $string );
				$string = str_replace( '</th><th', '</th>,<th', $string );
				$string = str_replace( '</tr><tr', "</tr>\n<tr", $string );

				$string = strip_tags( $string );
				$string = trim( $string );
				$string = wp_kses_post( $string );

				// Convert into array and delete any blank rows
				$rows = explode( "\n", $string );
				$data = array();
				foreach ( $rows as $row_number => $row ) {
					$row_exploded = explode( ',', $row );
					$key = $row_exploded[0];
					unset( $row_exploded[0] );
					if ( '' !== trim( str_replace( ',', '', $row ) ) ) {
						$data[$key] = $row_exploded;
					}
				}

				// First line of data is just from the heading
				unset($data['Username']);

				$key = str_replace( '-', '_', $class );
				update_post_meta( $post_id, '_seasons_' . $key, $data );

			}

		}

	}

}
