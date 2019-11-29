<?php get_header(); ?>

<div id="content">
	<div id="maincontent"><?php

		// Load main loop
		if ( have_posts() ) {
		
			// Start of the Loop
			while ( have_posts() ) {
				the_post();
				?>
				<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php

					// Use the built in thumbnail system, otherwise attempt to display the latest attachment
					if ( has_post_thumbnail() ) {
						?>
						<a href="<?php the_permalink(); ?>" class="post-thumbnail">
							<?php the_post_thumbnail( 'excerpt-thumb' ); ?>
						</a><?php
					} else {
						$args = array(
							'post_type'      => 'attachment',
							'post_mime_type' => 'image',
							'post_parent'    => get_the_ID(),
							'numberposts'    => 1,
						);
						$images = get_posts( $args );
						foreach( $images as $image ) {
							?>
							<a href="<?php the_permalink(); ?>" class="post-thumbnail">
								<?php echo wp_get_attachment_image( $image->ID, 'excerpt-thumb' ); ?>
							</a><?php
						}
					}
					?>
				
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<?php the_excerpt(); ?>
				</div><?php
			}
		}
		?>
		<?php get_template_part( 'template-parts/numeric-pagination' ); ?>
	</div>
</div>

<?php get_footer(); ?>