<?php

/**
 * Ryan's email test code
 */
add_action( 'init', 'ryanstestcode' );
function ryanstestcode() {
	if ( ! isset( $_GET['ryanstestcode'] ) ) {
		return;
	}

	wp_mail( 'ryanhellyer@gmail.com', 'The subject KIWI', 'The message' );
	wp_mail( 'ryan@forsite.nu', 'The subject KIWI', 'The message' );

	echo 'Email sent!';
	die;
}
