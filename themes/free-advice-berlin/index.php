<?php

/**
 * The main template file.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
get_header();


// Load main loop
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();
		echo '<article>';
		echo '<h1>';
		the_title();
		echo '</h1>';
		echo '<p id="updated">';
		echo sprintf( __( 'Last updated: %s', 'free-advice-berlin' ), get_the_modified_time( 'jS \of F Y' ) );
		if ( current_user_can( 'edit_pages' ) ) {
			echo '<a class="alignright" href="' . esc_url( get_edit_post_link() ) . '"><strong>Edit</strong></a>';
		}
		echo '</p>';
		the_content();
		echo '</article>';

		do_action( 'fab_after_content' );

		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() ) {
			comments_template( '', true );
		}

	}
}

get_footer();
