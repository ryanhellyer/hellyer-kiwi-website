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
class SRC_Tracks extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',            array( $this, 'init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'tracks_metaboxes' ) );
//		add_action( 'save_post',       array( $this, 'meta_boxes_save' ), 10, 2 );

	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'track',
			array(
				'public'             => true,
				'publicly_queryable' => false,
				'label'              => __( 'Track', 'src' ),
				'supports'           => array( 'thumbnail', 'title', 'editor' ),
				'menu_icon'          => 'dashicons-location',
			)
		);

	}

	/**
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function tracks_metaboxes() {
		$slug = 'track';

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Track Information', 'src' ),
			'object_types' => array( 'track', ),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Type', 'src' ),
			'id'         => 'track_type',
			'type'       => 'select',
			'options_cb' => 'src_get_track_types',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Country', 'src' ),
			'id'         => 'country',
			'type'       => 'select',
			'options_cb' => 'src_get_countries',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Track Map', 'src' ),
			'id'   => 'map',
			'type' => 'file',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Track Image 1', 'src' ),
			'id'   => 'image1',
			'type' => 'file',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Track Image 2', 'src' ),
			'id'   => 'image2',
			'type' => 'file',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Track Image 3', 'src' ),
			'id'   => 'image3',
			'type' => 'file',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Track Image 4', 'src' ),
			'id'   => 'image4',
			'type' => 'file',
		) );

	}

}
