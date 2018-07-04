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
