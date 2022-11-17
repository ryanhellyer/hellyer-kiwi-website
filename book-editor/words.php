<?php

$get = filter_input( INPUT_GET, 'pages', FILTER_SANITIZE_SPECIAL_CHARS );
$page_numbers = explode( ',', $get );
$pages = array();
foreach ( $page_numbers as $key => $page_number ) {
	$path = 'page-' . $page_number . '.html';
	if ( file_exists( $path ) ) {
		$pages[ $page_number ] = file_get_contents( $path );
	}
}
$shortened_json = json_encode( $pages, JSON_PRETTY_PRINT );

header( 'Content-Type: application/json; charset=utf-8' );

echo $shortened_json;
