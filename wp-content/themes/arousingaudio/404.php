<?php
/**
 * 404 error template.
 *
 * @package Arousing Audio
 * @since Arousing Audio 1.0
 */

$data[ 'title' ]   = __( 'Error 404: Page not found', 'arousingaudio' );
$data[ 'content' ] = '';
$data[ 'comments' ] = '';

// AJAX page
if ( isset( $_GET[ 'json' ] ) ) {
	echo json_encode( $data );
	die;
}


get_header(); 

require( 'template-parts/content.php' );

get_footer();
