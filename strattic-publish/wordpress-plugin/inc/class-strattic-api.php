<?php

/*
Features to add:
	events-manager
	bbpress
	Events Calendar (modern tribe)
	Google maps https://wordpress.org/plugins/wp-google-maps/
	WP Realty (if it's still around)
	WPML
*/

/**
 * Strattic Temporary Archive Fix.
 * 
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_API extends Strattic_Core {

	private $feed_formats = array( 'rss2', 'rss', 'rdf', 'atom' );
	private $post_ids;
	private $taxonomy_term_ids;
	private $important = false;
	private $post_dates;
	private $authors;
	private $important_urls;
	private $normal_urls;
	private $unimportant_urls;
	private $paths;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		if ( '/strattic-api/' === $this->get_current_path() ) {
			add_action( 'template_redirect', array( $this, 'get_direct_api_request' ) );
		}

		//add_action( 'template_redirect', array( $this, 'api_request_bailer' ) );
		//add_action( 'the_content', array( $this, 'get_paginated_urls' ), 4 );
	}

	/**
	 * Handle direct API Request.
	 *
	 * @param   array  $request  The request parameters
	 * @return  array  URLs
	 */
	public function get_direct_api_request() {
		header( 'Content-Type: application/json' );
		header( 'HTTP/1.1 200 OK' );

		$paths = $this->get_everything();

		$deployment_settings = get_option( 'strattic-deployment-settings' );

		$keys = array(
			'deployment-type',
			'cloudfront-url',
			'cloudfront-id',
			's3-bucket',
			'email',
			'cloudfront-url',
			'cloudfront-id',
			's3-bucket',
			'email',
		);
		foreach ( $keys as $key ) {

			if ( isset( $deployment_settings[ $key ] ) ) {
				$settings[ $key ] = $deployment_settings[ $key ];
			}

		}

		$data = array(
			'settings' => $settings,
			'paths'    => $paths,
		);

		echo json_encode( $data );

		die;
	}

	/**
	 * Grabs all the thingz!
	 *
	 * @param   array  $request  The request parameters
	 * @return  array  URLs
	 */
	public function get_everything( $request = '' ) {

		$time_start = microtime( true );

		// Increase maximum execution time to make sure that we can gather everything
		ini_set( 'max_execution_time', self::TIME_LIMIT );
		set_time_limit ( self::TIME_LIMIT );
		ini_set( 'memory_limit', self::MEMORY_LIMIT . 'M' );

		// Get all the required URLs
		$this->super_important_urls = array( '/' );
		$this->important_urls = array();
		$this->normal_urls = array( '/404.html', '/robots.txt' );
		$this->unimportant_urls = array();

//		$this->get_taxonomy_archives();
//		$execution_times[ 'get_taxonomy_archives' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_all_posts();
		$execution_times[ 'get_all_posts' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_post_type_archives();
		$execution_times[ 'get_post_type_archives' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_extra_pagination();
		$execution_times[ 'get_extra_pagination' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_date_archives();
		$execution_times[ 'get_date_archives' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_all_terms();
		$execution_times[ 'get_all_terms' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_feeds();
		$execution_times[ 'get_feeds' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_user_pages();
		$execution_times[ 'get_user_pages' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_static_files();
		$execution_times[ 'get_static_files' ] = round( microtime( true ) - $time_start, 2 );

		$this->comeet_redirects();
		$execution_times[ 'comeet_redirects' ] = round( microtime( true ) - $time_start, 2 );

		$this->get_redirects();
		$execution_times[ 'redirects' ] = round( microtime( true ) - $time_start, 2 );

		$this->super_important_urls[] = 'end-first-stage';
		$this->important_urls[] = 'end-second-stage';

		$urls = array_merge( $this->super_important_urls, $this->important_urls );

		$urls = array_merge( $urls, $this->normal_urls );

		$this->important_urls[] = 'end-third-stage';
		$urls = array_merge( $urls, $this->unimportant_urls );

		$urls = $this->strip_anchors( $urls );

		$paths = $this->strip_site_root( $urls );

		$paths = array_unique( $paths );

		// Add execution time header
		$execution_time = '';
		foreach ( $execution_times as $function => $time ) {

			if ( isset( $previous_time ) ) {
				$t = $time - $previous_time;
			} else {
				$t = $time;
			}

			$execution_time .= $function . '=' . $t . 's; ';
			$previous_time = $time;
		}
		header( 'Execution-time: ' . $execution_time );

		// Stash the URLs for later use
		update_option( 'strattic-paths', $paths, 'no' );

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

		foreach ( $paths as $key => $path ) {
			if ( '' === $path ) {
				unset( $paths[ $key ] );
			}
		}
		$paths = array_unique( $paths );

		// Store scan time (only stores when a complete scan is done)
		update_option( 'strattic-last-scan-time', time(), 'no' );

		return $paths;
	}

	/**
	 * Grab extra pagination pages.
	 * In particular, the home page pagination and blog post paginations.
	 *
	 * @global  object $wp_rewrite
	 * @return  array  URLs
	 */
	public function get_extra_pagination() {
		global $wp_rewrite;

		$start_urls = array(
			'Home URL'       => home_url(),
			'Kahena blog posts' => home_url() . '/blog', // Temporary hack to ensure that pages used as a blog work with pagination
		);

		if ( '0' !== get_option( 'page_for_posts' ) ) {
			$start_urls[ 'Blog posts URL' ] = get_permalink( get_option( 'page_for_posts' ) );
		}

		foreach ( $start_urls as $description => $url ) {
			$posts_count = wp_count_posts( 'post' )->publish;

			$this->important_urls = array_merge( $this->important_urls, $this->get_archive_pagination_urls( $url, $posts_count ) );
		}

	}


	/**
	 * Grab all date archives.
	 *
	 * @return  array  URLs
	 */
	public function get_date_archives() {
		global $wpdb;

		if ( null === $this->post_dates ) {
			$this->get_all_posts(); // If posts weren't already accessed, then access them anyway, as that primes the $this->post_dates var
		}

		// Get number of posts in each year
		$query = $wpdb->prepare('
			SELECT YEAR(%1$s.post_date) AS `year`, count(%1$s.ID) as `posts`
			FROM %1$s
			WHERE %1$s.post_type IN ("post")
			AND %1$s.post_status IN ("publish")
			GROUP BY YEAR(%1$s.post_date)
			ORDER BY %1$s.post_date',
			$wpdb->posts,
			'x',
			'x',
			'x',
			'x',
			'x',
			'x'
		);
		$yearly_post_counts = $wpdb->get_results( $query );
		$years = array();
		foreach ( $yearly_post_counts as $key => $year ) {
			$years[ $year->year ] = $year->posts;
		}

		foreach ( $this->post_dates as $post_id => $date ) {

			$year  = $date[ 'y' ];
			$month = $date[ 'm' ];
			$day   = $date[ 'd' ];

			// Get links
			$links[ 'year' ]  = get_year_link( $year );
			$links[ 'month' ] = get_month_link( $year, $month );
			$links[ 'day' ]   = get_day_link( $year, $month, $day );

			// Get paginated versions of the links
			foreach ( $links as $url ) {
				$this->important_urls[] = $url;

				if ( isset( $years[ $year ] ) ) {
					$posts_count = $years[ $year ];

					$archive_pagination_urls = $this->get_archive_pagination_urls( $url, $posts_count );
					$this->normal_urls = array_merge( $this->normal_urls, $archive_pagination_urls );

				}

			}

			/* Non-permalink code temporarily removed
			// Create non-permalink link
			/* Non-permalink code temporarily removed
			$this->normal_urls[] = home_url( '?m=' . $year . $month . $day );
			*/

		}

	}

	/**
	 * Grab all taxonomy archives.
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

					$urls[] = get_term_link( $term_slug, $taxonomy );
					/*
					if ( 'category' === $taxonomy ) {
						$paths[] = '/' . $category_base . '/' . $term_slug . '/';
					} else if ( 'post_tag' === $taxonomy ) {
						$paths[] = '/' . $tag_base . '/' . $term_slug . '/';
					} else {
						$paths[] = '/' . $taxonomy . '/' . $term_slug . '/';
					}
					*/

				}

				/* Non-permalink code temporarily removed

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

				*/

			}

		}
//print_r( $paths );die;
$this->super_important_urls[] = 'xxxxxxxxxxxxxxx';
		// Generate paths from URLs
		foreach ( $urls as $key => $full_url ) {
			$url = str_replace( home_url(), '', $full_url );
			$this->super_important_urls[] = $url;

			/* Removed non-permalink URLs
			// Include pagination URLs for non-permalink URLs
			if ( strpos( $url, '?' ) === false ) {
				$archive_pagination_urls = $this->get_archive_pagination_urls( $url );
				$this->normal_urls = array_merge( $this->normal_urls, $archive_pagination_urls );
			}
			*/

		}

	}

	/**
	 * Grabs list of all posts.
	 * Unlike the standard WordPress API, this grabs ALL posts.
	 * Grabs all posts regardless of post-type.
	 */
	public function get_all_posts() {

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

							// Updated posts should be published first
							if ( get_the_modified_date( 'U', get_the_ID() ) > get_option( 'strattic-last-scan-time' ) ) {
								$this->super_important_urls[] = get_the_permalink();
							} else {
								$this->important_urls[] = get_the_permalink();
							}

						}

						// Get embed URLs
// HACKED OUT DUE TO PROBLEMS WITH THE deploy.py FILE NOT ADDING IN TRAILING SLASHES AS REQUIRED
//						$this->normal_urls[] = get_post_embed_url( get_the_ID() );
						/* Non-permalink code temporarily removed
						$this->normal_urls[] = add_query_arg( array( 'embed' => 'true' ), get_the_permalink() );
						*/

						/* Non-permalink code temporarily removed
						// Get shortlink URLs
						$this->normal_urls[] = wp_get_shortlink( get_the_ID() );
						*/

						// Get attachment URLs (only bother with recent ones, as rest will get picked up by the server later)
						if (
							get_the_date( 'U' ) > get_option( 'strattic-last-scan-time' )
							&&
							'attachment' === $post_type
						) {

							$this->super_important_urls[] = wp_get_attachment_url( get_the_ID() ); // Full size file URL (this is not the attachment post URL)

							foreach ( get_intermediate_image_sizes() as $size ) {

								$src = wp_get_attachment_image_src( get_the_ID(), $size );

								if ( isset( $src[ 0 ] ) ) {
									$this->super_important_urls[] = $src[ 0 ];
								}

							}

						}

						/* Temporarily removed
						*/

						// Include pagination URLs for comments pages
						$url = get_the_permalink();

						$max_pages = $this->get_comment_pages_count();

						$count = 2;
						while ( $count < $max_pages ) {
							$this->normal_urls[] = get_comments_pagenum_link( $count, $max_pages );
							$count++;
						}

						// Include pagination URLs pages
						$the_post = get_post();
						$max_num_pages = substr_count( $the_post->post_content, '<!--nextpage-->' ) + 1;

						$pagenum = 2;
						while ( $pagenum <= $max_num_pages ) {

							$new_url = trailingslashit( get_permalink() ) . user_trailingslashit( $pagenum, 'single_paged' );
							$this->normal_urls[] = $new_url;

							$pagenum++;
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

						// Store author IDs (used later by author profile system - can't use get_users() as it ignores authors who aren't currently a user on the site)
						$this->authors[] = get_the_author_meta( 'ID' );
						$this->authors = array_unique( $this->authors );
					}

					wp_reset_postdata();
				}

				$offset = $offset + self::PER_PAGE;
			}

		}

	}


	/**
	 * Get post type archive URLs.
	 */
	public function get_post_type_archives() {

		// Loop through ALL post types
		$post_types = get_post_types( array( /*'_builtin' => true,*/ 'public' => true ), 'names', 'and' );
		foreach ( $post_types  as $post_type ) {

			$this->important_urls[] = get_post_type_archive_link( $post_type );

		}

	}

	/**
	 * Grabs list of all terms.
	 * Unlike the standard WordPress API, this grabs ALL terms.
	 */
	public function get_all_terms() {

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

					$this->important_urls[] = $url;
					$this->taxonomy_term_ids[ $taxonomy ][] = $term_id;

				}

				$offset = $offset + self::PER_PAGE;
			}

		}

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
		$this->super_important_urls[] = home_url() . '/' . $feed_slug . '/'; // main feed
		$this->super_important_urls[] = home_url() . '/' . $comments_slug . '/' . $feed_slug . '/'; // main comments feed

		/* Non-permalink code temporarily removed
		$this->normal_urls[] = home_url() . '/?feed=comments'; //XXX raw PHP file main comments feed
		*/

		foreach ( $this->feed_formats as $feed_format ) {
			$this->unimportant_urls[] = home_url() . '/' . $feed_slug . '/' . $feed_format . '/'; // main feed
			/* Non-permalink code temporarily removed
			$this->normal_urls[] = home_url() . '/?feed=' . $feed_format; // non-permalink main feed
			$this->normal_urls[] = home_url() . '/wp-' . $feed_format . '.php'; // raw PHP file main feed
			*/
			$this->unimportant_urls[] = home_url() . '/' . $comments_slug . '/' . $feed_slug . '/' . $feed_format . '/'; // main comments feed
			/* Non-permalink code temporarily removed
			$this->normal_urls[] = home_url() . '/wp-comments' . $feed_format . '.php'; // raw PHP file main comments feed
			$this->normal_urls[] = home_url() . '/?feed=comments' . $feed_format; // non-permalink main comments feed
			*/
		}

		// Generate single post comment feed paths

		if ( null === $this->post_ids ) {
			$this->get_all_posts(); // If posts weren't already accessed, then access them anyway, as that primes the $this->post_ids var
		}
		foreach ( $this->post_ids as $post_id ) {
			$page_path = str_replace( home_url(), '', get_permalink( $post_id ) );

			if ( get_option( 'permalink_structure') ) {
				$this->normal_urls[] = home_url() . $page_path . $feed_slug . '/';
			}
			foreach ( $this->feed_formats as $feed_format ) {

				if ( get_option( 'permalink_structure') ) {
					$this->normal_urls[] = home_url() . $page_path . $feed_slug . '/' . $feed_format . '/';
				}

				/* Non-permalink code temporarily removed
				$path = $page_path;
				$path = add_query_arg( 'feed', $feed_format, $path );
				$paths[] = add_query_arg( 'feed', $feed_format, $path );
				*/

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
						$this->normal_urls[] = home_url() . '/' . $category_base . '/' . $term_slug . '/' . $feed_slug . '/';
					} else if ( 'post_tag' === $taxonomy ) {
						$this->normal_urls[] = home_url() . '/' . $tag_base . '/' . $term_slug . '/' . $feed_slug . '/';
					} else {
						$this->normal_urls[] = home_url() . '/' . $taxonomy . '/' . $term_slug . '/' . $feed_slug . '/';
					}

				}

				foreach ( $this->feed_formats as $feed_format ) {

					if ( 'category' === $taxonomy ) {

						if ( get_option( 'permalink_structure') ) {
							$this->unimportant_urls[] = home_url() . '/' . $category_base . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/';
						}
						/* Non-permalink code temporarily removed
						$paths[] = '/?feed=' . $feed_format . '&cat=' . $term_id;
						$paths[] = '/wp-' . $feed_format . '.php?cat=' . $term_id;
						*/

					} else if ( 'post_tag' === $taxonomy ) {

						if ( get_option( 'permalink_structure') ) {
							$this->unimportant_urls[] = home_url() . '/' . $tag_base . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/';
						}
						/* Non-permalink code temporarily removed
						$paths[] = '/?feed=' . $feed_format . '&tag=' . $term_slug;
						$paths[] = '/wp-' . $feed_format . '.php?tag=' . $term_slug;
						*/
						
					} else {

						if ( get_option( 'permalink_structure') ) {
							$this->unimportant_urls[] = home_url() . '/' . $taxonomy . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/';
						}
						/* Non-permalink code temporarily removed
						$paths[] = '/?feed=' . $feed_format . '&' . $taxonomy . '=' . $term_slug;
						$paths[] = '/wp-' . $feed_format . '.php?' . $taxonomy . '=' . $term_slug;
						*/

					}

				}

			}

		}

		// Generate date archive feed paths
		foreach ( $this->post_dates as $post_id => $date ) {
			$year  = $date[ 'y' ];
			$month = $date[ 'm' ];
			$day   = $date[ 'd' ];

			$this->normal_urls[] = get_year_link( $year );
			$this->normal_urls[] = get_month_link( $year, $month );
			$this->normal_urls[] = get_day_link( $year, $month, $day );

			/* Non-permalink code temporarily removed
			if ( true === $this->important && get_option( 'permalink_structure' ) ) {

				foreach ( $this->feed_formats as $feed_format ) {
					$paths[] = '?m=' . $year . $month . $day . '&feed=' . $feed_format;
				}

			}
			*/



		}

		// Generate user feed paths
		foreach ( $this->authors as $key => $user_id ) {

			$this->normal_urls[] = get_author_feed_link( $user_id, '' );

			/* Non-permalink code temporarily removed
			if ( true === $this->important ) {

				if ( ! get_option( 'permalink_structure' ) ) {
					$paths[] = '?author=' . $user_id;
				}

			}
			*/

		}

	}

	/**
	 * Grabs list of redirects.
	 * Looks for redirects from popular plugins.
	 *
	 * @return  array
	 */
	public function get_redirects() {

		// Safe Redirect Manager plugin - from 10up
		$query = new WP_Query( array(
			'posts_per_page'         => 1000, // This may sometimes query too many posts - but the code is simpler this way ;)
			'offset'                 => 0,
			'post_type'              => 'redirect_rule',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$this->important_urls[] = home_url() . get_post_meta( get_the_ID(), '_redirect_rule_from', true );
			}

			wp_reset_postdata();
		}

		// Redirection plugin - from John Godley
		// Note: This may glitch out if using a sub-folder WordPress multisite install - this is because the Redirection plugin manually adds the folder slug into the table column
		global $wpdb;
		$table_name = $wpdb->prefix . 'redirection_items';
		$field_name = 'url';
		$prepared_statement = $wpdb->prepare( "SELECT {$field_name} FROM {$table_name} WHERE  action_type = %d", 'url' );
		if ( $wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name ) {
			// Do nothing since redirection table doesn't exist
		} else {

			$redirection_data = $wpdb->get_col( $prepared_statement );
			if ( is_array( $redirection_data ) ) {
				foreach ( $redirection_data as $key => $path ) {
					$this->important_urls[] = home_url() . $path;
				}
			}

		}

	}

	/**
	 * Grabs list of user pages.
	 */
	public function get_user_pages() {

		if ( null === $this->post_dates ) {
			$this->get_all_posts(); // If posts weren't already accessed, then access them anyway, as that primes the $this->post_dates var
		}

		foreach ( $this->authors as $key => $user_id ) {

			$url = get_author_posts_url( $user_id );
			$this->important_urls[] = $url;

			if ( true === $this->important ) {
				$number_posts = count_user_posts( $user_id );

				$archive_pagination_urls = $this->get_archive_pagination_urls( $url, $number_posts );
				$this->normal_urls = array_merge( $this->normal_urls, $archive_pagination_urls );
			}

			/* Non-permalink code temporarily removed
			$this->normal_urls[] = '?author=' . $user_id;
			*/

		}

	}

	/**
	 * Grabs modified static files.
	 */
	public function get_static_files() {
		$this->paths = array();

		$extensions = array( 'bmp', 'css', 'doc', 'docx', 'eot', 'gif', 'html', 'ico', 'jpeg', 'jpg', 'js', 'json', 'm4a', 'md', 'mp3', 'mp4', 'odp', 'ods', 'odt', 'ogg', 'otf', 'patch', 'pdf', 'png', 'ppt', 'pptx', 'rtf', 'svg', 'swf', 'textile', 'tif', 'tiff', 'ttf', 'txt', 'vtt', 'wav', 'webm', 'woff', 'woff2', 'xls', 'xlsx', 'xml', 'xsl' );

		$this->dir_walk( array( $this, 'path_scan_callback' ), ABSPATH, $extensions, true );
	}

	/**
	* Calls a function for every file in a folder.
	*
	* @author Vasil Rangelov a.k.a. boen_robot
	*
	* @param string $callback The function to call. It must accept one argument that is a relative filepath of the file.
	* @param string $dir The directory to traverse.
	* @param array $types The file types to call the function for. Leave as NULL to match all types.
	* @param bool $recursive Whether to list subfolders as well.
	* @param string $baseDir String to append at the beginning of every filepath that the callback will receive.
	*
	* @access private
	*/
	private function dir_walk($callback, $dir, $types = null, $recursive = false, $baseDir = '') {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file === '.' || $file === '..') {
					continue;
				}
				if (is_file($dir . $file)) {
					if (is_array($types)) {
						if (!in_array(strtolower(pathinfo($dir . $file, PATHINFO_EXTENSION)), $types, true)) {
							continue;
						}
					}
					$callback($baseDir . $file);
				}elseif($recursive && is_dir($dir . $file)) {
					$this->dir_walk($callback, $dir . $file . DIRECTORY_SEPARATOR, $types, $recursive, $baseDir . $file . DIRECTORY_SEPARATOR);
				}
			}
			closedir($dh);
		}
	}

	private function path_scan_callback( $path ) {
		$path = $path;

		$modified_time = filemtime( ABSPATH . $path );

		// Only add file if it's newer than the last scan time
		if ( $modified_time > get_option( 'strattic-last-scan-time' ) ) {
			$url = home_url() . '/' . $path;

			if (
				'wp-content/cache/' !== substr ( $path, 0, 17 ) // always ignore cache folder files - they're of no use in production anyway
			) {
				$this->unimportant_urls[] = $url;
			}

		}

	}

	/**
	 * Grabs list of Comeet redirects.
	 *
	 * @return  array  List of URLs
	 */
	public function comeet_redirects() {

		$options = get_option( 'Comeet_Options' );

		// Get list of URLs from external site
		$comeet_url = esc_url( 'https://www.comeet.co/careers-api/2.0/company/' . $options[ 'comeet_uid' ] . '/positions?token=' . $options[ 'comeet_token' ] ); // path to JSON file

		$response = wp_remote_get( $comeet_url, array( 'user-agent' => 'strattic-api-max-pagination' ) );

		if ( isset( $response[ 'body' ] ) && '' !== $response[ 'body' ] ) {
			$data = json_decode( $response[ 'body' ] );
		} else {
			return;
		}

		foreach ( $data as $key => $item ) {

			if ( isset( $item->url_active_page ) ) {
				$url = str_replace( $this->get_admin_settings()[ 'cloudfront-url' ][ 'value' ], STRATTIC_HOME_URL, $item->url_active_page );
				$this->important_urls[] = $url;
			}

		}

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
	 * Anchors are not required on links, so let's remove them.
	 *
	 * @param  array  $urls  The URLs
	 * @return array  $paths The paths for the URLs (without the anchors)
	 */
	public function strip_anchors( $urls ) {
		$deanchored_urls = array();

		foreach ( $urls as $url ) {
			$deanchored_urls[] = strtok( $url, '#' );
		}

		return $deanchored_urls;
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

	/**
	 * Getting the archive pagination URLs.
	 * We use http requests due to not be able to determine the max number of pages without loading the pages.
	 *
	 * @access  private
	 * @global  object  $wp_rewrite  Main WordPress query object
	 * @param   string  $url         The URL to add pagination to
	 * @param   int     $posts_count The number of posts to be paginated
	 * @return  array   $urls        The URLs
	 */
	private function get_archive_pagination_urls( $url, $posts_count = null ) {
		global $wp_rewrite;

		$urls = array();

		// Estimate of number of pages of pagination
		$posts_per_page = absint( get_option( 'posts_per_page' ) );

		if ( null === $posts_count ) {
			$posts_count = absint( wp_count_posts( 'post' )->publish );
		}
		$posts_count = absint( $posts_count );

		$max_num_pages = ceil( $posts_count / $posts_per_page );

		// Bail out if home page URL is not in URL (occurs if no blog post page is set)
		if ( strpos( $url, home_url() ) === false) {
			return false;
		}

		$pagenum = 2;
		while ( $pagenum <= $max_num_pages ) {

			$request = user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );

			if ( '/' === substr( $url, -1 ) ) {
				$urls[] = $url . $request;
			} else {
				$urls[] = $url . '/' . $request;
			}

			$pagenum++;
		}

		/* REMOVED DUE TO TOO MUCH RESOURCES BEING USED

		// Basic authentication code is based on work from https://johnblackbourn.com/wordpress-http-api-basicauth/
		$headers = array( 'user-agent' => 'strattic-api-max-pagination' );
		if ( defined( 'STRATTIC_PASSWORD' ) ) {
			$username = 'strattic';
			$headers[ 'Authorization' ] = 'Basic ' . base64_encode( $username . ':' . STRATTIC_PASSWORD );
		}

		$response = wp_remote_get(
			$url,
			array(
				'headers' => $headers
			)
		);

		if ( isset( $response[ 'body' ] ) && '' !== $response[ 'body' ] ) {

			$json_urls = $response[ 'body' ];
			$decoded_urls = json_decode( $json_urls );
			if ( is_array( $decoded_urls ) ) {
				$this->important_urls = $decoded_urls;
			}

		}
		*/

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

			$max_num_pages = $wp_query->max_num_pages;

			$count = 2;
			while ( $count <= $max_num_pages ) {
				$paginated_url = get_pagenum_link( $count );
				$urls[] = $paginated_url;

				$count++;
			}

//ryans_log( print_r( $wp_query, true ) );

			echo json_encode( $urls );
			die;

		}

	}

	/**
	 * Get the comment pages count.
	 * WP's get_comment_pages_count() doesn't work without all this stuff being processed first.
	 */
	private function get_comment_pages_count() {
		$separate_comments = false;
		global $wp_query, $withcomments, $post, $wpdb, $id, $comment, $user_login, $user_ID, $user_identity, $overridden_cpage;

		$req = get_option( 'require_name_email' );
		$commenter = wp_get_current_commenter();
		$comment_author = $commenter['comment_author'];
		$comment_author_email = $commenter['comment_author_email'];
		$comment_author_url = esc_url( $commenter['comment_author_url'] );

		$comment_args = array(
			'orderby'                   => 'comment_date_gmt',
			'order'                     => 'ASC',
			'status'                    => 'approve',
			'post_id'                   => $post->ID,
			'no_found_rows'             => false,
			'update_comment_meta_cache' => false, // We lazy-load comment meta for performance.
		);

		if ( get_option( 'thread_comments' ) ) {
			$comment_args['hierarchical'] = 'threaded';
		} else {
			$comment_args['hierarchical'] = false;
		}

		$per_page = 0;
		if ( get_option( 'page_comments' ) ) {
			$per_page = (int) get_query_var( 'comments_per_page' );
			if ( 0 === $per_page ) {
				$per_page = (int) get_option( 'comments_per_page' );
			}

			$comment_args['number'] = $per_page;
			$page                   = (int) get_query_var( 'cpage' );

			if ( $page ) {
				$comment_args['offset'] = ( $page - 1 ) * $per_page;
			} elseif ( 'oldest' === get_option( 'default_comments_page' ) ) {
				$comment_args['offset'] = 0;
			} else {
				// If fetching the first page of 'newest', we need a top-level comment count.
				$top_level_query = new WP_Comment_Query();
				$top_level_args  = array(
					'count'   => true,
					'orderby' => false,
					'post_id' => $post->ID,
					'status'  => 'approve',
				);

				if ( $comment_args['hierarchical'] ) {
					$top_level_args['parent'] = 0;
				}

				if ( isset( $comment_args['include_unapproved'] ) ) {
					$top_level_args['include_unapproved'] = $comment_args['include_unapproved'];
				}

				$top_level_count = $top_level_query->query( $top_level_args );

				$comment_args['offset'] = ( ceil( $top_level_count / $per_page ) - 1 ) * $per_page;
			}
		}

		$comment_args  = apply_filters( 'comments_template_query_args', $comment_args );
		$comment_query = new WP_Comment_Query( $comment_args );
		$_comments     = $comment_query->comments;

		// Trees must be flattened before they're passed to the walker.
		if ( $comment_args['hierarchical'] ) {
			$comments_flat = array();
			foreach ( $_comments as $_comment ) {
				$comments_flat[]  = $_comment;
				$comment_children = $_comment->get_children(
					array(
						'format'  => 'flat',
						'status'  => $comment_args['status'],
						'orderby' => $comment_args['orderby'],
					)
				);

				foreach ( $comment_children as $comment_child ) {
					$comments_flat[] = $comment_child;
				}
			}
		} else {
			$comments_flat = $_comments;
		}

		$wp_query->comments = apply_filters( 'comments_array', $comments_flat, $post->ID );

		$comments                        = &$wp_query->comments;
		$wp_query->comment_count         = count( $wp_query->comments );

		return $comment_query->max_num_pages;
	}

}
new Strattic_API;
