<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Psychedelic Society Berlin
 * @since Psychedelic Society Berlin 1.0
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<?php wp_head(); ?>
</head>
<body<?php
	// Display body classes (doesn't use body_class() function due to that adding an unnecessary space character).
	if ( ! empty( get_body_class() ) ) {
		echo ' class="' . esc_attr( implode( ' ', get_body_class() ) ) . '"';
	}
?>>

<nav id="header-menu">
	<ul><?php

		$menu_items = wp_get_nav_menu_items( 'Main Menu' );
		foreach ( $menu_items as $key => $menu_item ) {
			if ( isset( $menu_item->title ) ) {
				$title = $menu_item->title;
			} else {
				$title = $menu_item->post_title;
			}

			echo "\n\t\t";
			echo '<li><a href="' . esc_url( $menu_item->url ) . '" title="' . esc_attr( $menu_item->post_title ) . '">' . esc_html( $title ) . '</a></li>';
		}

	?>

	</ul>
</nav>

<header id="header">

	<div class="container">
		<h1>
			<a href="<?php echo esc_url( home_url() ); ?>" title="<?php bloginfo( 'title' ); ?>">
				<?php bloginfo( 'title' ); ?>
			</a>
		</h1><?php

		// If on front page, then show content.
		if ( is_front_page() && have_posts() ) {
			while ( have_posts() ) {
				the_post();

				the_content();
			}
		}

		?>

	</div><?php

	// If on front page, then show background video.
	if ( is_front_page() ) {
		?>

	<video id="header-video" loop autoPlay muted>
		<source src="<?php echo esc_url( get_stylesheet_directory_uri() ); ?>/videos/720.mp4" type="video/mp4">
	</video><?php
	}
	?>

</header>
