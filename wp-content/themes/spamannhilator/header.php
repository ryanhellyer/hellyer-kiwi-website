<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Spam Annhilator theme
 * @since Spam Annhilator theme 1.0
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
<!--
<div style="
	position: fixed;
	top: 0;
	right: 0;
	width: 100px;
	height: 100px;
	background: url(<?php echo esc_url( get_template_directory_uri() . '/images/beta.png' ); ?>);
"></div>
-->
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'src' ); ?></a>

<div id="cookie-notice" class="hidden"></div>

<header id="site-header" role="banner"><?php

if ( (string) get_the_ID() === get_option( 'page_on_front' ) ) {

	?>

	<h1>Protect Links<?php edit_post_link( '*', ' <sup>', '</sup>' ); ?></h1>
	<p>Easily block spam from Discord, Telegram and other invite links</p>

    <a href="#<?php echo esc_url( home_url( '/login/' ) ); ?>" class="button">Facebook Login (not working)</a>
    <p class="login-instructions">Login with Facebook to create anti-spam invite links</p><?php

} else {
	?>

	<h1><?php echo esc_html( get_the_title( get_the_ID() ) ); ?><?php edit_post_link( '*', ' <sup>', '</sup>' ); ?></h1><?php

}

	?>

</header>

<main id="main">
