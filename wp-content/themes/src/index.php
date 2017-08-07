<?php
/**
 * The main template file.
 *
 * @package SRC Theme
 * @since SRC Theme 1.0
 */

get_header();

echo '<div id="bbpress-wrapper">';

if ( ! is_page() ) {
	bbp_breadcrumb();
} else {
	?>
	<div class="bbp-breadcrumb">
		<p><a href="<?php echo esc_url( home_url() ); ?>" class="bbp-breadcrumb-home">Home</a> 
		 <span class="bbp-breadcrumb-sep">&rsaquo;</span> 
		 <span class="bbp-breadcrumb-current"><?php the_title(); ?></span> 
		</p>
	</div><?php

}


// Load main loop
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();


		if ( ! bbp_is_single_user() ) {
			?>

			<header class="entry-header">
				<h1>
					<?php

					// Don't display links on singular post titles
					if ( is_singular() ) {
						the_title();
						edit_post_link( 'edit', ' <small>(', ')</small>' );
					} else {
						?><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'hellish-simplicity' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a><?php
						edit_post_link( 'edit', ' <small>(', ')</small>' );
					}

					?>
				</h1>
			</header><!-- .entry-header --><?php
		}

		the_content();
	}

	do_action( 'src_after_content' );

	get_template_part( 'template-parts/numeric-pagination' );

}
else {
	get_template_part( 'template-parts/no-results' );
}


if (
	is_bbpress() // only display on forum pages
	&&
	! bbp_is_single_user() // But not member profiles
) {
?>

	<div id="sidebar"><?php dynamic_sidebar( 'sidebar' ); ?></div><!-- #sidebar -->

<?php } ?>

</div>

<?php get_footer();