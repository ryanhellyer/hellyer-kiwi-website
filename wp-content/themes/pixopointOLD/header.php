<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package PixoPoint
 * @since PixoPoint 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header id="site-header" role="banner">
	<hgroup>
		<h1>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<?php echo apply_filters( 'pixopoint_header_name', get_bloginfo( 'name' ) ); ?>
			</a>
		</h1>
		<h2><?php bloginfo( 'description' ); ?></h2>
	</hgroup>

	<?php
	// The primary menu
	if ( has_nav_menu( 'primary' ) ) { ?>	
	<nav role="navigation">
		<h1 class="assistive-text"><?php _e( 'Menu', 'pixopoint' ); ?></h1>
		<div class="assistive-text skip-link"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'pixopoint' ); ?>"><?php _e( 'Skip to content', 'pixopoint' ); ?></a></div>
		<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
	</nav><?php
	} ?>

</header><!-- #masthead .site-header -->

<div id="main" class="site-main">
