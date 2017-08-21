<?php
/**
 * The main template file.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */

get_header();

echo '<article id="main-content">';

// Load main loop
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();

		the_content();

		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || '0' != get_comments_number() ) {
			comments_template( '', true );
		}

	}

} else {
	get_template_part( 'template-parts/no-results' );
}

echo '</article>';

get_footer();
