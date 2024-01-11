<?php
/**
 * @package Fruit Shake
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

		<div class="entry-meta">
			<?php
				printf( __( 'Posted by <span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span> in', 'fruit-shake' ),
					get_author_posts_url( get_the_author_meta( 'ID' ) ),
					esc_attr( sprintf( __( 'View all posts by %s', 'fruit-shake' ), get_the_author() ) ),
					get_the_author()
				 );
			?>

			<span class="cat-links"><?php the_category( ', ' ); ?></span>

			<?php the_tags( '<span class="tag-links">' . __( 'and tagged with', 'fruit-shake' ) . ' </span>', ', ', '' ); ?>

			<a class="entry-date-link" href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'fruit-shake' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><time pubdate="" datetime="<?php the_date( 'c' ); ?>" class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></time></a>
		</div><!-- .entry-meta -->
	</header><!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for search pages ?>
	<div class="entry-summary">
		<?php the_excerpt( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'fruit-shake' ) ); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php if ( post_password_required() ) : ?>
			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'fruit-shake' ) ); ?>

			<?php else : ?>
				<?php
					$pattern = get_shortcode_regex();
					preg_match( "/$pattern/s", get_the_content(), $match );
					$atts    = isset( $match[3] ) ? shortcode_parse_atts( $match[3] ) : array();
					$images  = isset( $atts['ids'] ) ? explode( ',', $atts['ids'] ) : false;

					if ( ! $images ) :
						$images = get_posts( array(
							'post_parent'      => get_the_ID(),
							'fields'           => 'ids',
							'post_type'        => 'attachment',
							'post_mime_type'   => 'image',
							'orderby'          => 'menu_order',
							'order'            => 'ASC',
							'numberposts'      => 999,
							'suppress_filters' => false
						) );
					endif;

					if ( $images ) :
						$total_images = count( $images );
						$image        = array_shift( $images );
				?>

				<figure class="gallery-thumb">
					<a href="<?php the_permalink(); ?>"><?php echo wp_get_attachment_image( $image ); ?></a>
				</figure><!-- .gallery-thumb -->

				<p><em><?php printf( _n( 'This gallery contains <a %1$s>%2$s photo</a>.', 'This gallery contains <a %1$s>%2$s photos</a>.', $total_images, 'fruit-shake' ),
						'href="' . get_permalink() . '" title="' . esc_attr( sprintf( __( 'Permalink to %s', 'fruit-shake' ), the_title_attribute( 'echo=0' ) ) ) . '" rel="bookmark"',
						number_format_i18n( $total_images )
					); ?></em></p>
			<?php endif; ?>
			<?php the_excerpt(); ?>
		<?php endif; ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'fruit-shake' ), 'after' => '</div>' ) ); ?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<footer class="entry-meta">
		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="comments-link fruity"><?php comments_popup_link( __( 'Leave a comment', 'fruit-shake' ), __( '1 Comment', 'fruit-shake' ), __( '% Comments', 'fruit-shake' ) ); ?></span>
		<?php endif; ?>
		<?php edit_post_link( __( '[Edit]', 'fruit-shake' ), '<span class="edit-link fruity">', '</span>' ); ?>
	</footer><!-- #entry-meta -->
</article><!-- #post-## -->
