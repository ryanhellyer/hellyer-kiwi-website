<?php
/*
Plugin Name: Strattic API
Plugin URI: https://strattic.com/
Description: Provides extra WordPress Rest API endpoints.
Version: 1.0
Author: Strattic
Author URI: https://strattic.com/
*/



/*
 TODO: hunt out users and output their profile pages
 TODO: store every URL visited that is not in our list
       and spit those out separately
 TODO: check if actual spidering is happening - if not, then scrape pages for internal URLs
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

		add_action( 'rest_api_init', function () {
			register_rest_route( 'strattic/v1', '/all_terms', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_all_terms' ),
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( 'strattic/v1', '/redirects', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_redirects' ),
			) );
		} );

		add_action( 'rest_api_init', function () {
			register_rest_route( 'strattic/v1', '/users', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_user_pages' ),
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

		$archives = array();
		foreach ( $post_types  as $post_type ) {

			$archive_url = get_post_type_archive_link( $post_type );
			if ( false != $archive_url ) {
				$archives[] = $archive_url;
			}
		}

		return $archives;
	}

	/**
	 * Grabs list of all posts.
	 * Unlike the standard WordPress API, this grabs ALL posts.
	 * Grabs all posts regardless of post-type.
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
		$post_types = get_post_types( array( /*'_builtin' => true,*/ 'public' => true ), 'names', 'and' );
		$post_type_count = 0;
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

	/**
	 * Grabs list of all terms.
	 * Unlike the standard WordPress API, this grabs ALL terms.
	 *
	 * @param  object $request  The request object
	 * @return array
	 */
	public function get_all_terms( $request ) {
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
		$offset = $per_page * $page_number;

		// Loop through ALL terms
		$count = 0;
		$terms = get_terms(
			array(
				//'taxonomy'   => $taxonomy,
				'hide_empty' => false,
				'number'     => $per_page,
				'offset'     => $offset,
				'get'        => 'all',
			)
		);
		foreach ( $terms  as $term ) {
			$term_id = $term->term_id;

			$url = home_url() . get_term_link( $term, $term->term_taxonomy_id );

			$terms_list[] = $url;

		}

		return $terms_list;
	}

	/**
	 * Grabs list of redirects.
	 * Looks for redirects from popular plugins.
	 *
	 * @param  object $request  The request object
	 * @return array
	 */
	public function get_redirects( $request ) {
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
		$offset = $per_page * $page_number;

		$count = 0;

		// Safe Redirect Manager plugin - from 10up
		$query = new WP_Query( array(
			'posts_per_page'         => $per_page, // This may sometimes query too many posts - but the code is simpler this way ;)
			'offset'                 => $offset,
			'post_type'              => 'redirect_rule',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				// If number of posts collected is larger than total required, then just bail out
				if ( $count >= $per_page ) {
					break;
				}

				$count++;

				$redirects[] = array(
					'from' => home_url() . get_post_meta( get_the_ID(), '_redirect_rule_from', true ),
					'to'   => home_url() . get_post_meta( get_the_ID(), '_redirect_rule_to', true ),
				);

			}

			wp_reset_postdata();
		}

		return $redirects;
	}

	/**
	 * Grabs list of user pages.
	 *
	 * @param  object $request  The request object
	 * @return array
	 */
	public function get_user_pages( $request ) {
		$request_params = $request->get_query_params();

		$user_pages = array();

		$users = get_users( $args = array() );
$user_pages = $users;

		return $user_pages;
	}

}
new Strattic_API;
