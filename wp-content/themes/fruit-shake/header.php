<?php
/**
 * @package Fruit Shake
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php get_template_directory_uri() ; ?>/inc/js/html5.js" type="text/javascript"></script>
<![endif]-->

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
<?php do_action( 'before' ); ?>
	<header id="branding" role="banner">
			<div class="site-branding">
				<h1 id="site-title"><span><a href="<?php echo home_url( '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></span></h1>
				<h2 id="site-description"><?php bloginfo( 'description' ); ?></h2>
			</div>

			<div id="site-image">
				<?php
					// Let's check and see if we have a custom header image
					// If we don't, we'll show something that matches the fruity color scheme
					$header_image = get_header_image();

					if ( ! empty( $header_image ) ) :
				?>
				<img src="<?php echo esc_url( $header_image ); ?>" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="" />
				<?php endif; ?>

			</div><!-- #site-image -->

			<?php get_search_form(); ?>

			<?php if ( has_nav_menu( 'primary' ) ) : ?>
			<nav id="access" role="navigation">
				<h1 class="section-heading"><?php _e( 'Main menu', 'fruit-shake' ); ?></h1>
				<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'fruit-shake' ); ?>"><?php _e( 'Skip to content', 'fruit-shake' ); ?></a></div>

				<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
			</nav><!-- #access -->
			<?php endif; ?>
	</header><!-- #branding -->


	<div id="main" class="site-main">