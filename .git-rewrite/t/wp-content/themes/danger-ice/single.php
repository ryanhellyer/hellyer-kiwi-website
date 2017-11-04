<?php get_header(); ?>

<div id="content">
	<div id="maincontent"><?php

		// Load main loop
		if ( have_posts() ) {
		
			// Start of the Loop
			while ( have_posts() ) {
				the_post();
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
					<h2><?php the_title(); ?></h2>
					<?php the_content(); ?>
				</div><?php
			}
		}
		?>
		<?php get_template_part( 'template-parts/numeric-pagination' ); ?>
	</div>
</div>

<?php get_footer(); ?>