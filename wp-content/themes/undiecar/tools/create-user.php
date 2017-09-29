<?php

// Bail out if not processing users now
if ( ! isset( $_GET['create_user'] ) ) {
	return;
}


add_action( 'init', 'undiecar_create_user' );
function undiecar_create_user() {

	$password = md5( $_GET['create_user'] . time() );
//echo sanitize_title( $_GET['create_user'] );die;
	// should add display name in here, not just the username
	$user_id = wp_insert_user(
		array(
			'user_login'   => sanitize_title( $_GET['create_user'] ),
			'display_name' => esc_html( $_GET['create_user'] ),
			'user_pass'    => $password,
		)
	) ;

	//On success
	if ( ! is_wp_error( $user_id ) ) {
		echo 'User ID: ' . $user_id;
		echo "\n<br />\n";
		echo 'Password: ' . $password;
	} else {
		echo 'Something went wrong';
		print_r( $user_id );
	}

	die;
}
