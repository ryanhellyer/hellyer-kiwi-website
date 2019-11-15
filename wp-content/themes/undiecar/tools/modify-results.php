<?php


if ( ! isset( $_GET['modify_result'] ) ) {
	return;
}


$meta = get_post_meta( 4033 );
print_r( $meta );

die;
