<?php

$bla = array(
	'minMatchCharLength' => "1",
	'shouldSort'=>"true",
	'keys'=>array(
		"title",
		"author.firstName"
	),
	'location'=>"0",
	'threshold'=>"0",
	'distance'=>"100",
	'maxPatternLength'=>"32"
);
/*
$bla = json_encode( $bla );
print_r( $bla );
$bla = json_decode( $bla );
print_r( $bla );
die;

{"minMatchCharLength":"1","shouldSort":"true","keys":["title","author.firstName"],"location":"0","threshold":"0","distance":"100","maxPatternLength":"32"}



$keys = '[
		"title",
		"author.firstName"
	]';
$bla = '{
	"minMatchCharLength":"1",
	"shouldSort":"true",
	"keys":' . $keys . ',
	"location":"0",
	"threshold":"0",
	"distance":"100",
	"maxPatternLength":"32"
}';

$bla = json_decode( $bla );
print_r( $bla );
die;
*/

//strattic_search_settings={"minMatchCharLength":"1","shouldSort":"true","keys":"[&quot;title&quot;,&quot;author.firstName&quot;]","location":"0","threshold":"0","distance":"100","maxPatternLength":"32"};</script><script type="text/javascript" src="http://dev-hellyer.kiwi/unique-headers/wp-includes/js/admin-bar.min.js?ver=5.0-alpha-43293"></script><script type="text/javascript" src="http://dev-hellyer.kiwi/unique-headers/wp-includes/js/underscore.min.js?ver=1.8.3"></script><script type="text/javascript">


/**
 * Strattic search
 * 
 * @copyright Copyright (c), Strattic
 * @since 1.1
 */
class Strattic_Search extends Strattic_Core {

	public $fields;
	const SETTINGS_OPTION = 'strattic-search-settings';
	const RESULTS_OPTION = 'strattic-search-results';

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Add hooks
		add_action( 'admin_init',         array( $this, 'register_settings' ) );
		add_action( 'admin_init',         array( $this, 'add_option' ) );
		add_action( 'admin_menu',         array( $this, 'create_admin_page' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'script' ) );
		add_action( 'init',               array( $this, 'search_api' ) );
		add_action( 'init',               array( $this, 'search_request' ) );
		add_action( 'wp_footer',          array( $this, 'js_templates' ), 5 );

		// Add filters
		add_filter( 'get_search_form',    array( $this, 'modify_search_url' ) );

		// Cron stuff
		add_action( 'strattic_task',      array( $this, 'get_search_results' ) );
		add_action( 'save_post',          array( $this, 'reset_cron' ) );
		register_activation_hook( STRATTIC_DIR . '/strattic.php', array( $this, 'activate' ) );
		register_deactivation_hook( STRATTIC_DIR . '/strattic.php', array( $this, 'deactivate' ) );

