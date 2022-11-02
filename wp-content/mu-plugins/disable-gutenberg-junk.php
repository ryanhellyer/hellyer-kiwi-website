<?php

/**
 * Disable WordPress block editor CSS.
 */
function disable_gutenberg_junk() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'wc-block-style' );
}
add_action( 'wp_print_styles', 'disable_gutenberg_junk', 100 );
