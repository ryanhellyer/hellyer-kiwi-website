<?php
/*
	Registers settings, adds a new submenu in the admin panel and adds options to array
	@since 0.1

	PixoPoint Google Analytics plugin for WordPress
	Copyright (c) 2009 PixoPoint Web Development

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/


/**
 * Do not continue processing since file was called directly
 * @since 0.1
 */
if ( !defined( 'ABSPATH' ) )
	return;


/**
 * Adds admin menu, stores array and registers settings
 * @since 0.1.3
 */
function show_pixopoint_google_analytics_options() {

	// Add various options for admin page
	$page = add_dashboard_page(
		'PixoPoint Google Analytics',
		'Google Analytics',
		'administrator',
		'pixopoint_googleanalytics_options',
		'pixopoint_google_analytics_options'
	);

	// Add scripts
	add_action( "admin_print_scripts-$page", 'pixopoint_google_analytics_scripts' );

	// Add styles
	add_action( "admin_print_styles-$page", 'pixopoint_google_analytics_styles' );

	// Register Settings
	register_setting(
		'pixopoint-google-analytics', // Option group
		'pixopoint_google_analytics', // Option name
		'pixopoint_googleanalytics_optionsvalidate' // Validation callback
	);
}
add_action( 'admin_menu', 'show_pixopoint_google_analytics_options' );

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 * Uses pixopoint_google_analytics_get_option() for sanitization
 * @since 0.1
 */
function pixopoint_googleanalytics_optionsvalidate( $input ) {

	// Santize "site" code
	$output =  pixopoint_google_analytics_get_option( $input['site'] );

	// Crude hack to use network wide option - this should likely be done much better, but I couldn't see an immediately obvious way to handle update_site_option
	if ( is_super_admin() AND '' != $input['network'] )
		update_site_option( 'pixopoint_google_analytics_network', pixopoint_google_analytics_get_option( $input['network'] ) ); // Santize and store "network" code
	if ( is_super_admin() AND '' != $input['domain'] )
		update_site_option( 'pixopoint_google_analytics_domain', pixopoint_google_analytics_get_option( $input['domain'] ) ); // Santize and store "domain" code

	return $output;
}

/**
 * Add admin scripts
 * @since 0.1
 */
function pixopoint_google_analytics_scripts() { ?>
<script type="text/javascript"> 
function toggle_help(ele, ele2) {
	var expl = document.getElementById(ele2);
	if (expl.style.display == "block") {
		expl.style.display = "none";
		ele.innerHTML = "What's this?";
	} else {
		expl.style.display = "block";
		ele.innerHTML = "Hide explanation";
	}
}
</script>
<?php }

/**
 * Add admin styles
 * @since 0.1
 */
function pixopoint_google_analytics_styles() { ?>
<style type="text/css">
	#icon-pixopoint-google-analytics-pixopoint {
		background:url(<?php echo PIXOPOINT_GOOGLE_ANALYTICS_IMAGES_URL; ?>pixopoint_icon.png) no-repeat;
	}
	#icon-pixopoint-google-analytics-google {
		background:url(<?php echo PIXOPOINT_GOOGLE_ANALYTICS_IMAGES_URL; ?>google_icon.png) no-repeat;
	}
	textarea {
		width:100%;
	}
</style>
<?php }

/**
 * Loads the admin page
 * @since 0.1
 */
