<?php
/**
 * Runs on uninstall.
 */

// Check that we should be doing this
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}



delete_option( 'strattic-search-results' );
