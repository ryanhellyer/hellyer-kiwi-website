<?php
/**
 * The archive template file.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */

get_header();


echo '
	<article id="main-content">';

// Load main loop
if ( have_posts() ) {

	echo '
		<ul>';

	// Start of the Loop
	while ( have_posts() ) {
		the_post();

		echo '
			<article>
				<h2><a href="' . esc_url( get_permalink() ) . '">';

		the_title();

		echo '</a></h2>';
		echo "\n";

		$image_id = get_post_thumbnail_id( get_the_ID() );
		$image = wp_get_attachment_image_src( $image_id, 'src-featured' );
		$image_url = $image[0];
		if ( '' !== $image_url ) {
			echo '<img src="' . esc_url( $image_url ) . '" style="max-height:169px" />';
		}

		echo "\n";
		the_excerpt();
		echo '
				<a href="#" class="button">' . esc_html__( 'Read more', 'undiecar' ) . "</a>\n";
		echo '			</article>';

	}

	echo "\n</ul>";

	get_template_part( 'template-parts/numeric-pagination' );

} else {
	get_template_part( 'template-parts/no-results' );
}

echo '
	</article>';

get_footer();
