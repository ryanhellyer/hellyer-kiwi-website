<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @since             1.0.0
 * @package           pushpress_coaches
 *
 * @wordpress-plugin
 * Plugin Name:       PushPress Coaches
 * Plugin URI:
 * Description:       Displays your PushPress Coaches on your website.
 * Version:           1.0.0
 * Author:            PushPress, Inc.
 * Author URI:        http://sites.pushpress.com/
 * Text Domain:       pushpress_coaches
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pushpress-coaches-activator.php
 */
function activate_pushpress_coaches() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pushpress-coaches-activator.php';
	Pushpress_Coaches_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-pushpress-coaches-deactivator.php
 */
function deactivate_pushpress_coaches() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pushpress-coaches-deactivator.php';
	Pushpress_Coaches_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_pushpress_coaches' );
register_deactivation_hook( __FILE__, 'deactivate_pushpress_coaches' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pushpress-coaches.php';



/*******************
	ADMIN MENU
*******************/
function pushpress_coaches_admin_menu() {
	/*
	// does not need a menu for now
	add_submenu_page(
		'pushpress',
		__( 'PushPress Coaches', 'pushpress-coaches' ),
		__( 'Coaches', 'pushpress-coaches' ),
		'manage-options',
		'pushpress-coaches',
		array( 'Pushpress_Coaches_Admin', 'index' )
	);
	*/
}


function run_pushpress_coaches() {  
	add_action( 'admin_menu', 'pushpress_coaches_admin_menu', 20 );

	$plugin = new Pushpress_Coaches();
	$plugin->run();
}

run_pushpress_coaches();
