<?php

new Test;
class Test {

	public function __construct() {
		add_action( 'init',           array( $this, 'register_post_type' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
	}

	public function register_post_type() {

		$args = array(
			'public'             => true,
			'publicly_queryable' => true,
			'label'              => __( 'Invoice', 'wp-invoice' ),
			'supports'           => array(
				'title',
			)
		);
		register_post_type( 'invoice', $args );

	}

	public function add_metabox() {
		add_meta_box(
			'test-meta-box', // ID
			'Ryans test meta box', // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'invoice', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	public function meta_box() {

		echo '<p><strong>Why the flaming heck does get_the_ID() return different numbers before and after WP_Query() is run?</strong></p>';
		echo '<p>I discovered this problem with my code as it seems to be related to problem which you will notice if you change the page slug, save it, then save it again (without changing it). The page slug will default back to whatever the default WordPress slug would be based on the page title.';

		echo '<h3>get_the_ID() before query: ' . get_the_ID() . '</h3>';

		echo '<h3>Page list</h3>';
		echo '<ul style="list-style:disc;margin-left:15px;">';
		$query = new WP_Query(
			array(
				'posts_per_page' => 10,
				'post_type'      => 'page',
			)
		);
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				echo '<li>' . get_the_title( get_the_ID() ) . '</li>';
			}

			wp_reset_postdata();

		}
		echo '</ul>';

		echo '<h3>get_the_ID() after: ' . get_the_ID() . '</h3>';
	}

}
