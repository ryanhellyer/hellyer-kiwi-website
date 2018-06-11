<?php
/*
Plugin Name: Strattic API
Plugin URI: https://strattic.com/
Description: Provides extra WordPress Rest API endpoints.
Version: 1.0
Author: Strattic
Author URI: https://strattic.com/
*/

/**
 * Strattic Temporary Archive Fix.
 * 
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_API {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'rest_api_init', function () {
			register_rest_route( 'strattic/v1', '/archives', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_archives' ),
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( 'strattic/v1', '/all_posts', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_all_posts' ),
			) );
		} );

	}

	/**
	 * Grab a single listing by ID or slug.
	 *
	 * @param array $data The listing slug or ID.
	 * @return array  The raw post array
	 */
	public function get_archives( $request ) {
		$request_params = $request->get_query_params();

		$args = array(
			'_builtin' => false
		);
		$post_types = get_post_types( $args, 'names', 'and' ); 

		foreach ( $post_types  as $post_type ) {

			$archive_url = get_post_type_archive_link( $post_type );
			if ( false !== $archive_url ) {
				$archives[] = $archive_url;
			}
		}

		return $archives;
	}

	/**
	 * Grabs list of all posts.
	 * Unlike the standard WordPress API, this grabs ALL posts.
	 * Grabs all posts regardless of post-type, public status etc.
	 *
	 * @param  object $request  The request object
	 * @return array
	 */
	public function get_all_posts( $request ) {
		$request_params = $request->get_query_params();

		// Set number of posts per page
		$per_page = 10;
		if ( isset( $request_params[ 'per_page' ] ) ) {
			$per_page = absint( $request_params[ 'per_page' ] );
		}

		// Set number of page we're on
		$page_number = 0;
		if ( isset( $request_params[ 'page_number' ] ) && '0' !== $request_params[ 'page_number' ] ) {
			$page_number = absint( $request_params[ 'page_number' ] ) - 1;
		}

		// Loop through ALL post types
		$count = 0;
		$post_types = get_post_types( array(), 'names', 'and' );
		foreach ( $post_types  as $post_type ) {

			$offset = absint( $per_page * $page_number ) - $post_type_count;
			if ( $offset < 0 ) {
				$offset = 0;
			}

			$query = new WP_Query( array(
				'posts_per_page'         => $per_page, // This may sometimes query too many posts - but the code is simpler this way ;)
				'offset'                 => $offset,
				'post_type'              => $post_type,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			) );
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();

					// If number of posts collected is larged than total required, then just bail out
					if ( $count >= $per_page ) {
						break;
					}

					$count++;

					$posts[] = get_the_permalink();

				}

				wp_reset_postdata();
			}

			$post_type_count = $post_type_count + wp_count_posts( $post_type )->publish;
		}

		return $posts;
	}

}
new Strattic_API;
