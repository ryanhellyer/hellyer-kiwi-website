<?php

/**
 * Bing Search results
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Bing_Search {

	private $_bing_blob;
	private $_default_blob;
	private $_search_string;

	/*
	 * Class constructor
	 */
	public function __construct() {

		// Bail out now if no search string present
		if ( ! isset( $_GET['s'] ) )
			return;

		$this->_search_string = strtolower( esc_attr( $_GET['s'] ) );

		add_action( 'wp_head', array( $this, 'modify_query_object' ) );

		add_action( 'wp_head', array( $this, 'set_default_blob' ), 5 );
		add_action( 'init', array( $this, 'set_bing_blob' ), 10 );
		add_filter( 'pre_option_posts_per_page', array( $this, 'number_search_results' ) );

		add_filter( 'get_the_excerpt', array( $this, 'maybe_use_bing_excerpt' ) );
	}

	function number_search_results() {
		return 100;
	}

	/**
	 * Get option from DB
	 * 
	 * @param string $option
	 * @return string
	 */
	public function get_option( $option ) {
		$options = get_option( 'bing-search' );

		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}
		else {
			return false;
		}
	}

	/*
	 * Replace WordPress excerpt with Bing excerpt
	 *
	 * @global object $wp_query The main WP Query object
	 * @param string $excerpt The WordPress excerpt
	 * @return string The Bing excerpt
	 */
	public function maybe_use_bing_excerpt( $excerpt ) {
		global $wp_query;
		$posts = $wp_query->posts;
		foreach( $posts as $key => $post ) {
			if ( $post->ID == get_the_ID() ) {
				return $post->post_excerpt;
			}
		}
	}

	/*
	 * Need to do this early enough but not too late, so that url_to_postid() works correctly
	 */
	public function set_bing_blob() {

		// Set the type of search
		$types = array( 'Web', 'Image', 'Spell', 'RelatedSearch' );
		if ( isset( $_GET['type'] ) ) {
			if ( in_array( $_GET['type'], $types ) ) {
				$type = $_GET['type'];
			}
		} else {
			$type = 'Web';
		}

		// Build the request URI
		$search_string = str_replace( ' ', '%20', $this->get_option( 'search-string' ) ); // Replacing spaces
		$request_uri = 'https://api.datamarket.azure.com/Bing/Search/' . $type . '?$format=json&Query=%27' . $this->_search_string . '%20' . $search_string . '%27&$top=50';

		// Encode the credentials and create the stream context
		$auth = base64_encode( $this->get_option( 'api-key' ) . ':' . $this->get_option( 'api-key' ) );
		$data = array(
			'http' => array(
				'request_fulluri' => true,
				'ignore_errors'   => true,
				'header'          => "Authorization: Basic $auth"
			)
		);
		$context = stream_context_create( $data );

		// Get the response from Bing.
		$slug = 'bing2_' .  $this->_search_string;
		if ( false === ( $response = get_transient( $slug ) ) ) {
			$response = file_get_contents( $request_uri, 0, $context );
			set_transient( $slug, $response, apply_filters( 'bing_search_cache', DAY_IN_SECONDS ) ); // Cache the search result for a day
		}
		$bing_raw = json_decode( $response ); // Decode the response

		// Turn into more coherent array
		$post_ids = array();
		$bing_blob = array();
		if ( isset( $bing_raw->d->results ) ) {
			foreach( $bing_raw->d->results as $key => $bing ) {
				$id = url_to_postid( $bing->Url ); // Convert from URL to WordPress post ID
				if ( 0 == $id )
					continue; // Pages without IDs are not posts, therefore ignore them
				$bing_blob[] = array(
					'ID'      => $id,
					'bing_ID' => $bing->ID,
					'title'   => $post->post_title,
					'excerpt' => $bing->Description,
				);
			}
		}

		// Stick the blob into the object
		$this->_bing_blob = $bing_blob;
	}

	/*
	 * Get $wp_query post IDs
	 *
	 * @global array $wp_query The search query
	 * @return array The post IDs found
	 */
	public function set_default_blob() {
		global $wp_query;

		$posts = $wp_query->posts;
		$post_ids = array();
		foreach( $posts as $key => $value ) {

			if (
				true == $this->_is_word_in_string( $value->post_content, $this->_search_string ) ||
				true == $this->_is_word_in_string( $value->post_title, $this->_search_string )
			) {
				$post_ids[] = array(
					'ID'      => $value->ID,
					'author'  => $value->post_author,
					'date'    => $value->post_date,
					'title'   => $value->post_title,
					'content' => $value->post_content,
					'excerpt' => $value->post_excerpt,
				);
			}
		}

		$this->_default_blob = $post_ids;
	}

	/*
	 * Re-prioritizes the query object
	 *
	 * @global object $wp_query The WP Query object
	 */
	public function modify_query_object() {
		global $wp_query;

		// Now that the default search is ready, we can check the Bing results for relevancy/accuracy
		foreach( $this->_bing_blob as $bing_key => $bing_value ) {
			$id = $bing_value['ID'];
			foreach( $this->_default_blob as $key => $value ) {
				if ( $value['ID'] == $bing_value['ID'] ) {
					$found = true;
					if (
						true == $this->_is_word_in_string( $value['content'], $this->_search_string ) ||
						true == $this->_is_word_in_string( $value['title'], $this->_search_string )
					) {
						$bing_value['content'] = $value['content'];
						if ( '' != $excerpt['excerpt'] ) {
							$bing_value['excerpt'] = $value['excerpt'];
						} else {
							$bing_value['excerpt'] = $value['content'];
						}
						$bing_value['title'] = $excerpt['title'];
						$this->_bing_blob[$bing_key] = $bing_value;
					} else {
						unset( $this->_bing_blob[$bing_key] );
					}
				}
			}

			// If not found in default search, then grab the post manually
			if ( ! isset( $found ) ) {
				$post = get_post( $bing_value['ID'] );
				if (
					true == $this->_is_word_in_string( $post->post_content, $this->_search_string ) ||
					true == $this->_is_word_in_string( $post->post_title, $this->_search_string )
				) {
					$bing_value['content'] = $post->post_content;
					if ( '' != $post->post_excerpt ) {
						$bing_value['excerpt'] = $post->post_excerpt;
					} else {
						$bing_value['excerpt'] = $this->create_excerpt( $post->post_content );
					}
					$bing_value['title'] = $post->post_title;
					$this->_bing_blob[$bing_key] = $bing_value;
				} else {
					unset( $this->_bing_blob[$bing_key] );
				}
			}

			unset( $found );

		}

		// Merge bing results with default WordPress search results
		$merged_blob = array_merge( $this->_bing_blob, $this->_default_blob);

		// Remove duplicates
		$used_ids = array();
		foreach( $merged_blob as $key => $value ) {
			if ( in_array( $value['ID'], $used_ids ) ) {
				unset( $merged_blob[$key] );
			}
			$used_ids[] = $value['ID'];
		}

		// Cater for odd situation where page number 1 is 0 and page number 2 is 2
		$page_number = get_query_var( 'paged' );
		if ( 0 == $page_number )
			$page_number = 1;
		$page_number = $page_number - 1;

		// Modify $wp_query
		$count = 0;
		$post_count = 0;

		$wp_query->posts = array();

		foreach( $merged_blob as $key => $value ) {
			$this->create_search_block( $value['ID'], $value['author'], $value['date'], $value['excerpt'], $value['content'], $post_count );
			$post_count++;
		}

		$wp_query->post_count = $post_count;
		$wp_query->found_posts = $post_count;
		$wp_query->query_vars['posts_per_page'] = $post_count;

	}

	/*
	 * Add new block to $wp_query
	 *
	 * @param int $id           The WordPress post ID
	 * @param int $author       The author ID
	 * @param string $date      The date the post was published
	 * @param string $excerpt   The post excerpt
	 * @param string $content   The post content
	 * @count string $array_key The array key to use
	 */
	public function create_search_block( $id, $author, $date, $excerpt, $content, $array_key ) {
		global $wp_query;

		// Strip unwanted stuff out
		$content = $this->create_excerpt( $content );
		
		$excerpt = strip_tags( $excerpt );
		$content = strip_shortcodes( $content );
		$excerpt = strip_shortcodes( $excerpt );

		// Highlight text
		$content = $this->_highlight_words( $this->_search_string, $content );
		$excerpt = $this->_highlight_words( $this->_search_string, $excerpt );

		// Set block
		$single_result = new WP_Post( (object) '' );
		$single_result->ID = $id;
		$single_result->post_author = $author;
		$single_result->post_date = $date;
		if ( '' != $excerpt ) {
			$single_result->post_excerpt = $excerpt;
		} else {
			$single_result->post_excerpt = $content;
		}
		$single_result->post_content = $content;

		// Add block back into query
		$posts = $wp_query->posts;
		$posts[$array_key] = $single_result;
		$wp_query->posts = $posts;
	}

	/*
	 * Create excerpt from the content
	 */
	public function create_excerpt( $text ) {

		$raw_excerpt = $text;
		$text = strip_shortcodes( $text );

		$text = apply_filters( 'the_content', $text);
		$text = str_replace( ']]>', ']]&gt;', $text );
		$text = strip_tags( $text );
		$excerpt_length = apply_filters( 'excerpt_length', 55 );
		$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );
		$words = preg_split( "/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
		if ( count( $words ) > $excerpt_length ) {
				array_pop( $words );
				$text = implode( ' ', $words );
				$text = $text . $excerpt_more;
		} else {
				$text = implode( ' ', $words );
		}

		return apply_filters( 'wp_trim_excerpt', $text, $raw_excerpt );
	}

	/*
	 * Check if word is in string
	 *
	 * @param string $string The string to be searched
	 * @param string $word   The word to search for
	 * @return boolean True if word is in text, false if it is not
	 */
	private function _is_word_in_string( $text, $word ) {

		$text = preg_replace( '/[^a-z\s]/', '', strtolower( $text ) );
		$text = preg_split( '/\s+/', $text, NULL, PREG_SPLIT_NO_EMPTY );
		$text = array_flip( $text );

		$word_possibilities = array(
			$word . 'ing',
			$word . 'ed',
			$word . 's',
			$word . 'er',
			$word . 'y',
			$word . 'erer',
			$word . 'ly',
			$word . 'es',
			$word . 'tur',
			$word,
		);
		$found = false;
		foreach( $word_possibilities as $key => $word_possibility ) {
			if ( isset( $text[$word_possibility] ) )
				$found = true;
		}

		return $found;
	}

	/**
	 * Highlight words in a string
	 * Uses em tag since that's what Google uses
	 *
	 * @param string $text
	 * @param array $words
	 * @return string
	 */
	private function _highlight_words( $word, $text ) {

		$word_possibilities = array(
			$word . 'ing',
			$word . 'ed',
			$word . 's',
			$word . 'er',
			$word . 'y',
			$word . 'erer',
			$word . 'ly',
			$word . 'es',
			$word . 'tur',
			$word,
		);
		foreach( $word_possibilities as $key => $word_possibility ) {
			$word_possibility = preg_quote( $word_possibility ); // quote the text for regex
			$text = preg_replace( "/\b($word_possibility)\b/i", '<strong class="search-term">\1</strong>', $text ); // highlight the words
		}

		return $text;
	}

}
new Bing_Search();
