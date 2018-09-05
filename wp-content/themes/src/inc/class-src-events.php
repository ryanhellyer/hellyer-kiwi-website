<?php

if( isset( $_GET['test'])){
	$bla = get_post_meta( 53 );
	$bla = $bla['event_'][0];
	$bla = maybe_unserialize( $bla );
	print_r( $bla );
	die;
}

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

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',            array( $this, 'init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'events_metaboxes' ) );
		add_action( 'cmb2_admin_init', array( $this, 'entrants_metaboxes' ) );
		add_action( 'cmb2_admin_init', array( $this, 'cars_metaboxes' ) );

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

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'PRO Points Awarded', 'src' ),
			'description' => esc_html__( 'A comma delimited list of points awarded for the PRO division. If this is a single event season (AKA special event), the points will be ignored.', 'src' ),
			'default' => '10,8,6,5,4,3,2,1',
			'id'   => 'pro_points',
			'type' => 'text',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'AM Points Awarded', 'src' ),
			'description' => esc_html__( 'A comma delimited list of points awarded for the PRO division. If this is left blank, the system will assume there is no PRO or AM division.', 'src' ),
			'default' => '10,8,6,5,4,3,2,1',
			'id'   => 'am_points',
			'type' => 'text',
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
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function entrants_metaboxes() {
		$slug = 'entrant';

		/**
		 * Repeatable Field Groups
		 */
		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Entrants', 'src' ),
			'object_types' => array( 'season', ),
		) );

		$group_field_id = $cmb->add_field( array(
			'id'          => $slug,
			'type'        => 'group',
			'description' => esc_html__( 'Add all the entrants here.', 'src' ),
			'options'     => array(
				'group_title'   => esc_html__( 'Entrant {#}', 'src' ), // {#} gets replaced by row number
				'add_button'    => esc_html__( 'Add Another Entrant', 'src' ),
				'remove_button' => esc_html__( 'Remove Entrant', 'src' ),
				'sortable'      => true, // beta
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'       => esc_html__( 'Team Name', 'src' ),
			'id'         => 'team_name',
			'type'       => 'text',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'    => esc_html__( 'Team Colour', 'cmb2' ),
			'desc'    => esc_html__( 'The colour assigned to this team', 'cmb2' ),
			'id'      => 'team_colour',
			'type'    => 'colorpicker',
			'default' => '#dd0000',
		) );

		$count = 0;
		while ( $count < 4 ) {
			$count++;

			$cmb->add_group_field( $group_field_id, array(
				'name'       => esc_html__( 'Driver #' . $count . ' Name', 'src' ),
				'id'         => 'name_' . $count,
				'type'       => 'select',
				'options_cb' => array( $this, 'get_src_users' ),
			) );

			$cmb->add_group_field( $group_field_id, array(
				'name'        => esc_html__( 'Driver #' . $count . ' Number', 'src' ),
				'id'          => 'number_' . $count,
				'type'        => 'text',
			) );

			$cmb->add_group_field( $group_field_id, array(
				'name'        => esc_html__( 'Driver #' . $count . ' Car Model', 'src' ),
				'id'          => 'model_' . $count,
				'type'        => 'text',
			) );

			$cmb->add_group_field( $group_field_id, array(
				'name'       => esc_html__( 'Driver #' . $count . ' Level', 'src' ),
				'id'         => 'level_' . $count,
				'type'       => 'select',
				'options_cb' => array( $this, 'get_race_classes' ),
			) );

		}

	}

	/**
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function cars_metaboxes() {
		$slug = 'car';

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Cars', 'src' ),
			'object_types' => array( 'season', ),
		) );

		$group_field_id = $cmb->add_field( array(
			'id'          => $slug,
			'type'        => 'group',
			'description' => esc_html__( 'Add all the cars here.', 'src' ),
			'options'     => array(
				'group_title'   => esc_html__( 'Car {#}', 'src' ), // {#} gets replaced by row number
				'add_button'    => esc_html__( 'Add Another Car', 'src' ),
				'remove_button' => esc_html__( 'Remove Car', 'src' ),
				'sortable'      => true, // beta
			),
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name'        => esc_html__( 'Car Type', 'src' ),
			'description' => esc_html__( 'Put batches of cars together, which are suitable for use within a single team. This information will be used to (hopefully) intelligently work out what cars are left when new competitors try to sign up, and prevent them from submitting requests for cars which are unavailable.', 'src' ),
			'id'          => 'car_type',
			'type'        => 'text',
		) );

		$count = 0;
		while ( $count < 4 ) {
			$count++;

			$cmb->add_group_field( $group_field_id, array(
				'name'       => esc_html__( 'Car Model #' . $count, 'src' ),
				'id'         => 'car_' . $count,
				'type'       => 'text',
			) );

		}


	}

}
