<?php

/**
 * Cars.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Cars extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',              array( $this, 'init' ) );
		add_action( 'cmb2_admin_init',   array( $this, 'cars_metaboxes' ) );

		// Add filter
		add_action( 'the_content', array( $this, 'add_content_season_posts' ) );

	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'car',
			array(
				'public'             => true,
				'publicly_queryable' => false,
				'label'              => __( 'Cars', 'src' ),
				'supports'           => array( 'title', 'editor' ),
				'show_in_menu'       => 'edit.php?post_type=event',
			)
		);

	}

	/**
	 * Hook in and add a metabox to demonstrate repeatable grouped fields
	 */
	public function cars_metaboxes() {
		$slug = 'car';

		$cmb = new_cmb2_box( array(
			'id'           => $slug,
			'title'        => esc_html__( 'Images', 'src' ),
			'object_types' => array( 'car', ),
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

		// Fixed setups?
		$content .= '<p>';
		if ( 'on' === get_post_meta( get_the_ID(), 'fixed_setup', true ) ) {
			$content .= __( 'All races will be held with a fixed setup.', 'src' );
		} else {
			$content .= __( 'All races will be held with open setups. You are free to make any car setup changes you feel are appropriate.', 'src' );
		}
		$content .= '</p>';

/*
		// Add information about each car
		foreach ( $car_ids as $key => $car_id ) {

			if ( ! isset( $single_car ) ) {
				$content .= '<h4>' . esc_html( get_the_title( $car_id ) ) . '</h4>';
			}

			$content_post = get_post( $car_id );
			$content .= wpautop( $content_post->post_content );

			// Add gallery
			$count = 0;
			$image_ids = '';
			for ( $x = 0; $x < 5; $x++ ) {
				$image_id = get_post_meta( $car_id, 'image' . $x . '_id', true );
				if ( '' !== $image_id ) {
					$count++;
					if ( $x > 1 ) {
						$image_ids .= ',';
					}
					$image_ids .= $image_id;
				}
			}
			$content .= '[gallery link="file" columns="' . esc_attr( $count ) . '" size="medium" ids="' . esc_attr( $image_ids ) . '"]';

		}
*/
		
		return $content;
	}

}
