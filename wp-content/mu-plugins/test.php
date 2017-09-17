<?php

//Prompt_Comment_Form_Handling::enqueue_assets();

add_action( 'comment_form', 'dequeue_postmatic', 20 );
function dequeue_postmatic() {
	wp_dequeue_style( 'prompt-comment-form' );

	wp_dequeue_script( 'prompt-comment-form' );

}



add_action( 'init', 'bla' );
function bla() {

	if ( ! isset( $_GET['testmail'] ) ) {
		return;
	}

	$display_name = 'Ryan Hellyer';
	wp_mail(
		'ryanhellyer@gmail.com',
		'New Undiecar driver: ' . $display_name,
		'<a href="' . esc_url( 'https://undiecar.com/member/' . sanitize_title( $display_name ) ) . '/">' . esc_html( $display_name ) . '</a> has signed up to the Undiecar Championship.'
	);

	echo 'Mail sent';
	die;
}


