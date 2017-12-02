<?php


function do_not_cache_feeds(&$feed) {
   $feed->enable_cache(false);
 }

// add_action( 'wp_feed_options', 'do_not_cache_feeds' );
