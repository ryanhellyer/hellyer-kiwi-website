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
} else {
	echo '
		<p>Sorry, but we could not find any results for "' . get_search_query() . '" :(</p>

		<form method="get" action="' . esc_url( home_url() ) . '">
			<label for="search">Search</label>
			<input type="search" id="search" name="s" placeholder="Search" />
			<input type="submit" placeholder="Search ..." name="submit" value="Search" />
		</form>';
}

get_footer();
