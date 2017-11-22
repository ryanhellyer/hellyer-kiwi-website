<?php

//Prompt_Comment_Form_Handling::enqueue_assets();

add_action( 'comment_form', 'dequeue_postmatic', 20 );
function dequeue_postmatic() {
	wp_dequeue_style( 'prompt-comment-form' );

	wp_dequeue_script( 'prompt-comment-form' );

}
