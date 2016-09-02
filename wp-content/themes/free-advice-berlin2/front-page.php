<?php
/**
 * The main template file.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
get_header();


fuck_you_tanzia();


$query = new WP_Query(
	array(
		'post_type'      => 'page',
		'posts_per_page' => 200,
		'order'          => 'ASC',
		'orderby'        => 'title',

		// Improve performance
		'no_found_rows'          => true, // Since we don't need pagination
		'update_post_meta_cache' => false, // Post meta is unrequired here
		'update_post_term_cache' => false, // Terms are not required here
		//'fields' => 'ids';: useful when only the post IDs are needed (less typical).

		// Only show some on front page
		'meta_key'     => '_show',
		'meta_value'   => '1',
		'meta_compare' => '=='
	)
);

if ( $query->have_posts() ) :
	echo '
	<ul id="documents">';

	while ( $query->have_posts() ) :
		$query->the_post();
		?>

		<li>
			<a href="<?php the_permalink(); ?>">
				<strong><?php the_title(); ?></strong>

				<?php
				if ( function_exists( 'the_subheading' ) ) {
					the_subheading( '', '' );
				}
				?>

			</a>
		</li><?php

    endwhile;

	echo '
	</ul>';

endif;

free_advice_berlin_search_form();

get_footer();
