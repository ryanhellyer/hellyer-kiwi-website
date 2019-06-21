<?php

// Bail out now if not in test mode
if ( ! defined( 'STRATTIC_DEV' ) ) {
	return;
}


/**
 * Helper scripts for the Strattic platform.
 * These are totally insecure and should not be left on websites.
 */

/**
 * Load the database tool.
 */
if ( '/adminer/' === mb_substr( $_SERVER[ 'REQUEST_URI' ], 0, 9 ) ) {
	require( 'adminer/adminer.php' );
	die;
}

/**
 * Load the search replace tool.
 */
if ( isset( $_GET[ 'search_replace' ] ) ) {
	require( 'search-replace/index.php' );
	die;
}


function strattic_helper_notice() {
	?>
	<div class="notice notice-error">
		<p>
			The Strattic Helper plugin is running. It can be deactivated by removing <code>define( 'STRATTIC_DEV', true );</code> from the wp-config.php file.
		</p>
	</div><?php
}
add_action( 'admin_notices', 'strattic_helper_notice' );


/**
 * Add form for creating a new user.
 */
if ( isset( $_GET[ 'create_user' ] ) ) {
	add_action( 'init', 'strattic_create_user' );
}
function strattic_create_user() {
	if ( ! isset( $_POST[ 'username' ] ) ) {
		echo '
		<form method="POST" action="">
			<p>
				<label>Username</label>
				<input type="text" name="username" value="" />
			</p>
			<p>
				<label>Password</label>
				<input type="text" name="password" value="" />
			</p>
			<p>
				<label>Email address</label>
				<input type="text" name="email" value="" />
			</p>
			<p>
				<label>Role</label>
				<input type="text" name="role" value="administrator" />
			</p>
			<input type="submit" value="submit" />
		</form>
		';
	} else {

		$user_id = wp_create_user( $_POST[ 'username' ], $_POST[ 'password' ], $_POST[ 'email' ] );
		wp_update_user(
			array(
				'ID'   => $user_id,
				'role' => $_POST[ 'role' ]
			)
		);

		die( 'User has been created' );
	}

	die;
}
