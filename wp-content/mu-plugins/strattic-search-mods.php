<?php

//return;

// Only load this for main blog.
if (
	'test-strattic.io' !== $_SERVER['HTTP_HOST']
	&&
	'ryan.hellyer.kiwi' !== $_SERVER['HTTP_HOST']
//	&&
//	'geek.hellyer.kiwi' !== $_SERVER['HTTP_HOST']
) {
	return;
}

if ( is_admin() ) {
	return;
}

/**
 * Strip taxonomies out.
 */
add_filter(
	'strattic_search_fields',
	function() {
		return array();
	}
);

/**
 * Disable the search page.
 */
add_filter(
	'parse_query',
	function( $query, $error = true ) {
		if ( is_search() ) {
			$query->is_search = false;
			$query->query_vars['s'] = false;
			$query->query['s'] = false;

//		add_filter( 'the_posts', 'generate_search_page', -10 );

		}
	}
);

/**
 * Generate a "search" page for showing search results, if the search slug isn't set to a real page.
 *
 * @param object $posts Original posts object.
 * @global object $wp The main WordPress object.
 * @global object $wp_query The main WordPress query object.
 * @return object $posts Modified posts object.
function generate_search_page( $posts ) {
	global $wp, $wp_query;

	// Stop interfering with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page).
	if ( ! defined( 'STRATTIC_GENERATE_SEARCH_PAGE' ) ) {
		define( 'STRATTIC_GENERATE_SEARCH_PAGE', true );
	}

	$guid = esc_url( home_url() );

	// Create a fake virtual page.
	$post                 = new \stdClass();
	$post->post_author    = 1;
	$post->post_name      = 'search';
	$post->guid           = $guid;
	$post->post_title     = esc_html__( 'Search results generating &hellip;', 'strattic' );
	$post->post_content   = '';
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

	return $posts;
}
 */

// Only load this for main blog.
if ( 'test-strattic.io' === $_SERVER['HTTP_HOST'] ) {
	add_filter(
		'strattic_search_cache_time',
		function() {
			return 10;
		}
	);
}


if ( is_search() ) {
	$query->is_search = false;
	$query->query_vars['s'] = false;
	$query->query['s'] = false;
	$query->is_404 = false;
}
