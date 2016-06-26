<?php

//Prompt_Comment_Form_Handling::enqueue_assets();

add_action( 'template_redirect', 'dequeue_postmatic' );
function dequeue_postmatic() {
	wp_dequeue_style( 'prompt-comment-form' );
}
