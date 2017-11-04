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

	/*
	 * Class constructor
	 */
	public function __construct() {

		// Bail out now if no search string present
		if ( ! isset( $_GET['s'] ) )
			return;

											add_action( 'template_redirect', array( $this, 'bla' ) );
											return;
		add_action( 'posts_request', array( $this, 'cancel_query' ) );
		add_action( 'template_redirect', array( $this, 'modify_query_object' ) );
		add_action( 'init', array( $this, 'set_bing_blob' ) );
		add_filter( 'get_the_excerpt', array( $this, 'use_bing_excerpt' ) );
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
	public function use_bing_excerpt( $excerpt ) {
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
		global $bing_blob;

		$types = array( 'Web', 'Image', 'Spell', 'RelatedSearch' );

		if ( isset( $_GET['type'] ) ) {
			if ( in_array( $_GET['type'], $types ) ) {
				$type = $_GET['type'];
			}
		} else {
			$type = 'Web';
		}
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
		$response = get_transient( $slug );
		if ( empty( $transient ) ) {
			$response = file_get_contents( $request_uri, 0, $context );
			set_transient( $slug, $response, apply_filters( 'bing_search_cache', DAY_IN_SECONDS ) ); // Cache the search result for a day
		}

		// Decode the response.
		$bing_blob = json_decode( $response );
																			echo '<!--
																			BING BLOB
																			';
																			echo $request_uri;
																			echo "\n\n\n";
																			print_r( $bing_blob );
																			echo '
																			
																			
																			-->';

		foreach ( $bing_blob->d->results as $key => $value ) {
			$url = esc_url( $value->Url );
			$id = url_to_postid( $url );
			$value->ID = $id;
			$bing_blob->d->results[$key] = $value;
		}
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
			$query = false;
		}

		return $query;
	}

	/*
	 * Re-prioritizes the query object
	 *
	 * @global object $wp_query The WP Query object
	 * @global array $bing_blob The blob of data from Bing
	 */
	public function modify_query_object() {
		global $wp_query, $bing_blob;

		// Parse each result according to its metadata type.
		$count = 0;

		$post_count = 0;

		// Cater for odd situation where page number 1 is 0 and page number 2 is 2
		$page_number = get_query_var( 'paged' );
		if ( 0 == $page_number )
			$page_number = 1;
		$page_number = $page_number - 1;

		// Process $wp_query object based on Bing blog data
		foreach ( $bing_blob->d->results as $value ) {
			$url = esc_url( $value->Url );
			$id = (int) $value->ID;

			if ( isset( $_GET['type'] ) ) {
				$excerpt = $content = '<img src="' . $value->MediaUrl . '" alt="" />';
				$post_content = '';
				$post_title = '';
			} else {
				$bing_description = esc_html( $value->Description );
				$excerpt = $content = $this->_highlight_words( get_search_query(), $bing_description );
				$post = get_post( $id );
				$post_content = $post->post_content;
				$post_title = $post->post_title;
			}

			$author = 1;
			$date = '2012-08-22 10:12:43';
			echo '<!-- ' . $post->post_title . ' -->';
			if (
				true == $this->_is_word_in_string( $post_content, get_search_query() ) ||
				true == $this->_is_word_in_string( $post_title, get_search_query() ) ||
				isset( $_GET['type'] )
			) {
				if (
					( $this->get_option( 'posts-per-page' ) * $page_number ) < ( $count + 1 ) &&
					( $this->get_option( 'posts-per-page' ) * ( $page_number + 1 ) ) > $count
				) {
					$this->create_search_block( $id, $author, $date, $excerpt, $content, $post_count );
					$post_count++;
				}
				$count++;
			}
		}
		$max_num_pages = ceil( $count / $this->get_option( 'posts-per-page' ) );
//		$max_num_pages = 1;
		$wp_query->post_count = $post_count;
//		$wp_query->post_count = $count;
		$wp_query->found_posts = $count;
		$wp_query->max_num_pages = $max_num_pages;
		$wp_query->is_search = 1;
		$wp_query->is_paged = 1;
		$wp_query->is_404 = false;
																			echo '<!--

																			WP QUERY
																			';
																			print_r( $wp_query );
																			echo '
																			
																			
																			-->';
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
