<?php
/*
 * Plugin Name: PushPress Connect
 * Plugin URI: https://pushpress.com
 * Description: ...
 * Author: PushPress, Inc
 * Version: 1.0
 * Author URI: https://pushpress.com
*/

define( 'PUSHPRESS_HOST', 'http://api.pushpressdev.com' );
define( 'PUSHPRESS_LOCAL', false );
define( 'PUSHPRESS_DEV', true );
define( 'PUSHPRESS_API_VERSION', '1' );
define( 'PUSHPRESS_PLUGIN_VERSION', '2.0.0' );

define('PUSHPRESS_URL', plugins_url('', __FILE__ ) );
define('PUSHPRESS_DIR', dirname(__FILE__));
define('PUSHPRESS_FRONTEND', PUSHPRESS_DIR . '/templates/frontend/' );
define('PUSHPRESS_BACKEND', PUSHPRESS_DIR . '/templates/' );
define('PUSHPRESS_INC', PUSHPRESS_DIR . '/inc/' );

define( 'PUSHPRESS_CACHE_1', 300 );

require( 'inc/wp_pushpress_messages.php' );
require( 'inc/class-pushpress-connect.php' );
require( 'inc/wp_pushpress_model.php' );
require( 'lib/php-sdk/lib/Pushpress.php' );

new PushPress_Connect();
