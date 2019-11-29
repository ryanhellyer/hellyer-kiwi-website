<?php
/**
 * The 404 template file.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
get_header();


echo '
	<h1>404 Error</h1>

	<form method="get" action="' . esc_url( home_url() ) . '">
		<label for="search">' . __( 'Search', 'free-advice-berlin' ) . '</label>
		<input type="search" id="search" name="s" placeholder="' . __( 'Search', 'free-advice-berlin' ) . '" />
		<input type="submit" placeholder="' . __( 'Search ...', 'free-advice-berlin' ) . '" name="submit" value="' . __( 'Search', 'free-advice-berlin' ) . '" />
	</form>
</div>

<img id="page-404" src="' . esc_url( get_stylesheet_directory_uri() . '/images/404.png' ) . '" />

<div class="wrapper">
';


get_footer();
