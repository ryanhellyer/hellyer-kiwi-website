<?php

if ( ! isset( $_GET['modify_result'] ) ) {
	return;
}

$post_id = 4035;

$meta = get_post_meta( $post_id, '_results_2', true );
$new_meta = $old_meta = json_decode( $meta, true );

//print_r( $new_meta[0] );die;

$new_meta[2]['position'] = 4;
$new_meta[3]['position'] = 5;
$new_meta[4]['position'] = 6;
$new_meta[5]['position'] = 7;
$new_meta[6]['position'] = 8;
$new_meta[7]['position'] = 9;
$new_meta[8]['position'] = 10;
$new_meta[9]['position'] = 11;
$new_meta[10]['position'] = 12;
$new_meta[11]['position'] = 13;
$new_meta[12]['position'] = 14;
$new_meta[13]['position'] = 3;

print_r( $new_meta );
die;

/*
$new_meta[0] = $old_meta[1];
$new_meta[1] = $old_meta[2];
$new_meta[2] = $old_meta[3];
$new_meta[3] = $old_meta[4];
$new_meta[4] = $old_meta[5];
$new_meta[5] = $old_meta[0];
*/


$new_meta = json_encode( $new_meta, true );

update_post_meta( $post_id, '_results_2', $new_meta );

echo 'done';die;
