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

		$query = new WP_Query( array(
			'post_type'      => 'car',
			'posts_per_page' => 100
		) );

		$count = 0;
		$season_id = get_the_ID();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$count++;

				if ( 'on' === get_post_meta( $season_id, 'car-' . $count, true ) ) {

					$cars[get_the_ID()] = array(
						'title' => get_the_title( get_the_ID() ),
						'content' => wpautop( get_the_content( get_the_ID() ) ),
					);

					// Add images
					for ( $x = 1; $x < 4; $x++ ) {
						$cars[get_the_ID()]['image-' . $x] = get_post_meta( get_the_ID(), 'image' . $x . '_id', true );
					}

				}

			}
			wp_reset_postdata();
		}

		if ( ! isset( $cars ) ) {
			return $content;
		}

		// Add text
		if ( 1 === count( $cars ) ) {
			$single_car = true;

			foreach ( $cars as $key => $car ) {
				break;
			}

			$content .= '<h3>' . esc_html( $car['title'] ) . '</h3>';			
		} else {
			$content .= '<h3>' . esc_html__( 'Allowed cars', 'src' ) . '</h3>';
		}

		// Add information about each car
		foreach ( $cars as $car_id => $car ) {

			if ( ! isset( $single_car ) ) {
				$content .= '<h4>' . esc_html( $car['title'] ) . '</h4>';
			}

			$content .= wpautop( $car['content'] );

			// Fixed setups?
			$content .= '<p>';
			if ( 'on' === get_post_meta( get_the_ID(), 'fixed_setup', true ) ) {
				$content .= __( 'A fixed setup will be used.', 'src' );
			} else {
				$content .= __( 'Open setups are allowed. You are free to make any car setup changes you feel are appropriate.', 'src' );
			}
			$content .= '</p>';

			// Add gallery
			$count = 0;
			$image_ids = '';
			for ( $x = 1; $x < 5; $x++ ) {

				if ( isset( $car['image-' . $x] ) ) {

					$image_id = $car['image-' . $x];

					if ( '' !== $image_id ) {
						$count++;
						if ( $x > 1 ) {
							$image_ids .= ',';
						}
						$image_ids .= $image_id;
					}

				}

			}
			$content .= '[gallery link="file" columns="' . esc_attr( $count ) . '" size="medium" ids="' . esc_attr( $image_ids ) . '"]';

		}

		return $content;
	}

}
