<?php
/**
 * Plugin Name: Strattic Search
 * Plugin URI:  http://www.strattic.com
 * Description:
 *
 * Author: Strattic
 * Author URI: https://www.strattic.com/
 *
 * Copyright 2018 Strattic
 *
 * @package Strattic Search
 */

/**
 * Strattic search.
 *
 * @copyright Copyright (c), Strattic
 * @since 1.0
 */
class Strattic_Search {

	const VERSION              = '1.1';
	const SETTINGS_OPTION      = 'strattic-search-settings';
	const TEMPLATES_OPTION     = 'strattic-search-templates';
	const RESULTS_OPTION       = 'strattic-search-results';
	const HASHED_SEARCH_OPTION = 'strattic-hashed-search-results';
	const TIME_LIMIT           = HOUR_IN_SECONDS;
	const MEMORY_LIMIT         = 1024;
	const PER_PAGE             = 100;

	private $asset_prefix = '/'; // This is required for allowing static assets to be hosted in a sub-folder (used when proxy is used to serve only part of a site)

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Add hooks.
		add_action( 'template_redirect',       array( $this, 'init' ) );
		add_action( 'admin_init',              array( $this, 'register_settings' ) );
		add_action( 'admin_init',              array( $this, 'add_option' ) ); // Only loads on admin init due to bug which caused WordPress core failures.
				
		add_action( 'admin_enqueue_scripts',   array( $this, 'admin_scripts' ) );
		add_action( 'admin_menu',              array( $this, 'create_admin_settings_page' ) );
		add_action( 'admin_menu',              array( $this, 'create_template_settings_page' ) );
		add_action( 'init',                    array( $this, 'dynamic_js_file' ) );

		// Add filters.
		add_filter( 'get_search_form',         array( $this, 'modify_search_form' ), 999 );
		add_filter( 'the_posts',               array( $this, 'generate_fake_pages' ), -10 );
		add_filter( 'posts_results',           array( $this, 'load_custom_search_results' ), 999 );

		// Display search form in shortcode.
		add_shortcode( 'strattic_search_form', array( $this, 'search_form_shortcode' ) );

		// Providing manual bypass to force search index updates when required (note: this URL may change in future so do not rely on it or provide it to clients)
		if ( isset( $_GET['strattic_manual_search_index_update'] ) ) {
			add_action( 'init', array( $this, 'update_search_results' ) );
		}

		// Update the search results index
		add_action( 'strattic_update_search_results', array( $this, 'update_search_results' ) );
		/* Add Cron task - temporarily removed due to it not handling changed content
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		//wp_clear_scheduled_hook( 'strattic_cron_search' );
		$schedule = wp_get_schedule( 'strattic_update_search_results' );
		if ( '' == $schedule ) {
			wp_schedule_event( time(), 'every_10_minutes', 'strattic_update_search_results' );
		}
		*/

