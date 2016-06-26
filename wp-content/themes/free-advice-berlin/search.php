<?php
/**
 * The main template file.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
get_header();



// Load main loop
if ( have_posts() ) {

	echo '<h1>Search results for: "' . get_search_query() . '"</h1>';

	echo '<ul id="search-results">';

	// Start of the Loop
	while ( have_posts() ) {
		the_post();

		?>
		<li>
			<a href="<?php the_permalink(); ?>">
				<strong><?php the_title(); ?></strong>
				<?php the_excerpt(); ?>
			</a>
		</li><?php
	}
	echo '</ul>';

	echo '<p>Want to search for something different?</p>';
	free_advice_berlin_search_form();

} else {

	echo '
		<p>Sorry, but we could not find any results for "' . get_search_query() . '" :(</p>';

	free_advice_berlin_search_form();
}

get_footer();
