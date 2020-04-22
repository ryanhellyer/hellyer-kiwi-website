<?php

/**
 * Strattic Temporary Archive Fix.
 *
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_URLs extends Strattic_Core {

	private $feed_formats = array( 'rss2', 'rss', 'rdf', 'atom' );
	private $post_ids;
	private $taxonomy_term_ids;
	private $important = false;
	private $post_dates;
	private $authors;
	private $important_urls;
	private $paths;
	private $high_priority_terms = array();
	public $priority;
	public $ignore_paths;
	public $extensions = array( 'bmp', 'css', 'doc', 'docx', 'eot', 'gif', 'html', 'ico', 'jpeg', 'jpg', 'js', 'json', 'm4a', 'md', 'mp3', 'mp4', 'odp', 'ods', 'odt', 'ogg', 'otf', 'patch', 'pdf', 'png', 'ppt', 'pptx', 'rtf', 'svg', 'swf', 'textile', 'tif', 'tiff', 'ttf', 'txt', 'vtt', 'wav', 'webm', 'woff', 'woff2', 'xls', 'xlsx', 'xml', 'xsl' );

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Feed formats
		$features = get_option( 'strattic-disable-features' );
		if ( is_array( $features ) ) {

			foreach ( $features as $feature => $on ) {

				if ( 'on' === $on ) {
					$feature = str_replace( '-feeds', '', $feature );

					foreach ( $this->feed_formats as $key => $format ) {

						if ( $format === $feature ) {
							unset( $this->feed_formats[ $key ] );
						}

					}

				}

			}
		}

		// Set priority (this is overridden during deloyments)
		if ( isset( $_GET[ 'priority' ] ) ) {
			$this->priority = absint( $_GET[ 'priority' ] );
		} else {
			$this->priority = 0;
		}


		// Static file paths to ignore
		$uploads_dir = wp_upload_dir();
		$base_dir = $uploads_dir[ 'basedir' ];
		$this->uploads_path = $uploads_dir[ 'basedir' ].'/';
		$this->ignore_paths = array(
			$uploads_dir[ 'basedir' ].'/', // Ignore uploads directories
		);

		if ( strpos( $this->get_current_path(), '/strattic-urls' ) !== false ) {
			add_action( 'template_redirect', array( $this, 'dump_url_list' ) );
		}

		if ( strpos( $this->get_current_path(), '/strattic-api-request' ) !== false ) {
			add_action( 'template_redirect', array( $this, 'direct_api_request' ), 1 );
		}

		if ( strpos( $this->get_current_path(), '/strattic-publish-request' ) !== false ) {
			add_action( 'template_redirect', array( $this, 'api_deploy' ) );
		}
		//add_action( 'the_content', array( $this, 'get_paginated_urls' ), 4 );
	}

	public function api_deploy() {
		$strattic_ajax = new Strattic_AJAX;
		$strattic_ajax->api_deploy();
	}
	/**
	 * Get URL list.
	 *
	 * @param   array  $request  The request parameters
	 * @return  array  URLs
	 */
	public function dump_url_list() {

		$distribution_id = null;
		if ( isset( $_GET[ 'distribution_id' ] ) ) {
			$distribution_id = $_GET[ 'distribution_id' ];
		}

		$files = $this->get_everything( $distribution_id );

		echo 'Total: ' . count( $files ) . "\n<br />\n";
		if ( $distribution_id ) {
			echo 'Last relevant publication timestamp: ' . $this->get_last_relevant_publish_date( $distribution_id ) . "\n<br />\n";
		}
		foreach ( $files as $key => $file ) {
			echo "\n\n<br />\n";
			var_dump( $file );
		}

		die;
	}

	/**
	 * Output direct API Request.
	 *
	 * @param   array  $request  The request parameters
	 * @return  array  URLs
	 */
	public function direct_api_request( $distribution_id = null ) {
		status_header( 200 );
		header( 'Content-Type: application/json' );

		$data = $this->get_direct_api_request( $distribution_id = null );

		echo json_encode( $data );
		die;
	}

	/**
	 * Handle direct API Request.
	 *
	 * @param   array  $request  The request parameters
	 * @return  array  URLs
	 */
	public function get_direct_api_request( $distribution_id = null ) {

		$distribution_id = null;
		if ( isset( $_GET[ 'distribution_id' ] ) ) {
			$distribution_id = absint( $_GET[ 'distribution_id' ] );
		}

		// Get list of URLs
		$paths = $this->get_everything( $distribution_id );

		// Send the request
		$site_id = $this->get_current_site_strattic_id();
		$path = 'sites/' . $site_id . '/publish';
		$data = array(
			'siteDistributionId' => absint( $distribution_id ),
			'paths'              => array_values( $paths ),
		);

		return $data;
	}

	/**
	 * Grabs all the thingz!
	 *
	 * @param   array  $distribution_id  The distribution ID
	 * @return  array  URLs
	 */
	public function get_everything( $distribution_id = null ) {

		$time_start = microtime( true );

		// Increase maximum execution time to make sure that we can gather everything
		ini_set( 'max_execution_time', self::TIME_LIMIT );
		set_time_limit ( self::TIME_LIMIT );
		ini_set( 'memory_limit', self::MEMORY_LIMIT . 'M' );

		// Get all the required URLs
		$this->urls[] = array(
			'url'                => '/',
			'type'               => 'page',
			'priority'           => '10',
		);

		$this->urls[] = array(
			'url'                => '/404.html',
			'type'               => '404',
			'priority'           => '8',
		);

		$this->urls[] = array(
			'url'                => '/robots.txt',
			'type'               => 'robots',
			'priority'           => '5',
		);

		// This is a hack, and can be removed once minification is implemented properly
		$this->urls[] = array(
			'url'                => '/wp-content/uploads/minit/minit.css',
			'type'               => 'file',
			'priority'           => '9',
		);

		$this->get_sitemaps();
		$this->log_data( 'after get_sitemaps' );

//		$this->get_taxonomy_archives();
		$this->get_search_urls();
		$this->log_data( 'after get_search_urls' );

		$this->get_all_posts();
		$this->log_data( 'after get_all_posts' );

		$this->get_polylang_homepages();
		$this->log_data( 'after get_polylang_homepages' );

		$this->get_post_type_archives();
		$this->log_data( 'after get_post_type_archives' );

		$this->get_extra_pagination();
		$this->log_data( 'after get_extra_pagination' );

		$this->get_date_archives();
		$this->log_data( 'after get_date_archives' );

		$this->get_all_terms();
		$this->log_data( 'after get_all_terms' );

		$this->get_categories_in_root();
		$this->log_data( 'after get_categories_in_root' );

		$this->get_feeds();
		$this->log_data( 'after get_feeds' );

		$this->get_user_pages();
		$this->log_data( 'after get_user_pages' );

		$this->get_static_files();
		$this->log_data( 'after get_static_files' );

		$this->comeet_redirects($distribution_id);
		$this->log_data( 'after comeet_redirects' );

		$this->get_redirects();
		$this->log_data( 'after get_redirects' );

		$this->get_deleted_files( $distribution_id );
		$this->log_data( 'after get_deleted_files' );

		// Add extra links
		$options = array(
			'manual-links', // legacy
			'discovered-links', // legacy
			'extra-links',
		);
		foreach ( $options as $option ) {
			$new_paths = get_option( 'strattic-' . $option );

			if ( is_array( $new_paths ) ) {

				foreach ( $new_paths as $key => $new_path ) {

					$this->urls[] = array(
						'path'                => $new_path,
						'type'               => 'page',
						'priority'           => '1',
					);

				}

			}

		}

		$urls = $this->strip_anchors( $this->urls );
		$paths = $this->strip_site_root( $urls );
		$paths = $this->remove_repeat_paths( $paths );

		// Stripping out unrequired/unwanted path data
		foreach ( $paths as $key => $path ) {

			// Strip any blank paths out
			if ( ! isset( $path[ 'path' ] ) || '' == $path[ 'path' ] ) {
				unset( $paths[ $key ] );
			}

			// Stripping out type and priority since they're not currently being used by the API
			unset( $paths[ $key ][ 'type' ] );

			// Strip out lower priority items
			if (
				isset( $this->priority )
				&&
				isset( $paths[ $key ][ 'priority' ] )
				&&
				$paths[ $key ][ 'priority' ] < $this->priority
			) {
				unset( $paths[ $key ] );
			}

		}

		// Strip out files modified since last completed publication if on full publish
		if ( null !== $distribution_id && '1' !== get_transient( 'strattic-is-full-publish' ) ) {
			$last_relevant_timestamp = $this->get_last_relevant_publish_date( $distribution_id );

			foreach ( $paths as $key => $path ) {

				// Strip out any paths which weren't modified recently
				if ( isset( $path['last_modified_date'] ) && $last_relevant_timestamp && $last_relevant_timestamp < $path['last_modified_date'] ) {
					unset( $paths[ $key ] );
				}
			}

		} else {
			delete_transient( 'strattic-is-full-publish' ); // shut off full publish ready for next time
		}

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

		$features = get_option( 'strattic-disable-features' );
		if ( isset( $features[ 'archive-pagination' ] ) && 'on' === $features[ 'archive-pagination' ] ) {
			return;
		}

		$start_urls = array(
			'Home URL'       => home_url(),
			'Kahena blog posts' => home_url() . '/blog', // Temporary hack to ensure that pages used as a blog work with pagination
		);

		if ( '0' !== get_option( 'page_for_posts' ) ) {
			$start_urls[ 'Blog posts URL' ] = get_permalink( get_option( 'page_for_posts' ) );
		}

		foreach ( $start_urls as $description => $url ) {
			$posts_count = wp_count_posts( 'post' )->publish;

			$archive_pagination_urls = $this->get_archive_pagination_urls( $url, $posts_count );
			foreach ( $archive_pagination_urls as $key => $new_url ) {

				$this->urls[] = array(
					'url'                => $new_url,
					'type'               => 'page',
					'priority'           => '5',
				);

			}

		}

	}

	/**
	 * Grab sitemaps.
	 */
	public function get_sitemaps() {

		$this->urls[] = array(
			'url'                => '/sitemap.xml',
			'type'               => 'robots',
			'priority'           => '5',
		);

		$this->urls[] = array(
			'url'                => '/sitemap_index.xml',
			'type'               => 'robots',
			'priority'           => '5',
		);

		// Yoast SEO plugin
		if ( defined( 'WPSEO_PATH' ) ) {

			$providers = array(
				new WPSEO_Post_Type_Sitemap_Provider(),
				new WPSEO_Taxonomy_Sitemap_Provider(),
				new WPSEO_Author_Sitemap_Provider(),
			);

			$external_providers = apply_filters( 'wpseo_sitemaps_providers', array() );

			foreach ( $external_providers as $provider ) {
				if ( is_object( $provider ) && $provider instanceof WPSEO_Sitemap_Provider ) {
					$providers[] = $provider;
				}
			}


			$links            = array();
			$entries_per_page = (int) apply_filters( 'wpseo_sitemap_entries_per_page', 1000 );

			foreach ( $providers as $provider ) {
				$links = array_merge( $links, $provider->get_index_links( $entries_per_page ) );
			}

			foreach ( $links as $link ) {
				$url = $link['loc'];
				$modified_time = strtotime( $link['lastmod'] );

				$this->urls[] = array(
					'url'                => $url,
					'type'               => 'robots',
					'priority'           => '5',
					'last_modified_date' => $modified_time,
				);

			}

		}

	}

	/**
	 * Grab all date archives.
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

		if ( is_array( $this->post_dates ) ) {
			foreach ( $this->post_dates as $post_id => $date ) {

				$year  = $date[ 'y' ];
				$month = $date[ 'm' ];
				$day   = $date[ 'd' ];

				// Get links
				$links[ 'year' ]  = get_year_link( $year );
				$links[ 'month' ] = get_month_link( $year, $month );
				$links[ 'day' ]   = get_day_link( $year, $month, $day );


				// Get paginated versions of the links
				foreach ( $links as $type => $url ) {

					if ( 'year' === $type ) {

						$posts_count = 0;
						if ( isset( $years[ $year ] ) ) {
							$posts_count = $years[ $year ];
						}

					} else if ( 'month' === $type ) {
						$posts_count = $this->monthly_post_count( $year, $month );
					} else if ( 'day' === $type ) {
						$posts_count = $this->daily_post_count( $year, $month, $day );
					}

					$this->urls[] = array(
						'url'                => $url,
						'type'               => 'page',
						'priority'           => '5',
					);

					$archive_pagination_urls = $this->get_archive_pagination_urls( $url, $posts_count );
					foreach ( $archive_pagination_urls as $new_url ) {

						$this->urls[] = array(
							'url'                => $new_url,
							'type'               => 'page',
							'priority'           => '5',
						);

					}
				}

			}

			/* Non-permalink code temporarily removed
			// Create non-permalink link
			/* Non-permalink code temporarily removed
			$this->urls[] = home_url( '?m=' . $year . $month . $day );
			*/

		}

	}

	/**
	 * Monthly post count.
	 *
	 * @param   string  $year   The year
	 * @param   string  $month  The month
	 * @return  string
	 */
	protected function monthly_post_count( $year, $month ) {
		global $wpdb;

		$query = "SELECT count(ID) FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish' AND YEAR(post_date)=%d AND MONTH(post_date)=%d";

		return $wpdb->get_var( $wpdb->prepare( $query, $year, $month ) );
	}

	/**
	 * Daily post count.
	 *
	 * @param   string  $year   The year
	 * @param   string  $month  The month
	 * @param   string  $daily  The day
	 * @return  string
	 */
	protected function daily_post_count( $year, $month, $day ) {
		global $wpdb;

		$query = "SELECT count(ID) FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish' AND YEAR(post_date)=%d AND MONTH(post_date)=%d AND DAY(post_date)=%d";

		return $wpdb->get_var( $wpdb->prepare( $query, $year, $month, $day ) );
	}

	/**
	 * Grabs list of all posts.
	 * Unlike the standard WordPress API, this grabs ALL posts.
	 * Grabs all posts regardless of post-type.
	 */
	public function get_all_posts() {

		// Loop through ALL post types
		$post_types = get_post_types( array( /*'_builtin' => true,*/ 'public' => true ), 'names', 'and' );
		$post_types['attachment'] = 'attachment';
		foreach ( $post_types  as $post_type ) {

			$post_statuses = array( 'publish', 'private', 'inherit', 'trash' /* for attachments */ );

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

				// Add posts (including specific polylang languages)
				$this->get_posts( $offset, $post_type, $post_statuses );
				if ( class_exists( 'Polylang' ) ) {
					$polylangs = pll_languages_list();

					foreach ( $polylangs as $polylang ) {
						$this->get_posts( $offset, $post_type, $post_statuses, $polylang );
					}

				}

				$offset = $offset + self::PER_PAGE;

			}

		}

	}

	/**
	 * Get post type archive URLs.
	 */
	public function get_posts( $offset, $post_type, $post_statuses, $polylang = null ) {

		$args = array(
			'posts_per_page'         => self::PER_PAGE, // This may sometimes query too many posts - but the code is simpler this way ;)
			'offset'                 => $offset,
			'post_type'              => $post_type,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'post_status'            => $post_statuses,
		);

		if ( null !== $polylang ) {
			$args[ 'lang' ] = $polylang;
		}

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				// We need to allow attachments with use 'inherit', but we need to make sure their parents actually have a valid post status - which is what get_post_status() returns when inherit is used
				if ( ! in_array( get_post_status(), $post_statuses ) ) {
					continue;
				}

				// Get file extension from URL
				$url_parts = explode( '.', get_the_permalink() );
				if ( isset( $url_parts[0] ) && 1 < count( $url_parts ) ) {
					$count = count( $url_parts ) - 1;
					$extension = $url_parts[ $count ];
				}
				if ( in_array( $extension, $this->extensions ) ) {
					continue; // Bail out if post URL is actually a static file (sometimes happens with attachments)
				}

				// Get the main URL
				if (
					'publish' === get_post_status()
					||
					'trash' === get_post_status()
				) {

					$time = $this->get_most_recent_completed_published_timestamp( $this->get_live_distribution_id() );
					if ( $time > get_the_modified_date( 'U', get_the_ID() ) ) {
						$priority = 8;
					} else {
						$priority = 10;

						// Stash this posts taxonomy terms, as we need to make sure they are also higher priority
						$taxonomies = get_taxonomies( array( 'public' => true ) );
						foreach ( $taxonomies as $taxonomy ) {

							$terms = wp_get_post_terms( get_the_ID(), $taxonomy );
							foreach ( $terms as $key => $term ) {
								$this->high_priority_terms[] = $term->term_id;
							}

						}

					}
				
					$url = $this->get_published_url( get_post_status(), get_the_permalink() );

					$this->urls[] = array(
						'url'                => $url,
						'type'               => 'page',
						//'last_modified_date' => get_the_modified_date( 'U', get_the_ID() ),
						'priority'           => $priority,
					);

					// Get embed URLs
					$features = get_option( 'strattic-disable-features' );
					if ( ! isset( $features[ 'embed-pages' ] ) || 'on' !== $features[ 'embed-pages' ] ) {

						$this->urls[] = array(
							'url'                => get_post_embed_url( get_the_ID() ),
							'type'               => 'page',
							'priority'           => '2',
						);

					}

				}

				/* Non-permalink code temporarily removed
				$this->urls[] = add_query_arg( array( 'embed' => 'true' ), get_the_permalink() );
				*/

				/* Non-permalink code temporarily removed
				// Get shortlink URLs
				$this->urls[] = wp_get_shortlink( get_the_ID() );
				*/

				// Add file attachment URLs
				if ( 'attachment' === $post_type ) {

					// Only include recent attachments
					$time = $this->get_most_recent_completed_published_timestamp( $this->get_live_distribution_id() );
					if ( $time < get_the_modified_date( 'U', get_the_ID() ) ) {

						$features = get_option( 'strattic-disable-features' );
						if ( ! isset( $features[ 'attachment-pages' ] ) || 'on' !== $features[ 'attachment-pages' ] ) {
							$this->urls[] = array(
								'url' => wp_get_attachment_url( get_the_ID() ), // Full size file URL (this is not the attachment post URL)
								'priority' => 9,
							);
						}

						foreach ( get_intermediate_image_sizes() as $size ) {

							$src = wp_get_attachment_image_src( get_the_ID(), $size );

							if ( isset( $src[ 0 ] ) ) {

								$this->urls[] = array(
									'url'                => $src[ 0 ],
									'priority'           => '9',
								);


							}

						}

					}

				}

				// Include pagination URLs for comments pages
				$url = get_the_permalink();

				$max_pages = $this->get_comment_pages_count();

				$count = 2;
				while ( $count < $max_pages ) {

					$this->urls[] = array(
						'url'                => get_comments_pagenum_link( $count, $max_pages ),
						'type'               => 'page',
						'priority'           => '3',
					);

					$count++;
				}

				// Include pagination URLs pages
				$the_post = get_post();
				$max_num_pages = substr_count( $the_post->post_content, '<!--nextpage-->' ) + 1;

				$pagenum = 2;
				while ( $pagenum <= $max_num_pages ) {

					$new_url = trailingslashit( get_permalink() ) . user_trailingslashit( $pagenum, 'single_paged' );
					$this->urls[] = array(
						'url'                => $new_url,
						'type'               => 'page',
						'priority'           => '3',
					);

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

	}

	
	/**
	 * Return correct URL for post when published (even if currently trashed).
	 *
	 * @param  mixed $status
	 * @param  mixed $permalink
	 *
	 * @return void
	 */
	public static function get_published_url( $status, $permalink ) {
		// If trash, then need to modify the URL back to what it would be if published
		if ( 'trash' === $status ) {
			return str_replace( '__trashed/', '/', $permalink );
		} else {
			return $permalink;
		}
	}

	/**
	 * Get post type archive URLs.
	 */
	public function get_post_type_archives() {

		// Loop through ALL post types
		$post_types = get_post_types( array( /*'_builtin' => true,*/ 'public' => true ), 'names', 'and' );
		foreach ( $post_types  as $post_type ) {
			$url = get_post_type_archive_link( $post_type );

			if ( '' !== $url ) {
				$this->urls[] = array(
					'url'                => $url,
					'type'               => 'page',
					'priority'           => '7',
				);
			}

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

					if ( in_array( $term_id, $this->high_priority_terms ) ) {
						$priority = 9;
					} else {
						$priority = 5;
					}

					$this->urls[] = array(
						'url'                => $url,
						'type'               => 'page',
						'priority'           => $priority,
					);

					// Paginated versions of each URL
					$term = get_term( $term_id, $taxonomy );
					$term_slug = $term->slug;
					$number_posts = $term->count;

					$archive_pagination_urls = $this->get_archive_pagination_urls( $url, $number_posts );
					foreach ( $archive_pagination_urls as $key => $new_url ) {

						$this->urls[] = array(
							'url'                => $new_url,
							'type'               => 'page',
							'priority'           => '5',
						);

					}

					$this->taxonomy_term_ids[ $taxonomy ][] = $term_id;

				}

				$offset = $offset + self::PER_PAGE;
			}

		}

	}

	/**
	 * Get categories in root.
	 *
	 * If permalinks are set to /%category%/%postname%/, sub-categories are available without the parent category in the URL/
	 * This appears to be a bug in WordPress core which needs implemented.
	 */
	public function get_categories_in_root() {

		// This is only required when using the specific permalink structure
		if ( '/%category%/%postname%/' !== get_option( 'permalink_structure' ) ) {
			return;
		}

		// Loop through categories and grab URLs manually
		$categories = get_categories();
		foreach ( $categories as $key => $category ) {

			if ( 0 !== $category->parent && isset( $category->slug ) ) {

				$this->urls[] = array(
					'url'                => home_url() . '/' . $category->slug . '/', // main feed
					'priority'           => '3',
				);

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
		$this->urls[] = array(
			'url'                => home_url() . '/' . $feed_slug . '/', // main feed
			'type'               => 'page',
			'priority'           => '9',
		);

		$this->urls[] = array(
			'url'                => home_url() . '/' . $comments_slug . '/' . $feed_slug . '/', // main comments feed
			'type'               => 'page',
			'priority'           => '9',
		);

		/* Non-permalink code temporarily removed
		$this->urls[] = home_url() . '/?feed=comments'; //XXX raw PHP file main comments feed
		*/

		foreach ( $this->feed_formats as $feed_format ) {

			$this->urls[] = array(
				'url'                => home_url() . '/' . $feed_slug . '/' . $feed_format . '/',
				'type'               => 'feed',
				'priority'           => '2',
			);

			/* Non-permalink code temporarily removed
			$this->urls[] = home_url() . '/?feed=' . $feed_format; // non-permalink main feed
			$this->urls[] = home_url() . '/wp-' . $feed_format . '.php'; // raw PHP file main feed
			*/

			$this->urls[] = array(
				'url'                => home_url() . '/' . $comments_slug . '/' . $feed_slug . '/' . $feed_format . '/',
				'type'               => 'feed',
				'priority'           => '2',
			);

			/* Non-permalink code temporarily removed
			$this->urls[] = home_url() . '/wp-comments' . $feed_format . '.php'; // raw PHP file main comments feed
			$this->urls[] = home_url() . '/?feed=comments' . $feed_format; // non-permalink main comments feed
			*/
		}


		// If RSS feeds not loading, then don't bother with the remaining feed URLs
		if ( ! in_array( 'rss', $this->feed_formats ) ) {
			return;
		}


		// Generate single post comment feed paths

		if ( null === $this->post_ids ) {
			$this->get_all_posts(); // If posts weren't already accessed, then access them anyway, as that primes the $this->post_ids var
		}

		if ( is_array( $this->post_ids ) ) {
			foreach ( $this->post_ids as $post_id ) {
				$page_path = str_replace( home_url(), '', get_permalink( $post_id ) );

				if ( get_option( 'permalink_structure') ) {

					$this->urls[] = array(
						'url'                => home_url() . $page_path . $feed_slug . '/',
						'type'               => 'page',
						'priority'           => '3',
					);

				}
				foreach ( $this->feed_formats as $feed_format ) {

					if ( get_option( 'permalink_structure') ) {

						$this->urls[] = array(
							'url'                => home_url() . $page_path . $feed_slug . '/' . $feed_format . '/',
							'type'               => 'page',
							'priority'           => '3',
						);

					}

					/* Non-permalink code temporarily removed
					$path = $page_path;
					$path = add_query_arg( 'feed', $feed_format, $path );
					$paths[] = add_query_arg( 'feed', $feed_format, $path );
					*/

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

						$this->urls[] = array(
							'url'                => home_url() . '/' . $category_base . '/' . $term_slug . '/' . $feed_slug . '/',
							'type'               => 'page',
							'priority'           => '5',
						);

					} else if ( 'post_tag' === $taxonomy ) {

						$this->urls[] = array(
							'url'                => home_url() . '/' . $tag_base . '/' . $term_slug . '/' . $feed_slug . '/',
							'type'               => 'page',
							'priority'           => '5',
						);

					} else {

						$this->urls[] = array(
							'url'                => home_url() . '/' . $taxonomy . '/' . $term_slug . '/' . $feed_slug . '/',
							'type'               => 'page',
							'priority'           => '5',
						);

					}

				}

				foreach ( $this->feed_formats as $feed_format ) {

					if ( 'category' === $taxonomy ) {

						if ( get_option( 'permalink_structure') ) {

							$this->urls[] = array(
								'url'                => home_url() . '/' . $category_base . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/',
								'type'               => 'feed',
								'priority'           => '2',
							);

						}
						/* Non-permalink code temporarily removed
						$paths[] = '/?feed=' . $feed_format . '&cat=' . $term_id;
						$paths[] = '/wp-' . $feed_format . '.php?cat=' . $term_id;
						*/

					} else if ( 'post_tag' === $taxonomy ) {

						if ( get_option( 'permalink_structure') ) {

							$this->urls[] = array(
								'url'                => home_url() . '/' . $tag_base . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/',
								'type'               => 'feed',
								'priority'           => '2',
							);

						}
						/* Non-permalink code temporarily removed
						$paths[] = '/?feed=' . $feed_format . '&tag=' . $term_slug;
						$paths[] = '/wp-' . $feed_format . '.php?tag=' . $term_slug;
						*/

					} else {

						if ( get_option( 'permalink_structure') ) {

							$this->urls[] = array(
								'url'                => home_url() . '/' . $taxonomy . '/' . $term_slug . '/' . $feed_slug . '/' . $feed_format . '/',
								'type'               => 'feed',
								'priority'           => '2',
							);

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
		if ( is_array( $this->post_dates ) ) {
			foreach ( $this->post_dates as $post_id => $date ) {
				$year  = $date[ 'y' ];
				$month = $date[ 'm' ];
				$day   = $date[ 'd' ];

				$this->urls[] = array(
					'url'                => get_year_link( $year ),
					'type'               => 'page',
					'priority'           => '5',
				);

				$this->urls[] = array(
					'url'                => get_month_link( $year, $month ),
					'type'               => 'page',
					'priority'           => '5',
				);

				$this->urls[] = array(
					'url'                => get_day_link( $year, $month, $day ),
					'type'               => 'page',
					'priority'           => '5',
				);

				/* Non-permalink code temporarily removed
				if ( true === $this->important && get_option( 'permalink_structure' ) ) {

					foreach ( $this->feed_formats as $feed_format ) {
						$paths[] = '?m=' . $year . $month . $day . '&feed=' . $feed_format;
					}

				}
				*/



			}
		}

		// Generate user feed paths
		if ( is_array( $this->authors ) ) {
			foreach ( $this->authors as $key => $user_id ) {

				$this->urls[] = array(
					'url'                => get_author_feed_link( $user_id, '' ),
					'type'               => 'page',
					'priority'           => '5',
				);

				/* Non-permalink code temporarily removed
				if ( true === $this->important ) {

					if ( ! get_option( 'permalink_structure' ) ) {
						$paths[] = '?author=' . $user_id;
					}

				}
				*/

			}
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

				$this->urls[] = array(
					'url'                => home_url() . get_post_meta( get_the_ID(), '_redirect_rule_from', true ),
					'type'               => 'page',
					'priority'           => '4',
				);

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

					$this->urls[] = array(
						'url'                => home_url() . $path,
						'type'               => 'page',
						'priority'           => '4',
					);

				}
			}

		}

	}

	/**
	 * Grabs list of URLs generated by the Polylang plugin.
	 */
	public function get_polylang_homepages() {
		// If Polylang not installed, then bail out now
		if ( class_exists( 'Polylang' ) ) {
			$all_languages = pll_languages_list();
			foreach ($all_languages as $lang_slug) {
				$home_in_lang = pll_home_url($lang_slug);
				$this->urls[] = array(
					'url'      => $home_in_lang,
					'type'     => 'polylang',
					'priority' => 9,
				);
			}
		}
	}

	/**
	 * Grabs list of search related URLs.
	 */
	public function get_search_urls() {

		// Bail out now if search not turned on
		if ( 'on' !== Strattic_Search::get_option( 'search-on' ) ) {
			return;
		}

		// The search index
		$this->urls[] = array(
			'url'                => apply_filters( 'strattic_assets_prefix', '/' ) . 'strattic-search.js',
			'type'               => 'index',
			'priority'           => '4',
		);

		// The search page
		$this->urls[] = array(
			'url'                => '/' . Strattic_Search::get_option( 'search-page-slug' ) . '/',
			'type'               => 'index',
			'priority'           => '4',
		);

	}


	/**
	 * Grabs list of user pages.
	 */
	public function get_user_pages() {

		if ( null === $this->post_dates ) {
			$this->get_all_posts(); // If posts weren't already accessed, then access them anyway, as that primes the $this->post_dates var
		}

		if ( is_array( $this->authors ) ) {
			foreach ( $this->authors as $key => $user_id ) {

				$url = get_author_posts_url( $user_id );

				$this->urls[] = array(
					'url'                => $url,
					'type'               => 'page',
					'priority'           => '5',
				);

				$number_posts = count_user_posts( $user_id );

				$archive_pagination_urls = $this->get_archive_pagination_urls( $url, $number_posts );
				foreach ( $archive_pagination_urls as $key2 => $new_url ) {

					$this->urls[] = array(
						'url'                => $new_url,
						'type'               => 'page',
						'priority'           => '5',
					);

				}

				/* Non-permalink code temporarily removed
				$this->normal_urls[] = '?author=' . $user_id;
				*/

			}
		}

	}

	/**
	 * Grabs modified static files.
	 */
	public function get_static_files() {
		$this->paths = array();
		$this->dir_walk( array( $this, 'path_scan_callback' ), ABSPATH, $this->extensions, true );
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
		$uploadsSkip = $dir == $this->uploads_path;
		
		// Skip ignore paths
		// foreach ( $this->ignore_paths as $ignore_path ) {
		// 	if ( strpos( $dir, $ignore_path ) !== false && !$uploadsSkip) {
		// 		return $callback;
		// 	}
		// }

		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if ($file === '.' || $file === '..') {
					continue;
				}

				if (is_file($dir . $file) && !$uploadsSkip) {
					if (is_array($types)) {
						if (!in_array(strtolower(pathinfo($dir . $file, PATHINFO_EXTENSION)), $types, true)) {
							continue;
						}
					}
					$callback($baseDir . $file);
				}elseif($recursive && is_dir($dir . $file)) {
					if ($uploadsSkip) {
						$success = preg_match('/^2[0-9][0-9][0-9]$/', $file, $match);
						if ($success) {
							continue;
						}
					}
					$this->dir_walk($callback, $dir . $file . DIRECTORY_SEPARATOR, $types, $recursive, $baseDir . $file . DIRECTORY_SEPARATOR);
				}
			}
			closedir($dh);
		}
	}

	private function path_scan_callback( $path ) {
		$modified_time = filemtime( ABSPATH . $path );
		$file_size = filesize( ABSPATH . $path );

		$new_url = home_url() . '/' . $path;


		$this->urls[] = array(
			'url'                => $new_url,
			'type'               => 'file',
			'priority'           => '5', // Needs to be changed according to last_modified date
			'file_size'          => $file_size,
			'last_modified_date' => $modified_time,
		);


	}

	/**
	 * Grabs list of Comeet redirects.
	 *
	 * @return  array  List of URLs
	 */
	public function comeet_redirects($distribution_id) {

		// Bail out now if Comeet not loaded
		if ( ! class_exists( 'ComeetData' ) ) {
			return;
		}

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

				$new_url = $this->convert_comeet_url_to_staging( $item->url_active_page, $distribution_id );
				$new_url = trailingslashit( $new_url );

 				$this->urls[] = array(
					'url'                => $new_url,
					'type'               => 'page',
					'priority'           => '5',
				);

			}

		}

		// Crude hack for thankyou page
		$this->urls[] = array(
			'url'                => home_url() . '/single-page-position/co/thankyou/',
			'type'               => 'page',
			'priority'           => '5',
		);

		$this->get_more_comeet_urls($distribution_id);

	}

	/**
	 * Convert a Comeet URL to use the format of a staging site URL.
	 *
	 * @param  string  $url  The unedited URL
	 * @return string  The modified URL
	 */
	private function convert_comeet_url_to_staging( $url, $distribution_id ) {

		// We need to fudge this to https due to problems with Comeet
		$url = str_replace( 'http://', 'https://', $url );

		// Iterate through and convert all URLs - no performance problem here as get_distribution_info() is cached
		foreach ( $this->get_distribution_info() as $key => $distribution ) {

			if ( isset(  $distribution[ 'url' ] ) && isset(  $distribution[ 'id' ] ) && $distribution_id ===  $distribution[ 'id' ] ) {
				$new_url = $distribution[ 'url' ];

				// Convert from live site URL to staging site URL
				$url = str_replace(
					$new_url,
					home_url(),
					$url
				);

			}
		}

// Hard coding to hack in suppport for www.hysolate.com in an emergency
$url = str_replace( 'https://www.hysolate.com', '', $url );

		return $url;
	}

	/**
	 * Gets more Comeet URLs.
	 * We were unsure WHY these URLs are required, but they were added via the /careers/ page, so we have copied the code from within the Comeet plugin to access these too.
	 *
	 * @access private
	 * @global object  $wp_query  The main WordPress query object
	 */
	private function get_more_comeet_urls($distribution_id) {
		global $wp_query;

		$options = $this->get_comeet_options();

		$comeet_cat = ( isset( $wp_query->query_vars[ 'comeet_cat' ] ) ) ? urldecode( $wp_query->query_vars[ 'comeet_cat' ] ) : null;

		list( $comeet_groups, $data, $group_element ) = ComeetData::get_groups( $options, $comeet_cat );
		$comeet_group = $options[ 'advanced_search' ];
		$post = get_post( $options[ 'post_id' ] );
		$base = get_the_permalink( $post->ID );

		if ( isset( $comeet_groups ) && ! empty( $comeet_groups ) ) {
			foreach ( $comeet_groups as $category ) {
				if ( isset( $data ) ) {
					foreach ( $data as $post ) {
						if ( isset( $group_element ) ) {

							$url = $this->comeet_generate_careers_url( $base, $category, $post );
							$url = $this->convert_comeet_url_to_staging( $url, $distribution_id );
							$url = trailingslashit( $url );

							$this->urls[] = array(
								'url'                => $url,
								'type'               => 'page',
								'priority'           => '5',
							);


						}
					}
				}
			}
		}

	}

	/**
	 * Generating a career URL for Comeet.
	 * It was unclear when implementing this why the URLs obtained directly from the API are not the
	 *   only ones required. But this is how the careers page URLs are generated within the Comeet
	 *   plugin, and so we have replicated that functionality here too.
	 *
	 * @param  string  $base
	 * @param  string  $category
	 * @param  object  $post
	 * @return
	 *
	 *
	 */
	private function comeet_generate_careers_url( $base, $category, $post ) {

		$comeet_prefix = 'co';

		return rtrim( $base, '/' ) . '/' . $comeet_prefix . '/' . strtolower( clean( $category ) ) . '/' . $post[ 'uid' ] . '/' . strtolower( clean( $post[ 'name' ] ) ) . '/all';
	}

	/**
	 * Gets plugin config options
	 *
	 * @access private
	 * @return array
	 */
	private function get_comeet_options() {
		$db_opt = 'Comeet_Options';

		$options = array(
			'comeet_token'                          => '',
			'comeet_uid'                            => '',
			'location'                              => '',
			'post_id'                               => '',
			'advanced_search'                       => 1,
			'comeet_color'                          => '278fe6',
			'comeet_bgcolor'                        => '',
			'comeet_stylesheet'                     => 'comeet-cards.css',
			'comeet_subpage_template'               => 'page.php',
			'comeet_positionpage_template'          => 'page.php',
			'comeet_auto_generate_location_pages'   => '1',
			'comeet_auto_generate_department_pages' => '1'
		);

		$saved = get_option( $db_opt );

		if ( ! empty( $saved ) ) {
			foreach ( $saved as $key => $option ) {
				$options[ $key ] = $option;
			}
		}

		if ( $saved != $options ) {
			update_option( $db_opt, $options );
		}

		return $options;
	}

	/**
	 * Simplify API request by stripping out the site root.
	 *
	 * @access private
	 * @param  array  $urls  The URLs
	 * @return array  $paths The paths for the URLs (without the site root)
	 */
	private function strip_site_root( $urls ) {
		$paths = array();

		$home_url = home_url();
		$key = 0;
		foreach ( $urls as $url ) {

			if ( isset( $url[ 'url' ] ) ) {
				$paths[ $key ][ 'path' ] = $url[ 'url' ];
			}

			$paths[ $key ][ 'path' ] = esc_html(str_replace( $home_url, '', $url[ 'path' ] ) );

			if ( isset( $url[ 'last_modified_date' ] ) ) {
				$paths[ $key ][ 'last_modified_date' ] = $url[ 'last_modified_date' ];
			}

			if ( isset( $url[ 'type' ] ) ) {
				$paths[ $key ][ 'type' ] = $url[ 'type' ];
			}

			if ( isset( $url[ 'priority' ] ) ) {
				$paths[ $key ][ 'priority' ] =  $url[ 'priority' ];
			}

			$key++;
		}

		return $paths;
	}


	/**
	 * Remove repeat paths.
	 *
	 * @access private
	 * @param  array  $paths  The paths
	 * @return array  $paths  The paths with repeate stripped
	 */
	private function remove_repeat_paths( $path_objects ) {

		$tempArr = array_unique(array_column($path_objects, 'path'));
		$unique_results = array_intersect_key($path_objects, $tempArr);

		return $unique_results;
	}

	/**
	 * Anchors are not required on links, so let's remove them.
	 *
	 * @access private
	 * @param  array  $urls  The URLs
	 * @return array  $paths The paths for the URLs (without the anchors)
	 */
	private function strip_anchors( $urls ) {
		$deanchored_urls = array();

		$key = 0;
		foreach ( $urls as $url ) {

			if ( isset( $url[ 'url' ] ) ) {
				$deanchored_urls[ $key ][ 'path' ] = strtok( $url[ 'url' ], '#' );
			}

			if ( isset( $url[ 'path' ] ) ) {
				$deanchored_urls[ $key ][ 'path' ] = strtok( $url[ 'path' ], '#' );
			}

			if ( isset( $url[ 'last_modified_date' ] ) ) {
				$deanchored_urls[ $key ][ 'last_modified_date' ] = $url[ 'last_modified_date' ];
			}

			if ( isset( $url[ 'type' ] ) ) {
				$deanchored_urls[ $key ][ 'type' ] = $url[ 'type' ];
			}

			if ( isset( $url[ 'priority' ] ) ) {
				$deanchored_urls[ $key ][ 'priority' ] =  $url[ 'priority' ];
			}

			$key++;
		}

		return $deanchored_urls;
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

		$features = get_option( 'strattic-disable-features' );
		if ( isset( $features[ 'archive-pagination' ] ) && 'on' === $features[ 'archive-pagination' ] ) {
			return $urls;
		}

		// Estimate of number of pages of pagination
		$posts_per_page = absint( get_option( 'posts_per_page' ) );

		if ( null === $posts_count ) {
			$posts_count = absint( wp_count_posts( 'post' )->publish );
		}
		$posts_count = absint( $posts_count );

		$max_num_pages = ceil( $posts_count / $posts_per_page );

		// Simple hack to limit the maximum number of pagination pages
		if ( $max_num_pages > 1000 ) {
			$max_num_pages = 1000;
		}

		// Bail out if home page URL is not in URL (occurs if no blog post page is set)
		if ( strpos( $url, home_url() ) === false) {
			return array();
		}

		$pagenum = 2;
		while ( $pagenum <= $max_num_pages ) {

			$request = user_trailingslashit( $wp_rewrite->pagination_base . "/" . $pagenum, 'paged' );
			$request_simplified = $pagenum . '/';

			if ( '/' === substr( $url, -1 ) ) {
				$urls[] = $url . $request;
				$urls[] = $url . $request_simplified;
			} else {
				$urls[] = $url . '/' . $request;
				$urls[] = $url . '/' . $request_simplified;
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
				$this->urls = $decoded_urls;
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

	/**
	 * Add URLs for objects which have been deleted completely
	 *
	 * @param int $distribution_id
	 * @return void
	 */
	protected function get_deleted_files( $distribution_id ) {
		$distribution_type = $this->get_distribution_type( $distribution_id );
		$deleted_files_key = Strattic_Deleted_Posts::get_option_key( $distribution_type );
		$deleted_files_urls = get_option( $deleted_files_key );
		foreach( $deleted_files_urls as $url ) {
			$this->urls[] = array(
				'url'                => $url,
				'type'               => 'page',
				'priority'           => '10',
			);
		}
		update_option( $deleted_files_key, [] );
	}

}
