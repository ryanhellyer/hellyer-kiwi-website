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
		add_action( 'cmb2_admin_init', array( $this, 'events_metaboxes' ) );
		add_action( 'add_meta_boxes',  array( $this, 'add_metabox' ) );
		add_action( 'save_post',       array( $this, 'meta_boxes_save' ), 10, 2 );

	}

	/**
	 * Init.
	 */
	public function init() {

		$post_types = array(
			'season' => array(
				'public' => true,
				'label'  => 'Season',
				'supports' => array( 'thumbnail' )
			),
		);

		foreach ( $post_types as $post_type => $args ) {
			register_post_type( $post_type, $args );
		}

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
			'options_cb' => array( $this, 'get_countries' ),
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
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'example', // ID
			__( 'Drivers and results', 'src' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'season', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'low'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the example meta box.
	 */
	public function meta_box() {

		?>

		<input type="hidden" id="seasons-drivers" name="seasons-drivers" value="" />
		<input type="hidden" id="seasons-nonce" name="seasons-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>" />
<style>
#results-csv-wrapper {
	width: 100%;
	overflow-x: scroll;
}

#results-csv-table {
	border-spacing: 0;
	border-collapse: separate;
}

#results-csv-table tr.first-row td {
	font-weight: bold;
}

#results-csv-table {
	border-top: 1px solid #ddd;
	border-left: 1px solid #ddd;
}

#results-csv-table td {
	border-right: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
	padding: 4px 4px;
}

#results-csv-table td span {
	display: block;
	min-height: 1rem;
}

</style>
<div id="results-csv-wrapper">
		<table id="results-csv-table"><?php

		$csv = get_post_meta( get_the_ID(), '_seasons_drivers', true );
		$rows = explode( "\n", $csv );
		foreach ( $rows as $row_number => $row ) {

			// Disable top row
			$disabled = '';
			if ( 0 === $row_number ) {
				$disabled = 'disabled="disabled" ';
			}

			$row_exploded = explode( ',', $row );

			$class = '';
			if ( 0 === $row_number ) {
				$class = ' class="first-row"';
			}

			echo '<tr' . $class . '>';
			$blank = '';
			foreach ( $row_exploded as $column => $cell ) {
				echo '<td>';

				$blank .= '<td><span></span></td>';

				if ( 0 !== $row_number ) {
					echo '<span>';
				}

				echo esc_html( trim( $cell ) );

				if ( 0 !== $row_number ) {
					echo '</span>';
				}

				echo '</td>';
			}
			echo '</tr>';

		}
		echo '<tr>' . $blank . '</tr>';

		?>
		</table>
</div>
<script>

(function () {

	window.addEventListener(
		'load',
		function (){

			/**
			 * Making the table cells editable.
			 */
			var results_csv = document.getElementById("results-csv-table").getElementsByTagName('span');
			for(i = 0; i < results_csv.length; i++) {
				results_csv[i].contentEditable = "true";
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

				console.log( e.target.id );

				var results_csv_table = document.getElementById("results-csv-table");
				var seasons_drivers = document.getElementById("seasons-drivers");

				seasons_drivers.value = results_csv_table.innerHTML;
			}

		}
	);
})();

</script><?php
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
			$string = str_replace( '</tr><tr>', "</tr>\n<tr>", $string );
			$string = strip_tags( $string );
			$string = trim( $string );
			$string = wp_kses_post( $string );

			// Delete any blank rows
			$rows = explode( "\n", $string );
			foreach ( $rows as $row_number => $row ) {
				if ( '' === str_replace( ',', '', $row ) ) {
					unset( $rows[$row_number] );
				}
			}
			$string = implode( "\n", $rows );

			update_post_meta( $post_id, '_seasons_drivers', $string );
		}

	}

}
