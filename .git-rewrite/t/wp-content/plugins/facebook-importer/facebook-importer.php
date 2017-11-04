<?php
/*
Plugin Name: Facebook Importer
Plugin URI: http://geek.ryanhellyer.net/products/facebook-importer/
Description: Import Facebook wall posts to WordPress
Author: ryanhellyer
Author URI: http://geek.ryanhellyer.net/
Version: 1.0
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

define( 'FACEBOOKIMPORT_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR

add_action( 'init', 'import_facebook_wall' );
function import_facebook_wall() {
	if ( is_admin() )
		return;
	
	if ( ! isset( $_GET['facebook'] ) )
		return;

	// Grab the wall by itself
	$wall = file_get_contents( FACEBOOKIMPORT_DIR . 'wall.htm' );
	$wall = explode( '</h1>', $wall );
	$wall = explode( '<div class="footer">', $wall[1] );
	$wall = $wall[0];
	
	// Seperate the wall into posts
	$posts = explode( '<p><div class="meta">', $wall );
	$count = 0;
	foreach( $posts as $key => $value ) {
	
		// Ignore first key
		if ( 0 == $key )
			continue;
	
		// Grab a post
		$post = $posts[$key];
		
		$date = explode( '</div>', $post );
		
		$remainder = $date[1];
		$date = $date[0];
		$date = str_replace( ' at ', ' ', $date );
		$date = explode( ', ', $date );
		$date = $date[1];
		$date = explode( ' UTC', $date );
		$date = $date[0];
		$date = strtotime( $date );
		$date = date( 'Y-m-d H:i:s', $date );
	
		$remainder = explode( '<div class="comment">', $remainder );
		
		// Grab the event
		$event = $remainder[0];
		
		// Get the post content
		if ( isset( $remainder[1] ) )
			$content = $remainder[1];
		else
			$content = '';

/*	
*/
		// Create post object
		$my_post = array(
			'post_title'    => strip_tags( $event ),
			'post_content'  => $content,
			'post_status'   => 'publish',
			'post_author'   => get_current_user_id(),
			'post_date'     => $date,
		);
	
		// Insert the post into the database
		wp_insert_post( $my_post );
//echo $count . ': ' . strip_tags( $event ) . "\n" . $content . "\n\n";

		$count++;
	}
	die('done');
}
