<?php

/**
 * Get message conversion strings.
 * We convert short strings in order to make filtering of the strings easier.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */

if ( ! isset( $this->display_name ) ) {
	$this->display_name = 'unknown';
}

$messages = array(
	'facebook-graph-error'    => array(
		'text' => esc_html__( 'Facebook Graph has returned an error', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'facebook-sdk-error'      => array(
		'text' => esc_html__( 'The Facebook SDK has returned an error', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'facebook-401'            => array(
		'text' => esc_html__( 'Facebook has returned a 401 Unauthorised error', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'facebook-400'            => array(
		'text' => esc_html__( 'Facebook has returned a 400 Bad Request error', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'access-token-error'      => array(
		'text' => esc_html__( 'Error getting long-lived access token', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'you-have-been-logged-in' => array(
		'text' => $this->display_name . ', you have been logged in',
	),
	'login-process-failed'    => array(
		'text' => $this->display_name . ', ' . esc_html__( 'Logging in process failed', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'user-not-found'          => array(
		'text' => esc_html__( 'User not found', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'you-are-registered'      => array(
		'text' => sprintf(
			esc_html__( '%s, thank you for registering on our site', 'simple-facebook-login' ),
			$this->display_name
		),
	),
	'user-generation-failed'  => array(
		'text' => esc_html__( 'Sorry, but the user generation process has failed', 'simple-facebook-login' ),
		'class' => 'error',
	),
	'facebook-user-not-found' => array(
		'text' => 'facebook-user-not-found',// If result is not object, then assume user was not found
		'class' => 'error',
	),
);

if ( ! current_user_can( 'manage_options' ) ) {
	$messages[ 'already-logged-in' ] = array(
		'text' => esc_html__( 'You are already logged in ;)', 'simple-facebook-login' ),
	);
} else {
	$messages[ 'already-logged-in' ] = array(
		'text' => sprintf(
			esc_html__( 'If you have not done so already, you need to add the callback URL "%s" to your Facebook app at https://developers.facebook.com/apps/', 'simple-facebook-login' ),
			esc_url( $this->get_callback_url() )
		),
	);
}

if ( isset( $this->facebook_login_url ) ) {
	$messages[ 'login-button' ] = array(
		'text' => '<a id="simple-facebook-login" class="button" href="' . esc_url( $this->facebook_login_url ) . '">' . esc_html__( 'Facebook login', 'simple-facebook-login' ) . '</a>',
	);
}
