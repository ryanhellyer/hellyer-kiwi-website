<?php
/**
 * The main template file.
 *
 * @package PixoPoint
 * @since PixoPoint 1.1
 */

get_header(); ?>

<div id="content-area">
	<div id="site-content" role="main"><?php

// If on search page, then display what we searched for
if ( is_search() ) { ?>
		<h1 class="page-title">
			<?php printf( __( 'Search Results for: "%s" ...', 'pixopoint' ), get_search_query() ); ?>
		</h1><?php
}

// Load main loop
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>><?php

			// Display post thumbnails when not on singular posts
			if ( ! is_singular() ) {
				// Use the built in thumbnail system, otherwise attempt to display the latest attachment
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'excerpt-thumb' );
				} elseif ( function_exists( 'get_the_image' ) ) {
					get_the_image( array( 'size' => 'thumbnail' ) );
				} else {
					$author_email = get_the_author_meta( 'email' );
					echo get_avatar( $author_email, PIXOPOINT_THUMB_SIZE );
				}
			}
			?>

			<header class="entry-header">
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'pixopoint' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

				<?php
				// Don't display meta information on static pages
				if ( ! is_page() ) { ?>
				<div class="entry-meta">
					<?php

					// Author URL
					$author_url = get_the_author_meta( 'user_url' );
					if ( '' == $author_url ) {
						$author_url = get_author_posts_url( get_the_author_meta( 'ID' ) );
					}

					printf(
						__( 'Posted on <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><span class="byline"> by <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'pixopoint' ),
						esc_url( get_permalink() ),
						esc_attr( get_the_time() ),
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date() ),
						esc_url( $author_url ),
						esc_attr( sprintf( __( 'View all posts by %s', 'pixopoint' ), get_the_author() ) ),
						get_the_author()
					);

					// Category listings
					$categories_list = get_the_category_list( __( ', ', 'pixopoint' ) );
					if ( $categories_list ) {

						// If there is only one category, then don't bother showing it
						$categories = get_categories();
						if ( 1 < count( $categories ) ) {

						?>
						<span class="cat-links">
							<?php printf( __( ' in %1$s', 'pixopoint' ), $categories_list ); ?>
						</span><?php
						}
					}


					// Tag listings
					$tags_list = get_the_tag_list( '', __( ', ', 'pixopoint' ) );
					if ( $tags_list ) {
						?>
						<span class="sep"> | </span>
						<span class="tags-links">
							<?php printf( __( 'Tagged %1$s', 'pixopoint' ), $tags_list ); ?>
						</span><?php
					}

					// Edit link
					edit_post_link( __( 'Edit', 'pixopoint' ), '<span class="sep"> | </span><span class="edit-link">', '</span>' );
					?>
				</div><!-- .entry-meta --><?php
				}
				?>

			</header><!-- .entry-header -->
		
			<div class="entry-content"><?php

				/*
				 * Display full content for home page and single post pages
				 */
				if ( is_singular() ) {
					the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'pixopoint' ) );
					wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'pixopoint' ), 'after' => '</div>' ) );
				} else {
					the_excerpt();
				}
				?>
			</div><!-- .entry-content -->

		</article><!-- #post-<?php the_ID(); ?> --><?php

		// If comments are open or we have at least one comment, load up the comment template
		if ( comments_open() || '0' != get_comments_number() )
			comments_template( '', true );

	}

	get_template_part( 'template-parts/numeric-pagination' );

}
else {
	get_template_part( 'template-parts/no-results' );
}
?>

	</div><!-- #site-content -->
	<?php get_sidebar(); ?>
</div><!-- #content-area -->

<?php get_footer(); ?>