		$this->asset_prefix = apply_filters( 'strattic_assets_prefix', $this->asset_prefix );
	}

	/**
	 * Initialize Strattic Search.
	 */
	public function init() {
		if (
			is_page( $this->get_option( 'search-page-slug' ) )
			&&
			'on' === $this->get_option( 'search-on' )
			&&
			! is_admin()
		) {
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'wp_footer', array( $this, 'js_templates' ), 5 );
		}

	}

	/**
	 * Resets the WP Cron task when a post is edited.
	 * This causes the task to trigger one minute later.
	 * This ensures that the search index is updated shortly after a post is edited.
	 */
	public function reset_cron() {
		wp_clear_scheduled_hook( 'strattic_search_task' );
		wp_schedule_event( time(), 'hourly', 'strattic_search_task' );
	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {

		// Strattic specific options page
		if ( function_exists( 'autoload_strattic' ) ) {

			// Main settings page
			$a = self::SETTINGS_OPTION;
			$b = 'strattic';
			add_settings_section(
				$a,
				esc_html__( 'Search', 'strattic' ),
				null,
				$b
			);
			add_settings_field(
				$a,
				esc_html__( 'Search field', 'strattic' ),
				array( $this, 'sanitize' ),
				$b,
				$a
			);
			register_setting(
				'strattic-settings',       // The settings group name
				$a,                        // The option name
				array( $this, 'sanitize' ) // The sanitization callback
			);

			// Templates page
			$a = self::TEMPLATES_OPTION;
			$b = 'strattic';
			add_settings_section(
				$a,
				esc_html__( 'Search templates', 'strattic' ),
				null,
				$b
			);
			add_settings_field(
				$a,
				esc_html__( 'Search template field', 'strattic' ),
				array( $this, 'sanitize' ),
				$b,
				$a
			);
			register_setting(
				'strattic-search-templates',       // The settings group name
				$a,                        // The option name
				array( $this, 'sanitize' ) // The sanitization callback
			);

		} else {

			// Regular options page
			register_setting(
				'strattic-settings',       // The settings group name
				self::SETTINGS_OPTION,     // The option name
				array( $this, 'sanitize' ) // The sanitization callback
			);

		}

	}

	/**
	 * Add default option.
	 */
	public function add_option() {

		foreach ( $this->fields() as $key => $field_vars ) {
			$slug = $field_vars[ 'slug' ];

			$default =  '';
			if ( isset( $field_vars[ 'default' ] ) ) {
				$default = $field_vars[ 'default' ];
			}

			$values[$slug] = $default;

		}

		//delete_option( self::SETTINGS_OPTION );
		add_option( self::SETTINGS_OPTION, $values, null, 'no' );

	}

	/**
	 * Create the admin settings page and add it to the menu.
	 */
	public function create_admin_settings_page() {

		if ( function_exists( 'autoload_strattic' ) ) {

			add_action( 'strattic_settings', array( $this, 'internal_admin_page' ), 50 );

		} else {

			add_options_page(
				esc_html__( 'Strattic Search', 'strattic' ),
				esc_html__( 'Search', 'strattic' ),
				'manage_options',
				self::SETTINGS_OPTION,
				array( $this, 'admin_page' )
			);

		}

	}

	/**
	 * Add Generate Search Templates to the Strattic menu
	 */
	public function create_template_settings_page() {

		if ( function_exists( 'autoload_strattic' ) ) {

			add_submenu_page(
				defined( 'STRATTIC_DEV' ) ? 'strattic' : false,
				esc_html__( 'Search Templates', 'strattic' ),
				esc_html__( 'Search Templates', 'strattic' ),
				'publish_posts',
				'strattic-templates',
				array( $this, 'render_generate_templates_page' )
			);

		} else {

			// Need to add external plugin support here

		}

	}

	/**
	 * Output the Generate Search Template Page
	 */
	public function render_generate_templates_page() {

		if ( ! class_exists( 'Strattic_Template_Parser' ) ) {
			require dirname( __FILE__ ) . '/strattic-template-parser.php';
		}

		$parser = new Strattic_Template_Parser( $this );

		$search_results      = $parser->get_search_results();
		$search_many_results = $parser->get_search_many_results();
		$search_no_results   = $parser->get_search_no_results();

		require dirname( __FILE__ ) . '/views/search-templates.php';
	}

	/**
	 * Output the admin page used internally on stratt.com.
	 */
	public function internal_admin_page() {

		echo '<h2>' . esc_html__( 'Search', 'strattic' ). '</h2>';

		require dirname( __FILE__ ) . '/views/search-admin-settings.php';
	}

	/**
	 * Output the admin page.
	 *
	 * @global  string  $title  The page title set by add_submenu_page()
	 */
	public function admin_page() {
		global $title;

		require dirname( __FILE__ ) . '/views/search-admin.php';
	}

	/**
	 * Sanitize the stored array data.
	 *
	 * @param array $input The input array
	 * @return array The sanitized array
	 */
	public function sanitize( $input ) {
		$output = array();

		// Loop through each bit of data.
		foreach ( $this->fields() as $key => $field ) {
			$key = $field['slug'];

			// Skip if value not found
			if ( ! isset( $input[ $key ] ) ) {
				continue;
			}

			// If set to inactive, then use default.
			if ( true !== $field['active'] && isset( $field['default'] ) ) {
				$output[ $key ] = $field['default'];
				continue;
			}

			// Sanitize input data.
			$type = $field['type'];
			if ( 'number' === $type ) {
				if ( is_numeric( $input[ $key ] ) ) {
					$value = absint( 10 * $input[ $key ] ) / 10;
				} else {
					$value = '';
				}
			} elseif ( 'textarea' === $type || 'editor' === $type ) {
				$value = $input[ $key ]; // Bypassing sanitization due to use of templates in textarea setting.
			} elseif ( 'checkbox' === $type ) {
				$value = '';
				if ( 'on' === $input[ $key ] ) {
					$value = 'on';
				}
			} elseif ( 'text' === $type ) {
				$value = wp_kses( $input[ $key ], array() );
			}

			// Create array for saving.
			$output[ $key ] = $value;

		}

		// The data is split across multiple admin pages, so we need to add in the previously saved data
		$old_data = get_option( self::SETTINGS_OPTION );
		if ( is_array( $old_data ) ) {
			foreach ( $old_data as $key => $value ) {
				if ( ! isset( $output[$key] ) ) {
					$output[$key] = $value;
				}
			}
		}

		// Flush results option - forces search results to be updated on next page load.
		delete_option( self::RESULTS_OPTION );

		// Return the sanitized data.
		return $output;
	}

	/**
	 * Load scripts.
	 */
	public function scripts() {

		$on = $this->get_option( 'search-on' );
		if ( isset( $on ) && 'on' === $on ) {
			wp_enqueue_script( 'strattic-search', home_url() . $this->asset_prefix . 'strattic-search.js', array(), self::VERSION, false );
		}

		add_action( 'wp_footer', array( $this, 'search_vars' ) );
	}

	/**
	 * Load admin scripts.
	 */
	public function admin_scripts() {
		if ( ! is_admin() || 'strattic-templates' !== filter_input( INPUT_GET, 'page' ) ) {
			return;
		}

		$code_mirror_settings = wp_enqueue_code_editor(
			array(
				'type'       => 'text/html',
				'codemirror' => array(
					'indentUnit' => 4,
					'tabSize'    => 4,
					'mode'       => 'text/html',
					'lint'       => false,
				),
			)
		);

		wp_enqueue_script(
			'strattic-code-editor',
			STRATTIC_PLUGIN_URL . 'plugins/strattic-search/assets/strattic-search-editor.js',
			array(),
			STRATTIC_VERSION
		);
		wp_localize_script( 'strattic-code-editor', 'codeMirrorSettings', $code_mirror_settings );
		wp_enqueue_style( 'strattic-templates', STRATTIC_PLUGIN_URL . 'plugins/strattic-search/assets/strattic-search.css', array(), self::VERSION );

	}

	/**
	 * Load the dynamically generated JS file at /strattic-search.js.
	 */
	public function dynamic_js_file() {

		// Bail out if not loading search JS file
		if (
			$this->asset_prefix . 'strattic-search.js?ver=' . self::VERSION !== $this->get_current_path()
			&&
			$this->asset_prefix . 'strattic-search.js?ver=' . self::VERSION !== $_SERVER[ 'REQUEST_URI' ]
			&&
			$this->asset_prefix . 'strattic-search.js' !== $this->get_current_path()
			&&
			$this->asset_prefix . 'strattic-search.js' !== $_SERVER[ 'REQUEST_URI' ]
		) {
			return;
		}

		header( 'Content-Type: application/javascript' );

		// Get JSON stored search index data
		$data = get_option( self::RESULTS_OPTION );

		// Get JS file content
		$assets_path = dirname( __FILE__ ) . '/assets/';
		$all_taxes = $this->get_all_taxes();
		$js_chunks = array(
			'Algolia app id'         => 'var strattic_algolia_app_id = "' . esc_js( $this->get_option( 'algolia-app-id' ) ) . '"',
			'Algolia search key'     => 'var strattic_algolia_search_key = "' . esc_js( $this->get_option( 'algolia-search-key' ) ) . '"',
			'Algolia index'          => 'var strattic_algolia_index = "' . esc_js( $this->get_option( 'algolia-index' ) ) . '"',
			'Strattic search vars'   => 'var strattic_home_url = "' . esc_js( esc_url( home_url() ) ) . '";',
			'Site taxes'   			 => 'var all_taxes = ' . json_encode( $all_taxes, JSON_PRETTY_PRINT ) . ';',
			'He HTML decoding'       => file_get_contents( $assets_path . 'he.js' ),
			'Mustaches'              => file_get_contents( $assets_path . 'mustaches.min.js' ),
			'Strattic Search'        => file_get_contents( $assets_path . 'search.js' ),
		);

		if ( $this->get_option( 'algolia-app-id' ) ) {
			$js_chunks['Algolia Search'] = file_get_contents( $assets_path . 'algoliasearch.min.js' );
		} else {
			$js_chunks['JSON search index'] = 'var strattic_search_data = ' . json_encode( $data );
			$js_chunks['Fuse JS'] = file_get_contents( $assets_path . 'fuse.min.js' );
		}

		$js = '';
		foreach ( $js_chunks as $name => $js_chunk ) {

			$js .= '/* ' . esc_html( $name ) . " */\n";
			$js .= $js_chunk;
			$js .= "\n\n";
		}

		echo $js;
		die;
	}

	/**
	 * Modify the search form HTML.
	 *
	 * @param  string  $html  The search form HTML
	 * @return string  The modified search form HTML
	 */
	public function modify_search_form( $html ) {

		// Different action URLs for taxonommies ...
		if ( $this->is_taxonomy() && 'on' === $this->get_option( 'search-taxonomies' ) ) {
			global $wp;

			$origin_path = $this->get_taxonomy_search_origin_path();
			$potential_path = $origin_path . $this->get_taxonomy_search_slug() . '/';

			$strattic_taxonomy = apply_filters( 'strattic_taxonomy', '' );
			$strattic_term = apply_filters( 'strattic_term', '' );
			if ( '' !== $strattic_taxonomy && '' !== $strattic_term ) {
				$url = get_term_link( $strattic_term, $strattic_taxonomy );

				if ( is_string( $url ) ) {
					$actual_path = str_replace( home_url(), '', $url );
				} else {
					// term didn't exist, so default to a regular search instead
					$actual_path = '/';
				}

			} else {
				$actual_path = '/' . strtolower( $wp->request ) . '/';
			}

			// Convert normal search form URLs
			$before_url = esc_url( home_url() . '/' );
			$after_url = esc_url( home_url() . $actual_path . $this->get_option( 'search-page-slug' ) . '/' );
			$html = $this->modify_search_url( $html, $before_url, $after_url );

			// Convert special taxonomy search URLs (found in some sites with existing customised search systems)
			$before_url = esc_url( home_url() . $actual_path );
			$html = $this->modify_search_url( $html, $before_url, $after_url );

		} else {
			$before_url = home_url( '/' );
			$after_url      = home_url( '/' . $this->get_option( 'search-page-slug' ) . '/' );
			$html = $this->modify_search_url( $html, $before_url, $after_url );
		}

		// Convert from s to q query var
		$before = 'name="s"';
		$after = 'name="q"';
		$html = str_replace( $before, $after, $html );

		return $html;
	}

	private function modify_search_url( $html, $before_url, $after_url ) {
		$before = 'action="' . esc_url( $before_url ) . '"';
		$after = 'action="' . esc_url( $after_url ) . '"';
		$html = str_replace( $before, $after, $html );
		return $html;
	}

	/**
	 * Load custom search results.
	 *
	 * @param array $posts The array of posts to filter.
	 * @return array
	 */
	public function load_custom_search_results( $posts ) {
		if ( ! is_search() ) {
			return $posts;
		}

		if ( 'strattic-single-sample' === filter_input( INPUT_GET, 'search-type' ) ) {
			$posts = array( $this->generate_sample_post() );
		}

		if ( 'strattic-multi-sample' === filter_input( INPUT_GET, 'search-type' ) ) {
			global $wp_query;
			$wp_query->max_num_pages = 10;

			$posts = array( $this->generate_sample_post() );
		}

		return $posts;
	}

	/**
	 * Totally strip all shortcodes.
	 * The built in strip_shortcodes() function does not remove all shortcodes as not all are registered at all times within WordPress.
	 *
	 * @param  string  $content   Content to strip shortcodes from
	 * @return string  The modified content
	 */
	private function totally_strip_shortcodes( $content ) {
		$pattern = '|[[\/\!]*?[^\[\]]*?]|si';
		$replace = '';
		return preg_replace( $pattern, $replace, $content );
	}

	/**
	 * Strips unwanted HTML, such as <style> tags.
	 *
	 * @param  string  $content   Content to strip shortcodes from
	 * @return string  The modified content
	 */
	private function strip_unwanted_html( $content ) {

		// Strip style tags
		$content = preg_replace( '/<style>([\s\S]*?)<\/style>/', '', $content );

		return $content;
	}

	/**
	 * Grabs list of all posts.
	 *
	 * @access  protected
	 * @return  array  The data
	 */
	protected function get_search_data() {

		// Loop through ALL post types
		$post_types = get_post_types( array( /*'_builtin' => true,*/ 'public' => true ), 'names', 'and' );
		$post_types = apply_filters( 'strattic_search_post_types', $post_types );

		$count = 0;
		foreach ( $post_types  as $post_type ) {

			$post_statuses = array( 'publish' /* for attachments */ );

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
					'has_password'           => false,
					'orderby'                => 'date',
					'order'                  => 'DESC',
				) );

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();

						// We need to allow attachments with use 'inherit', but we need to make sure their parents actually have a valid post status - which is what get_post_status() returns when inherit is used
						if ( ! in_array( get_post_status(), $post_statuses ) ) {
							continue;
						}

						// Replacing nasty shortcodes which shouldn't appear in results, with blank results
						remove_shortcode( 'av_codeblock' );
						add_shortcode( 'av_codeblock', function() {
							return '';
						});

						// Get the excerpt - if excerpt contains short code characters, guess it may have a shortcode, in which case it is best to shorten the content manually, as WordPress tends to include the WordPress provided excerpt with the shortcode chopped in half
						$excerpt = $this->trim_excerpt( get_the_excerpt() );
						if (
							'' === $excerpt
							||
							strpos( $excerpt, '[' ) !== false
							||
							strpos( $excerpt, ']' ) !== false
						) {
							$excerpt = $this->trim_excerpt(
								$this->strip_unwanted_html(
									$this->totally_strip_shortcodes(
										$this->strip_nasty_shortcodes(
											get_the_content()
										)
									)
								)
							);
						}

						$data[ $count ][ 'id' ]                    = get_the_ID();
						$data[ $count ][ 'path' ]                  = str_replace( home_url(), '', get_the_permalink() );
						$data[ $count ][ 'title' ]                 = get_the_title();
						$data[ $count ][ 'thumbnail' ]             = str_replace( home_url(), '', get_the_post_thumbnail() );
						$data[ $count ][ 'date' ]             = get_the_date();
						$data[ $count ][ 'time' ]             = get_the_time();
						$data[ $count ][ 'excerpt' ]               = $this->strip_html_entities(
							$this->strip_unwanted_html(
								$this->strip_html_comments(
									$this->totally_strip_shortcodes(
										$this->strip_nasty_shortcodes(
											wp_kses(
												$excerpt,
												array()
											)
										)
									)
								)
							)
						);
						$data[ $count ][ 'content' ]               = $this->strip_html_entities(
							$this->strip_html_comments(
								$this->strip_unwanted_html(
									$this->totally_strip_shortcodes(
										$this->strip_nasty_shortcodes(
											wp_kses(
												get_the_content(),
												array()
											)
										)
									)
								)
							)
						);

						$nickname = get_the_author_meta( 'nickname' );
						$firstName = get_the_author_meta( 'first_name' );
						$lastName = get_the_author_meta( 'last_name' );

						$data[ $count ][ 'author' ][ 'nickname' ] = $nickname;
						$data[ $count ][ 'author' ][ 'firstName' ] = $firstName;
						$data[ $count ][ 'author' ][ 'lastName' ]  = $lastName;
						$data[ $count ][ 'author' ][ 'name' ] = trim($firstName . ' ' . $lastName);
						// $data[ $count ][ 'author' ][ 'name2' ] = $firstName . ' ' . $lastName;


						$taxonomies = get_taxonomies(
							array(
								'public'   => true,
								'_builtin' => true
							),
							'names',
							'and'
						);
						$all_terms = array();
						if ( is_array( $taxonomies ) ) {
							foreach ( $taxonomies  as $taxonomy ) {
								$terms = get_the_terms( get_the_ID(), $taxonomy );

								if ( is_array( $terms ) ) {
									foreach ( $terms as $term ) {
										$data[ $count ][ 'terms' ][ $taxonomy ][] = $term->slug;
									}

								}

							}
						}

						$count++;
					}

					wp_reset_postdata();
				}

				$offset = $offset + self::PER_PAGE;
			}

		}

		return $data;
	}

	/**
	 * Trim excerpt to correct length.
	 */
	private function trim_excerpt( $content ) {

		if ( '' !== $this->get_option( 'search-excerpt-length' ) ) {
			$content = wp_trim_words( $content, $this->get_option( 'search-excerpt-length' ) );
		} else {
			$content = wp_trim_excerpt( $content );
		}

		return $content;
	}

	/**
	 * Strip nasty shortcodes from content.
	 * Used for removing shortcodes which contain content which is never meant to be displayed on the site.
	 * This is only required for when using specific plugins which needlessly spew irrelevant content into pages, via shortcodes.
	 */
	private function strip_nasty_shortcodes( $content ) {
		global $shortcode_tags;

		// List of all nasty shortcodes to strip out
		$tags = array(
			'av_codeblock', // Avia layout builder - dumps code vommit into content areas
		);

		$_tags = $shortcode_tags; // store temp copy
		foreach ($_tags as $tag => $callback) {
			if (!in_array($tag, $tags)) // filter unwanted shortcode
				unset($shortcode_tags[$tag]);
		}

		$shortcoded = do_shortcode($content);
		$shortcode_tags = $_tags; // put all shortcode back
		return $shortcoded;
	}

	/**
	 * Add JS templates.
	 * Used for generating HTML from AJAX requests to the WordPress REST API.
	 */
	public function js_templates() {

		echo '
<script type="text/html" id="tmpl-strattic-search-main-template">
' . do_shortcode( $this->get_option( 'search-main-template' ) ) . '
</script>
<script type="text/html" id="tmpl-strattic-search-results-template">
' . do_shortcode( $this->get_option( 'search-results-template' ) ) . '
</script>

<script type="text/html" id="tmpl-strattic-search-not-found-template">
' . do_shortcode( $this->get_option( 'search-not-found-template' ) ) . '
</script>

<script type="text/html" id="tmpl-strattic-search-pagination-template">
' . do_shortcode( $this->get_option( 'search-pagination-template' ) ) . '
</script>
';

	}

	/**
	 * Output search variables to page footer.
	 */
	public function search_vars() {

		$settings = array();
		foreach ( $this->fields() as $key => $field ) {

			if ( ! isset( $field['js_slug'] ) || ( substr( $field['js_slug'], 0, strlen( 'algoliaAdmin' ) ) === 'algoliaAdmin' ) ) {
				continue;
			}

			$slug    = $field[ 'slug' ];
			$js_slug = $field[ 'js_slug' ];
			$type    = $field[ 'type' ];

			if ( 'checkbox' === $type && '' !== $this->get_option( $slug ) ) {
				$settings[ $js_slug ] = 'true';
			} else if ( '' !== $this->get_option( $slug ) ) {
				$settings[ $js_slug ] = esc_js( $this->get_option( $slug ) );
			}

		}

		// $settings[ 'keys'] = 'somefillerstring';
		$keys = str_replace( '&quot;', "'", $this->get_option( 'keys' ) );

		// Set taxonomy specific search data
		if ( $this->is_taxonomy_search() ) {

			$query = $this->calculate_taxonomy_search_query();

			$settings['taxonomy'] = $query['taxonomy'];

			$term = $query['term'];
			// Add current term
			$terms = array();
			$terms[] = $term;

			$settings['terms'] = $this->generate_tax_sub_terms($terms, $settings['taxonomy']);
		}

		$settings_json = json_encode( $settings, JSON_PRETTY_PRINT );
		// $settings_json = str_replace( '"' . $settings[ 'keys' ] . '"', $keys, $settings_json );
		// It's very difficult to sanitize this, as the keys needed to be added, which contain a raw JSON string
		echo '<script>strattic_search_settings=' . $settings_json . ';</script>';
	}

	public function generate_tax_sub_terms( $terms, $taxonomy ) {
		$allterms = array();

		foreach ( $terms as $term ) {
			// Adding sub-terms of currently selected taxonomy
			$term_object = get_term_by( 'slug', $term, $taxonomy );
			$term_id = $term_object->term_id;
			$sub_terms = get_term_children( $term_id, $taxonomy );
			$allterms[] = $term;
			foreach ( $sub_terms as $term_id ) {
				$term_object = get_term_by( 'id', $term_id, $taxonomy );
				$term_slug = $term_object->slug;

				$allterms[] = $term_slug;
			}
		}
		return $allterms;
	}

	/**
	 * Create a fake page called "fake"
	 *
	 * $fake_slug can be modified to match whatever string is required
	 *
	 *
	 * @param   object  $posts  Original posts object
	 * @global  object  $wp     The main WordPress object
	 * @global  object  $wp     The main WordPress query object
	 * @return  object  $posts  Modified posts object
	 */
	public function generate_fake_pages( $posts ) {
		global $wp, $wp_query;

		// Get data for detecting taxonomies
		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'_builtin' => true,
			),
			'names',
			'and'
		);

		$all_terms = array();
		foreach ( $taxonomies as $taxonomy_slug ) {

			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy_slug,
					'hide_empty' => false,
				)
			);

			foreach ( $terms as $term ) {
				$url = get_term_link( $term->term_id );
				$path = str_replace( home_url(), '', $url );
				$all_term_paths[] = $path;
			}

		}

		$search_length = strlen( $this->get_option( 'search-page-slug' ) );
		$origin_path = '/' . substr_replace( strtolower( $wp->request ), '', -1 - $search_length ) . '/';
		$search_slug = substr( strtolower( $wp->request ), 0 - $search_length );
		$potential_path = $origin_path . $search_slug . '/';
		$actual_path = '/' . strtolower( $wp->request ) . '/';

		// Only generate fake page when on the URL to be generated
		if (
			! defined( 'FAKE_PAGE' )
			&&
			(
				// If on normal search page
				( strtolower( $wp->request ) == $this->get_option( 'search-page-slug' ) )
				&&
				'' !== $this->get_option( 'search-page-slug' )
			)
			||
			$this->is_taxonomy_search()
		) {

			// stop interferring with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page)
			if ( ! defined( 'FAKE_PAGE' ) ) {
				define( 'FAKE_PAGE', true );
			}

			// If on taxonomy term search page
			if ( $this->is_taxonomy_search() ) {
				$origin_path = $this->get_taxonomy_search_origin_path();
				$taxonomy = ': ' . $origin_path;
				$guid = esc_url( home_url() . '/' . $origin_path  . '/' . $this->get_option( 'search-page-slug' ) );
			} else {
				$taxonomy = '';
				$guid = esc_url( home_url() . '/' . $this->get_option( 'search-page-slug' ) );
			}

			// create a fake virtual page
			$post = new stdClass;
			$post->post_author    = 1;
			$post->post_name      = esc_html( $this->get_option( 'search-page-slug' ) );
			$post->guid           = $guid;
			$post->post_title     = '<span id="strattic-results-header">'.esc_html__( 'Search results', 'strattic' ).'</span>';
			$post->post_content   = '<div id="strattic-search-results"></div>';
			$post->ID             = -1;
			$post->post_type      = 'page';
			$post->post_status    = 'static';
			$post->comment_status = 'closed';
			$post->ping_status    = 'open';
			$post->comment_count  = 0;
			$post->post_date      = current_time( 'mysql' );
			$post->post_date_gmt  = current_time( 'mysql', 1 );
			$post->filter         = 'raw';

			$posts                = NULL;
			$posts[]              = new WP_Post( $post );

			// make WP Query believe this is a real page too
			$wp_query->is_page             = true;
			$wp_query->is_singular         = true;
			$wp_query->is_home             = false;
			$wp_query->is_archive          = false;
			$wp_query->is_category         = false;
			unset( $wp_query->query[ 'error' ] );
			$wp_query->query_vars[ 'error' ] = '';
			$wp_query->is_404 = false;
		}

		return $posts;
	}

	/**
	 * Return a sample post.
	 *
	 * @return WP_Post
	 */
	public function generate_sample_post() {
		// We need to use a real Post, so we'll borrow the earliest published post, and modify its data.
		$first_post = new WP_Query(
			array(
				'posts_per_page'   => 1,
				'orderby'          => 'date',
				'order'            => 'ASC',
				'suppress_filters' => true, // Needed in order to prevent infinite looping through this filter.
			)
		);

		$post = $first_post->posts[0];

		$post->post_author       = wp_get_current_user()->ID;
		$post->post_date         = '2015-10-21 19:28:00';
		$post->post_date_gmt     = '2015-10-22 02:28:00';
		$post->post_content      = '<!-- wp:paragraph --><p>Moonbeam sneezy krakow infernal kelp odour souse postlude incisor cookout.</p><!-- /wp:paragraph -->';
		$post->post_title        = 'Cowbell Borsch Calamine Engineer';
		$post->post_name         = 'cowbell-borsch-calamine-engineer';
		$post->post_excerpt      = 'Angular moochip labour wildly portage ultimate tatting bonanza brussels nautilus.';
		$post->post_modified     = '2018-10-21 20:28:00';
		$post->post_modified_gmt = '2018-10-22 03:28:00';
		$post->comment_status    = 'open';
		$post->ping_status       = 'open';
		$post->guid              = add_query_arg( 'p', $post->ID, home_url() );

		return $post;
	}

	/**
	 * Returns true if on a taxonomy archive page.
	 *
	 * @return  bool | string
	 */
	private function is_taxonomy() {

		$taxonomy = apply_filters( 'strattic_taxonomy', ( is_tax() || is_tag() || is_category() ) );

		if ( false !== $taxonomy ) {
			return true;
		}

	}
	private function get_all_taxes() {
		$all_taxes = array();
		// Get data for detecting taxonomies
		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'_builtin' => true,
			),
			'names',
			'and'
		);

		foreach ( $taxonomies as $taxonomy_slug ) {
			$all_taxes[$taxonomy_slug] = array();

			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy_slug,
					'hide_empty' => false,
				)
			);
			$terms_by_id = array();
			foreach ( $terms as $term ) {
				$terms_by_id[$term->term_id] = $term;
			}

			foreach ( $terms as $term ) {
				if ($term->parent != 0) {
					$term->parent_slug = $terms_by_id[$term->parent]->slug;
				}
				$all_taxes[$taxonomy_slug][$term->slug] = $term;
			}
		}
		return $all_taxes;
	}

	/**
	 * @global  object  $wp       The main WordPress object
	 */
	private function is_taxonomy_search() {
		global $wp;

		// Get data for detecting taxonomies
		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'_builtin' => true,
			),
			'names',
			'and'
		);

		$all_terms = array();
		foreach ( $taxonomies as $taxonomy_slug ) {

			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy_slug,
					'hide_empty' => false,
				)
			);

			foreach ( $terms as $term ) {
				$url = get_term_link( $term->term_id );
				$path = str_replace( home_url(), '', $url );
				$all_term_paths[] = $path;
			}

		}

		$origin_path = $this->get_taxonomy_search_origin_path();
		$potential_path = $origin_path . $this->get_taxonomy_search_slug() . '/';
		$actual_path = '/' . strtolower( $wp->request ) . '/';

		if (
			// If on taxonomy term search page
			$potential_path === $actual_path
			&&
			in_array( $origin_path, $all_term_paths )

		) {
			return true;
		}

	}

	/**
	 * @global  object  $wp       The main WordPress object
	 */
	private function is_multi_taxonomy_search() {
		global $wp;

		// Get data for detecting taxonomies
		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'_builtin' => true,
			),
			'names',
			'and'
		);

		$all_terms = array();
		foreach ( $taxonomies as $taxonomy_slug ) {

			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy_slug,
					'hide_empty' => false,
				)
			);

			foreach ( $terms as $term ) {
				$url = get_term_link( $term->term_id );
				$path = str_replace( home_url(), '', $url );
				$all_term_paths[] = $path;
			}

		}


		$origin_path = $this->get_taxonomy_search_origin_path();
		$potential_path = $origin_path . $this->get_taxonomy_search_slug() . '/';
		$actual_path = '/' . strtolower( $wp->request ) . '/';
		echo $actual_path;
		die();
		// if (
		// 	// If on taxonomy term search page
		// 	$potential_path === $actual_path
		// 	&&
		// 	in_array( $origin_path, $all_term_paths )

		// ) {
		// 	return true;
		// }

	}

	/**
	 * Calculate the taxonomy search query data.
	 * This is required because we can't directly acccess the taxonomy and term slugs as we are not on an actual taxonomy page.
	 */
	private function calculate_taxonomy_search_query() {

		// Get data for detecting taxonomies
		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'_builtin' => true,
			),
			'names',
			'and'
		);

		$all_terms = array();
		foreach ( $taxonomies as $taxonomy_slug ) {

			$terms = get_terms(
				array(
					'taxonomy' => $taxonomy_slug,
					'hide_empty' => false,
				)
			);

			foreach ( $terms as $term ) {
				$url = get_term_link( $term->term_id );

				$path = str_replace( home_url(), '', $url );

				if ( strpos( $_SERVER['REQUEST_URI'], $path ) !== false ) {
					$taxonomy_data['taxonomy'] = $taxonomy_slug;
					$taxonomy_data['term'] = $term->slug;
				}

			}

		}

		if ( isset( $taxonomy_data ) ) {
			return $taxonomy_data;
		} else {
			return false;
		}

	}

	/**
	 * @global  object  $wp       The main WordPress object
	 */
	private function get_taxonomy_search_origin_path() {
		global $wp;

		return '/' . substr_replace( strtolower( $wp->request ), '', -1 - $this->get_taxonomy_search_length() ) . '/';
	}

	/**
	 * @global  object  $wp       The main WordPress object
	 */
	private function get_taxonomy_search_length() {
		return strlen( $this->get_option( 'search-page-slug' ) );
	}

	/**
	 * @global  object  $wp       The main WordPress object
	 */
	private function get_taxonomy_search_slug() {
		global $wp;

		$taxonomy_search_slug = substr( strtolower( $wp->request ), 0 - $this->get_taxonomy_search_length() );
		$taxonomy_search_slug = apply_filters( 'strattic_taxonomy', $taxonomy_search_slug );

		// the search slug found via the taxonomy should match the intended one
		if ( $taxonomy_search_slug !== $this->get_option( 'search-page-slug' ) ) {
			return;
		}

		return  $taxonomy_search_slug;

	}

	protected function get_changed_data( $data, $hashedData ) {
		$changed_data = array();
		foreach( $data as $key => $value ) {
			$path = $value['path'];
			$newHash = sha1(json_encode($value));
			if (!$hashedData || !isset($hashedData[$path]) || $hashedData[$path] != $newHash) {
				$changed_data[] = $value;
			}
		}
		return $changed_data;
	}

	protected function hash_data( $data ) {
		$hashedData = array();
		foreach( $data as $key => $value ) {
			$hashedData[$data[$key]['path']] = sha1(json_encode($value));
		}
		return $hashedData;
	}

	/**
	 * Shrinks the amount of data stored.
	 * This is a crude method.
	 * It simply runs a bunch of filtering until the index is small enough.
	 *
	 * Uses array_values() as array needs reindexed each time to obtain the correct array size.
	 *
	 * @access  protected
	 * @param   string  $data  The data
	 * @return  array  The shrunk data
	 */
	protected function shrink_data( $data ) {

		$allowed_size = absint( $this->get_option( 'size' ) );

		$processed_data = $data;

		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Without content
		$processed_data = $data;
		foreach( $data as $key => $value ) {
			unset( $processed_data[ $key ][ 'content'] );
		}
		$processed_data = array_values( $processed_data );
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Without author
		// foreach( $data as $key => $value ) {
		// 	unset( $processed_data[ $key ][ 'author'] );
		// }
		$processed_data = array_values( $processed_data );
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Without excerpt
		foreach( $data as $key => $value ) {
			unset( $processed_data[ $key ][ 'excerpt'] );
		}
		$processed_data = array_values( $processed_data );
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Strip punctuation from titles
		foreach ( $processed_data as $key => $value ) {
			$title = $value[ 'title' ];
			$title = preg_replace( '/[[:punct:]]/', ' ', $title );
			$title = trim( $title );

			$processed_data[ $key ][ 'title' ] = $title;

		}
		$processed_data = array_values( $processed_data );
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Reduce number of words in titles until the size is okay
		$total_words = 100;
		while (
			$allowed_size < $this->get_string_size( $processed_data )
			&&
			$total_words > 0
		) {

			foreach ( $processed_data as $key => $value ) {
				$title = $value[ 'title' ];

				$words = str_word_count ( $title, 1 );
				$word_count = count( $words );
				$last_word = end( $words );

				if ( $word_count > $total_words ) {
					$shortened_title = str_replace( $last_word, '', $title );
					$shortened_title = trim( $shortened_title );
					$processed_data[ $key ][ 'title' ] = $shortened_title;
					$processed_data = array_values( $processed_data );

				}

			}

			$total_words = $total_words - 1;
		}
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Limit length of titles (just in case we have one 10,000 character long title)
		$max_length = 20;
		foreach ( $processed_data as $key => $value ) {
			$title = $value[ 'title' ];

			if ( $max_length < strlen( $title ) ) {
				unset( $processed_data[ $key ] );
			}

		}
		$processed_data = array_values( $processed_data );
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Remove results until the size is okay
		while (
			$allowed_size < $this->get_string_size( $processed_data )
			&&
			count( $processed_data ) > 0
		) {

			// Remove the first array item
			array_shift( $processed_data );
			$processed_data = array_values( $processed_data );

		}

		return $processed_data;
	}

	/**
	 * Update Algolia index with latest changes.
	 */
	public function update_algolia_index( $data ) {

		// Grab and process the data
		$formatted_data = $this->algolia_formatted_data( $data );

		// Load Algolia library
		if ( $this->get_option( 'algolia-admin-key' ) && $this->get_option( 'algolia-admin-key' ) ) {

			try {

				$client = \Algolia\AlgoliaSearch\SearchClient::create(
					$this->get_option( 'algolia-app-id' ),
					$this->get_option( 'algolia-admin-key' )
				);

				$index = $client->initIndex( $this->get_option( 'algolia-index' ) );
				$taxonomies = get_taxonomies(
					array(
						'public'   => true,
						'_builtin' => true
					),
					'names',
					'and'
				);
				$attributesForFaceting = array();
				foreach ( $taxonomies as $taxonomy ) {
					array_push( $attributesForFaceting, 'filterOnly(taxonomy-' . $taxonomy . ')' );
				}

				$index->setSettings(
					array(
						'attributesForFaceting' => $attributesForFaceting,
						'searchableAttributes' => array(
							'title',
							'excerpt',
							'content',
							'first_name',
							'last_name',
						)
					)
				);

				$index->saveObjects( $formatted_data, [ 'autoGenerateObjectIDIfNotExist' => true ] );
			} catch (\Throwable $th) {
				wp_die( 'Error encountered when submitting data to Algolia' );
			}
		}

	}
	private function forceEncoding( $text ) {
		$encoded = iconv(mb_detect_encoding($text, mb_detect_order(), true), "UTF-8", $text);
		return $encoded;
	}
	/**
	 * Formatting data for Algolia input.
	 *
	 * @param  array  $data            The raw data from Strattic search
	 * @return string $formatted_data  The JSON encoded/Algolia formattted string
	 */
	private function algolia_formatted_data( $data ) {

		if ( ! is_array( $data ) ) {
			return array();
		}

		$formatted_data = array();
		foreach ( $data as $key => $page ) {

			if ( ! isset( $page['path'] ) ) {
				continue; // No point in continuing if we don't have the file path ...
			}
			$formatted_data[$key]['id'] = $this->forceEncoding($page['id']);
			$formatted_data[$key]['path'] = $this->forceEncoding($page['path']);
			$formatted_data[$key]['objectID'] = $this->forceEncoding($page['path']);
			if ( isset( $page['date'] ) ) {
				$formatted_data[$key]['date'] = $this->forceEncoding($page['date']);
			}
			if ( isset( $page['time'] ) ) {
				$formatted_data[$key]['time'] = $this->forceEncoding($page['time']);
			}
			if ( isset( $page['title'] ) ) {
				$formatted_data[$key]['title'] = $this->forceEncoding($page['title']);
			}
			if ( isset( $page['thumbnail'] ) ) {
				$formatted_data[$key]['thumbnail'] = $this->forceEncoding($page['thumbnail']);
			}

			if ( isset( $page['excerpt'] ) ) {
				$excerpt = substr( $page['excerpt'], 0, 5000 );
				$formatted_data[$key]['excerpt'] = $this->forceEncoding(wp_kses_post( $excerpt ));
			}

			if ( isset( $page['content'] ) ) {
				$content = substr( $page['content'], 0, 5000 );
				$formatted_data[$key]['content'] = $this->forceEncoding(wp_kses_post( $content ));
			}

			if ( isset( $page['author'] ) ) {
				$formatted_data[$key]['author'] = $page['author'];
			}
			// if ( isset( $page['first_name'] ) ) {
			// 	$formatted_data[$key]['first_name'] = $this->forceEncoding(esc_html( $page['author']['firstName'] ));
			// }

			// if ( isset( $page['last_name'] ) ) {
			// 	$formatted_data[$key]['last_name'] = $this->forceEncoding(esc_html( $page['author']['lastName'] ));
			// }


			$taxonomies = get_taxonomies(
				array(
					'public'   => true,
					'_builtin' => true
				),
				'names',
				'and'
			);

			if ( is_array( $taxonomies ) ) {
				foreach ( $taxonomies  as $taxonomy ) {
					if ( isset( $page['terms'][$taxonomy] ) ) {
						$formatted_data[$key]['taxonomy-' . $taxonomy] = $page['terms'][$taxonomy];
						if ($taxonomy == 'category') {
							$formatted_data[$key]['categories'] = $page['terms'][$taxonomy];
						}
					}
				}
			}

		}

		return $formatted_data;
	}

	/**
	 * Get string size.
	 *
	 * @access  private
	 * @param   array   $data   The data to analyse
	 * @return  int   The string size in kilobytes
	 */
	private function get_string_size( $data ) {

		$json_data = json_encode( $data );
		$size = ( mb_strlen( $json_data ) / 1000 );

		return $size;
	}

	/**
	 * Get array data from option.
	 *
	 * @param   string   $option  The array key to select
	 * @return  string   The requested option data
	 */
	static public function get_option( $option ) {
		$values = get_option( self::SETTINGS_OPTION );

		$value = '';
		if ( isset( $values[ $option ] ) ) {
			$value = $values[ $option ];
		} else {

			// Fallback to single option if data not found in array
			$value = get_option( 'strattic-search-settings-' . $option );

		}

		// Dynamically inserting Algolia environment variables
		if (
			(
				'algolia-app-id' === $option
				||
				'algolia-admin-key' === $option
				||
				'algolia-search-key' === $option
				||
				'algolia-index' === $option
			)
			&&
			'' === $value // Only dynamically insert if value not already found in regular settings
		) {

			$option = str_replace( '-', '_', strtoupper( $option ) );
			if ( isset( $_ENV[$option] ) ) {
				$value = $_ENV[$option];
			}

		}

		return $value;
	}

	/**
	 * Get template htML.
	 *
	 * @param   string   $option  The array key to select
	 * @return  string   The requested option data
	 */
	public function get_template( $option ) {
		$values = get_option( self::TEMPLATES_OPTION );

		$value = '';
		if ( isset( $values[ $option ] ) ) {
			$value = $values[ $option ];
		}

		return $value;
	}

	/**
	 * Get the current page URL.
	 *
	 * @access  private
	 * @return  string  The URL
	 */
	private function get_current_url() {

		// Bail out if server variable not set - problem when using WP CLI
		if ( ! isset( $_SERVER[ 'SERVER_NAME' ] ) ) {
			return '';
		}

		$url = 'http';
		if ( is_ssl() ) {
			$url .= 's';
		}
		$url .= '://';
		$url .= $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];

		return $url;
	}

	/**
	 * Get the current page URL.
	 *
	 * @access  private
	 * @return  string  The URL path
	 */
	private function get_current_path() {

		$url = $this->get_current_url();
		$path = str_replace( home_url(), '', $url );

		return $path;
	}

	/**
	 * Strip HTML comments.
	 *
	 * @access private
	 * @param  string  $content  The text with HTML comments
	 * @return string  Text without HTML comments
	 */
	private function strip_html_comments( $content = '' ) {
		return preg_replace( '/<!--(.|\s)*?-->/', '', $content );
	}

	/**
	 * Strip HTML entitites.
	 *
	 * @access private
	 * @param  string  $content  The text with entities
	 * @return string  Text without entities
	 */
	private function strip_html_entities( $content = '' ) {
		$content = preg_replace( "/&#?[a-z0-9]+;/i", '', $content );
		return $content;
	}

	/**
	 * @access private
	 * @return array  The editable fields
	 */
	private function fields() {
		// Fields.
		$fields = array(
			// Strattic vars.
			array(
				'slug'        => 'search-on',
				'label'       => __( 'Search on?', 'strattic' ),
				'description' => __( 'Turns the search index on. Only turn this off if the site does not require search.', 'strattic' ),
				'default'     => 'on',
				'type'        => 'checkbox',
				'active'      => true,
			),
			array(
				'slug'        => 'search-taxonomies',
				'label'       => __( 'Search taxonomies?', 'strattic' ),
				'description' => __( 'Should taxonomy terms be searched?', 'strattic' ),
				'default'     => '',
				'type'        => 'checkbox',
				'active'      => true,
			),
			array(
				'slug'        => 'search-excerpt-length',
				'label'       => __( 'Excerpt word length', 'strattic' ),
				'description' => __( 'Set the maximum number of words used in an excerpt. Leave blank to use WordPress default.', 'strattic' ),
				'default'     => '',
				'type'        => 'number',
				'active'      => true,
			),
			array(
				'slug'        => 'search-page-slug',
				'label'       => __( 'Default search page slug', 'strattic' ),
				'description' => __( 'This is the page which the search form submits search requests to.', 'strattic' ),
				'default'     => 'search',
				'type'        => 'text',
				'active'      => true,
			),
			array(
				'slug'        => 'algolia-app-id',
				'js_slug'     => 'algoliaAppId',
				'label'       => __( 'Algolia app ID', 'strattic' ),
				'description' => sprintf(
					__( 'The Algolia app ID.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'text',
				'active'      => false,
			),
			array(
				'slug'        => 'algolia-admin-key',
				'js_slug'     => 'algoliaAdminKey',
				'label'       => __( 'Algolia app key', 'strattic' ),
				'description' => sprintf(
					__( 'The Algolia app key.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'text',
				'active'      => false,
			),
			array(
				'slug'        => 'algolia-search-key',
				'js_slug'     => 'algoliaSearchKey',
				'label'       => __( 'Algolia Search key', 'strattic' ),
				'description' => sprintf(
					__( 'The Algolia search key.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'text',
				'active'      => false,
			),
			array(
				'slug'        => 'algolia-index',
				'js_slug'     => 'algoliaIndex',
				'label'       => __( 'Algolia Index', 'strattic' ),
				'description' => sprintf(
					__( 'The Algolia index.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'text',
				'active'      => false,
			),
			array(
				'slug'           => 'search-main-template',
				'label'          => __( 'Search main template', 'strattic' ),
				'description'    => __( 'The template to wrap around search results.', 'strattic' ),
				'default'     => '<div id="search-results">
	'.sprintf( esc_html__( 'Search results for: %s', 'strattic' ), '<strong>{{search_string}}</strong>' ).'
	{{{content}}}
</div>',
				'template'       => 'template-output-one-result',
				'template_label' => __( 'Search Results', 'strattic' ),
				'type'           => 'editor',
				'active'         => true,
			),
			array(
				'slug'           => 'search-results-template',
				'label'          => __( 'Search results template', 'strattic' ),
				'description'    => __( 'The template to use for search results.', 'strattic' ),
				'default'        => '<h2 class="entry-title" itemprop="headline">
	<a href="{{url}}" rel="bookmark">{{title}}</a>
</h2>
<p>{{excerpt}} ... <a href="{{url}}" class="more-link">'. esc_html( 'Read more', 'strattic' ) . '</a></p>',
				'template'       => 'template-output-one-result',
				'template_label' => __( 'Search Results', 'strattic' ),
				'type'           => 'editor',
				'active'         => true,
			),
			array(
				'slug'           => 'search-pagination-template',
				'label'          => __( 'Search results pagination template', 'strattic' ),
				'description'    => __( 'The template used for providing the pagination to search results.', 'strattic' ),
				'default'        => '
<div id="pagination-item-current">
<span aria-current="page" class="page-numbers current">
	<span class="meta-nav screen-reader-text">' . __( 'Page', 'strattic-search' ) . ' </span>{{number}}
</span>
</div>
<div id="pagination-item">
<a class="page-numbers" href="{{url}}">
	<span class="meta-nav screen-reader-text">' . __( 'Page', 'strattic-search' ) . ' </span>{{number}}
</a>
</div>
<div id="pagination-nav-prev">
<a class="prev page-numbers" href="{{url}}">
	<svg class="icon icon-arrow-left" aria-hidden="true" role="img">
		<use href="#icon-arrow-left" xlink:href="#icon-arrow-left"></use>
	</svg>
	<span class="screen-reader-text">' . __( 'Previous Page', 'strattic-search' ) . '</span>
</a>
</div>
<div id="pagination-nav-next">
<a class="next page-numbers" href="{{url}}">
	<span class="screen-reader-text">' . __( 'Next Page', 'strattic-search' ) . '</span>
	<svg class="icon icon-arrow-right" aria-hidden="true" role="img">
		<use href="#icon-arrow-right" xlink:href="#icon-arrow-right"></use>
	</svg>
</a>
</div>
<div id="pagination-nav">
<nav class="navigation pagination" role="navigation">
	<h2 class="screen-reader-text">' . __( 'Posts navigation', 'strattic-search' ) . '</h2>
	<div class="nav-links">
		{{{nav}}}
	</div>
</nav>
</div>
',
				'template'       => 'template-output-many-results',
				'template_label' => __( 'Search Results with Pagination', 'strattic' ),
				'type'           => 'editor',
				'active'         => true,
			),
			array(
				'slug'           => 'search-not-found-template',
				'label'          => __( 'Search not found template', 'strattic' ),
				'description'    => __( 'The template to use when search not found.', 'strattic' ),
				'default'        => '<h2 class="entry-title" itemprop="headline">
' . __( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'strattic' ) . '
</h2>[strattic_search_form]' . "\n",
				'template'       => 'template-output-no-results',
				'template_label' => __( 'Empty Search Results', 'strattic' ),
				'type'           => 'editor',
				'active'         => true,
			),
		);

		return apply_filters( 'strattic_search_pro', $fields );
	}

	public function search_form_shortcode( $args ) {
		global $strattic_taxonomy, $strattic_term, $strattic_terms;

		if ( isset( $args['taxonomy'] ) && isset( $args['term'] ) ) {

			$strattic_taxonomy = $args['taxonomy'];

			add_filter( 'strattic_taxonomy', function( $taxonomy ) {
				global $strattic_taxonomy;

				return $strattic_taxonomy;
			});

			$strattic_term = $args['term'];
			add_filter( 'strattic_term', function( $taxonomy ) {
				global $strattic_term;

				return $strattic_term;
			});

		}

		if ( isset( $args['taxonomy'] ) && isset( $args['terms'] ) ) {
			$strattic_taxonomy = $args['taxonomy'];
			$strattic_terms = $args['terms'];

			add_filter( 'get_search_form', function($html) {
				global $strattic_taxonomy;
				global $strattic_terms;
				$closeIndex = strpos($html, "</form>");
				$taxInput = '<input type="hidden" name="taxonomy" value="'.$strattic_taxonomy.'" />';
				$termsInput = '<input type="hidden" name="terms" value="'.$strattic_terms.'" />';

				if ($closeIndex !== false) {
					$before = substr($html, 0, $closeIndex - strlen($html));
					$after = substr($html, $closeIndex);
					$html = $before . $taxInput . $termsInput . $after;
				}
				return $html;
			});

		}

		$html = get_search_form( false );

		return $html;
	}

	/**
	 * Adjust the available Cron schedules.
	 */
	public function cron_schedules( $schedules ) {

		$schedules['every_minute'] = array(
			'interval' => 60,
			'display'  => __( 'Once per minute', 'strattic' )
		);
		$schedules['every_10_minutes'] = array(
			'interval' => 60 * 10,
			'display'  => __( 'Once every 10 minutes', 'strattic' )
		);

		return $schedules;
	}

	/**
	 * Updating the search results.
	 */
	public function update_search_results() {

		// Get JSON stored search index data
		$data = get_option( self::RESULTS_OPTION );
		$old_hashed_data = get_option( self::HASHED_SEARCH_OPTION );

		// Increase maximum execution time to make sure that we can gather everything
		ini_set( 'max_execution_time', self::TIME_LIMIT );
		set_time_limit ( self::TIME_LIMIT );
		ini_set( 'memory_limit', self::MEMORY_LIMIT . 'M' );

		// Grab and process the data
		$data = $this->get_search_data();
		$new_hashed_data = $this->hash_data( $data );
		$changed_data = $this->get_changed_data( $data, $old_hashed_data );

		if ( count( $changed_data ) > 0 ) {
			$this->update_algolia_index( $changed_data );

		}
		$data = $this->shrink_data( $data );
		update_option( self::HASHED_SEARCH_OPTION, $new_hashed_data, false );
		update_option( self::RESULTS_OPTION, $data, false );
	}

}
new Strattic_Search;
