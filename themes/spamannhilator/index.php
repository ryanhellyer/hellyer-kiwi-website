<?php

/**
 * The main template file.
 *
 * @package Spam Annhilator theme
 * @since Spam Annhilator theme 1.0
 */

// No post found, so serve 404 page instead
if ( ! have_posts() ) {

	status_header( 404 );
	nocache_headers();

}

get_header();

if ( have_posts() ) {

	// Load main loop
	while ( have_posts() ) {
		the_post();

		echo '<article id="main-content">';
		the_content();
		echo '</article>';

	}

} else {

	echo '<article id="main-content">';
	echo wpautop( "We can't find what you were looking for." );
	echo '</article>';

}

get_footer();
