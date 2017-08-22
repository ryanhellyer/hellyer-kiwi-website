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

	<nav><?php /*
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

		</ul>*/?>
	</nav>

	<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( get_bloginfo( 'name', 'display' ) ); ?>"><?php esc_html_e( get_bloginfo( 'name', 'display' ) ); ?></a></h1>

</header><!-- #site-header -->

<?php

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

if ( defined( 'SRC_MEMBERS_TEMPLATE' ) ) {
	global $display_name;
	$title = $display_name;
	$content = '';
	$image_url = get_template_directory_uri() . '/images/cars/404-page.jpg';
} else if ( is_404() ) {
	$title = '404 error';
	$content = '';
	$image_url = get_template_directory_uri() . '/images/cars/404-page.jpg';
} else if ( is_single() || is_page() ) {

	$title = get_the_title( get_the_ID() );
	$content = '';
	$image_url = get_the_post_thumbnail_url( get_the_ID(), 'src-featured' );

	// If no image URL, then grab the one from the featured image on front page
	if ( false === $image_url ) {

		$featured_item = new WP_Query( $args );
		if ( $featured_item->have_posts() ) {
			while ( $featured_item->have_posts() ) {
				$featured_item->the_post();

				$image_url = get_the_post_thumbnail_url( get_the_ID(), 'src-featured' );
			}
		}
		wp_reset_query();

	}

} else {

	$featured_item = new WP_Query( $args );
	if ( $featured_item->have_posts() ) {
		while ( $featured_item->have_posts() ) {
			$featured_item->the_post();

			$title = get_the_title();
			$content = get_the_content();
			$image_url = get_the_post_thumbnail_url();
		}
	}

}
wp_reset_query();

$image_url = apply_filters( 'src_featured_image_url', $image_url );
$title     = apply_filters( 'src_featured_title', $title );

?>

<section id="featured-news" style="background-image: url(<?php echo esc_url( $image_url ); ?>)">
	<div class="text">
		<h1><?php echo esc_html( $title ); ?></h1>
		<?php echo do_shortcode( $content ); /* shouldn't be escaped */ ?>
	</div>
</section><!-- #featured-news -->
<main id="main">
