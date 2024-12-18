<?php

/**
 * Numeric pagination.
 *
 * Code developed from the excellent Genesis theme by StudioPress (http://studiopress.com/)
 *
 * @package Hellish Simplicity
 * @since Hellish Simplicity 1.1
 */


if ( ! is_singular() ) { // do nothing

	global $wp_query;

	// Stop execution if there's only 1 page
	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	echo '

		<ul id="numeric-pagination">';

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged') ) : 1;
	$max = intval( $wp_query->max_num_pages );

	// Add current page to the array
	if ( $paged >= 1 ) {
		$links[] = $paged;
	}

	// Add the pages around the current page to the array
	if ( $paged >= 3 ) {
		$links[] = $paged - 1; $links[] = $paged - 2;
	}
	if ( ($paged + 2) <= $max ) { 
		$links[] = $paged + 2; $links[] = $paged + 1;
	}

	// Previous Post Link
	if ( get_previous_posts_link() ) {
		printf( '<li>%s</li>' . "\n", get_previous_posts_link( esc_html__( '&laquo; Previous', 'hellish-simplicity') ) );
	}

	// Link to first Page, plus ellipeses, if necessary
	if ( ! in_array( 1, $links ) ) {
		if ( $paged == 1 ) {
			$current = ' class="active"';
		} else {
			$current = null;
		}
		printf(
			'<li %s><a href="%s">%s</a></li>' . "\n",
			$current,
			get_pagenum_link(1),
			'1'
		);
	
		if ( ! in_array( 2, $links ) ) {
			echo '<li class="pagination-gap">&hellip;</li>';
		}

	}
	
	// Link to Current page, plus 2 pages in either direction (if necessary).
	sort( $links );
	foreach( (array)$links as $link ) {
		$current = ( $paged == $link ) ? 'class="active"' : '';
		printf(
			"\n" . '			<li %s><a href="%s">%s</a></li>',
			$current,
			get_pagenum_link( $link ),
			$link
		);
	}
	
	// Link to last Page, plus ellipses, if necessary
	if ( !in_array( $max, $links ) ) {
		if ( !in_array( $max - 1, $links ) ) {
			echo '<li class="pagination-gap">&hellip;</li>' . "\n";
		}

		$current = ( $paged == $max ) ? 'class="active"' : '';
		if ( '' != $current ) {
			$current = ' ' . $current; // Remove redundant space
		}
		printf(
			"\n" . '			<li%s><a href="%s">%s</a></li>',
			$current,
			get_pagenum_link( $max ),
			$max
		);
	}
	
	// Next Post Link
	if ( get_next_posts_link() ) {
		printf(
			"\n" . '			<li>%s</li>',
			get_next_posts_link( esc_html__( 'Next &raquo;', 'hellish-simplicity' ) )
		);
	}

	echo '
		</ul>';
}

