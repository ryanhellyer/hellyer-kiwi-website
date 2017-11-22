<?php

/**
 * Teams.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Teams extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',              array( $this, 'init' ) );
		add_action( 'cmb2_admin_init',   array( $this, 'teams_metaboxes' ) );
		add_action( 'cmb2_admin_init',   array( $this, 'seasons_metaboxes' ) );

		// Add filter
		add_action( 'the_content', array( $this, 'add_content_season_posts' ) );

	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'team',
			array(
				'public'             => true,
				'publicly_queryable' => true,
				'label'              => __( 'Teams', 'src' ),
				'supports'           => array( 'title', 'editor', 'thumbnail' ),
				'show_in_menu'       => 'edit.php?post_type=event',
			)
		);

	}

	/**
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function seasons_metaboxes() {
		$slug = 'seasons';

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Seasons', 'src' ),
			'object_types' => array( 'team', ),
		) );

		$query = new WP_Query( array(
			'post_type'      => 'season',
			'posts_per_page' => 100
		) );

		$seasons = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$cmb->add_field( array(
					'name' => esc_html( get_the_title( get_the_ID() ) ),
					'id'         => 'season-' . sanitize_title( get_the_title( get_the_ID() ) ),
					'type'       => 'checkbox',
				) );

			}
		}

	}

	/**
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function teams_metaboxes() {
		$slug = 'team';

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Images', 'src' ),
			'object_types' => array( 'team', ),
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Image 1', 'src' ),
			'id'   => 'image1',
			'type' => 'file',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Image 2', 'src' ),
			'id'   => 'image2',
			'type' => 'file',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Image 3', 'src' ),
			'id'   => 'image3',
			'type' => 'file',
		) );

		$cmb->add_field( array(
			'name' => esc_html__( 'Image 4', 'src' ),
			'id'   => 'image4',
			'type' => 'file',
		) );

	}

	/**
	 * Add coontent to seasons posts.
	 *
	 * @param  string  $content  The page content
	 * @return string  The modified page content
	 */
	public function add_content_season_posts( $content ) {

		// Only show on the season post-type
		if ( 'season' !== get_post_type() ) {
			return $content;
		}

		$car_ids = get_post_meta( get_the_ID(), 'cars', true );

		if ( ! isset( $car_ids[0] ) ) {
			return $content;
		}

/*
		// Add text
		if ( 1 === count( $car_ids ) ) {
			$single_car = true;
			$content .= '<h3>' . esc_html( get_the_title( $car_ids[0] ) ) . '</h3>';			
		} else {
			$content .= '<h3>' . esc_html__( 'Allowed cars', 'src' ) . '</h3>';
		}
*/

		return $content;
	}

	public function seasons() {

		$seasons = array(
			'4',
			'3',
			'2',
			'1',
		);

		return $seasons;
	}

}
