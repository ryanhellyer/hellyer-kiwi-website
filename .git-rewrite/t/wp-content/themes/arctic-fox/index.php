<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @package WordPress
 * @subpackage Arctic_Fox
 */


/**
 * Load header
 */
get_header();



echo '<div class="wrapper blog-wrapper">';


/**
 * Load posts / loop
 */
if ( have_posts() ) :
while ( have_posts() ) : the_post(); ?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header"><?php

			/**
			 * Display page title.
			 * Add notice specifically for sticky posts.
			 */
			if ( is_sticky() ) : ?>
				<hgroup>
					<?php if ( ! is_singular() ) : ?>
					<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'arcticfox' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
					<?php else : ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>
					<?php endif; ?>
					<h3 class="entry-format"><?php _e( 'Featured', 'arcticfox' ); ?></h3>
				</hgroup>
			<?php
			else : ?>
			<h1 class="entry-title">
				<?php if ( ! is_singular() ) : ?>
				<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'arcticfox' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
				<?php endif; ?>
					<?php the_title(); ?>
				<?php if ( ! is_singular() ) : ?>
				</a>
				<?php endif; ?>
			</h1><?php
			endif;

			/**
			 * Add post meta information.
			 * Only used for the 'post' post-type as this information may not be suitable for custom post-types/
			 */
			if ( 'post' == get_post_type() ) : ?>
			<div class="entry-meta"><?php
			printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s" pubdate>%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'arcticfox' ),
				esc_url( get_permalink() ),
				esc_attr( get_the_time() ),
				esc_attr( get_the_date( 'c' ) ),
				esc_html( get_the_date() ),
				esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
				esc_attr( sprintf( __( 'View all posts by %s', 'arcticfox' ), get_the_author() ) ),
				get_the_author()
			); ?>
			</div><!-- .entry-meta -->
			<?php endif;

			/**
			 * Display comments link.
			 */
			if ( comments_open() && ! post_password_required() ) : ?>
			<div class="comments-link">
				 (<?php comments_popup_link( '<span class="leave-reply">' . __( 'Reply', 'arcticfox' ) . '</span>', _x( '1', 'comments number', 'arcticfox' ), _x( '%', 'comments number', 'arcticfox' ) ); ?> comments)
			</div>
			<?php endif; ?>
		</header><!-- .entry-header -->

		<?php
		/**
		 * Display excerpts for search results.
		 */
		if ( is_search() ) : ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary --><?php

		/**
		 * Display the whole content and pages link for everything but search results.
		 */
		else : ?>
		<div class="entry-content">
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'arcticfox' ) ); ?>
		</div><!-- .entry-content -->
		<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'arcticfox' ) . '</span>', 'after' => '</div>' ) ); ?>
		<?php endif;

		/**
		 * Display information in the post footer.
		 */
		?>
	    	<footer class="entry-meta">
			<?php $show_sep = false; ?>
			<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'arcticfox' ) );
				if ( $categories_list ):
			?>
			<span class="cat-links">
				<?php printf( __( '<span class="%1$s">Posted in</span> %2$s', 'arcticfox' ), 'entry-utility-prep entry-utility-prep-cat-links', $categories_list );
				$show_sep = true; ?>
			</span>
			<?php endif; // End if categories ?>
			<?php
			/* translators: used between list items, there is a space after the comma */
			$tags_list = get_the_tag_list( '', __( ', ', 'arcticfox' ) );
				if ( $tags_list ):
				if ( $show_sep ) : ?>
			<span class="sep"> | </span>
				<?php endif; // End if $show_sep ?>
			<span class="tag-links">
				<?php printf( __( '<span class="%1$s">Tagged</span> %2$s', 'arcticfox' ), 'entry-utility-prep entry-utility-prep-tag-links', $tags_list );
				$show_sep = true; ?>
			</span><?php
			endif; // End if $tags_list
			endif; // End if 'post' == get_post_type()
			if ( comments_open() ) :
			if ( $show_sep ) : ?>
			<span class="sep"> | </span>
			<?php endif; // End if $show_sep ?>
			<span class="comments-link"><?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'arcticfox' ) . '</span>', __( '<b>1</b> Reply', 'arcticfox' ), __( '<b>%</b> Replies', 'arcticfox' ) ); ?></span>
			<?php endif; // End if comments_open()
			edit_post_link( __( 'Edit', 'arcticfox' ), '<span class="edit-link">', '</span>' );

			if ( is_singular() && get_the_author_meta( 'description' ) && ( ! function_exists( 'is_multi_author' ) || is_multi_author() ) ) : // If a user has filled out their description and this is a multi-author blog, show a bio on their entries ?>
			<div id="author-info">
				<div id="author-avatar">
					<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'twentyeleven_author_bio_avatar_size', 68 ) ); ?>
				</div><!-- #author-avatar -->
				<div id="author-description">
    				<h2><?php printf( __( 'About %s', 'twentyeleven' ), get_the_author() ); ?></h2>
					<?php the_author_meta( 'description' ); ?>
					<div id="author-link">
						<a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" rel="author">
							<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'twentyeleven' ), get_the_author() ); ?>
						</a>
					</div><!-- #author-link	-->
				</div><!-- #author-description -->
			</div><!-- #entry-author-info -->
			<?php endif; ?>

		</footer><!-- #entry-meta -->
	</article><!-- #post-<?php the_ID(); ?> --><?php

	comments_template( '', true );

endwhile;

/**
 * Provide a message and handy search box if nothing is found.
 */
else : ?>
	<article id="post-0" class="post no-results not-found">
		<header class="entry-header">
	    		<h1 class="entry-title"><?php _e( 'Nothing Found', 'arcticfox' ); ?></h1>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<p><?php
				_e( 'Apologies, but no results were found for the requested archive.', 'arcticfox' );
				if ( is_search() )
					_e( 'Perhaps try searching for something different.', 'arcticfox' );
				else
					_e( 'Perhaps searching will help find a related post.', 'arcticfox' );
			?></p>
			<?php get_search_form(); ?>
		</div><!-- .entry-content -->
	</article><!-- #post-0 -->
<?php endif; ?>


	<?php arcticfox_pagination(); ?>

	<footer role="contentinfo">
		<span id="to-top">To top &#8593;</span>
		<a href="http://wordpress.org/" title="<?php _e( 'Semantic Personal Publishing Platform', 'arcticfox' ); ?>" rel="generator"><?php printf( __( 'Proudly powered by %s', 'arcticfox' ), 'WordPress' ); ?></a>
		<br />
		Theme by <a href="http://metronet.no/" title="Metronet Norge AS">Metronet Norge AS</a>
	</footer>
</div><!-- .wrapper -->


<?php

/**
 * Load footer
 */
get_footer();

