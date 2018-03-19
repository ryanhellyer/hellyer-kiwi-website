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
 * @package           pushpress_lead_form
 *
 * @wordpress-plugin
 * Plugin Name:       PushPress Lead Form
 * Plugin URI:        
 * Description:       Displays your PushPress Lead Form on your website.
 * Version:           1.0.0
 * Author:            PushPress, Inc.
 * Author URI:        http://sites.pushpress.com/
 * Text Domain:       pushpress_lead_form
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-pushpress-lead-form-activator.php
 */
function activate_pushpress_lead_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-pushpress-lead-form-activator.php';
	Pushpress_Lead_Form_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_pushpress_lead_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-pushpress-lead-form.php';



/*******************
	ADMIN MENU
*******************/
function pushpress_lead_form_admin_menu() {
	add_submenu_page( 'pushpress', __( "PushPress Lead Form", 'pushpress-lead-form' ), __( "Lead Form", 'pushpress-lead-form' ), 'manage_options', 'pushpress-lead-form', array( 'Pushpress_Lead_Form_Admin', 'index' ) );
}


function run_pushpress_lead_form() {  
	define('PUSHPRESS_LEAD_FORM_URL', plugins_url('', __FILE__ ));
	define('PUSHPRESS_LEAD_FORM_DIR', dirname(__FILE__));
			
	add_action('admin_menu', 'pushpress_lead_form_admin_menu');

	$plugin = new Pushpress_Lead_Form();
	$plugin->run();
}

run_pushpress_lead_form();
