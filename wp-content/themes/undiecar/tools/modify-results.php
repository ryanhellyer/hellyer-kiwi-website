<?php

if ( ! isset( $_GET['modify_result'] ) ) {
	return;
}

$post_id = 4035;

$meta = get_post_meta( $post_id, '_results_2', true );
$new_meta = $old_meta = json_decode( $meta, true );

//print_r( $new_meta[0] );die;

$new_meta[2]['position'] = 3;
$new_meta[3]['position'] = 4;
$new_meta[4]['position'] = 5;
$new_meta[5]['position'] = 6;
$new_meta[6]['position'] = 2;


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
