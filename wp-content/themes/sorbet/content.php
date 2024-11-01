<?php
/**
 * @package Sorbet
 */

$format = get_post_format();
$formats = get_theme_support( 'post-formats' );
?>

<?php if ( '' != get_the_post_thumbnail() ) : ?>
	<figure class="entry-thumbnail">
		<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_post_thumbnail( 'index-thumb' ); ?></a>
	</figure>
<?php endif; ?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( $format && in_array( $format, $formats[0] ) ): ?>
		<a class="entry-format" href="<?php echo esc_url( get_post_format_link( $format ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'sorbet' ), get_post_format_string( $format ) ) ); ?>"><span class="screen-reader-text"><?php echo get_post_format_string( $format ); ?></span></a>
	<?php endif; ?>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
	</header><!-- .entry-header -->

	<?php if ( is_search() ) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
	<div class="entry-content">
		<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'sorbet' ) ); ?>
		<?php wp_link_pages( array( 'before' => '<div class="page-links">', 'after' => '</div>', 'link_before' => '<span class="active-link">', 'link_after' => '</span>' ) ); ?>
	</div><!-- .entry-content -->
	<?php endif; ?>

	<footer class="entry-meta">
		<?php sorbet_posted_on(); ?>
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>

			<?php
				/* translators: used between list items, there is a space after the comma */
				the_tags( '<span class="tags-links">', __( ', ', 'sorbet' ), '</span>' );
			?>
		<?php endif; // End if 'post' == get_post_type() ?>

		<?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
		<span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'sorbet' ), __( '1 Comment', 'sorbet' ), __( '% Comments', 'sorbet' ) ); ?></span>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'sorbet' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-meta -->
</article><!-- #post-## -->
