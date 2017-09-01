<?php
/**
 * The archive template file.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */

get_header();

echo '<article id="main-content">';

// Load main loop
if ( have_posts() ) {

	echo '<ul>';

	// Start of the Loop
	while ( have_posts() ) {
		the_post();

		echo '<article>';
		echo '<h2><a href="' . esc_url( get_permalink() ) . '">';
			the_title();
		echo '</a></h2>';
		the_excerpt();
		echo '</article>';

	}

	echo '</ul>';

} else {
	get_template_part( 'template-parts/no-results' );
}

echo '</article>';

get_footer();
