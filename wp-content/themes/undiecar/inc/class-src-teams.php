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
		add_action( 'init',            array( $this, 'init' ) );
		add_action( 'cmb2_admin_init', array( $this, 'teams_metaboxes' ) );
		add_action( 'add_meta_boxes',  array( $this, 'drivers_metabox' ) );
		add_action( 'save_post',       array( $this, 'drivers_save' ), 10, 2 );

		// Add filters
		add_filter( 'the_content',     array( $this, 'drivers_list' ) );
		add_filter( 'the_content',     array( $this, 'add_gallery' ) );

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
	 * Add drivers metabox.
	 */
	public function drivers_metabox() {
		add_meta_box(
			'drivers', // ID
			__( 'Drivers', 'src' ), // Title
			array(
				$this,
				'drivers_html', // Callback to method to display HTML
			),
			array( 'team' ), // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Drivers metabox.
	 */
	 public function drivers_html() {

		if ( 'team' !== get_post_type() ) {
			return;
		}

		echo '<p>' . esc_html__( 'Enter the driver names here', 'src' ) . '</p>';

		$count = 1;
		while ( $count < 4 ) {
			$display_name = '';

			$user_id = get_post_meta( get_the_ID(), '_driver_' . $count, true );
			if ( is_numeric( $user_id ) ) {
				$user = get_userdata( $user_id );
				$display_name = $user->data->display_name;
			} else if ( 'error' === $user_id ) {
				$display_name = $user_id;
			}

			echo '
			<p>
				<label for="' . esc_attr( 'driver-' . $count ) . '">' . esc_html( sprintf( __( 'driver #%s', 'src' ), $count ) ) . '</label>
				<input type="text" id="' . esc_attr( 'driver-' . $count ) . '" name="' . esc_attr( 'driver-' . $count ) . '" value="' . esc_attr( $display_name ) . '" />
			</p>';

			$count++;
		}

		echo '<input type="hidden" id="drivers-nonce" name="drivers-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">';

	}

	/**
	 * Save results upload save.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function drivers_save( $post_id, $post ) {

		if ( ! isset( $_POST['drivers-nonce'] ) ) {
			return $post_id;
		}

		// Do nonce security check
		if ( ! wp_verify_nonce( $_POST['drivers-nonce'], __FILE__ ) ) {
			return $post_id;
		}

		$count = 1;
		while ( $count < 4 ) {

			if ( isset( $_POST['driver-' . $count] ) ) {
				$driver_name = wp_kses_post( $_POST['driver-' . $count] );
				$username = sanitize_title( $driver_name );
				$user = get_userdatabylogin( $username );
				if ( isset( $user->ID ) ) {
					$user_id = $user->ID;
					update_post_meta( $post_id, '_driver_' . $count, $user_id );
				} else {
					update_post_meta( $post_id, '_driver_' . $count, 'error' );
				}
			}

			$count++;
		}

	}

	/**
	 * Add drivers list.
	 *
	 * @param  string  $content  The post content
	 * @return string  The post content with drivers list added
	 */
	public function drivers_list( $content ) {

		if ( 'team' !== get_post_type() ) {
			return $content;
		}

		$drivers_list = '';
		$count = 0;
		while ( $count < 3 ) {
			$count++;

			$driver_id = get_post_meta( get_the_ID(), '_driver_' . $count, true );
			$drivers_list .= $this->get_driver_block( $driver_id );
		}

		if ( '' !== $drivers_list ) {
			$content .= '<h3>' . esc_html__( 'Current drivers', 'src' ) . '</h3>';
			$content .= $drivers_list;
		}

		return $content;
	}

	/**
	 * Adding gallery.
	 *
	 * @param  string  $content  The post content
	 * @return string  The modified post content
	 */
	public function add_gallery( $content ) {

		if ( 'team' !== get_post_type() ) {
			return $content;
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

		$content .= '[gallery link="file" columns="' . esc_attr( $count ) . '" size="src-four" ids="' . esc_attr( $image_ids ) . '"]';

		return $content;
	}

}
