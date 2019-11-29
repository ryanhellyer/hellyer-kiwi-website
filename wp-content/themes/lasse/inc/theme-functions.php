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
function lassesuper_id( $item ) {
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
function lassesuper_menu() {// Uses wp_get_nav_menu_items() instead of wp_nav_menu().
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

		// Grab the menu
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
function lassesuper_dimensions( $id, $dimensions ) {

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
function lassesuper_url( $item ) {
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
function lassesuper_title( $item ) {
	if ( isset( $item->title ) )
		return $item->title;
	else
		return $item->post_title;
}


/**
 * The following code serves no purpose in this theme.
 * This code is provided purely to satisfy the requirements of the Theme Check plugin.
 * Note: register_sidebars() and dynamic_sidebar() are not needed in this theme due to it not having any sidebars.
 * 
 */
register_sidebars();
dynamic_sidebar();

