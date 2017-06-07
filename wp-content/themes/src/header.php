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
	<a class="sign-up" href="<?php bbp_user_profile_url( bbp_get_current_user_id() ); ?>"><?php esc_html_e( 'Welcome', 'src' ); ?> <span><?php esc_html_e( 'View Profile', 'src' ); ?></span></a>
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

if ( is_front_page() ) {


	if ( is_user_logged_in() ) {

		$args = array(
			'post_type'              => 'topic',
			'meta_key'               => '_thumbnail_id',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,  // useful when pagination is not needed.
			'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids'
		);

	} else {

		$args = array(
			'post_type'              => 'any',
			'post_status'            => 'private',
			'posts_per_page'         => 1,
			'p'                      => get_option( 'src_featured_page' ),
			'no_found_rows'          => true,  // useful when pagination is not needed.
			'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids',
		) ;

	}

	$featured_item = new WP_Query( $args );
	if ( $featured_item->have_posts() ) {
		while ( $featured_item->have_posts() ) {
			$featured_item->the_post();

			?>

<section id="featured-news" style="background-image: url(<?php echo esc_url( get_the_post_thumbnail_url() ); ?>">
	<div class="text">
		<h1><?php the_title(); ?></h1>
		<a href="<?php the_permalink(); ?>" class="button"><?php esc_html_e( 'Read More', 'src' ); ?></a>
	</div>
</section><!-- #featured-news --><?php

		}
	}

}
?>

<main id="main">
