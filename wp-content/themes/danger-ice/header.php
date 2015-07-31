<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Hellish Simplicity
 * @since Hellish Simplicity 1.1
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<div id="wrapper">

	<div id="header">
		<h1 id="title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>

		<ul class="sf-menu" id="nav">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'sort_column' => 'menu_order', 'container_class' => 'wrapper menu-header' ) ); ?>
		</ul>
	</div>

	<div id="featured-image-wrapper">
	</div>

	<div id="content-wrapper">

		<div id="featured-image">
			<div>
				<img class="header-image" src="<?php echo get_template_directory_uri(); ?>/images/diha-header1.jpg" />
			</div>
		</div>
