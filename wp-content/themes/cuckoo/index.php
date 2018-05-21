<?php
/**
 * The main template file.
 *
 * @package Cuckoo Nord
 * @since Cuckoo Nord 1.0
 */
get_header();

// Load main loop
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();
		echo '<article>';

		echo '<h1>';

		if ( is_home() ) {
			echo '<a href="' . esc_url( get_permalink() ) . '">';
		}

		the_title();

		if ( is_home() ) {
			echo '</a>';
		}

		echo '</h1>';

		the_content();

		echo '</article>';

	}

	echo '<div id="posts-navigation">';
	posts_nav_link( ' &nbsp; ', '&laquo; ' . esc_html( 'Previous page', 'cuckoo' ), esc_html__( 'Next page' ) . ' &raquo;' );
	echo '</div>';

}

get_footer();
