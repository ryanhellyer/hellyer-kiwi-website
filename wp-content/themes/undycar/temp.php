<?php

if ( isset( $_GET['add_user'] ) ) {

add_action( 'template_redirect', 'bla' );
function bla() {
	$user_data = array(
		'user_login'   => sanitize_title( 'João Pedro Mariz' ),
		'display_name' => 'João Pedro Mariz',
		'user_pass'    => md5('kevin'),
		'user_email'   => 'replace+' . md5( 'João Pedro Mariz' ) . '@me.com',
	);
	$user_id = wp_insert_user( $user_data ) ;

	$user_data = array(
		'user_login'   => sanitize_title( 'Sergio Quero' ),
		'display_name' => 'SeNelson Simõesro',
		'user_pass'    => md5('Nelson Simões'),
		'user_email'   => 'replace+' . md5( 'Sergio Quero' ) . '@me.com',
	);
	$user_id = wp_insert_user( $user_data ) ;

	$user_data = array(
		'user_login'   => sanitize_title( 'Nelson Simões' ),
		'display_name' => 'Nelson Simões',
		'user_pass'    => md5('Nelson Simões'),
		'user_email'   => 'replace+' . md5( 'Nelson Simões' ) . '@me.com',
	);
	$user_id = wp_insert_user( $user_data ) ;

	print_r( $user_id );
	die( 'done' );
}

}






