<?php
/*
 * Loading the Strattic WordPress plugin.
 * This is only required when using the Strattic service.
 * https://www.strattic.com/
 */

if ( ! defined( 'STRATTIC_DIR' ) ) {
	define( 'STRATTIC_DIR', '/usr/local/bin/strattic-wordpress-plugin/' );
}

if ( file_exists( STRATTIC_DIR . 'strattic.php' ) ) {
	require( STRATTIC_DIR . 'strattic.php' );
}
