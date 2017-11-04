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

	/*
	 * Class constructor
	 */
	public function __construct() {

		// Bail out now if no search string present
		if ( ! isset( $_GET['s'] ) )
			return;

//											add_action( 'template_redirect', array( $this, 'bla' ) );
//											return;
		add_action( 'posts_request', array( $this, 'cancel_query' ) );
		add_action( 'template_redirect', array( $this, 'modify_query_object' ) );

		add_action( 'init', array( $this, 'set_bing_blob' ), 10 );
		add_action( 'template_redirect', array( $this, 'set_default_blob' ), 5 );

		add_filter( 'get_the_excerpt', array( $this, 'maybe_use_bing_excerpt' ) );
	}

											function bla() {
												global $wp_query;

												// Grab default query results
												$posts = $wp_query->posts;
												foreach( $posts as $key => $value ) {
													$default_query_ids[] = $value->ID;
												}
												$default_query_ids = array_values( $default_query_ids );

												// Change post ID in query
												$new_query_ids[0] = 4434;

												// Add new post ID into $wp_query object
												$query = array(
													'post__in' => $new_query_ids,
												);
												$wp_query = new WP_Query($query);
												$post__in = $wp_query->get( 'post__in' );
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
		$request_uri = 'https://api.datamarket.azure.com/Bing/Search/' . $type . '?$format=json&Query=%27' . esc_attr( $_GET['s'] ) . '%20' . $this->get_option( 'search-string' ) . '%27&$top=50';

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
		$slug = 'bing_search_' .  get_search_query();
		if ( false === ( $response = get_transient( $slug ) ) ) {
			$response = file_get_contents( $request_uri, 0, $context );
			set_transient( $slug, $response, apply_filters( 'bing_search_cache', DAY_IN_SECONDS ) ); // Cache the search result for a day
		}
		$bing_raw = json_decode( $response ); // Decode the response

		// Turn into more coherent array
		$post_ids = array();
		foreach( $bing_raw->d->results as $key => $value ) {
			$id = url_to_postid( $value->Url );
			$bing_blob[] = array(
				'ID'      => $id,
				'bing_ID' => $value->ID,
				'title'   => $value->Title,
				'excerpt' => $value->Description,
			);
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
				true == $this->_is_word_in_string( $value->post_content, get_search_query() ) ||
				true == $this->_is_word_in_string( $value->post_excerpt, get_search_query() ) ||
				true == $this->_is_word_in_string( $value->post_title, get_search_query() )
			) {
				$post_ids[] = array(
					'ID'      => $value->ID,
					'title'   => $value->post_title,
					'content' => $value->post_content,
					'excerpt' => $value->post_excerpt,
				);
			}
		}

		$this->_default_blob = $post_ids;
	}

	/*
	 * Get post IDs corresponding to results from Bing
	 *
	 * @global array $wp_query The search query
	 * @return array The post IDs found
	 */
	public function set_bing_ids() {

		$post_ids = array();
		foreach( $this->bing_blob->d->results as $key => $bing ) {

			// Get WordPress post information
			$id = url_to_postid( $value->Url );
			$post = get_post( $id );

			if (
				true == $this->_is_word_in_string( $post->post_content, get_search_query() ) ||
				true == $this->_is_word_in_string( $post->post_title, get_search_query() )
			) {
				$post_ids[] = array(
					'ID'      => $id,
					'bing_ID' => $bing->ID,
					'title'   => $bing->Title,
					'excerpt' => $bing->Description,
				);
			}
		}

		$this->bing_ids = $post_ids;
	}

	/*
	 * Cancel the default WordPress query
	 *
	 * Adapted from code by Vadym Khukhrianskyi (http://vadimk.com/2010/05/11/disable-wordpress-search-query/)
	 *
	 * @param string $query The search query
	 * @return string The non-existent query
	 */
	public function cancel_query( $query ) {

		if ( ! is_admin() && ! is_feed() && is_search() ) {
																						//$num_bing_posts = count( $this->bing_blob->d->results );

			// Count 
																//			if ( 5 < $num_bing_posts ) {
																								//				$query = false;
																//			}
		}

		return $query;
	}

	/*
	 * Re-prioritizes the query object
	 *
	 * @global object $wp_query The WP Query object
	 */
	public function modify_query_object() {
		global $wp_query;

		// Merge bing results with default WordPress search results
		$merged_blob = array_merge( $this->_bing_blob, $this->_default_blob );
echo '<!-- ';print_r( $merged_blob );echo ' -->';
		// Created merged IDs array
		$merged_ids = array();
		foreach( $merged_blob as $key => $value ) {
			$merged_ids[] = $value['ID'];
		}
		$merged_ids = array_unique( $merged_ids );

		// Rekey array
		$new_merged_ids = array();
		foreach( $merged_ids as $key => $value ) {
			$new_merged_ids[] = $value;
		}
		$merged_ids = $new_merged_ids;

		// Add new post ID into $wp_query object
		$query = array(
			'post__in' => $merged_ids,
		);
		$wp_query = new WP_Query($query);
		$post__in = $wp_query->get( 'post__in' );

		//
		
																						return;
	}

	public function create_search_block( $id, $author, $date, $excerpt, $content, $count ) {
		global $wp_query;

		// Set block
		$single_result = new WP_Post( (object) '' );
		$single_result->ID = $id;
		$single_result->post_author = $author;
		$single_result->post_date = $date;
		$single_result->post_excerpt = $excerpt;
		$single_result->post_content = $content;

		// Add block back into query
		$posts = $wp_query->posts;
		$posts[$count] = $single_result;
		$wp_query->posts = $posts;
	}

	/*
	 * Check if word is in string
	 *
	 * @param string $string The string to be searched
	 * @param string $word   The word to search for
	 */
	private function _is_word_in_string( $string, $word ) {
		if ( stristr( $string, $word ) === false ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Highlight words in a string
	 *
	 * @param string $text
	 * @param array $words
	 * @return string
	 */
	private function _highlight_words( $word, $text ) {
		$word = preg_quote( $word ); // quote the text for regex
		$text = preg_replace( "/\b($word)\b/i", '<strong>\1</strong>', $text ); // highlight the words

		return $text;
	}

}
new Bing_Search();
