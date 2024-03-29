<?php

/**
stick all these in a class, and allow for easy addition of more later.
 */

function pressabl_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['gpx'] = 'application/gpx+xml';
	$mimes['kml'] = 'application/vnd.google-earth.kml+xml';
	$mimes['webp'] = 'image/webp';

	$mimes['pptx'] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
	$mimes['ppt'] = 'application/vnd.ms-powerpoint';
	$mimes['docx'] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
	$mimes['doc'] = 'application/msword';
	$mimes['pdf'] = 'application/pdf';
	$mimes['zip'] = 'application/zip';
	$mimes['mp3'] = 'audio/mpeg';

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

	if ( false !== strpos( $filename, '.svg' ) ) {
		$types['ext'] = 'svg';
		$types['type'] = 'image/svg+xml';
	}

	if ( false !== strpos( $filename, '.gpx' ) ) {
		$types['ext'] = 'gpx';
		$types['type'] = 'application/gpx+xml';
	}

	if ( false !== strpos( $filename, '.kml' ) ) {
		$types['ext'] = 'kml';
		$types['type'] = 'vnd.google-earth.kml+xml';
	}

	if ( false !== strpos( $filename, '.webp' ) ) {
		$types['ext'] = 'webp';
		$types['type'] = 'image/webp';
	}

	return $types;
}
