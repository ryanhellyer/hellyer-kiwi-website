<?php
/**
 * The main template file.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */

get_header();

echo '

<main id="main">
	<article id="main-content">

		' . wpautop( "We can't find what you were looking for. Perhaps searching will help." ) . '
		' . get_search_form( false ) . '
		<br /><br />
		<img src="' . esc_url( get_template_directory_uri() . '/images/cars/background4.jpg' ) . '" />

	</article>
</main>';

get_footer();