function pixopoint_google_analytics_options() {
		?>
<div class="wrap">
	<form method="post" action="options.php" id="options">
		<?php settings_fields( 'pixopoint-google-analytics' ); ?>
		<?php
			// Display PixoPoint logo for single installs or super admins
			if ( is_multisite() AND !is_super_admin() )
				screen_icon( 'pixopoint-google-analytics-google' );
			// Display Google logo for multi-site end-users
			else
				screen_icon( 'pixopoint-google-analytics-pixopoint' );
		?>
		<h2>
			<?php
			// Display "PixoPoint" text only for single installs or super admins
			if ( !is_multisite() OR is_super_admin() )
				_e( 'PixoPoint ', 'pixopoint_theme_integrator_lang' );

			_e( 'Google Analytics', 'pixopoint_theme_integrator_lang' );
			?>
		</h2>

		<table class="form-table" style="width:100%;"> 
			<tr> 
				<th scope="row" style="width:400px;" valign="top"> 
					<label><?php _e( 'Google Analytics User Account', 'pixopoint_theme_integrator_lang' ); ?></label> <small><a href="#" onclick="javascript:toggle_help(this, 'expl');">What's this?</a></small> 
				</th>
				<td>
					<?php
					/*
						Explanation dropdown courtesy of "Google Analytics for WordPress"
						http://yoast.com/wordpress/analytics/#utm_source=wordpress&utm_medium=plugin&utm_campaign=google-analytics-for-wordpress&utm_content=v407
						Joost de Valk
					*/
					?>
					<input id="uastring" type="text" size="20" maxlength="40" name="pixopoint_google_analytics[site]" type="text" value="<?php echo pixopoint_google_analytics_get_option( 'site' ); ?>" />
					<br />
					<div id="expl" style="display:none;"> 
						<h3><?php _e( 'Explanation', 'pixopoint_theme_integrator_lang' ); ?></h3> 
						<p><?php _e( "Google Analytics is a statistics service provided
							free of charge by Google.  This plugin simplifies
							the process of including the <em>basic</em> Google
							Analytics code in your blog, so you don't have to
							edit any PHP. If you don't have a Google Analytics
							account yet, you can get one at 
							<a href='https://www.google.com/analytics/home/'>analytics.google.com</a>.
							", 'pixopoint_theme_integrator_lang' ); ?>
						</p>
						<p><?php _e( 'In the Google interface, you will be provided with a "Web Property ID" 
							(seen below circled in red). Copy and paste that into the "Google Analytics User Account" box above.
							', 'pixopoint_theme_integrator_lang' ); ?>
						</p>
						<img src="<?php echo PIXOPOINT_GOOGLE_ANALYTICS_IMAGES_URL; ?>analytics-site.png" alt="" />
					</div>
				</td>
			</tr>
			<?php

			// Only display option to edit network account for super admins
			if ( is_super_admin() ) {
			?>
			<tr>
				<th scope="row" style="width:400px;" valign="top"> 
					<label><?php _e( 'Google Analytics User Account for network', 'pixopoint_theme_integrator_lang' ); ?> <small>(<?php _e( 'For tracking stats across your entire network', 'pixopoint_theme_integrator_lang' ); ?>)</small></label
				</th> 
				<td> 
					<input id="uastring" type="text" size="20" maxlength="40" name="pixopoint_google_analytics[network]" type="text" value="<?php	echo pixopoint_google_analytics_get_option( 'network' ); ?>" />
					<br />
				</td>
			</tr>
			<?php
			/*
			<tr>
				<th scope="row" style="width:400px;" valign="top"> 
					<label><?php _e( 'Enter domain name for main account', 'pixopoint_theme_integrator_lang' ); ?>
				</th>
				<td>
					<input type="text" size="20" maxlength="40" name="pixopoint_google_analytics[domain]" type="text" value="<?php echo pixopoint_google_analytics_get_option( 'domain' ); ?>" />
					<br />
				</td>
			</tr>
			<?php
			*/
			}
			?>
		</table>


		<input type="hidden" name="action" value="update" />
		<div style="clear:both;padding-top:20px;"></div>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e( 'Update Analytics' ) ?>" /></p>

		<div style="clear:both;padding-top:20px;"></div>
	</form>
</div>


<?php
}

/**
 * Serve error if running PHP older than version 5.0
 * @since 0.1
 */
if ( !function_exists( 'pixopoint_php4_error_message' ) ) :
function pixopoint_php4_error_message() {
	echo '
		<div id="message" class="updated fade" style="opacity:1;">
			<p>
				Sorry, but this plugin only supports php 5.0 or above. Some features may or may not work as expected.
			</p>
		</div>';
}
endif;
if ( version_compare( phpversion(), '5.0', '<' ) )
	add_action( 'admin_notices', 'pixopoint_php4_error_message' ); // Serve error to < PHP5.0
