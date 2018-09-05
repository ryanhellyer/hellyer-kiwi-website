<?php
/**
 * The Header for our theme.
 *
 * Displays the head and header sections of the theme
 *
 * @package WordPress
 * @subpackage Arctic_Fox
 * @since 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php wp_title(); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header id="branding" role="banner">
	<nav id="nav" role="navigation">
		<div class="wrapper">
			<h3 class="assistive-text"><?php _e( 'Main menu', 'arcticfox' ); ?></h3>
			<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'arcticfox' ); ?>"><?php _e( 'Skip to primary content', 'arcticfox' ); ?></a>
			<a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'arcticfox' ); ?>"><?php _e( 'Skip to secondary content', 'arcticfox' ); ?></a>
			<ul><?php

				/**
				 * Our navigation menu.
				 * Uses arcticfox_menu() to grab appropriate menu info.
				 * Uses anchor links instead of absolute links for (most) pages.
				 * Non-pages and posts page use absolute URL
				 */
				foreach( arcticfox_menu() as $item ) :
					echo "\n			<li id='" . arcticfox_id( $item ) . "' class='menu-item'><a rel='" . arcticfox_id( $item ) . "' href=\"";

					/**
					 * Set unset variables for WP_DEBUG.
					 */
					if ( !isset( $item->type_label ) )
						$item->type_label = '';
					if ( !isset( $item->type ) )
						$item->type = '';

					/**
					 * Display URL for posts page.
					 * Display URL for custom menu items.
					 * Needs absolute URL unlike other pages.
					 */
					if ( arcticfox_id( $item ) == get_option( 'page_for_posts' ) || 'custom' == $item->type )
						echo arcticfox_url( $item );

					/**
					 * Display URL for pages
	    				 * Need anchor links
	    				 */
					elseif ( 'Page' == $item->type_label || arcticfox_id( $item ) )
						echo home_url( '/' ) . '#page-' . arcticfox_id( $item );

					/**
					 * Display URL for pages
					 * Need anchor links
					 */
					else
						echo arcticfox_url( $item );

					/**
					 * Display URL for pages
					 */
					echo '">' . arcticfox_title( $item ) . '</a></li>';

				endforeach;
			?>

			</ul>
		</div>
	</nav><!-- #nav -->
</header><!-- #branding -->
