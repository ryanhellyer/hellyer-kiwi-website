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

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',            array( $this, 'init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'events_metaboxes' ) );

	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'event',
			array(
				'public' => true,
				'label'  => __( 'Event', 'src' ),
				'supports' => array( 'title', 'editor' ),
				'menu_icon' => 'dashicons-flag',
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

}
