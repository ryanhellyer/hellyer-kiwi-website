<?php
/**
 * The main template file.
 *
 * @package Arousing Audio
 * @since Arousing Audio 1.0
 */

// Generate main page content string
$content = '';

$continue = true;
foreach ( arousingaudio_get_posts() as $key => $post ) {
	$slug = $post[ 'slug' ];

	// Filter based on taxonomy
	if ( is_tax( 'genre' ) ) {
		$term_slug = get_queried_object()->slug;

		$continue = false;
		foreach ( $post[ 'genre-terms' ] as $key => $term ) {
			if ( $term_slug == $term[ 'slug'] ) {
				$continue = true;
			}
		}

	}

	// Filter out unwanted results
	if ( true == $continue ) {

		// Convert duration to human readable format
		$duration_in_seconds = $post[ 'length' ];
		$duration_whole_minutes = floor( $duration_in_seconds / 60 );
		$duration_left_seconds = $duration_in_seconds - ( $duration_whole_minutes * 60 );
		$duration = $duration_whole_minutes . ':' .$duration_left_seconds;

		$term_slug = '';
		if ( isset( $post[ 'genre-terms' ][ 0 ][ 'slug' ] ) ) {
			$term_slug = ' genre-' . $post[ 'genre-terms' ][ 0 ][ 'slug' ];
		}

		$url = get_permalink( $post[ 'id' ] );

		$content .= '
				<a class="' . esc_attr( 'box' . $term_slug ) . '" href="' . esc_url( $url ) . '">
					<span>
					<strong>
						' . esc_html( $post[ 'title' ] ) . ' three four five six
					</strong>
					</span>
				</a>';

	}

}

$content .= '';

$data = array();

$data[ 'title' ]   = '';
$data[ 'content' ] = $content;

// AJAX page
if ( isset( $_GET[ 'json' ] ) ) {

	echo json_encode( $data );
	die;
}


get_header(); 

require( 'template-parts/content.php' );

get_footer();
