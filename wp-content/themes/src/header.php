<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package SRC
 * @since SRC 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'src' ); ?></a>

<header id="site-header" role="banner">
	<a class="sign-up" href="<?php echo esc_url( get_user_profile_link() ); ?>"><?php esc_html_e( 'Welcome', 'src' ); ?> <span><?php esc_html_e( 'View Profile', 'src' ); ?></span></a>
	<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( get_bloginfo( 'name', 'display' ) ); ?>"><?php esc_html_e( get_bloginfo( 'name', 'display' ) ); ?></a></h1>
	<nav id="main-menu-wrap">
		<ul id="main-menu"><?php

			echo "\n\n";

			// Output header menu
			wp_nav_menu(
				array(
					'theme_location' => 'header',
					'container'      => '',
					'items_wrap'     => '%3$s',
				)
			);

			?>

		</ul>
	</nav>

</header><!-- #site-header -->

<?php
if ( is_super_admin() ) {
if ( is_front_page() ) {
	?>
<section id="featured-news" style="background-image: url(http://dev-hellyer.kiwi/bbpress/wp-content/themes/src-theme/images/featured-image.jpg);">
	<div class="text">
		<h1>Hockenheim Results: Stunning wins for Rosanski and Speedy</h1>
		<a href="#" class="button">Check out the results</a>
	</div>
</section><!-- #featured-news -->

<?php
}
}
?>

<main id="main">
