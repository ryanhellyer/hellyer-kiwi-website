<?php
/**
 * The main template file.
 *
 * @package Psychedelic Society Berlin
 * @since Psychedelic Society Berlin 1.0
 */

get_header();


// Load main loop.
if ( have_posts() ) {

	// Start of the Loop.
	while ( have_posts() ) {
		the_post();
?>
<section id="page">
	<div class="container">
		<h1>
			<?php the_title(); ?>
		</h1>

		<?php the_post_thumbnail( 'events-full-width' ); ?>

		<?php the_content(); ?>
	</div>
</section>
<?php

	}
}

get_footer();
