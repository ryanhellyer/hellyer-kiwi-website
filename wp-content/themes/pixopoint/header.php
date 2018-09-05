<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package PixoPoint
 * @since PixoPoint 1.1
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

	<div class="hgroup">
		<h1>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<?php
					// Output header text (need fallback to keep WordPress.org them demo happy)
					$header_text = get_option( 'header-text' );
					if ( $header_text ) {
						echo $header_text; // Not escaped, since needs to include HTML
					} else {
						echo 'Hellish<span>Simplicity</span><small>.tld</small>';
					}
				?>
			</a>
		</h1>
		<h2><?php bloginfo( 'description' ); ?></h2>
		<img src="<?php echo get_template_directory_uri(); ?>/images/ryan-cut-small.png" />
	</div><!-- .hgroup -->

	<nav id="nav">
		<div>
			<?php wp_nav_menu(); ?>
		</div>
	</nav><!-- #nav -->

</header><!-- #masthead -->

<div id="main" class="site-main">
