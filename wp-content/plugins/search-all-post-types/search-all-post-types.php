<?php
/*
Plugin Name: Search all post-types
Plugin URI: https://geek.hellyer.kiwi/plugins/
Description: Search all post-types
Version: 1.0
Author: Ryan Hellyer / Pippin Williamson
Author URI: https://geek.hellyer.kiwi/

Heavily modified from http://www.remicorson.com/include-all-your-wordpress-custom-post-types-in-search/
*/

/**
 * Add All Custom Post Types to search
 *
 * Returns the main $query.
 *
 * @param   string   $query  The default search query
 * @return  string   The new search query
*/

function bpress_add_cpts_to_search( $query ) {

	// Check to verify it's search page
	if ( is_search() ) {

		// Get public post types
		$post_types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'objects' );

		// Get bbPress reply post-type
		$reply_post_types = get_post_types( array( 'name' => 'reply' ), 'objects' );
		$post_types = array_merge( $post_types, $reply_post_types );

		$searchable_types = array();

		// Add available post types
		if ( $post_types ) {
			foreach ( $post_types as $type ) {
				$searchable_types[] = $type->name;
			}
		}

		$query->set( 'post_type', $searchable_types );
	}

	return $query;
}
add_action( 'pre_get_posts', 'bpress_add_cpts_to_search' );