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

	<nav id="main-menu-wrapper">
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

			// Add login/logout button
			if ( is_user_logged_in() ) {

				$current_user = wp_get_current_user();
				$username = $current_user->data->user_login;
				if ( 'ryan' === $username ) {
					$username = 'ryan-hellyer';
				}
				echo '<li><a href="' . esc_url( home_url() . '/member/' . $username . '/' ) . '">View profile</a></li>';

				echo '<li><a href="' . esc_url( wp_logout_url( home_url() ) ) . '">Log out</a></li>';
			} else {
				echo '<li><a href="' . esc_url( home_url() . '/login/' ) . '">Log in</a></li>';
				echo '<li><a href="' . esc_url( home_url() . '/register/' ) . '">Register</a></li>';
			}


			?>

		</ul>
	</nav>

	<h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_attr_e( get_bloginfo( 'name', 'display' ) ); ?>"><?php 
		$site_title = get_bloginfo( 'name', 'display' );
		$site_title = explode( ' ', $site_title );
		echo $site_title[0];
		if ( isset( $site_title[1] ) ) {
			echo '<span> ' . $site_title[1] . '</span>';
		}
	?></a></h1>

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

if ( is_search() ) {
	$title = sprintf( esc_html__( 'Search Results for: "%s"', 'undiecar' ), get_search_query() );
	$content = '';
	$image_url = get_template_directory_uri() . '/images/cars/richard-browell.jpg';
} else if ( defined( 'SRC_MEMBERS_TEMPLATE' ) ) {
	global $display_name;
	$title = $display_name;
	$content = '';
	$image_url = get_template_directory_uri() . '/images/cars/radicals-summit.jpg';
} else if ( is_404() ) {
	$title = '404 error';
	$content = '';
	$image_url = get_template_directory_uri() . '/images/cars/trucks-bathurst.jpg';
} else if ( is_archive() || is_home() ) {

	$title = get_the_title( get_option( 'page_for_posts' ) );
	$content = '';
	$image_url = get_the_post_thumbnail_url( get_option( 'page_for_posts' ), 'src-featured' );

} else if (
	( is_single() || is_page() )
	&&
	! is_front_page()
) {

	$title = get_the_post_type() . '-'.get_the_ID() . get_the_title( get_the_ID() );
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
$style = '';
if ( ! is_attachment() ) {
	$style = ' style="background-image: url(' . esc_url( $image_url ) . ')"';
}

?>

<section id="featured-news"<?php echo $style; ?>>
	<div class="text">
		<h1><?php
			echo esc_html( $title );
			edit_post_link( '*', '<sup>', '</sup>' );
		?></h1>
		<?php echo do_shortcode( $content ); /* shouldn't be escaped */ ?>
	</div>
</section><!-- #featured-news -->
<main id="main">
