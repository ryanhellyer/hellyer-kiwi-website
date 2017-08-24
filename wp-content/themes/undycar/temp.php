<?php

if ( isset( $_GET['add_user'] ) ) {

add_action( 'template_redirect', 'bla' );
function bla() {
	$user_data = array(
		'user_login'   => 'kevin-gimu00e9nez',
		'display_name' => 'Kevin Giménez',
		'user_pass'    => md5('kevin'),
		'user_email'   => 'replace+' . md5( 'kevin-gimu00e9nez' ) . '@me.com',
	);
	$user_id = wp_insert_user( $user_data ) ;
	print_r( $user_id );
	die( 'done' );
}

}