		// Fields
		$this->fields = array(

			// Strattic vars
			0 => array(
				'slug'        => 'search-on',
				'label'       => esc_html__( 'Search on?', 'strattic' ),
				'description' => esc_html__( 'Turns the search index on. Only turn this off if the site does not require search.', 'strattic' ),
				'default'     => 'on',
				'type'        => 'checkbox',
			),
			1 => array(
				'slug'        => 'size',
				'label'       => esc_html__( 'Size (kB)', 'strattic' ),
				'description' => esc_html__( 'Limits the size (in kB) of the search index.', 'strattic' ),
				'default'     => '512',
				'type'        => 'number',
			),
			2 => array(
				'slug'        => 'search-page-slug',
				'label'       => esc_html__( 'Default search page slug', 'strattic' ),
				'description' => esc_html__( 'This is the page which the search form submits search requests to.', 'strattic' ),
				'default'     => 'search',
				'type'        => 'text',
			),
			3 => array(
				'slug'        => 'search-template',
				'label'       => esc_html__( 'Search template', 'strattic' ),
				'description' => esc_html__( 'This is search template used. THIS FEATURE NEEDS IMPLEMENTED AS A WP JS TEMPLATE.', 'strattic' ),
				'default'     => '<h2 class="entry-title" itemprop="headline">
	<a href="URL" rel="bookmark">TITLE</a>
</h2>
<p>TEXT</p>
<p>
	<a href="URL" class="more-link">READMORE</a>
</p>',
				'type'        => 'textarea',
			),

			// Fuse JS vars
			10 => array(
				'slug'        => 'case-sensitive',
				'js_slug'     => 'caseSensitive',
				'label'       => esc_html__( 'Case sensitive', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'Indicates whether comparisons should be case sensitive.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'checkbox',
			),
			11 => array(
				'slug'        => 'include-score',
				'js_slug'     => 'includeScore',
				'label'       => esc_html__( 'Include score', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'Whether the score should be included in the result set. A score of 0 indicates a perfect match, while a score of 1 indicates a complete mismatch.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'checkbox',
			),
			12 => array(
				'slug'        => 'include-matches',
				'js_slug'     => 'includeMatches',
				'label'       => esc_html__( 'Include matches', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'Whether the matches should be included in the result set. When %1$strue%2$s, each record in the result set will include the indices of the matched characters: %1$sindices: [start, end]%2$s. These can consequently be used for highlighting purposes.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'checkbox',
			),
			13 => array(
				'slug'        => 'minimum-character-length',
				'js_slug'     => 'minMatchCharLength',
				'label'       => esc_html__( 'Minimum character length', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'When set to include matches, only the matches whose length exceeds this value will be returned. (For instance, if you want to ignore single character index returns, set to %1$s2%2$s)', 'strattic' ),
					'<code>',
					'</code>'
				),
				'default'     => '1',
				'type'        => 'number',
			),
			14 => array(
				'slug'        => 'sort',
				'js_slug'     => 'shouldSort',
				'label'       => esc_html__( 'Sort', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'Whether to sort the result list, by score.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'default'     => 'on',
				'type'        => 'checkbox',
			),
			15 => array(
				'slug'        => 'tokenize',
				'js_slug'     => 'tokenize',
				'label'       => esc_html__( 'Tokenize', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'When true, the algorithm will search individual words and the full string, computing the final score as a function of both. In this case, the %1$sthreshold%2$s, %1$sdistance%2$s, and %1$slocation%2$s are inconsequential for individual tokens, and are thus ignored.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'checkbox',
			),
			16 => array(
				'slug'        => 'match-all-tokens',
				'js_slug'     => 'matchAllTokens',
				'label'       => esc_html__( 'Match all tokens', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'When %1$strue%2$s, the result set will only include records that match all tokens. Will only work if %1$stokenize%2$s is also %1$strue%2$s.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'checkbox',
			),
			17 => array(
				'slug'        => 'find-all-matches',
				'js_slug'     => 'findAllMatches',
				'label'       => esc_html__( 'Find All Matches', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'When true, the matching function will continue to the end of a search pattern even if a perfect match has already been located in the string.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'checkbox',
			),
			18 => array(
				'slug'        => 'id',
				'js_slug'     => 'id',
				'label'       => esc_html__( 'ID', 'strattic' ),
				'description' => sprintf(
					esc_html__( 'The name of the identifier property. If specified, the returned result will be a list of the items\' identifiers, otherwise it will be a list of the items.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'text',
			),
			19 => array(
				'slug'        => 'keys',
				'js_slug'     => 'keys',
				'label'       => esc_html__( 'Keys', 'strattic' ),
				'default'     => esc_attr( '["title","author.firstName"]', 'strattic' ),
				'description' => esc_html( 'List of properties that will be searched. This supports nested properties, weighted search, searching in arrays of strings and objects', 'strattic' ),
				'type'        => 'textarea',
			),
			20 => array(
				'slug'        => 'location',
				'js_slug'     => 'location',
				'label'       => esc_html__( 'Location', 'strattic' ),
				'default'     => '0',
				'description' => sprintf(
					esc_html__( 'Determines approximately where in the text is the pattern expected to be found.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'number',
			),
			21 => array(
				'slug'        => 'threshold',
				'js_slug'     => 'threshold',
				'label'       => esc_html__( 'Threshold', 'strattic' ),
				'default'     => '0.6',
				'description' => sprintf(
					esc_html__( 'At what point does the match algorithm give up. A threshold of %1$s0.0%2$s requires a perfect match (of both letters and location), a threshold of %1$s1.0%2$s would match anything.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'number',
			),
			22 => array(
				'slug'        => 'distance',
				'js_slug'     => 'distance',
				'label'       => esc_html__( 'Distance', 'strattic' ),
				'default'     => '100',
				'description' => sprintf(
					esc_html__( 'Determines how close the match must be to the fuzzy location (specified by %1$slocation%2$s). An exact letter match which is %1$sdistance%2$s characters away from the fuzzy location would score as a complete mismatch. A %1$sdistance%2$s of %1$s0%2$s requires the match be at the exact %1$slocation%2$s specified, a %1$sdistance%2$s of %1$s1000%2$s would require a perfect match to be within %1$s800%2$s characters of the %1$slocation%2$s to be found using a %1$sthreshold%2$s of %1$s0.8%2$s.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'number',
			),
			23 => array(
				'slug'        => 'max-pattern-length',
				'js_slug'     => 'maxPatternLength',
				'label'       => esc_html__( 'Max pattern length', 'strattic' ),
				'default'     => '32',
				'description' => sprintf(
					esc_html__( 'The maximum length of the pattern. The longer the pattern (i.e. the search query), the more intensive the search operation will be. Whenever the pattern exceeds the %1$smaxPatternLength%2$s, an error will be thrown.', 'strattic' ),
					'<code>',
					'</code>'
				),
				'type'        => 'number',
			),
		);

	}

	/**
	 * Resets the WP Cron task when a post is edited.
	 * This causes the task to trigger one minute later.
	 * This ensures that the search index is updated shortly after a post is edited.
	 */
	public function reset_cron() {
		wp_clear_scheduled_hook( 'strattic_task' );
		wp_schedule_event( time(), 'hourly', 'strattic_task' );
	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {

		register_setting(
			self::SETTINGS_OPTION,   // The settings group name
			self::SETTINGS_OPTION,   // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);

	}

	/**
	 * Add default option.
	 */
	public function add_option() {

		foreach ( $this->fields as $key => $field_vars ) {
			$slug = $field_vars[ 'slug' ];

			$default =  '';
			if ( isset( $field_vars[ 'default' ] ) ) {
				$default = $field_vars[ 'default' ];
			}

			$values[ $slug ] = $default;

		}

		//delete_option( self::SETTINGS_OPTION );
		add_option( self::SETTINGS_OPTION, $values, null, 'no' );

	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {

		add_submenu_page(
			self::SETTINGS_OPTION,
			esc_html__( 'Search', 'strattic' ),
			esc_html__( 'Search', 'strattic' ),
			'manage_options',
			self::SETTINGS_OPTION,
			array( $this, 'admin_page' )
		);

	}

	/**
	 * Output the admin page.
	 *
	 * @global  string  $title  The page title set by add_submenu_page()
	 */
	public function admin_page() {
		global $title;

		require( dirname( dirname( __FILE__ ) ) . '/views/search-admin.php' );

	}

	/**
	 * Sanitize the page or product ID.
	 *
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {
		$output = array();

		// Loop through each bit of data
		foreach( $this->fields as $key => $field ) {
			$key = $field[ 'slug' ];

			// Skip if value not found
			if ( ! isset( $input[ $key ] ) ) {
				$output[ $key ] = '';
			}

			// Sanitize input data
			$type = $field[ 'type' ];
			if ( 'number' === $type ) {
				$value = absint( $input[ $key ] );
			} else if ( 'textarea' === $type ) {
				$value = wp_kses( $input[ $key ], array() );
$value = $input[ $key ]; // Bypassing sanitization due to use of templates in textarea setting
			} else if ( 'checkbox' === $type ) {
				$value = '';
				if ( 'on' === $input[ $key ] ) {
					$value = 'on';
				}
			} else if ( 'text' === $type ) {
				$value = wp_kses( $input[ $key ], array() );
			}

			// Create array for saving
			$output[ $key ] = $value;

		}

		// Return the sanitized data
		return $output;
	}

	/**
	 * Load script.
	 */
	public function script() {

		// Bail out if in admin panel
		if ( is_admin() ) {
			return;
		}

		// Bail out if search functionality is turned off
		if ( 'on' !== $this->get_option( 'search-on' ) ) {
			return;
		}

		wp_enqueue_script( 'wp-util' );
		wp_enqueue_script( 'fuse-js', STRATTIC_ASSETS . 'fuse.min.js', null, STRATTIC_VERSION );
		wp_enqueue_script( 'strattic-search-json', home_url() . '/strattic-search/', array( 'fuse-js' ), null );
		wp_enqueue_script( 'strattic-fuse-js', STRATTIC_ASSETS . 'search.js', array( 'strattic-search-json' ), STRATTIC_VERSION, true );

		add_action( 'wp_footer', array( $this, 'search_vars' ) );
	}

	/**
	 * Get search api.
	 */
	public function search_api() {

		// Bail out if not on search page
		if ( '/strattic-search/' !== $this->get_current_path() ) {
			return;
		}


		$data = $this->get_search_results();
		$json_data = json_encode( $data );

		echo 'var data = ' . $json_data;
		die;
	}

	/**
	 * Output results for search request.
	 * Intended for implement search on the local site, not on static.
	 */
	public function search_request() {

		// Bail out if not on search page
		if ( '/strattic-search/cached/' !== $this->get_current_path() ) {
			return;
		}

		if ( get_option( self::RESULTS_OPTION ) ) {
			$data = get_option( self::RESULTS_OPTION );
		} else {
			$data = $this->get_search_results();
		}

		$json_data = json_encode( $data );

		echo $json_data;
		die;
	}

	/**
	 * Get search results.
	 *
	 * @param  array  The search results
	 */
	public function get_search_results() {

		// Increase maximum execution time to make sure that we can gather everything
		ini_set( 'max_execution_time', self::TIME_LIMIT );
		set_time_limit ( self::TIME_LIMIT );
		ini_set( 'memory_limit', self::MEMORY_LIMIT . 'M' );

		// Grab and process the data
		$data = $this->get_search_data();
		$data = $this->shrink_data( $data );

		// Stash the data for use locally
		update_option( self::RESULTS_OPTION, $data, 'no' );

		return $data;
	}

	/**
	 * Modify the search form URL.
	 *
	 * @param  string  $html  The search form HTML
	 * @return string  The modified search form HTML
	 */
	public function modify_search_url( $html ) {

		$before = 'action="' . esc_url( home_url( '/' ) ) . '"';
		$after = 'action="' . esc_url( home_url( '/' . $this->get_option( 'search-page-slug' ) . '/' ) ) . '"';
		$html = str_replace( $before, $after, $html );

		return $html;
	}

	/**
	 * Grabs list of all posts.
	 *
	 * @access  private
	 * @return  array  The data
	 */
	private function get_search_data() {

		// Loop through ALL post types
		$post_types = get_post_types( array( /*'_builtin' => true,*/ 'public' => true ), 'names', 'and' );

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
				) );

				if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
						$query->the_post();

						// We need to allow attachments with use 'inherit', but we need to make sure their parents actually have a valid post status - which is what get_post_status() returns when inherit is used
						if ( ! in_array( get_post_status(), $post_statuses ) ) {
							continue;
						}

						$data[ $count ][ 'path' ]                  = str_replace( home_url(), '', get_the_permalink() );
						$data[ $count ][ 'title' ]                 = get_the_title();
						$data[ $count ][ 'excerpt' ]               = wp_kses( get_the_excerpt(), array() );
						$data[ $count ][ 'content' ]               = wp_kses( get_the_content(), array() );
						$data[ $count ][ 'author' ][ 'firstName' ] = get_the_author_meta( 'first_name' );
						$data[ $count ][ 'author' ][ 'lastName' ]  = get_the_author_meta( 'last_name' );

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
	 * Shrinks the amount of data stored.
	 * This is a crude method.
	 * It simply runs a bunch of filtering until the index is small enough.
	 *
	 * Uses array_values() as array needs reindexed each time to obtain the correct array size.
	 *
	 * @access  private
	 * @param   string  $data  The data
	 * @return  array  The shrunk data
	 */
	private function shrink_data( $data ) {

		$allowed_size = absint( $this->get_option( 'size' ) );

		// Test without excerpt initially as no point in measuring excerpt and content at once
		$processed_data = $data;
		foreach( $processed_data as $key => $value ) {
			unset( $processed_data[ $key ][ 'excerpt'] );
		}
		$processed_data = array_values( $processed_data );
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Without authors or excerpt
		foreach( $processed_data as $key => $value ) {
			unset( $processed_data[ $key ][ 'author'] );
		}
		$processed_data = array_values( $processed_data );
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

		// Without content
		foreach( $data as $key => $value ) {
			unset( $processed_data[ $key ][ 'author'] );
		}
		$processed_data = array_values( $processed_data );
		if ( $allowed_size > $this->get_string_size( $processed_data ) ) {
			return $processed_data;
		}

		// Without content, authors or excerpt
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
	 * @access  private
	 * @param   string   $option  The array key to select
	 * @return  string   The requested option data
	 */
	private function get_option( $option ) {
		$values = get_option( self::SETTINGS_OPTION );

		$value = '';
		if ( isset( $values[ $option ] ) ) {
			$value = $values[ $option ];
		}

		return $value;
	}

	/*
	 * Activate Cron job.
	 * This task is set to activate hourly, but it is updated to occur immediately when a post is edited.
	 */
	public function activate() {
		wp_schedule_event( time(), 'hourly', 'strattic_task' );
	}

	/*
	 * Deactivate Cron job.
	 */
	public function deactivate() {
		wp_clear_scheduled_hook( 'strattic_task' );
	}

	/**
	 * Add JS templates.
	 * Used for generating HTML from AJAX requests to the WordPress REST API.
	 */
	public function js_templates() {

		echo '
<script type="text/html" id="tmpl-strattic-search-template">
' . $this->get_option( 'search-template' ) . '
</script>';

	}

	/**
	 * Output search variables to page footer.
	 */
	public function search_vars() {
		$settings = array();
		foreach ( $this->fields as $key => $field ) {

			if ( ! isset( $field[ 'js_slug' ] ) ) {
				continue;
			}

			$slug    = $field[ 'slug' ];
			$js_slug = $field[ 'js_slug' ];
			$type    = $field[ 'type' ];

			if ( 'checkbox' === $type && '' !== $this->get_option( $slug ) ) {
				$settings[ $js_slug ] = 'true';
			} else if ( '' !== $this->get_option( $slug ) ) {

				$settings[ $js_slug ] = esc_js( esc_html( $this->get_option( $slug ) ) );
			}

		}

		$settings[ 'keys'] = 'somefillerstring';

		$settings_json = json_encode( $settings, JSON_PRETTY_PRINT );
		$settings_json = str_replace( '"' . $settings[ 'keys' ] . '"', $this->get_option( 'keys' ), $settings_json );

		// It's very difficult to sanitize this, as the keys needed to be added, which contain a raw JSON string
		echo '<script>strattic_search_settings=' . $settings_json . ';</script>';
	}

}
