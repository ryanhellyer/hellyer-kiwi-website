<?php
/**
 * The main template file.
 *
 * @package Psychedelic Society Berlin
 * @since Psychedelic Society Berlin 1.0
 */

get_header();


?>

<section id="archive">
	<div class="container">
		<h1>Events</h1>
		<p>Here you can find all of our events.</p>
			<ul class="gallery gallery-columns-4">
<?php

// Load main loop.
if ( have_posts() ) {

	// Start of the Loop.
	while ( have_posts() ) {
		the_post();
?>
				<li>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
						<?php the_post_thumbnail( 'events-thumb' ); ?>
						<h2><?php the_title(); ?></h2>
						<p><?php the_excerpt(); ?></p>
					</a>
				</li>
			</a>
		</article>
<?php

	}
}

?>
			</ul>
		</div>

	</div>
</section>

<?php

get_footer();
