<?php

/*
 DONE: increase time-out, RAM etc.
 DONE: add "latest changes" for newly modified content
 DONE: add ability to manually include URLs
 DONE: find RSS feeds
 DONE: add attachment pages and associated files
 DONE: hunt out users and output their profile pages
 DONE: store every URL visited that is not in our list
       and spit those out separately
       requires logging
 TODO: check if actual spidering is happening - if not, then scrape pages for internal URLs
 TODO: look for pagination in archives
 TODO: find comments pagination pages
 TODO: paginatION FOR USER PAGES

 TODO: handle embeddable pages, eg: https://undiecar.com/contact/embed/

events-manager
bbpress
Events Calendar (modern tribe)

*/

/**
 * Strattic Temporary Archive Fix.
 * 
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_API {

	const PER_PAGE = 100;
	const MEMORY_LIMIT = 1024;
	const TIME_LIMIT = HOUR_IN_SECONDS;

	private $feed_formats = array( 'rss2', 'rss', 'rdf', 'atom' );
	private $post_ids;
	private $taxonomy_term_ids;
	private $important = false;
	private $post_dates;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'rest_api_init', function () {
			register_rest_route( 'strattic/v1', '/everything', array(
				'methods'  => 'GET',
				'callback' => array( $this, 'get_everything' ),
			) );
		} );

		add_action( 'template_redirect', array( $this, 'api_request_bailer' ) );

		add_action( 'template_redirect', array( $this, 'get_paginated_urls' ), 4 );
	}

	/**
	 * Getting the archive pagination URLs.
	 * We use http requests due to not be able to determine the max number of pages without loading the pages.
	 *
	 * @access  private
	 * @global  object  $wp_query  The main WP Query object
	 * @return  array   $urls      The archive pagination URLs
	 */
	private function get_archive_pagination_urls( $url ) {
		$urls = array();
/****************************************************
 ****************************************************
 ****************************************************/
delete_option( 'strattic-discovered-links' );


		$response = wp_remote_get( $url, array( 'user-agent' => 'strattic-api-max-pagination' ) );
		if ( isset( $response[ 'body' ] ) && '' !== $response[ 'body' ] ) {

			$json_urls = $response[ 'body' ];
			$decoded_urls = json_decode( $json_urls );
			if ( is_array( $decoded_urls ) ) {
				$urls = $decoded_urls;
			}

		}

		return $urls;
	}

	/**
	 * Get number of pagination pages.
	 * Halts WordPress loading and instead returns the maximum number of pages for pagination in the current archive.
	 *
	 * @global  object  $wp_query  The main WordPress query
	 */
	function get_paginated_urls() {

		if ( 'strattic-api-max-pagination' === $_SERVER[ 'HTTP_USER_AGENT' ] ) {
			global $wp_query;
			$urls = array();

			$max_num_pages = $wp_query->max_num_pages;

			$count = 2;
			while ( $count <= $max_num_pages ) {
				$paginated_url = get_pagenum_link( $count );
				$urls[] = $paginated_url;

				$count++;
			}

			echo json_encode( $urls );
			die;

		}

	}






	/**
	 * Grabs all the thingz!
	 *
	 * @param   array  $request  The request parameters
	 * @return  array  URLs
	 */
	public function get_everything( $request = '' ) {

		$time_start = microtime( true );

		$request_params = $request->get_query_params();
		if ( isset( $request_params[ 'important' ] ) && 'true' === $request_params[ 'important' ] ) {
			$this->important = true;
		}

		// Increase maximum execution time to make sure that we can gather everything
		ini_set( 'max_execution_time', self::TIME_LIMIT );
		set_time_limit ( self::TIME_LIMIT );
		ini_set( 'memory_limit', self::MEMORY_LIMIT . 'M' );

		// Get all the required URLs
		$urls = array( '/' );
		$urls = array_merge( $urls, $this->get_taxonomy_archives() );
		$urls = array_merge( $urls, $this->get_all_posts() );
		$urls = array_merge( $urls, $this->get_date_archives() );
		$urls = array_merge( $urls, $this->get_all_terms() );
		$urls = array_merge( $urls, $this->get_feeds() );
		$urls = array_merge( $urls, $this->get_user_pages() );

		$paths = $this->strip_site_root( $urls );

		$paths = array_unique( $paths );

		// Add execution time header
		$execution_time = round( microtime( true ) - $time_start, 2 );
		header("Execution-time: " . $execution_time . ' seconds' );

		// Stash the URLs for later use
		if ( true === $this->important ) {
			update_option( 'strattic-paths', $paths, 'no' );
		}

		// Add manual and discovered links
		$options = array(
			'manual-links',
			'discovered-links',
		);
		foreach ( $options as $option ) {
			$new_paths = get_option( 'strattic-' . $option );

			if ( is_array( $new_paths ) ) {
				$paths = array_merge( $paths, $new_paths );
			}

		}

		$paths = array_unique( $paths );

		return $paths;
	}

	/**
	 * Grab all date archives.
	 *
	 * @return  array  URLs
	 */
	public function get_date_archives() {
		$urls = array();

		if ( null === $this->post_dates ) {
			$this->get_all_posts(); // If posts weren't already accessed, then access them anyway, as that primes the $this->post_dates var
		}

		foreach ( $this->post_dates as $post_id => $date ) {

			// Get links
			$links[ 'year' ]  = get_year_link( $date[ 'y' ] );
			$links[ 'month' ] = get_month_link( $date[ 'y' ], $date[ 'm' ] );
			$links[ 'day' ]   = get_day_link( $date[ 'y' ], $date[ 'm' ], $date[ 'd' ] );

			// Get paginated versions of the links
			foreach ( $links as $url ) {
				$urls[] = $url;

				$archive_pagination_urls = $this->get_archive_pagination_urls( $url );
				$urls = array_merge( $urls, $archive_pagination_urls );
			}

			// Create non-permalink link
			if ( true === $this->important ) {
				$urls[] = home_url( '?m=' . $year . $month . $day );
			}

		}

		return $urls;
	}

	/**
	 * Grab all taxonomy archives.
	 *
	 * @return  array  URLs
	 */
	public function get_taxonomy_archives() {

		if ( null === $this->taxonomy_term_ids ) {
			$this->get_all_terms(); // If terms weren't already accessed, then access them anyway, as that primes the $this->taxonomy_term_ids var
		}

		$category_base = 'category';
		if ( '' !== get_option( 'category_base' ) ) {
			$category_base = get_option( 'category_base' );
		}

		$tag_base = 'tag';
		if ( '' !== get_option( 'tag_base' ) ) {
			$tag_base = get_option( 'tag_base' );
		}

		foreach ( $this->taxonomy_term_ids as $taxonomy => $terms ) {

			foreach ( $terms as $term_id ) {

				$term = get_term( $term_id, $taxonomy );
				$term_slug = $term->slug;

				if ( get_option( 'permalink_structure') ) {

					if ( 'category' === $taxonomy ) {
						$paths[] = '/' . $category_base . '/' . $term_slug . '/';
					} else if ( 'post_tag' === $taxonomy ) {
						$paths[] = '/' . $tag_base . '/' . $term_slug . '/';
					} else {
						$paths[] = '/' . $taxonomy . '/' . $term_slug . '/';
					}

				}

				if ( true === $this->important ) {

					if ( 'category' === $taxonomy ) {

						if ( get_option( 'permalink_structure') ) {
							$paths[] = '/' . $category_base . '/' . $term_slug . '/';
						}
						$paths[] = '/?cat=' . $term_id;

					} else if ( 'post_tag' === $taxonomy ) {

						if ( get_option( 'permalink_structure') ) {
							$paths[] = '/' . $tag_base . '/' . $term_slug . '/';
						}
						$paths[] = '/?tag=' . $term_slug;
						
					} else {

						if ( get_option( 'permalink_structure') ) {
							$paths[] = '/' . $taxonomy . '/' . $term_slug . '/';
						}
						$paths[] = '/?' . $taxonomy . '=' . $term_slug;

					}

				}

			}

		}

		// Generate URLs from paths
		$urls = array();
		foreach ( $paths as $key => $path ) {
			$url = home_url( $path );
			$urls[] = $url;

			$archive_pagination_urls = $this->get_archive_pagination_urls( $url );
ryans_log( $url );
ryans_log( print_r( $archive_pagination_urls, true ) );
			$urls = array_merge( $urls, $archive_pagination_urls );
		}

		return $urls;
	}

	/**
	 * Grabs list of all posts.
	 * Unlike the standard WordPress API, this grabs ALL posts.
	 * Grabs all posts regardless of post-type.
	 *
	 * @return  array  URLs
	 */
	public function get_all_posts() {
		$urls = array();

		// Loop through ALL post types
		$post_types = get_post_types( array( /*'_builtin' => true,*/ 'public' => true ), 'names', 'and' );

		foreach ( $post_types  as $post_type ) {

			$post_statuses = array( 'publish', 'private', 'inherit' /* for attachments */ );

			// Count total posts in this post-type
			$total_count = 0;
			$post_counts = wp_count_posts( $post_type );
			foreach ( $post_statuses as $post_status ) {

				if ( isset( $post_counts->$post_status ) ) {
					$total_count = $total_count + $post_counts->$post_status;
				}

			}

			// Loop until all items found (don't run all in one go to avoid detonating the database)
			$pages = floor( $total_count / self::PER_PAGE ) + 1;
			$offset = 0;
			while ( $offset < ( $pages * self::PER_PAGE ) ) {

				$query = new WP_Query( array(
					'posts_per_page'         => self::PER_PAGE, // This may sometimes query too many posts - but the code is simpler this way ;)
					'offset'                 => $offset,
					'post_type'              => $post_type,
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
					'post_status'            => $post_statuses,
				) );

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();

						// We need to allow attachments with use 'inherit', but we need to make sure their parents actually have a valid post status - which is what get_post_status() returns when inherit is used
						if ( ! in_array( get_post_status(), $post_statuses ) ) {
							continue;
						}

						// Get the main URL
						if ( 'publish' === get_post_status() || true === $this->important ) {
							$urls[] = get_the_permalink();
						}

						// Get shortlink URLs
						if ( true === $this->important ) {
							$urls[] = wp_get_shortlink( get_the_ID() );
						}

						// Get attachment URLs
						if ( true === $this->important && 'attachment' === $post_type ) {

							$urls[] = wp_get_attachment_url( get_the_ID() );

							foreach ( get_intermediate_image_sizes() as $size ) {

								$src = wp_get_attachment_image_src( get_the_ID(), $size );

								if ( isset( $src[ 0 ] ) ) {
									$urls[] = $src[ 0 ];
								}

							}

						}

						// Store post dates (used later by date archives system)
						$dates = array(
							'y' => get_the_date( 'Y' ),
							'm' => get_the_date( 'm' ),
							'd' => get_the_date( 'd' ),
						);
						$key = md5( print_r( $dates, true ) );
						$this->post_dates[ $key ] = $dates;

						// Store post IDs (used later by feed system)
						$this->post_ids[] = get_the_ID();

					}

					wp_reset_postdata();
				}

				$offset = $offset + self::PER_PAGE;
			}

		}

		return $urls;
	}

	/**
	 * Grabs list of all terms.
	 * Unlike the standard WordPress API, this grabs ALL terms.
	 *
	 * @return  array  URLs
	 */
	public function get_all_terms() {
		$urls = array();

		$taxonomies = get_taxonomies( array( 'public' => true ) );
		foreach ( $taxonomies as $taxonomy ) {
			$total_count = wp_count_terms( $taxonomy );

			// Loop until all items found (don't run all in one go to avoid detonating the database)
			$pages = floor( $total_count / self::PER_PAGE ) + 1;
			$offset = 0;
			while ( $offset < ( $pages * self::PER_PAGE ) ) {

				$count = 0;
				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomy,
						'hide_empty' => false,
						'number'     => self::PER_PAGE,
						'offset'     => $offset,
						'get'        => 'all',
					)
				);
				foreach ( $terms  as $term ) {
					$term_id = $term->term_id;

					$url = get_term_link( $term, $term->term_taxonomy_id );

					$urls[] = $url;
					$this->taxonomy_term_ids[ $taxonomy ][] = $term_id;

				}

				$offset = $offset + self::PER_PAGE;
			}

		}

		return $urls;
	}

	/**
	 * Grabs list of feeds.
	 *
	 * Based on feed formats from https://perishablepress.com/what-is-my-wordpress-feed-url/
	 *
	 * @return  array  paths
	 */
	public function get_feeds() {
		global $wp_rewrite;

		// Get various slugs which can be modified by WordPress
		$comments_slug = $wp_rewrite->comments_base;
		$feed_slug = $wp_rewrite->feed_base;

		$category_base = 'category';
		if ( '' !== get_option( 'category_base' ) ) {
			$category_base = get_option( 'category_base' );
		}

		$tag_base = 'tag';
		if ( '' !== get_option( 'tag_base' ) ) {
			$tag_base = get_option( 'tag_base' );
		}

		// Generate main feed paths
		$paths[] = '/' . $feed_slug . '/'; // main feed
		$paths[] = '/' . $comments_slug . '/' . $feed_slug . '/'; // main comments feed

		if ( true === $this->important ) {

			$paths[] = '/?feed=comments'; //XXX raw PHP file main comments feed
			foreach ( $this->feed_formats as $feed_format ) {
				$paths[] = '/' . $feed_slug . '/' . $feed_format . '/'; // main feed
				$paths[] = '/?feed=' . $feed_format; // non-permalink main feed
				$paths[] = '/wp-' . $feed_format . '.php'; // raw PHP file main feed
				$paths[] = '/' . $comments_slug . '/' . $feed_slug . '/' . $feed_format . '/'; // main comments feed
				$paths[] = '/wp-comments' . $feed_format . '.php'; // raw PHP file main comments feed
				$paths[] = '/?feed=comments' . $feed_format; // non-permalink main comments feed
			}

		}

		// Generate single post comment feed paths
		if ( true === $this->important ) {

			if ( null === $this->post_ids ) {
				$this->get_all_posts(); // If posts weren't already accessed, then access them anyway, as that primes the $this->post_ids var
			}
			foreach ( $this->post_ids as $post_id ) {
				$page_path = str_replace( home_url(), '', get_permalink( $post_id ) );

				if ( get_option( 'permalink_structure') ) {
					$paths[] = $page_path . $feed_slug . '/';
				}
				foreach ( $this->feed_formats as $feed_format ) {

					if ( get_option( 'permalink_structure') ) {

						$path = $page_path;
						$path = add_query_arg( 'feed', $feed_format, $path );
						$path = add_query_arg( 'p', $post_id, $path );

						$paths[] = $page_path . $feed_slug . '/' . $feed_format . '/';
					}

					$path = $page_path;
					$path = add_query_arg( 'feed', $feed_format, $path );
					$paths[] = add_query_arg( 'feed', $feed_format, $path );

				}

			}

		}

		// Generate taxonomy feed paths
		if ( null === $this->taxonomy_term_ids ) {
			$this->get_all_terms(); // If terms weren't already accessed, then access them anyway, as that primes the $this->taxonomy_term_ids var
		}
		foreach ( $this->taxonomy_term_ids as $taxonomy => $terms ) {

			foreach ( $terms as $term_id ) {

				$term = get_term( $term_id, $taxonomy );
				$term_slug = $term->slug;

				if ( get_option( 'permalink_structure') ) {

					if ( 'category' === $taxonomy ) {
						$paths[] = '/' . $category_base . '/' . $term_slug . '/' . $feed_slug . '/';
					} else if ( 'post_tag' === $taxonomy ) {
						$paths[] = '/' . $tag_base . '/' . $term_slug . '/' . $feed_slug . '/';
					} else {
						$paths[] = '/' . $taxonomy . '/' . $term_slug . '/' . $feed_slug . '/';
					}

				}

				if ( true === $this->important ) {

					foreach ( $this->feed_formats as $feed_format ) {

						if ( 'category' === $taxonomy ) {

							if ( get_option( 'permalink_structure') ) {
								$paths[] = '/' . $category_base . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/';
							}
							$paths[] = '/?feed=' . $feed_format . '&cat=' . $term_id;
							$paths[] = '/wp-' . $feed_format . '.php?cat=' . $term_id;

						} else if ( 'post_tag' === $taxonomy ) {

							if ( get_option( 'permalink_structure') ) {
								$paths[] = '/' . $tag_base . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/';
							}
							$paths[] = '/?feed=' . $feed_format . '&tag=' . $term_slug;
							$paths[] = '/wp-' . $feed_format . '.php?tag=' . $term_slug;
							
						} else {

							if ( get_option( 'permalink_structure') ) {
								$paths[] = '/' . $taxonomy . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/';
							}
							$paths[] = '/?feed=' . $feed_format . '&' . $taxonomy . '=' . $term_slug;
							$paths[] = '/wp-' . $feed_format . '.php?' . $taxonomy . '=' . $term_slug;

						}

					}

				}

			}

		}

		// Generate date archive feed paths
		foreach ( $this->post_dates as $post_id => $date ) {
			$year  = $date[ 'y' ];
			$month = $date[ 'm' ];
			$day   = $date[ 'd' ];

			$urls[] = get_year_link( $year );
			$urls[] = get_month_link( $year, $month );
			$urls[] = get_day_link( $year, $month, $day );

			if ( true === $this->important && get_option( 'permalink_structure' ) ) {

				foreach ( $this->feed_formats as $feed_format ) {
					$paths[] = '?m=' . $year . $month . $day . '&feed=' . $feed_format;
				}

			}

		}

		// Generate user feed paths
		$users = get_users( $args = array() );
		$urls = array();
		foreach ( $users as $key => $user ) {
			if ( isset( $user->ID ) ) {
				$user_id = $user->ID;

				$urls[] = get_author_feed_link( $user_id, '' );

				if ( true === $this->important ) {

					if ( ! get_option( 'permalink_structure' ) ) {
						$urls[] = '?author=' . $user_id;
					}

					foreach ( $this->feed_formats as $feed_format ) {

						$urls[] = get_author_feed_link( $user_id, $feed_format );

					}

				}

			}
		}
		$user_feed_paths = $this->strip_site_root( $urls );
		$paths = array_merge( $paths, $user_feed_paths );

		// Strip paths which don't actually exist
		$urls = array();
		$content_type = '';
		foreach ( $paths as $key => $path ) {
			$url = home_url( $path );

			if ( true === $this->important ) {
//				$response = wp_remote_head( $url, array( 'user-agent' => 'strattic-api' ) );
//				$content_type = $response[ 'headers' ][ 'content-type' ];
//ryans_log( $path );
			}

			// Ignore anything with a text/html content type (everything else is presumably something of interest)
			if ( 'text/html' !== $content_type ) {
				$urls[] = home_url( $path );
			}

		}

		return $urls;
	}

	/**
	 * Grabs list of redirects.
	 * Looks for redirects from popular plugins.
	 *
	 * @param   array   $request  The request parameters
	 * @return  array
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
		$urls = array();
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

		return $urls;
	}

	/**
	 * Grabs list of user pages.
	 *
	 * @return  array
	 */
	public function get_user_pages() {

		$users = get_users( $args = array() );
		foreach ( $users as $key => $user ) {
			if ( isset( $user->ID ) ) {
				$user_id = $user->ID;

				$urls[] = get_author_posts_url( $user_id );

				if ( true === $this->important ) {
					$urls[] = '?author=' . $user_id;
				}

			}
		}

		return $urls;
	}

	/**
	 * Simplify API request by stripping out the site root.
	 *
	 * @param  array  $urls  The URLs
	 * @return array  $paths The paths for the URLs (without the site root)
	 */
	public function strip_site_root( $urls ) {
		$paths = array();

		foreach ( $urls as $url ) {
			$path = str_replace( home_url(), '', $url );
			$paths[] = $path;
		}

		return $paths;
	}

	/**
	 * Bail on API requests.
	 * We have no use for the response body, so we just quit as soon as the header returns.
	 * This saves substantial time in requesting pages.
	 */
	public function api_request_bailer() {

		if ( 'strattic-api' === $_SERVER[ 'HTTP_USER_AGENT' ] ) {
			die( 'Strattic API request complete' );
		}

	}

}
new Strattic_API;
