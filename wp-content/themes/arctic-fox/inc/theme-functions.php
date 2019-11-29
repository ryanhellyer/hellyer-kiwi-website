<?php
/**
 * Template called theme functions
 * Any direct calls within template files are made to regular functions instead of to methods
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */


/**
 * Grabs the ID.
 * Uses different code depending on whether using wp_nav_menu or defaulting to static pages.
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function arcticfox_id( $item ) {
	if ( isset( $item->object_id ) )
		return $item->object_id;
	else
		return $item->ID;
}

/**
 * Grabs the menu.
 * If no menu is found, then defaults back to static pages.
 *
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function arcticfox_menu() {// Uses wp_get_nav_menu_items() instead of wp_nav_menu().

	// Grab the menu location
	$locations = get_nav_menu_locations();
	if ( $locations ) {
		foreach( $locations as $loc_name=>$loc_id ) {
			if ( 'primary' == $loc_name )
				$location_id = $loc_id;
		}
	}
	$menu = '';
	if ( isset( $location_id ) ) {
		$location = wp_get_nav_menu_object( $location_id );
		$location = $location->name;
		$menu = wp_get_nav_menu_items( $location );
	}

	// If no menu set, then use static pages
	if ( !$menu ) {
		$pages_args = array(
			'number'         => 6,
			'hierarchical'   => 0,
		);
		$menu = get_pages( $pages_args );
	}

	return $menu;
}

/*
 * Page dimensions
 * Sets the width and left/top positioning for content blocks in pages
 * 
 * @since 1.0
 * @author Ryan Hellyer <ryan@metronet.no>
 */
function arcticfox_dimensions( $id, $dimensions ) {

	$style ='';
	foreach( $dimensions as $dimension => $css ) {

		// Add individual dimension
		$distance = get_post_meta( $id, $dimension, true );

		// If measurement exists as custom field, then add it
		if ( $distance ) {
			$style .= $css . ':' . (int) $distance;
			// Add units on
			if ( 'top' == $dimension )
				$style .= 'px';
			else
				$style .= '%';

			// Close style
			$style .= ';';
		}
	}

	if ( isset( $style ) )
		$style = 'style="' . $style . '" ';
	else
		$style = '';

	if ( isset( $width ) || isset( $top ) || isset( $left ) )
		$style .= '"';

	return $style;
}

/**
 * Grabs the URL.
 * Uses different code depending on whether using wp_nav_menu or defaulting to static pages.
 *
 * @since 1.0
 */
function arcticfox_url( $item ) {
	if ( isset( $item->url ) )
		return $item->url;
	else
		return get_permalink( $item->ID );
}

/**
 * Grabs the Title.
 * Uses different code depending on whether using wp_nav_menu or defaulting to static pages.
 *
 * @since 1.0
 */
function arcticfox_title( $item ) {
	if ( isset( $item->title ) )
		return $item->title;
	else
		return $item->post_title;
}

/**
 * Pagination
 */
function arcticfox_pagination( $nav_id ) {
	global $wp_query, $post;

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {
		$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
		$next = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = 'site-navigation paging-navigation';
	if ( is_single() )
		$nav_class = 'site-navigation post-navigation';

	?>
	<nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">
		<h1 class="assistive-text"><?php _e( 'Post navigation', 'twentyfourteen' ); ?></h1>

	<?php if ( is_single() ) : // navigation links for single posts ?>

		<?php previous_post_link( '%link', __( '<div class="nav-previous"><span class="meta-nav">Previous Post</span>%title</div>', 'twentyfourteen' ) ); ?>
		<?php next_post_link( '%link', __( '<div class="nav-next"><span class="meta-nav">Next Post</span>%title</div>', 'twentyfourteen' ) ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : // navigation links for home, archive, and search pages ?>

		<div class="pagination loop-pagination">
		<?php
			/* Get the current page. */
			$current = ( get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1 );

			/* Get the max number of pages. */
			$max_num_pages = intval( $wp_query->max_num_pages );

			/* Set up arguments for the paginate_links() function. */
			$args = array(
				'base' => add_query_arg( 'paged', '%#%' ),
				'format' => '',
				'total' => $max_num_pages,
				'current' => $current,
				'prev_text' => __( '&larr; Previous', 'twentyfourteen' ),
				'next_text' => __( 'Next &rarr;', 'twentyfourteen' ),
				'mid_size' => 1
			);

			echo paginate_links( $args )
		?>
		</div>
	<?php endif; ?>

	</nav><!-- #<?php echo $nav_id; ?> -->
	<?php
}

/**
 * The following code serves no purpose in this theme.
 * This code is provided purely to satisfy the requirements of the Theme Check plugin.
 * Note: register_sidebars() and dynamic_sidebar() are not needed in this theme due to it not having any sidebars.
 * 
 */
register_sidebars();
dynamic_sidebar();

