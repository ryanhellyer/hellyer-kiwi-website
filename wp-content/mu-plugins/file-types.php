<?php

function pressabl_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['gpx'] = 'file/gpx+xml';
	$mimes['kml'] = 'file/kml+xml';

	return $mimes;
}
add_filter( 'upload_mimes', 'pressabl_mime_types' );

//define( 'ALLOW_UNFILTERED_UPLOADS', true );
