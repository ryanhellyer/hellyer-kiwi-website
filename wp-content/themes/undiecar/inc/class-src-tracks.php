<?php

/**
 * Tracks.
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
		add_filter( 'the_content',     array( $this, 'add_extra_content' ) );

	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'track',
			array(
				'public'             => true,
				'publicly_queryable' => true,
				'label'              => __( 'Tracks', 'src' ),
				'supports'           => array( 'thumbnail', 'title', 'editor' ),
				'show_in_menu'       => 'edit.php?post_type=event',
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
			'name'       => esc_html__( 'Type', 'src' ),
			'id'         => 'track_type',
			'type'       => 'select',
			'options_cb' => 'src_get_track_types',
		) );

		$cmb->add_field( array(
			'name'       => esc_html__( 'Length', 'src' ),
			'description'=> esc_html__( 'The track length in kilometers', 'src' ),
			'id'         => 'track_length',
			'type'       => 'text',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Country', 'src' ),
			'id'         => 'country',
			'type'       => 'select',
			'options_cb' => 'src_get_countries',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Track Logo', 'src' ),
			'id'   => 'logo',
			'type' => 'file',
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

	/**
	 * Adding extra content.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function add_extra_content( $content ) {

		if ( 'track' !== get_post_type() ) {
			return $content;
		}

		// Add logo
		$logo_id = get_post_meta( get_the_ID(), 'logo_id', true );
		$logo_url = wp_get_attachment_image_src( $logo_id, 'medium' );
		if ( isset( $logo_url[0] ) ) {
			$logo_url = $logo_url[0];
			$content = '<img src="' . esc_url( $logo_url ) . '" class="alignright size-medium" />' . $content;
		}

		// Add map
		$map_id = get_post_meta( get_the_ID(), 'map_id', true );
		$map_url = wp_get_attachment_image_src( $map_id, 'full' );
		if ( isset( $map_url[0] ) ) {
			$map_url = $map_url[0];
			$content .= '<img src="' . esc_url( $map_url ) . '" class="aligncenter size-full" />';
		}

		// Add gallery
		$count = 0;
		$image_ids = '';
		for ( $x = 0; $x < 5; $x++ ) {
			$image_id = get_post_meta( get_the_ID(), 'image' . $x . '_id', true );
			if ( '' !== $image_id ) {
				$count++;
				if ( $x > 1 ) {
					$image_ids .= ',';
				}
				$image_ids .= $image_id;
			}
		}
		if ( '' !== $image_ids ) {
			$content .= '[gallery link="file" columns="' . esc_attr( $count ) . '" size="src-four" ids="' . esc_attr( $image_ids ) . '"]';
		}

		return $content;
	}

}
