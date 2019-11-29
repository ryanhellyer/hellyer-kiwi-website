<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package PixoPoint
 * @since PixoPoint 1.0
 */

get_header(); ?>

<div id="content-area">
	<div id="site-content" role="main"><?php

if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();
		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header">
				<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'pixopoint' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
			</header><!-- .entry-header -->
		
			<div class="entry-content"><?php

				if ( is_home() || is_single() || is_page() ) {
					the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'pixopoint' ) );
					wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'pixopoint' ), 'after' => '</div>' ) );
				} else {
					the_excerpt();
				}
				?>
			</div><!-- .entry-content --><?php

			// Don't display meta information on static pages
			if ( ! is_page() ) { ?>
			<footer class="entry-meta">
				<?php
					printf( __( 'Posted on <a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><span class="byline"> by <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'pixopoint' ),
					esc_url( get_permalink() ),
					esc_attr( get_the_time() ),
					esc_attr( get_the_date( 'c' ) ),
					esc_html( get_the_date() ),
					esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
					esc_attr( sprintf( __( 'View all posts by %s', 'pixopoint' ), get_the_author() ) ),
					get_the_author()
				);

				// Category listings
				$categories_list = get_the_category_list( __( ', ', 'pixopoint' ) );
				if ( $categories_list ) {
				?>
				<span class="cat-links">
					<?php printf( __( ' in %1$s', 'pixopoint' ), $categories_list ); ?>
				</span><?php
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

				// Comments info.
				if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) { ?>
				<span class="sep"> | </span>
				<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'pixopoint' ), __( '1 Comment', 'pixopoint' ), __( '% Comments', 'pixopoint' ) ); ?></span><?php
				}

				// Edit link
				edit_post_link( __( 'Edit', 'pixopoint' ), '<span class="sep"> | </span><span class="edit-link">', '</span>' );
				?>
			</footer><!-- .entry-meta --><?php
			} ?>

		</article><!-- #post-<?php the_ID(); ?> --><?php
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