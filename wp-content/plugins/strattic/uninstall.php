<?php
/**
 * Runs on uninstall.
 */

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}



delete_option( 'strattic-manual-urls' );
delete_option( 'strattic-discovered-links' );
delete_option( 'strattic-paths' );
delete_option( 'strattic-job-id' );
delete_option( 'strattic-id-token' );
delete_option( 'strattic-refresh-token' );
delete_option( 'strattic-status-lock' );
delete_option( 'strattic-minification' );
delete_option( 'strattic-minify-html' );
delete_option( 'strattic-minify-css' );
delete_option( 'strattic-minify-js' );

// Legacy options
delete_option( 'strattic-last-scan-time' );
