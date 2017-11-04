<?php
/**
 * The Header for our theme.
 *
 * Displays the head and header sections of the theme
 *
 * @package WordPress
 * @subpackage Lasse_Super
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
			<h3 class="assistive-text"><?php _e( 'Main menu', 'lassesuper' ); ?></h3>
			<a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'lassesuper' ); ?>"><?php _e( 'Skip to primary content', 'lassesuper' ); ?></a>
			<a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'lassesuper' ); ?>"><?php _e( 'Skip to secondary content', 'lassesuper' ); ?></a>
			<ul><?php

				/**
				 * Our navigation menu.
				 * Uses lassesuper_menu() to grab appropriate menu info.
				 * Uses anchor links instead of absolute links for (most) pages.
				 * Non-pages and posts page use absolute URL
				 */
				foreach( lassesuper_menu() as $item ) :
					echo "\n			<li id='" . lassesuper_id( $item ) . "' class='menu-item'><a rel='" . lassesuper_id( $item ) . "' href=\"";

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
					if ( lassesuper_id( $item ) == get_option( 'page_for_posts' ) || 'custom' == $item->type )
						echo lassesuper_url( $item );

					/**
					 * Display URL for pages
	    				 * Need anchor links
	    				 */
					elseif ( 'Page' == $item->type_label || lassesuper_id( $item ) )
						echo home_url( '/' ) . '#page-' . lassesuper_id( $item );

					/**
					 * Display URL for pages
					 * Need anchor links
					 */
					else
						echo lassesuper_url( $item );

					/**
					 * Display URL for pages
					 */
					echo '">' . lassesuper_title( $item ) . '</a></li>';

				endforeach;
			?>

			</ul>
		</div>
	</nav><!-- #nav --><?php

	/**
	 * Load header image only for front page
	 */
	if ( is_front_page() ) : ?>
	<div id="header-image-wrapper">
	<?php
		// Check to see if the header image has been removed
		$header_image = get_header_image();
		if ( ! empty( $header_image ) ) :
	?>
		<a id="header-image" href="<?php echo home_url( '/' ); ?>">
			<?php
				// The header image
				// Check if this is a post or page, if it has a thumbnail, and if it's a big one
				if ( is_singular() &&
						has_post_thumbnail( $post->ID ) &&
						( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( HEADER_IMAGE_WIDTH, HEADER_IMAGE_WIDTH ) ) ) &&
						$image[1] >= HEADER_IMAGE_WIDTH ) :
					// Houston, we have a new header image!
					echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
				else : ?>
				<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="" />
			<?php endif; // end check for featured image or standard header ?>
		</a>
	<?php endif; // end check for removed header image ?>
		<h1 id="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php bloginfo( 'name' ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		<p id="site-description"><?php bloginfo( 'description' ); ?></p>

	</div><?php endif; ?>
</header><!-- #branding -->
