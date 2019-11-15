<?php


if ( ! isset( $_GET['modify_result'] ) ) {
	return;
}


$old_meta = get_post_meta( 4033, '_results_1', true );
$new_meta = $old_meta = json_decode( $meta, true );

$new_meta[0] = $old_meta[1];
$new_meta[1] = $old_meta[2];
$new_meta[2] = $old_meta[3];
$new_meta[3] = $old_meta[4];
$new_meta[4] = $old_meta[0];


print_r( $new_meta );

die;
