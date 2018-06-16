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

		$styles = '';
		if ( is_home() ) {
			$styles .= ' style="background:';

			$image_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'cuckoo-archive' );
			if ( isset( $image_src[ 0 ] ) ) {
				$image_url = $image_src[ 0 ];

				$styles .= 'url(' . esc_url( $image_url ) . ')';
				$styles .= ';background-size: cover';
			} else {
				$styles .= '#000';
			}
			$styles .= ';"';

		}

		echo '<article' . $styles . '>';

		echo '<h1>';

		if ( is_home() || is_search() ) {
			echo '<a href="' . esc_url( get_permalink() ) . '">';
		}

		the_title();

		if ( is_home() ) {
			echo '</a>';
		}

		echo '</h1>';

		if ( ! is_home() ) {
			the_content();
		}

		echo '</article>';

	}

	echo '<div id="posts-navigation">';
	posts_nav_link( ' &nbsp; ', '&laquo; ' . esc_html( 'Previous page', 'cuckoo' ), esc_html__( 'Next page' ) . ' &raquo;' );
	echo '</div>';

}

get_footer();
