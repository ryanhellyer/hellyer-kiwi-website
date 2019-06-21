<?php
/*
 * Loading the Strattic WordPress plugin.
 * This is only required when using the Strattic service.
 * https://www.strattic.com/
 */

if ( ! defined( 'STRATTIC_WORDPRESS_PLUGIN_DIR' ) ) {
    define( 'STRATTIC_WORDPRESS_PLUGIN_DIR', '/var/www/plugin/' );
}

if ( file_exists( STRATTIC_WORDPRESS_PLUGIN_DIR . 'strattic.php' ) ) {
    require( STRATTIC_WORDPRESS_PLUGIN_DIR . 'strattic.php' );
}