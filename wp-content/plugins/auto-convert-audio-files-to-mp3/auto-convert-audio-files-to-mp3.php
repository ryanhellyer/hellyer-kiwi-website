<?php
/*
Plugin Name: Convert audio files
Plugin URI: https://geek.hellyer.kiwi/plugins/convert-audio-files/
Description: Converts audio files from WAV to MP3
Version: 1.0
Author: Ryan Hellyer
Author URI: https://geek.hellyer.kiwi/

*/


/**
 * Add non-standard mime type support.
 */
function acf_myme_types( $mime_types ){

	$mime_types[ 'flac' ] = 'audio/flac';
	$mime_types[ 'ogg' ] = 'application/ogg';

	return $mime_types;
}
add_filter( 'upload_mimes', 'acf_myme_types', 1, 1 );


add_filter( 'wp_handle_upload_prefilter', 'acf_filter_audio_on_upload' );
/**
 * Modifying file name and format as it is uploaded.
 * This is a bit hacky, as we're uploading something with a .mp3 extension, even when it isn't an MP3.
 */
function acf_filter_audio_on_upload( $data ) {
	global $caf_extension;

	$name_exploded = explode( '.', $data[ 'name' ] );// substr( $data[ 'name' ], -4 );
	$caf_extension = $name_exploded[ count( $name_exploded ) - 1 ];
	$slug      = substr( $data[ 'name' ], 0, -4 );
//$ext = $caf_extension;
	if (
		'wav' == $caf_extension
		||
		'ogg' == $caf_extension
		||
		'flac' == $caf_extension
	) {
		$dir = wp_upload_dir();
		$slug = sanitize_title( $slug );

		$data[ 'name' ] = $slug . '.mp3';
		$new_temp_name = $dir[ 'path' ] . 'temporary.mp3';

		$data[ 'name' ] = $slug . '.mp3';
		$data[ 'type' ] = 'audio/mp3';

	} else {
		$caf_extension = 'EJECT!';
	}

$file = '/home/ryan/nginx/arousingaudio.com/public_html/audio/test.txt';
$contents = file_get_contents( $file );
file_put_contents( $file, $contents . "\n\n" . $ext . ': ' . $caf_extension . "\n".print_r( $data, true ) );

	return $data;
}

add_filter( 'wp_handle_upload', 'acf_filter_audio_after_upload' );
/**
 * Converting uploaded file to MP3 after it has been uploaded.
 */
function acf_filter_audio_after_upload( $data ) {
	global $caf_extension;

	// Bail out now if it wasn't processed as other format yet.
	if ( 'EJECT!' == $caf_extension ) {
		return $data;
	}

	$file = $data[ 'file' ];

	$extension = substr( $file, -4 );
	$slug      = substr( $file, 0, -4 );

	rename( $file, $slug );

	$command = 'ffmpeg -i ' . $slug . ' ' . $file;
	$result = shell_exec( $command );

	unlink( $slug );

	return $data;
}
