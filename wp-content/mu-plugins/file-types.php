<?php

function pressabl_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['gpx'] = 'application/gpx+xml';
	$mimes['kml'] = 'application/vnd.google-earth.kml+xml';
	$mimes['webp'] = 'image/webp';

	return $mimes;
}
add_filter( 'upload_mimes', 'pressabl_mime_types' );

//define( 'ALLOW_UNFILTERED_UPLOADS', true );


add_filter( 'wp_check_filetype_and_ext', 'pressabl_file_and_ext_webp', 10, 4 );
function pressabl_file_and_ext_webp( $types, $file, $filename, $mimes ) {

	/*
	if ( false !== strpos( $filename, '.webp' ) ) {
		$types['ext'] = 'kml';
		$types['type'] = 'files/kml+xml';
	}
	*/

	if ( false !== strpos( $filename, '.webp' ) ) {
		$types['ext'] = 'webp';
		$types['type'] = 'image/webp';
	}

	return $types;
}
