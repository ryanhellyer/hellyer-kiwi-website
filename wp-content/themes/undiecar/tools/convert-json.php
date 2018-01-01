<?php

if ( ! isset( $_GET['convert_json'] ) ) {
	return;
}

$dir = wp_upload_dir();

$stats = file_get_contents( $dir['basedir'] . '/iracing-members.json' );
$stats = json_decode( $stats, true );

foreach ( $stats as $key => $stat ) {
	$simpler_stats[] = $key;
//	print_r( $stat );
//	die;
}

$simpler_stats = json_encode( $simpler_stats, JSON_UNESCAPED_UNICODE );
file_put_contents( $dir['basedir'] . '/iracing-members-simple.json', $simpler_stats );


echo $dir['basedir'] . '/iracing-members-simple.json';
die();
