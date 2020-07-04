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

		if ( is_search() ) {

			echo '<h3><a href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . esc_html( get_the_title( get_the_ID() ) ) . '</a></h3>';
			the_excerpt();
			echo '<hr />';

		} else {

			the_content();

//			// If comments are open or we have at least one comment, load up the comment template
//			if ( comments_open() || '0' != get_comments_number() ) {
//				comments_template( '', true );
//			}

		}

	}

} else {
	get_template_part( 'template-parts/no-results' );
}

if ( is_attachment() ) {
	do_action( 'undiecar_after_content' );
}

echo '</article>';

get_footer();
