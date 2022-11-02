<?php

/**
 * Core features of the Strattic Search plugin.
 *
 * @copyright Copyright (c), Strattic / Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Strattic Search
 * @since Strattic Search 2.3.0
 */
class Strattic_Search_Core {

	var $request_uri;

	const VERSION_NUMBER = '2.3.36';

	/**
	 * Constructor.
	 */
	public function __construct() {

		// No need to load this in the admin panel.
		if ( is_admin() ) {
			return;
		}

		$this->request_uri = filter_input( INPUT_SERVER, 'REQUEST_URI' );

		$path = '/strattic-search.json';
		if ( $path === substr( $this->request_uri, 0, strlen( $path ) ) ) {
			add_action( 'init', array( $this, 'display_index' ) );
		}

		$get = filter_input( INPUT_GET, 'strattic-search-refresh' );
		if ( isset( $get ) ) {
			add_action( 'init', array( $this, 'cache_index' ) );
		}

		// Set name attribute to s (overridden later if search doesn't work on all pages).
		add_filter(
			'strattic_search_name_attr',
			function() {
				return 's';
			}
		);

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'load_search_engine' ), 9 ); // Needs lower priority to ensure it is loaded before the JSON dump.
	}

	/**
	 * Load everything on init.
	 */
	public function init() {

		// Bail out now if not meant to be searching.
		if ( ! $this->is_search() ) {
			return;
		}

		$this->strattic_specific_stuff();

		add_action( 'template_redirect', array( $this, 'buffer' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );

		add_filter( 'the_posts', array( $this, 'generate_search_page' ), -10 );
	}

	/**
	 * Load any code specific to the search engine.
	 * Can be overridden by creating a class named "Strattic_Search_Engine".
	 */
	public function load_search_engine() {

		// Only load if the class does not already exist (allows for overriding this functionality).
		if ( ! class_exists( 'Strattic_Search_Engine', false ) ) { // Second param is to avoid the autoloader being triggered.
			require( dirname( __FILE__ ) . '/class-strattic-search-engine.php' );
		}

		new Strattic_Search_Engine();
	}

	/**
	 * Load scripts.
	 */
	public function scripts() {

		if ( ! is_admin() ) {
			$data = apply_filters( 'strattic_search_page_data', array() );

			$plugin_dir = plugin_dir_url( dirname( __FILE__ ) );
			wp_enqueue_script( 'php-date', $plugin_dir . 'js/php-date.js', array(), self::VERSION_NUMBER, true );
			wp_enqueue_script( 'mustaches', $plugin_dir . 'js/mustaches.min.js', array(), self::VERSION_NUMBER, true );
			wp_enqueue_script( 'strattic-search', $plugin_dir . 'js/strattic-search.js', array( 'strattic-search-config' ), self::VERSION_NUMBER, true );
			wp_add_inline_script(
				'strattic-search',
				'let strattic_search = ' . wp_json_encode(
					$data,
					JSON_PRETTY_PRINT
				),
				'before'
			);

		}
	}

	/**
	 * Grabs list of all posts.
	 *
	 * @global object $wpdb The WordPress database object.
	 */
	public function display_index() {
		header( 'Content-Type: application/json' );

		// It's important not to cache the JSON blob as it needs to be fresh each time.
		header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		header( 'Cache-Control: post-check=0, pre-check=0', false );
		header( 'Pragma: no-cache' );

		$key = 'strattic_search_index';
		$index = $this->get_warm_transient( $key );
$index =false;
		if ( false === ( $index ) ) {
			$index = $this->get_index(); // Only runs on first plugin loading or if transient cache is flushed.
			$this->set_warm_transient( $key, $index );
		}

		echo wp_json_encode( $index, JSON_PRETTY_PRINT );
		die;
	}

	/**
	 * Caching the index.
	 */
	public function cache_index() {
		$key   = 'strattic_search_index';
		$index = $this->get_index();

		$this->set_warm_transient( $key, $index );

		return true;
	}

	/**
	 * Add json file path to Strattic.
	 *
	 * @param array $paths The paths to be published.
	 * @return array The modified list of paths to be published.
	 */
	public function strattic_add_path( $paths ) {

		$paths[] = array(
			'path'     => '/strattic-search.json',
			'priority' => 6,
		);

		return $paths;
	}

	/**
	 * Starting page buffer.
	 */
	public function buffer() {
//return;
//$html = file_get_contents( dirname( __FILE__ ) . '/page.html' );
//$html = $this->modify_search_forms( $html );
//echo $html;
//die;
		ob_start( array( $this, 'ob' ) );
	}

	/**
	 * Processing page buffer.
	 *
	 * @param  string $html The pages HTML.
	 * @return string The filtered page output.
	 */
	public function ob( $html ) {
		$html = $this->modify_search_forms( $html );

		return $html;
	}

	/**
	 * Generate a "search" page for showing search results, if the search slug isn't set to a real page.
	 *
	 * @param object $posts Original posts object.
	 * @global object $wp The main WordPress object.
	 * @global object $wp_query The main WordPress query object.
	 * @return object $posts Modified posts object.
	 */
	public function generate_search_page( $posts ) {
		global $wp, $wp_query;

		// Bail out now if no need to generate a specific search page.
		$active_pages = apply_filters( 'strattic_active_pages', '' );
		if (
			'all' === $active_pages // No need for a specific page if it works on all pages.
			&&
// should handle an array, not a string here.
			substr( $this->request_uri, 0, strlen( $active_pages) ) !== $active_pages // Handles scenario where we only use the home page.
		) {
			return $posts;
		}

		// Set name attribute to q.
		add_filter(
			'strattic_search_name_attr',
			function() {
				return 'q';
			}
		);

		// Only generate fake page when on the URL to be generated.
		if (
			empty( $posts ) &&
			! isset( $this->generated )
		) {

			// Stop interfering with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page).
			if ( ! isset( $this->generated ) ) {
				$this->generated = true;
			}

			$guid = esc_url( home_url() . $active_pages );

			// Create a fake virtual page.
			$post                 = new \stdClass();
			$post->post_author    = 1;
			$post->post_name      = esc_html( basename( $active_pages ) );
			$post->guid           = $guid;
			$post->post_title     = esc_html__( 'Search results', 'strattic-search' );
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

			$posts   = null;
			$posts[] = new \WP_Post( $post );

			// Make WP Query believe this is a real page too.
			unset( $wp_query->query['error'] );
			$wp_query->query_vars['error'] = '';

			$wp_query->is_page     = true;
			$wp_query->is_singular = true;
			$wp_query->is_home     = false;
			$wp_query->is_archive  = false;
			$wp_query->is_category = false;
			$wp_query->is_404      = false;
		}

		return $posts;
	}

	/**
	 * Implementing features which are specific to the Strattic platform.
	 */
	public function strattic_specific_stuff() {

		// Bail out now, as this is not a Strattic site.
		if ( ! function_exists( 'strattic' ) ) {
			return;
		}

		$this->strattic_disable_search();
		add_filter( 'strattic_paths', array( $this, 'strattic_add_path' ) );
	}

	/**
	 * Are we meant to be searching?
	 *
	 * @return bool True if searching, false if not searching.
	 */
	private function is_search() {

		// Definitely shouldn't be searching in the admin panel.
		if ( is_admin() ) {
			return false;
		}

		$data = apply_filters( 'strattic_search_page_data', array() );
		if ( 'all' === $data['active_pages'] ) {
			return true; // If allowing search on all pages.
		} else if ( // If on a designated search page.
			substr( $this->request_uri, 0, strlen( $data['active_pages'] ) ) === $data['active_pages']
		) {
			return true;
		}

		return false;
	}


	/**
	 * Grabs list of all posts.
	 *
	 * @access private
	 * @global object $wpdb The WordPress database object.
	 */
	private function get_posts() {
		global $wpdb;

		$post_types           = apply_filters( 'strattic_search_post_types', array( 'post', 'page' ) );
		$post_types_string = implode( ',', array_fill( 0, count( $post_types ), '%s' ) );
		$post_statuses        = array( 'publish' ); // PUT FILTER HERE IN FUTURE RYAN!
		$posts_table_name     = "{$wpdb->prefix}posts";
		$rel_table_name       = "{$wpdb->prefix}term_relationships";
		$term_tax_table_name  = "{$wpdb->prefix}term_taxonomy";
		$taxonomies           = get_taxonomies( array( 'public' => true ) );
		$taxonomies_string = implode( ',', array_fill( 0, count( $taxonomies ), '%s' ) );

		// QUERY SHOULD BE SIMPLIFIED.
		$query_string =
			'SELECT p.*, GROUP_CONCAT(tt.term_id) as term_ids FROM `%1s` AS p '
			. 'LEFT JOIN `%1s` tr ON tr.object_id = p.id '
			. 'LEFT JOIN `%1s` tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy IN (' . $taxonomies_string . ') '
			. ' WHERE post_status IN ( %s ) 
			AND post_type IN ( ' . $post_types_string . ' ) '
			. 'GROUP BY p.id';

		$args = array_merge(
			array(
				$posts_table_name,
				$rel_table_name,
				$term_tax_table_name,
			),
			$taxonomies,
			$post_statuses,
			$post_types
		);

		$query = $wpdb->prepare(
			$query_string,
			$args
		);

		$posts = $wpdb->get_results( $query );

		$post_index = array();
		foreach ( $posts as $key => $post ) {

			// Do not search in password protected posts.
			if ( isset( $post->post_password ) && '' !== $post->post_password ) {
				continue;
			}

			// Only search in published posts.
			if ( isset( $post->post_status ) && 'publish' !== $post->post_status ) {
				continue;
			}

			// Collect the term IDs.
			$term_ids = array();
			foreach ( (array) $post->term_ids as $key => $post_term_ids ) {
				$post_term_ids = explode( ',', $post_term_ids );
				foreach ( $post_term_ids as $key => $term_id ) {
					$term_ids[] = absint( $term_id );
				}
			}

			// Work out if sticky or not.
			$sticky = false;
			if ( is_sticky( $post->ID ) ) {
				$sticky = true;
			}

			$post_index[] = array(
				'id'                 => absint( $post->ID ),
				'path'               => esc_html( str_replace( home_url(), '', get_permalink( $post ) ) ),
				'author_id'          => absint( $post->post_author ),
				'timestamp'          => strtotime( $post->post_date_gmt ),
				'content'            => apply_filters( 'the_content', wp_kses_post( $post->post_content ) ),
				'title'              => wp_kses_post( $post->post_title ),
				'excerpt'            => wp_kses_post( $this->get_the_excerpt( $post ) ),
				'slug'               => esc_attr( $post->post_name ),
				'modified_timestamp' => strtotime( $post->post_modified_gmt ),
				'term_ids'           => $term_ids,
				'post_type'          => esc_html( $post->post_type ),
				'sticky'             => $sticky,
				'attachments'        => $this->get_attachments( $post->ID ),
			);

		}

		// Put posts in order of timestamp.
		usort(
			$post_index,
			function( $a, $b ) {
				return $b['timestamp'] <=> $a['timestamp'];
			}
		);

		return $post_index;
	}

	/**
	 * Output the search index.
	 *
	 * @access private
	 * @return array The search index.
	 */
	private function get_index() {
		$index = array(
			'debounce_timer'       => 500,
			'active_theme'         => esc_html( basename( get_template_directory() ) ),
			'home_url'             => esc_url( home_url() ),
			'posts'                => $this->get_posts(),
			'order'                => 'DESC',
			'orderby'              => 'timestamp',
			'taxonomies'           => $this->get_taxonomy_data(),
			'authors'              => $this->get_author_data(),
			'date_format'          => esc_html( get_option( 'date_format' ) ),
			'home_title'           => esc_html( get_option( 'blogname' ) ) . ' &#8211; ' . esc_js( get_option( 'blogdescription' ) ),
			'posts_per_page'       => absint( get_option( 'posts_per_page' ) ),
			'pagination_page_text' => esc_html__( 'page', 'strattic-search' ),
			'prev_button_text'     => esc_html__( '&laquo; Previous', 'strattic-search' ),
			'next_button_text'     => esc_html__( 'Next &raquo;', 'strattic-search' ),
'templates'            => array(), // Main page templates should be moved here.
		);

		if ( isset( $_SERVER['REQUEST_SCHEME'] ) && isset( $_SERVER['HTTP_HOST'] ) ) {
			$index['home_path'] = str_replace( esc_url( wp_kses_post( wp_unslash( $_SERVER['REQUEST_SCHEME'] ) ) . '://' . wp_kses_post( wp_unslash( $_SERVER['HTTP_HOST'] ) ) ), '', esc_url( home_url() ) ); // For when WordPress is in a sub-folder.
		}

		$index         = apply_filters( 'strattic_search_index', $index );
		$index['size'] = round( strlen( wp_json_encode( $index ) ) / 1000 ) . ' kB';

		return $index;
	}

	/**
	 * Gets the transient.
	 *
	 * If the transient timer is set to expire, then the transient is returned
	 * and an http request is run to trigger a cache refresh.
	 *
	 * @access private
	 * @param string $key The key for the transient.
	 * @return string The transients contents.
	 */
	private function get_warm_transient( $key ) {
		if ( ! get_transient( $key . '_timer' ) ) {

			// Run http request to refresh search cache.
			wp_remote_get( home_url() . '?strattic-search-refresh' );
		}

		return get_transient( $key );
	}

	/**
	 * Sets the transient.
	 *
	 * Also sets a transient timer. Used in get_warm_transient to ensure
	 * that a result is always returned.
	 *
	 * @access private
	 * @param string $key The key for the transient.
	 * @param string $value The value of the transient.
	 */
	private function set_warm_transient( $key, $value ) {
		$time_to_cache = apply_filters( 'strattic_search_cache_time', MINUTE_IN_SECONDS );
		set_transient( $key . '_timer', true, $time_to_cache );
		set_transient( $key, $value );
	}

	/**
	 * Return the post excerpt, if one is set, else generate it using the
	 * post content. If original text exceeds $num_of_words, the text is
	 * trimmed and an ellipsis (…) is added to the end.
	 *
	 * Based on https://gist.github.com/kellenmace/6209d5f1e465cdcc800e690b472f8f16.
	 *
	 * @access private
	 * @param object $post The WordPress post object.
	 * @return string The generated excerpt.
	 */
	private function get_the_excerpt( $post ) {
		$text = $post->post_content;
		$text = wp_trim_words( $text, apply_filters( 'excerpt_length', 55 ) );
		$text = strip_shortcodes( $text );

		return apply_filters( 'get_the_excerpt', $text, $post );
	}

	/**
	 * Get extra fields.
	 *
	 * @access private
	 * @return array The extra fields.
	 */
	private function get_extra_fields() {
		return apply_filters( 'strattic_search_fields', $this->get_taxonomy_data() );
	}

	/**
	 * Get taxonomy data.
	 *
	 * @access private
	 * @return array The taxonomy data.
	 */
	private function get_taxonomy_data() {
		$data = array();

		$taxonomies = get_taxonomies(
			array(
				'public'   => true,
				'_builtin' => true,
			),
			'objects',
			'and'
		);

		foreach ( $taxonomies as $taxonomy => $taxonomy_data ) {

			// Add taxonomy data.
			$data[ $taxonomy ]['taxonomy_data'] = array(
				'name'   => $taxonomy_data->name,
				'label'  => $taxonomy_data->label,
				'labels' => array(
					'name'          => $taxonomy_data->labels->name,
					'singular_name' => $taxonomy_data->labels->singular_name,
					'search_items'  => $taxonomy_data->labels->search_items,
					'all_items'     => $taxonomy_data->labels->all_items,
				),
			);

			// Add term data.
			$terms = get_terms(
				array(
					'taxonomy'   => $taxonomy,
					'hide_empty' => false,
				)
			);

			foreach ( $terms as $term ) {

				$data[ $taxonomy ]['terms'][] = array(
					'id'   => $term->term_id,
					'name' => $term->name,
					'slug' => $term->slug,
				);

			}
		}

		return $data;
	}

	/**
	 * Get author data.
	 *
	 * @access private
	 * @return array The author data.
	 */
	private function get_author_data() {

		$authors = array();
		$users   = get_users();
		foreach ( $users as $key => $user ) {
			$user_id = absint( $user->data->ID );

			if ( 0 < count_user_posts( $user_id ) ) {
				$authors[ $user_id ] = array(
					'display_name'  => esc_html( $user->data->display_name ),
					'user_login'    => esc_html( $user->data->user_login ),
					'user_nicename' => esc_html( $user->data->user_nicename ),
					'path'          => esc_url( str_replace( home_url(), '', get_author_posts_url( $user->data->ID ) ) ),
				);
			}
		}

		return $authors;
	}

	/**
	 * Adding advanced fields.
	 *
	 * @access private
	 * @return string The modified search form HTML.
	 */
	private function search_form() {
		$extra_fields = '';
		foreach ( $this->get_extra_fields() as $taxonomy => $taxonomy_data ) {
			$taxonomy_label = $taxonomy_data['taxonomy_data']['labels']['singular_name'];

			$terms = $taxonomy_data['terms'];

			$extra_fields .= '<p>';
			$extra_fields .= '<label for="' . esc_attr( 'strattic-' . $taxonomy ) . '">' . esc_html( $taxonomy_label ) . '</label>';
			$extra_fields .= '<select data-taxonomy="' . esc_attr( $taxonomy ) . '">';
			// translators: %s is the taxonomy label.
			$extra_fields .= '<option value="">' . sprintf( esc_html__( 'Select a %s', 'strattic-search' ), esc_html( strtolower( $taxonomy_label ) ) ) . '</option>';

			foreach ( $terms as $term ) {
				$extra_fields .= '<option value="' . esc_attr( $term['id'] ) . '">' . esc_html( $term['name'] ) . '</option>';
			}

			$extra_fields .= '</select>';
			$extra_fields .= '</p>';
		}

		return $extra_fields;
	}

	/**
	 * Find all the opening <form> tags in the HTML string.
	 * Forms with a name attribute of "s" may need to have that attribute changed to q and their action URL changed to the one set in the search slug setting.
	 * Displays taxonomy selectors if required.
	 *
	 * Adapted from the Strattic plugin.
	 *
	 * @access private
	 * @param string $string The HTML of the page.
	 * @return string The HTML of the page with the attributes changed.
	 */
	private function modify_search_forms( $string ) {
		$regex    = '/<form(\s+[^>]*>|>)/';
		$matches  = array();
		$form_end = '</form>';

		preg_match_all( $regex, $string, $matches );
		foreach ( $matches[0] as $form_start ) {

			// Get full form HTML.
			$form_chunks = explode( $form_end, $string );
			unset( $original_form_html );
			foreach ( $form_chunks as $key => $chunk ) {
				$form_innards = explode( $form_start, $chunk );

				if ( isset( $form_innards[1] ) ) {
					$original_form_html = $form_start . $form_innards[1] . $form_end;
				} else {
					continue;
				}
			}

			// If no form HTML present, then bail out now.
			if ( ! isset( $original_form_html ) ) {
				return $string;
			}

			// Get Dom node for the form.
			$doc = new DOMDocument();
			libxml_use_internal_errors( true ); // Suppresses warnings from bad HTML.
			$doc->loadHTML( mb_convert_encoding( $original_form_html, 'HTML-ENTITIES', 'UTF-8' ) );

			$forms        = $doc->getElementsByTagName( 'form' );
			$form_element = $forms->item( 0 ); // We only have one form in this Dom node (due to splitting it into bits via regex earlier), so it has to be the first one.

			// Loop through each input field and modify it's attribute if required.
			$inputs = $form_element->getElementsByTagName( 'input' );
			foreach ( $inputs as $key => $input_element ) {
				if ( 's' === $input_element->getAttribute( 'name' ) ) {

					// Change name attribute if required (only applies to unsupported themes).
					$name_attr = apply_filters( 'strattic_search_name_attr', 's' );
					if ( 's' !== $name_attr ) {
						$input_element->setAttribute( 'name', 'q' );
						$active_pages = apply_filters( 'strattic_active_pages', '' );
						$page_slug    = basename( $active_pages );
						$action_url   = esc_url( home_url() . '/' . $page_slug . '/' );
						$form_element->setAttribute( 'action', $action_url );
					}

					// Add extra form fields in (but not on the admin bar).
					$form_fields = $doc->createDocumentFragment();
					$id          = $input_element->getAttribute( 'id' );
					if ( 'adminbar-search' !== $id ) { // Ignore admin bar.
						$form_fields->appendXML( $this->search_form() );
						$form_element->appendChild( $form_fields );
					}

					$new_form_html = $doc->saveHTML( $form_element );
					$string        = str_replace( $original_form_html, $new_form_html, $string );
					continue;
				}
			}
		}

		return $string;
	}

	/**
	 * Get attachments for a post.
	 *
	 * @access private
	 * @param int $post_id The post ID to get attachments for.
	 * @return array The attachments.
	 */
	private function get_attachments( $post_id ) {
		$attachments = array();

		$sizes   = get_intermediate_image_sizes();
		$sizes[] = 'full';
		foreach ( $sizes as $size ) {
			$thumbnail_src        = get_the_post_thumbnail_url( $post_id, $size );
			$attachments[ $size ] = $thumbnail_src;
		}

		return $attachments;
	}

	/**
	 * Disable Strattic search.
	 * Since we are overriding it, it is not necessary.
	 *
	 * @access private
	 */
	private function strattic_disable_search() {
		if ( 'on' === strattic()->search->settings->get_option( 'search-on' ) ) {
			strattic()->search->settings->delete_option( 'search-on' );
		}
	}

}
