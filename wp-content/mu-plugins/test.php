<?php

/**
 * Ryan's email test code
 */
add_action( 'init', 'ryanstestcode' );
function ryanstestcode() {
	if ( ! isset( $_GET['ryanstestcode'] ) ) {
		return;
	}

	$headers[] = 'From: Me Myself <me@example.net>';
	$headers[] = 'Cc: John Q Codex <jqc@wordpress.org>';
	$headers[] = 'Cc: iluvwp@wordpress.org'; // note you can just use a simple email address

	$to = 'ryan@forsite.nu';
	$subject = 'Test subject';
	$message = 'This is a test string';
	wp_mail( $to, $subject, $message, $headers );
	echo 'Email sent!';
	die;
}
