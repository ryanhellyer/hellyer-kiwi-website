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
		' . do_shortcode( '[gallery columns="2" size="large" link="file" ids="3428,3607,1707,3451" orderby="rand"]' ) . '

	</article>
</main>';

get_footer();
