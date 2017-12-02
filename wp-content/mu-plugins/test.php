<?php


/**
 * Setting a new cache time for feeds in WordPress
 */
function prefix_set_feed_cache_time( $seconds ) {
	return 1;
}
add_filter( 'wp_feed_cache_transient_lifetime' , 'prefix_set_feed_cache_time' );
