<?php
/**
 * The main template file.
 *
 * @package Arousing Audio
 * @since Arousing Audio 1.0
 */

$id = $wp_query->post->ID;
$data = arousingaudio_get_post( $id );

// AJAX page
if ( isset( $_GET[ 'json' ] ) ) {

	echo json_encode( $data );
	die;
}

get_header();

require( 'template-parts/content.php' );

get_footer();
