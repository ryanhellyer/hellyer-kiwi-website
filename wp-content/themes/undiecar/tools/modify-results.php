<?php


if ( ! isset( $_GET['modify_result'] ) ) {
	return;
}


$meta = get_post_meta( 4033, '_results_1', true );
$meta = json_decode( $meta, true );
print_r( $meta );

die;
