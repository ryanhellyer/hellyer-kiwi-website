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
		add_action( 'cmb2_admin_init', array( $this, 'cars_metaboxes' ) );
//		add_action( 'save_post',       array( $this, 'meta_boxes_save' ), 10, 2 );

	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'season',
			array(
				'public' => true,
				'label'  => __( 'Season', 'src' ),
				'supports' => array( 'thumbnail', 'title', 'editor' )
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
			'name' => esc_html__( 'Manufacturer', 'src' ),
			'id'   => 'manufacturer',
			'type' => 'text',
		) );

		$cmb->add_group_field( $group_field_id, array(
			'name' => esc_html__( 'Model', 'src' ),
			'id'   => 'model',
			'type' => 'text',
		) );

	}

}
