<?php
/*
Plugin Name: Pressabl Admin
Plugin URI: http://pixopoint.com/
Description: Extra stuff specific to Pressabl.com
Author: PixoPoint
Version: 0.1
Author URI: http://pixopoint.com/
*/


/**
 * Define some constants
 * @since 0.1
 */
define( 'PRESSABL_ADMIN_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'PRESSABL_ADMIN_URL', WP_PLUGIN_URL . '/' . basename( PRESSABL_ADMIN_DIR )  . '/' ); // Plugin folder URL
define( 'PRESSABL_ADMIN_SCRIPTS_URL', PRESSABL_ADMIN_URL . 'scripts/' ); // Scripts URL

/**
 * Add favicon
 * Checks for expected site specific images, before defaulting to Pressabl favicon
 * @since 0.1
 */
function pressabl_admin_favicon() {
/*
	if ( !function_exists( 'wppb_storage_folder' ) )
		return;

	// Display appropriate favicon
	if ( file_exists( wppb_storage_folder() . '/favicon.ico' ) )
		echo "\n<link rel='shortcut icon' href='" .  wppb_storage_folder( '', 'url' ) . "/favicon.ico' />\n";
	elseif ( file_exists( wppb_storage_folder() . '/favicon.png' ) )
		echo "\n<link rel='shortcut icon' href='" .  wppb_storage_folder( '', 'url' ) . "/favicon.png' />\n";
	elseif ( file_exists( wppb_storage_folder() . '/favicon.gif' ) )
		echo "\n<link rel='shortcut icon' href='" .  wppb_storage_folder( '', 'url' ) . "/favicon.gif' />\n";
	elseif ( file_exists( wppb_storage_folder() . '/favicon.jpg' ) )
		echo "\n<link rel='shortcut icon' href='" .  wppb_storage_folder( '', 'url' ) . "/favicon.jpg' />\n";
	else
*/
//	echo "\n<link rel='shortcut icon' href='" .  get_template_directory_uri() . "/favicon.ico' />\n";

}
//add_action( 'admin_head', 'pressabl_admin_favicon' );
//add_action( 'wp_head', 'pressabl_admin_favicon' );

/**
 * Styling for the login page
 * @since 0.1
 */
function pressabl_login() { 
	echo '<link rel="stylesheet" type="text/css" href="' . PRESSABL_ADMIN_URL . 'pressabl-admin.css" />'; 
}
//add_action( 'login_head', 'pressabl_login' );

/**
 * Load plugin(s)
 * @since 0.1
 */
if ( 'on' == pressabl_admin_get_option( 'simple-colorbox' ) ) {
	if ( is_dir( WP_PLUGIN_DIR . '/simple-colorbox' ) )
		require( WP_PLUGIN_DIR . '/simple-colorbox/index.php' );
}
if ( 'on' == pressabl_admin_get_option( 'advanced-widgets' ) ) {
	if ( is_dir( WP_PLUGIN_DIR . '/widgets-reloaded' ) )
		require( WP_PLUGIN_DIR . '/widgets-reloaded/widgets-reloaded.php' );
	if ( is_dir( WP_PLUGIN_DIR . '/hybrid-tabs' ) )
		require( WP_PLUGIN_DIR . '/hybrid-tabs/hybrid-tabs.php' );
}
if ( 'on' == pressabl_admin_get_option( 'wp-grins-ssl' ) ) {
	if ( is_dir( WP_PLUGIN_DIR . '/wp-grins-ssl' ) )
		require( WP_PLUGIN_DIR . '/wp-grins-ssl/wp-grins.php' );
}
if ( 'on' == pressabl_admin_get_option( 'webhooks' ) ) {
	if ( is_dir( WP_PLUGIN_DIR . '/hookpress' ) )
		require( WP_PLUGIN_DIR . '/hookpress/hookpress.php' );
}
if ( 'on' == pressabl_admin_get_option( 'simplecms' ) ) {
	if ( is_dir( WP_PLUGIN_DIR . '/ryans-simple-cms' ) )
		require( WP_PLUGIN_DIR . '/ryans-simple-cms/index.php' );
}

/**
 * Init
 * @since 0.1
 */
function pressabl_admin_init() {
	// Add stuff to hooks
//	add_action( 'admin_head', 'pressabl_admin_css' ); // Admin panel specific CSS
//	add_action( 'wp_dashboard_setup', 'pressabl_admin_remove_dashboard_widgets' ); // Remove dashboard widgets

	// Remove stuff from hooks - if not super admin
	if ( !is_super_admin() ) {
		remove_action( 'personal_options', '_admin_bar_preferences' ); // Remove the Admin Bar preference in user profile
		remove_action( 'admin_menu', 'cfc_config_page' ); // Disables Comments for Cookies admin page
		remove_action( 'admin_menu', 'kpg_stop_sp_reg_init' ); // Disables Stop Spammers Plugin admin page
		global $SyntaxHighlighter;
		remove_action( 'admin_menu', array( $SyntaxHighlighter, 'register_settings_page' ) );

		remove_action( 'admin_menu', 'bb2_admin_pages' );

	}

}
add_action( 'init', 'pressabl_admin_init' );
remove_action('admin_menu', array(&$this, 'admin_menu')); // Add menu in admin.

/**
 * Admin styling
 * @since 0.1
 */
function pressabl_admin_css() {
  echo '<style type="text/css">
	/* The logo in the top left */
        #wp-admin-bar-wp-logo {
		background: url(' . PRESSABL_ADMIN_URL . 'pressabl-icon.png) 50% center no-repeat !important;
        }
        #wp-admin-bar-wp-logo:hover {
		background: #fff url(' . PRESSABL_ADMIN_URL . 'pressabl-icon.png) 50% center no-repeat !important;
        }
        #wp-admin-bar-wp-logo a {
		background: none !important;
	}
        #wp-admin-bar-wp-logo li {
		display: none;
	}
        #header-logo{
		background: url(' . PRESSABL_ADMIN_URL . 'pressabl_heading_icon.png) left center no-repeat;
		width: 32px;
		height: 32px;
	}
	/* Remove WP version number from footer */
	#footer-upgrade {
		display: none;
	}
	/* Remove Help link */
	#contextual-help-link-wrap {
		zdisplay: none;
	}
</style>';
}

/**
 * Replace footer text
 * @since 0.1
 */
function pressabl_admin_footer () {
	echo 'Thank you for creating a site with <a href="http://pressabl.com/">Pressabl</a>!';
}
//add_filter( 'admin_footer_text', 'pressabl_admin_footer' );

/**
 * Remove menus
 * @since 0.1
 */
function pressabl_admin_remove_dashboard_widgets() {
	global $wp_meta_boxes;

	// Remove dashboard widgets
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links'] );
	unset( $wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_primary'] );
	unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary'] );
}

/**
 * Adds admin menu, stores array and registers settings
 * @since 0.1
 */
function show_pressabl_admin_options() {
	// Add various options for admin page
	$page = add_theme_page(
		'Extras',
		'Extras',
		'administrator',
		'pressabl_admin_features_page',
		'pressabl_admin_features'
	);
	add_action( "admin_print_styles-$page", 'pressabl_admin_features_styles' ); // Add styles

	$page = add_theme_page(
		'Template Help',
		'Template Help',
		'administrator',
		'pressabl_admin_help_page',
		'pressabl_admin_help'
	);
	add_action( "admin_print_styles-$page", 'pressabl_admin_features_styles' ); // Add styles

	// Register Settings
	register_setting(
		'pressabl-admin-features', // Option group
		'pressabl_admin_features', // Option name
		'pressabl_admin_features_validate' // Validation callback
	);

}
add_action( 'admin_menu', 'show_pressabl_admin_options' );

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 * Uses pixopoint_google_analytics_get_option() for sanitization
 * @since 0.1
 */
function pressabl_admin_features_validate( $input ) {

	// Santize checkboxes
	if ( 'on' == $input['simple-colorbox'] OR '' == $input['simple-colorbox'] )
		$output['simple-colorbox'] = $input['simple-colorbox'];
	if ( 'on' == $input['wp-grins-ssl'] OR '' == $input['wp-grins-ssl'] )
		$output['wp-grins-ssl'] = $input['wp-grins-ssl'];
	if ( 'on' == $input['advanced-widgets'] OR '' == $input['advanced-widgets'] )
		$output['advanced-widgets'] = $input['advanced-widgets'];
	if ( 'on' == $input['webhooks'] OR '' == $input['webhooks'] )
		$output['webhooks'] = $input['webhooks'];
	if ( 'on' == $input['simplecms'] OR '' == $input['simplecms'] )
		$output['simplecms'] = $input['simplecms'];
	if ( 'on' == $input['script_anythingslider'] OR '' == $input['script_anythingslider'] )
		$output['script_anythingslider'] = $input['script_anythingslider'];
	if ( 'on' == $input['script_menu'] OR '' == $input['script_menu'] )
		$output['script_menu'] = $input['script_menu'];
	if ( 'on' == $input[''] OR '' == $input[''] )
		$output[''] = $input[''];
	if ( 'on' == $input[''] OR '' == $input[''] )
		$output[''] = $input[''];
/*
	// Upload file
	$data = $_FILES['favicon'];
	if ( '' != $data['name'] ) {
		$ext = substr( strrchr( $data['name'], '.' ), 1 );
		// Spit an error out when not an image - would be better to send admin notice instead
		if ( $ext!= 'ico' AND $ext!= 'jpg' AND $ext != 'gif' AND $ext != 'png')
			die( 'Only ico, jpg, gif or png files are allowed to be uploaded!' ); // Kill execution so they get to see the error
		if ( file_exists( wppb_storage_folder() . '/favicon.png' ) );
		if ( file_exists( wppb_storage_folder() . '/favicon.png' ) );
		if ( file_exists( wppb_storage_folder() . '/favicon.gif' ) );
		if ( file_exists( wppb_storage_folder() . '/pressabl/favicon.jpg' ) );
		if ( file_exists( wppb_storage_folder() . '/pressabl/favicon.ico' ) );
		file_put_contents( wppb_storage_folder() . '/favicon.' . $ext, file_get_contents( $data['tmp_name'] ) ); // Save favicon
 	}
*/

	return $output;
}

/**
 * Grab option data
 * @since 0.1
 */
function pressabl_admin_get_option( $option ) {

	$options = get_option( 'pressabl_admin_features' );

	return $options[$option];
}

/**
 * Add admin styles
 * @since 0.1
 */
function pressabl_admin_features_styles() { ?>
<style type="text/css">
	#icon-pressabl-admin {
		background:url(<?php echo PRESSABL_ADMIN_URL; ?>pressabl_heading_icon.png) no-repeat;
	}
</style>
<?php }

/**
 * Loads the admin page
 * @since 0.1
 */
function pressabl_admin_features() {
	?>
<div class="wrap">
	<form method="post" action="options.php" enctype="multipart/form-data">
		<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />
		<?php settings_fields( 'pressabl-admin-features' ); ?>
		<?php screen_icon( 'pressabl-admin' ); ?>

		<h2><?php _e( 'Extra Features', 'pressabl_admin_lang' ); ?></h2>

		<script type="text/javascript"> 
		function toggle_help(ele, ele2) {
			var expl = document.getElementById(ele2);
			if (expl.style.display == "block") {
				expl.style.display = "none";
				ele.innerHTML = "What's this?";
			} else {
				expl.style.display = "block";
				ele.innerHTML = "Hide";
			}
		}
		</script>

		<table class="form-table">
			<?php
			// Layovers - Simple Colorbox
			if ( is_dir( WP_PLUGIN_DIR . '/simple-colorbox' ) ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e( 'Image overlays', 'pressabl_admin_lang' ); ?></th> 
				<td>
					<label>
						<input name="pressabl_admin_features[simple-colorbox]" type="checkbox"<?php
							if ( 'on' == pressabl_admin_get_option( 'simple-colorbox' ) )
								echo " checked='checked'";
							?> />
						<?php _e( 'Adds a pleasant layover for image links', 'pressabl_admin_lang' ); ?>
					</label>
				</td> 
			</tr> 
			<?php endif; ?>

			<?php
			// Advanced Widgets - Widgets Reloaded
			if ( is_dir( WP_PLUGIN_DIR . '/widgets-reloaded' ) ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e( 'Advanced widgets', 'pressabl_admin_lang' ); ?></th> 
				<td>
					<label> 
						<input name="pressabl_admin_features[advanced-widgets]" type="checkbox"<?php
							if ( 'on' == pressabl_admin_get_option( 'advanced-widgets' ) )
								echo " checked='checked'";
							?> />
						<?php _e( 'Replaces several widgets with advanced versions with more fine grained control of their behaviour.', 'pressabl_admin_lang' ); ?>
					</label>
				</td> 
			</tr> 
			<?php endif; ?>

			<?php
			// WP Grins SSL
			if ( is_dir( WP_PLUGIN_DIR . '/wp-grins-ssl' ) ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e( 'Comment smilies', 'pressabl_admin_lang' ); ?></th> 
				<td>
					<label> 
						<input name="pressabl_admin_features[wp-grins-ssl]" type="checkbox"<?php
							if ( 'on' == pressabl_admin_get_option( 'wp-grins-ssl' ) )
								echo " checked='checked'";
							?> />
						<?php _e( 'Provides clickable smilies for your comment area.', 'pressabl_admin_lang' ); ?>
					</label>
				</td> 
			</tr> 
			<?php endif; ?>

			<?php
			// Web hooks
			if ( is_dir( WP_PLUGIN_DIR . '/hookpress' ) ) : ?>
			<tr valign="top">
				<th scope="row"><?php _e( 'Webhooks', 'pressabl_admin_lang' ); ?></th> 
				<td>
					<label> 
						<input name="pressabl_admin_features[webhooks]" type="checkbox"<?php
							if ( 'on' == pressabl_admin_get_option( 'webhooks' ) )
								echo " checked='checked'";
							?> />
						<?php _e( 'Add support for web hooks.', 'pressabl_admin_lang' ); ?>
					</label>
				</td> 
			</tr> 
			<?php endif; ?>

			<?php // Dropdown menu ?>
			<tr valign="top">
				<th scope="row"><?php _e( 'Dropdown menu', 'pressabl_admin_lang' ); ?></th> 
				<td>
					<label>
						<input name="pressabl_admin_features[script_menu]" type="checkbox"<?php
							if ( 'on' == pressabl_admin_get_option( 'script_menu' ) )
								echo " checked='checked'";
							?> />
						<?php _e( 'Adds a smooth animated effect and a suckerfish script for triggering IE6 support in dropdown menus. Requires the parent UL tag of your menu to have a class of "sf-menu".', 'pressabl_admin_lang' ); ?>
					</label>
				</td> 
			</tr>

			<?php // Slider gallery ?>
			<tr valign="top">
				<th scope="row"><?php _e( 'Slider gallery', 'pressabl_admin_lang' ); ?></th> 
				<td>
					<label>
						<input name="pressabl_admin_features[script_anythingslider]" type="checkbox"<?php
							if ( 'on' == pressabl_admin_get_option( 'script_anythingslider' ) )
								echo " checked='checked'";
							?> />
						<?php _e( 'Adds the "<a href="http://css-tricks.com/">Anything Slider</a>" jQuery plugin by Chris Coyier.', 'pressabl_admin_lang' ); ?>
					</label>
				</td> 
			</tr> 

			<?php // Simple CMS ?>
			<tr valign="top">
				<th scope="row"><?php _e( 'Simple CMS', 'pressabl_admin_lang' ); ?></th> 
				<td>
					<label>
						<input name="pressabl_admin_features[simplecms]" type="checkbox"<?php
							if ( 'on' == pressabl_admin_get_option( 'simplecms' ) )
								echo " checked='checked'";
							?> />
						<?php _e( 'Extraneous admin areas which are unnecessary for a bare bones, basic CMS for a static website are removed from the administration menus. Note: Only affects users at "Editor" level or below.', 'pressabl_admin_lang' ); ?>
					</label>
				</td> 
			</tr> 
<?php /*
			<tr valign="top">
				<th scope="row">
					<?php _e( 'Upload a favicon', 'pressabl_admin_lang' ); ?>
					<small><a href="#" onclick="javascript:toggle_help(this, 'expl');"><?php _e( 'What\'s this?', 'pressabl_admin_lang' ); ?></a></small>
				</th> 
				<td>
					<p>
						<input name="favicon" type="file" />
						<input type="hidden" name="pressabl_admin_features[favicon]" value="" />
						<style type="text/css">
							.pressabl-favicon {
								position: relative;
								top: 3px;
								margin: 0  0 0 10px;
								width: 16px;
								height: 16px;
							}
						</style>
						<?php
						// Display favicon if it already exists
						if ( file_exists( wppb_storage_folder() . '/favicon.ico' ) )
							echo "<img class='pressabl-favicon' src='" .  wppb_storage_folder( '', 'url' ) . "/favicon.ico' alt='' />";
						elseif ( file_exists( wppb_storage_folder() . '/favicon.png' ) )
							echo "<img class='pressabl-favicon' src='" .  wppb_storage_folder( '', 'url' ) . "/favicon.png' alt='' />";
						elseif ( file_exists( wppb_storage_folder() . '/favicon.gif' ) )
							echo "<img class='pressabl-favicon' src='" .  wppb_storage_folder( '', 'url' ) . "/favicon.gif' alt='' />";
						?>
						<div id="expl" style="display:none;"> 
							<h3><?php _e( 'Explanation', 'pressabl_admin_lang' ); ?></h3> 
							<p>
								<?php _e( "A favicon (short for favorites icon), also known as a shortcut icon, website icon, 
								URL icon, or bookmark icon is a 16×16 pixel square icon associated with your website.
								Browsers typically display a page\'s favicon in the browser's address bar, next to 
								the page's name in a list of bookmarks and next to the page's title on a browser tab.
								", 'pressabl_admin_lang' ); ?>
							</p>
							<p>
								<?php _e( "Only favicons with a file format of .ico will work in Internet Explorer. You can upload
								a PNG, GIF or JPG file here if you like, but to ensure that your favicon shows up for
								the broadest selection of browsers, you should first convert your 16x16 image to the .ico
								file format. <a href='http://tools.dynamicdrive.com/favicon/'>Dynamic Drive</a> provides a
								handy converter tool for creating .ico files.
								", 'pressabl_admin_lang' ); ?>
							</p>
							<img src="<?php echo PRESSABL_ADMIN_URL; ?>favicon.png" alt="" />
						</div>
					</p>
				</td> 
			</tr>
			*/ ?>
		</table>

		<input type="hidden" name="action" value="update" />
		<div style="clear:both;padding-top:20px;"></div>
		<p class="submit"><input type="submit" name="Submit" value="<?php _e( 'Update Options', 'pressabl_admin_lang' ) ?>" /></p>

		<div style="clear:both;padding-top:20px;"></div>
	</form>
</div>


<?php }

/**
 * Loads the admin page
 * @since 0.1
 */
function pressabl_admin_help() {
	?>
<div class="wrap">
<?php screen_icon( 'pressabl-admin' ); ?>

<h2><?php _e( 'Help', 'pressabl_admin_lang' ); ?></h2>

<h3><?php _e( 'Shortcodes', 'pressabl_admin_lang' ); ?></h3>

<p>
	<?php _e( 'Below is a comprehensive list of all of the shortcodes provided by the Pressabl. You may also use shortcodes provided with WordPress itself.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[admin_note]</h3>
<p>
	<?php _e( 'Any text held within this shortcode will not be displayed to anyone but those who can publish posts.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[admin_note]A message from one site admin to another site admin.[/admin_note]</code>

<h3>[category_description]</h3>
<p>
	<?php _e( 'Returns the description of a category defined in the category settings screen for the current category (Posts > Categories).', 'pressabl_admin_lang' ); ?>
</p>

<h3>[comment_form]</h3>
<p>
	<?php _e( 'This tag outputs a complete commenting form for use within a template. The contents of the comment form can be edited via the comments template.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[comment_navigation]</h3>
<?php _e( 'TBA', 'pressabl_admin_lang' ); ?>
</p>

<h3>[comments_number]</h3>
<p>
	<?php _e( 'Displays the total number of comments, Trackbacks, and Pingbacks for the current post. This tag must be within The Loop.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[copyright]</h3>
<p>
	<?php _e( 'Displays the copyright notice in the footer of the theme. If the [copyright] shortcode is not loaded somewhere on each page, then a copyright notice will be automatically added at the end of hte page.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[get_archives]</h3>
<p>
	<?php _e( 'The [get_archives] shortcode displays a date-based archives list. This shortcode can be used anywhere within a template.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>type</strong> - <?php _e( 'The type of archive list to display. Choose from "monthly", "daily", "weekly", "postbypost" (posts ordered by post date), "alpha" (same as postbypost but posts are ordered by post title).', 'pressabl_admin_lang' ); ?></li>
	<li><strong>limit</strong> - <?php _e( 'Number of archives to get. Default is no limit.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>show_post_count</strong> - <?php _e( 'Display number of posts in an archive or do not. For use with all <strong>type</strong> except \'postbypost\'.', 'pressabl_admin_lang' ); ?></li>
</ul>

<h3>[get_avatar]</h3>
<p>
	<?php _e( 'Retrieve the avatar for a user. Most commonly used in the comments section.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>size</strong> - <?php _e( 'The size (in px) of the image to return.', 'pressabl_admin_lang' ); ?></li>
</ul>

<h3>[get_header]</h3>
<p>
	<?php _e( 'Includes the header template from the "includes" tab.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[get_footer]</h3>
<p>
	<?php _e( 'Includes the footer template from the "includes" tab.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[get_template_directory_uri]</h3>
<p>
	<?php _e( 'Displays the URL to the template directory.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[if]</h3>
<p>
	<?php _e( 'The [if] shortcode can be used in your templates to change what content is displayed and how that content is 
	displayed on a particular page depending on what conditions that page matches. For example, you might want to 
	display a snippet of text above the series of posts, but only on the main page of your site. With the 
	is_home() Conditional Tag, that task is made easy.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>condition</strong> - <?php _e( 'Used to specify what you are checking for. The following options are avaialble is_page, is_category, is_single, is_tag, is_author, in_category, is_sticky, is_date, is_year, is_month, is_day, is_time, is_archive, is_search, is_404, is_user_logged_in, is_paged, is_attachment, is_singular, comments_open, has_tag, is_page_template, is_preview, pings_open, is_trackback, post_password_required, have_comments. Adding ! to the front of any of the options will cause it to check for the reverse option, ie [if condition=!is_page]this is not a page[/if] will display "this is not a page" if it used on a non-page template.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>slug</strong> - <?php _e( 'Used to specify particular slugs to check for. This works with the following optiosn is_page, is_category, is_single, is_tag, is_author, in_category.', 'pressabl_admin_lang' ); ?></li>
</ul>

<h3>[list_comments]</h3>
<p>
	<?php _e( 'Displays all comments for a post or Page.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[loginout]</h3>
<p>
	<?php _e( 'Adds a login or logout link.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[loop]</h3>
<p>
	<?php _e( 'The [loop] shortcode is a term that refers to the main process of WordPress. You use The 
	Loop in your template files to show posts to visitors.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>category_name</strong> - <?php _e( 'Only displays posts from the category name specified.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>tag</strong> - <?php _e( 'Only displays posts from the tag specified.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>author_name</strong> - <?php _e( 'Only displays posts from the author specified.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>name</strong> - <?php _e( 'Only displays a post with the specified slug.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>pagename</strong> - <?php _e( 'Only displays a static page with the specified slug.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>post_type</strong> - <?php _e( 'Only displays a particular post-type (typically "post" or "page").', 'pressabl_admin_lang' ); ?></li>
	<li><strong>posts_per_page</strong> - <?php _e( 'Controls the number of posts displayed per page. Use \'posts_per_page\' => -1 to show all posts.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>offset</strong> - <?php _e( 'You can displace or pass over one or more initial posts which would normally be collected by your query through the use of the offset parameter.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>orderby</strong> - <?php _e( 'Sort retrieved posts by this field. Options include orderby=author, orderby=date, orderby=title, orderby=modified orderby=menu_order, orderby=parent, orderby=ID, orderby=rand, orderby=meta_value, orderby=meta_value_num, orderby=none and orderby=comment_count.', 'pressabl_admin_lang' ); ?></li>
	<li><strong>order</strong> - <?php _e( 'Designates the ascending or descending order of the ORDERBY parameter. Options include order=ASC or order=DESC.', 'pressabl_admin_lang' ); ?></li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[loop post_type="post" posts_per_page="6" orderby="author" order="ASC"]</code>

<h3>[nav_menu]</h3>
<p>
	<?php _e( 'Displays a navigation menu created in the "Appearance" -> "Menus" panel. To use this feature you must activate either the primary, secondary or both menu types via the "Extras" tab.', 'pressabl_admin_lang' ); ?>
<h4><?php _e( 'Options:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>theme_location</strong> - <?php _e( 'You can choose to use either the primary or secondary theme location.', 'pressabl_admin_lang' ); ?></li>
</ul>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[nav_menu theme_location=primary]</code>

<h3>[next_posts_link]</h3>
<p>
	<?php _e( 'Prints a link to the next set of posts.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[page_menu]</h3>
<p>
	<?php _e( 'Displays a list of WordPress Pages as links, along with the home page.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>include</strong> - Only list pages identified with the corresponding id's</li>
	<li><strong>exclude</strong> - Define a comma-separated list of Page IDs to be excluded from the list</li>
	<li><strong>show_home</strong> - Add "Home" as the first item in the list of "Pages". Choices are "true" (default) or "false". The URL assigned to "Home" is pulled from the Site address (URL) in Administration > Settings > General.</li>
	<li><strong>link_before</strong> - Sets the text or html that proceeds the link text inside <em>&lt;a&gt;</em> tag.</li>
	<li><strong>link_after</strong> - Sets the text or html that follows the link text inside <em>&lt;a&gt;</em> tag.</li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>&lt;ul class="menu"&gt;[page_menu include="1,4,7" showhome="true"]&;t;/ul&gt;</code>

<h3>[numeric_pagination]</h3>
<p>
	<?php _e( 'Displays a numerical pagination list.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[numeric_pagination]</code>

<h3>[post_class]</h3>
<p>
	<?php _e( 'Allows authors to style more effectively with CSS. This shortcode prints out different post container classes which can be added, typically, in the index, single, and other templates featuring post content.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>&lt;div class="[post_class]"&gt;</code>

<h3>[post_thumbnail]</h3>
<p>
	<?php _e( 'Displays a post thumbnail when activated via the "Extras" tab.', 'pressabl_admin_lang' ); ?>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>width</strong> - <?php _e( 'sets the width of the image', 'pressabl_admin_lang' ); ?></li>
	<li><strong>height</strong> - <?php _e( 'sets the height of the image', 'pressabl_admin_lang' ); ?></li>
</ul>
</p>

<h3>[previous_posts_link]</h3>
<p>
	<?php _e( 'Prints a link to the previous set of posts within the current query.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[return]</h3>
<p>
	<?php _e( 'Creates a PHP return. Generally only useful within the comments section for ending the comments template early.', 'pressabl_admin_lang' ); ?>
</p>

<h3>[the_tags]</h3>
<p>
	<?php _e( 'Displays a link to the tag or tags a post belongs to. If no tags are associated with the current entry, the associated category is displayed instead. This tag should be used within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>separator</strong> - <?php _e( 'Text or character to display between each tag link. The default is a comma (,) between each tag.', 'pressabl_admin_lang' ); ?></li>
</ul>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_tags separator=", "]</code>


<h3>[the_category]</h3>
<p>
	<?php _e( 'Displays a link to the category or categories a post belongs to. This tag must be used within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>separator</strong> - <?php _e( 'Text or character to display between each tag link. The default is a comma (,) between each category.', 'pressabl_admin_lang' ); ?></li>
</ul>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_category separator=", "]</code>


<h3>[the_content]</h3>
<p>
	<?php _e( 'Displays the contents of the current post. This tag must be within The_Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>&lt;div class="content"&gt;[the_content]&lt;/div&gt;</code>

<h3>[the_excerpt]</h3>
<p>
	<?php _e( 'Displays the excerpt of the current post with [...] at the end', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>words</strong> - Limits the number of words displayed</li>
	<li><strong>characters</strong> - Limits the number of characters displayed (will not work when used with "words")</li>
	<li><strong>readmore</strong> - The text displayed when more content is available. Defaults to [...].</li>
	<li><strong>link</strong> - "yes" will add a link from the readmore text to the actual post. Default is "no"</li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_excerpt words="20" readmore="... read more" link="yes"]</code>

<h3>[the_id]</h3>
<p>
	<?php _e( 'Displays the numeric ID of the current post. This tag must be within the loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>&lt;div id="post-[the_ID]"&gt;</code>

<h3>[dropdown_categories]</h3>
<p>
	<?php _e( 'Display or retrieve the HTML dropdown list of categories.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[dropdown_categories]</code>

<h3>[dropdown_pages]</h3>
<p>
	<?php _e( 'Displays a list of pages in a select (i.e dropdown) box with no submit button.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[dropdown_pages]</code>

<h3>[dropdown_users]</h3>
<p>
	<?php _e( 'Create dropdown HTML content of users.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[dropdown_users]</code>

<h3>[list_authors]</h3>
<p>
	<?php _e( "Displays a list of the blog's authors (users), and if the user has authored any posts, the author name is displayed as a link to their posts.", 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[list_authors]</code>

<h3>[list_bookmarks]</h3>
<p>
	<?php _e( 'Displays bookmarks found in the Administration > Links panel.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[list_bookmarks]</code>

<h3>[list_categories]</h3>
<p>
	<?php _e( 'Displays a list of Categories as links. When a Category link is clicked, all the posts in that Category will display on a Category Page using the appropriate Category Template.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>include</strong> - Only include the categories detailed in a comma-separated list by unique ID, in ascending order.</li>
	<li><strong>exclude</strong> - Exclude one or more categories from the results. This parameter takes a comma-separated list of categories by unique ID, in ascending order.</li>
	<li><strong>orderby</strong> - Sort categories alphabetically, by unique Category ID, or by the count of posts in that Category. The default is sort by category name. Valid values: ID, name, slug, count.</li>
	<li><strong>order</strong> - Sort order for categories (either ascending or descending). Valid values: ASC, DESC.</li>
	<li><strong>hide_empty</strong> -  Toggles the display of categories with no posts. The default is "yes".</li>
	<li><strong>hierarchical</strong> - Display sub-categories as inner list items (below the parent list item) or inline. The default is "yes" (display sub-categories below the parent list item).</li>
	<li><strong>number</strong> - Sets the number of Categories to display.</li>
	<li><strong>depth</strong> -This parameter controls how many levels in the hierarchy of Categories are to be included in the list of Categories. The default value is 0 (display all Categories and their children). 0 - All Categories and child Categories (Default). -1 - All Categories displayed in flat (no indent) form (overrides hierarchical). 1 - Show only top level Categories. n - Value of n (some number) specifies the depth (or level) to descend in displaying Categories.</li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[list_categories include="3,4" orderby=name order=ASC hide_empty=yes hierarchical=yes number=20 depth="-1"]</code>

<h3>[list_pages]</h3>
<p>
	<?php _e( 'Provides a simple login form for use anywhere within WordPress. By default, it echoes the HTML immediately.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>include</strong> -  Only include certain Pages in the list generated by wp_list_pages. Like exclude, this parameter takes a comma-separated list of Page IDs.</li>
	<li><strong>exclude</strong> -  Define a comma-separated list of Page IDs to be excluded from the list (example: 'exclude=3,7,31'). There is no default value.</li>
	<li><strong>sort_column</strong> -  Sorts the list of Pages in a number of different ways. The default setting is sort alphabetically by Page title. Possible values: 'post_title' - Sort Pages alphabetically (by title) - default; 'menu_order' - Sort Pages by Page Order. N.B. Note the difference between Page Order and Page ID. The Page ID is a unique number assigned by WordPress to every post or page. The Page Order can be set by the user in the Write>Pages administrative panel. See the example below.; 'post_date' - Sort by creation time; 'post_modified' - Sort by time last modified; 'ID' - Sort by numeric Page ID; 'post_author' - Sort by the Page author's numeric ID; 'post_name' - Sort alphabetically by Post slug.</li>
	<li><strong>sort_order</strong> - Change the sort order of the list of Pages (either ascending or descending). The default is ascending. Valid values: 'ASC' - Sort from lowest to highest (Default). 'DESC' - Sort from highest to lowest.</li>
	<li><strong>number</strong> - Sets the number of Pages to display.</li>
	<li><strong>depth</strong> - This parameter controls how many levels in the hierarchy of pages are to be included in the list generated by wp_list_pages. The default value is 0 (display all pages, including all sub-pages). Possible values: 0 (default) Displays pages at any depth and arranges them hierarchically in nested lists; -1 Displays pages at any depth and arranges them in a single, flat list; 1 Displays top-level Pages only; 2, 3 … Displays Pages to the given depth</li>
	<li><strong>child_of</strong> -  Displays the sub-pages of a single Page only; uses the ID for a Page as the value. Note that the child_of parameter will also fetch "grandchildren" of the given ID, not just direct descendants. Defaults to 0.</li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[list_pages]</code>

<h3>[wp_login_form]</h3>
<p>
	<?php _e( 'Provides a simple login form for use anywhere within WordPress.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[wp_login_form]</code>

<h3>[login_url]</h3>
<p>
	<?php _e( 'Returns the URL that allows the user to log in to the site.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[login_url]</code>

<h3>[loginout]</h3>
<p>
	<?php _e( 'Displays a login link, or if a user is logged in, displays a logout link. An optional, redirect argument can be used to redirect the user upon login or logout.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[loginout]</code>

<h3>[lostpassword_url]</h3>
<p>
	<?php _e( 'Returns the URL that allows the user to retrieve the lost password.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[lostpassword_url]</code>

<h3>[logout_url]</h3>
<p>
	<?php _e( 'Returns the URL that allows the user to log out to the site.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[logout_url]</code>

<h3>[single_post_title]</h3>
<p>
	<?php _e( 'Displays or returns the title of the post when on a single post page (permalink page). This tag can be useful for displaying post titles outside The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[single_post_title]</code>

<h3>[the_shortlink]</h3>
<p>
	<?php _e( 'Used on single post permalink pages, this template tag displays a "URL shortening" url for the current post. This will mean the URL has a format of /?p=1234,', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_shortlink]</code>

<h3>[tag_cloud]</h3>
<p>
	<?php _e( 'Displays a list of tags in what is called a "tag cloud", where the size of each tag is determined by how many times that particular tag has been assigned to posts.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>smallest</strong> -  The smallest tag (lowest count). Default is 8.</li>
	<li><strong>largest</strong> - The largest tag (higest count). Default is 22.</li>
	<li><strong>number</strong> - The number of tags to display.</li>
	<li><strong>orderby</strong> -  Order of the tags. Valid values: 'name'; 'count'</li>
	<li><strong>order</strong> -  Sort order. Valid values - Must be Uppercase: 'ASC' (default), 'DESC' or 'RAND'</li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[tag_cloud smallest=9 largest=60 number=40 orderby=name order=RAND]</code>

<h3>[tag_description]</h3>
<p>
	<?php _e( 'Displays the description of a tag.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[tag_description]</code>

<h3>[the_author]</h3>
<p>
	<?php _e( 'The author of a post can be displayed by using this short code. Must be used within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_author]</code>

<h3>[the_author_link]</h3>
<p>
	<?php _e( 'Displays a link to the Website for the author of a post. The Website field is set in the user\'s profile (Administration > Profile > Your Profile). The text for the link is the author\'s Profile Display name publicly as field. Must be used within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_author_link]</code>

<h3>[the_author_meta]</h3>
<p>
	<?php _e( 'Displays the desired meta data for a user. If this tag is used within The Loop, the user ID value need not be specified, and the displayed data is that of the current post author. A user ID can be specified if this tag is used outside The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>field</strong> -  Field name for the data item to be displayed. Valid values: user_nicename, user_url, display_name, nickname, first_name, last_name, description, jabber, aim, yim.</li>
	<li><strong>userID</strong> - If the user ID fields is used, then this function display the specific field for this user ID.</li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_author_meta field=user_url userID=24]</code>

<h3>[the_author_posts]</h3>
<p>
	<?php _e( 'Displays the total number of posts an author has published. Drafts and private posts aren\'t counted. Must be used within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_author_posts]</code>

<h3>[the_author_posts_link]</h3>
<p>
	<?php _e( 'Displays a link to all posts by an author. The link text is the user\'s Display name publicly as field. Must be used within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_author_posts_link]</code>

<h3>[wp_dropdown_users]</h3>
<p>
	<?php _e( 'xxx', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>show_option_all</strong> -  Causes the HTML for the dropdown to allow you to select All of the users. Valid values: yes or no (or leave blank).</li>
	<li><strong>show_option_none</strong> -  Causes the HTML for the dropdown to allow you to select NONE of the users. 	Valid values: yes or no (or leave blank).</li>
	<li><strong>orderby</strong> -  Key to sort options by. Valid values: ID, user_nicename and display_name.</li>
	<li><strong>order</strong> -   Sort order for options. Valid values: ASC, DESC.</li>
	<li><strong>include</strong> -  Comma separated list of users IDs to include. For example, 'include=4,12' means users IDs 4 and 12 will be displayed. Defaults to exclude all.</li>
	<li><strong>exclude</strong> -  Comma separated list of users IDs to exclude. For example, 'exclude=4,12' means users IDs 4 and 12 will NOT be displayed. Defaults to exclude nothing..</li>
</ul>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[wp_dropdown_users show_option_all=yes orderby=user_nicename order=DESC include=1,4,6,3]</code>

<h3>[get_calendar]</h3>
<p>
	<?php _e( 'Displays the calendar. Days with posts are styled as such. This tag can be used anywhere within a template.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[get_calendar]</code>

<h3>[the_date]</h3>
<p>
	<?php _e( 'Displays or returns the date of a post, or a set of posts if published on the same day.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_date format="l, F j, Y"]</code>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>format</strong> -  Used for formatting the date. Visit the "<a href="http://codex.wordpress.org/Formatting_Date_and_Time">formatting date and time</a>" post on WP.org for more information.</li>
</ul>

<h3>[edit_tag_link]</h3>
<p>
	<?php _e( 'Displays a link to edit the current tag, if the user is logged in and allowed to edit the tag. It must be within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[edit_tag_link text="Edit tag"]</code>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>Text</strong> -  The link text.</li>
</ul>

<h3>[edit_post_link]</h3>
<p>
	<?php _e( 'Displays a link to edit the current post, if a user is logged in and allowed to edit the post. It must be within The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[edit_post_link text="Edit post"]</code>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>Text</strong> -  The link text.</li>
</ul>

<h3>[edit_comment_link]</h3>
<p>
	<?php _e( 'Displays a link to edit the current comment, if the user is logged in and allowed to edit the comment. It must be within The Loop, and within a comment loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[edit_comment_link text="Edit comment"]</code>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>Text</strong> -  The link text.</li>
</ul>

<h3>[get_shortlink]</h3>
<p>
	<?php _e( 'Displays a "URL shortening" link for the current post. This will mean the URL has a format of /?p=1234.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[get_shortlink]</code>

<h3>[single_cat_title]</h3>
<p>
	<?php _e( 'Displays or returns the category title for the current page. For pages displaying tags rather than categories (e.g. "/tag/geek") the name of the tag is displayed instead of the category. Can be used only outside The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[single_cat_title]</code>

<h3>[single_month_title]</h3>
<p>
	<?php _e( 'Displays the month and year title for the current page. This shortcode only works when the m or archive month argument has been passed to the current page (this occurs when viewing a monthly archive page). <strong>Note:</strong> This tag works only on date archive pages, not on category templates or others.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[single_month_title text=" : "]</code>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>Text</strong> -  Text which seperates the month from the year.</li>
</ul>

<h3>[single_post_title]</h3>
<p>
	<?php _e( 'Displays the title of the post when on a single post page (permalink page). This tag can be useful for displaying post titles outside The Loop.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[single_post_title text="Current post: "]</code>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>Text</strong> -  The link text.</li>
</ul>

<h3>[single_tag_title]</h3>
<p>
	<?php _e( 'Displays the tag title for the current archive page.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[single_tag_title text="Current tag: "]</code>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>Text</strong> -  The link text.</li>
</ul>

<h3>[the_search_query]</h3>
<p>
	<?php _e( 'Displays the search query for the current request, if a search was made.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[the_search_query]</code>

<h3>[home_url]</h3>
<p>
	<?php _e( 'Displays the home url for the current site.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>[home_url]</code>

<h3>[the_permalink]</h3>
<p>
	<?php _e( 'Displays the URL for the permalink to the post currently being processed in The Loop. This tag must be within The Loop, and is generally used to display the permalink for each post, when the posts are being displayed.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>&lt;a href="[the_permalink]"&gt;Some text here&lt;/a&gt;</code>

<h3>[the_time]</h3>
<p>
	<?php _e( 'Displays the time of the current post. This tag must be used within The Loop.', 'pressabl_admin_lang' ); ?>
<h4><?php _e( 'Parameters:', 'pressabl_admin_lang' ); ?></h4>
<ul>
	<li><strong>format</strong> - <?php _e( 'formats the time to display in. Defaults to the time format configured in your WordPress options. See <a href="http://codex.wordpress.org/Formatting_Date_and_Time">Formatting Date and Time</a>.</li>', 'pressabl_admin_lang' ); ?>
</ul>
</p>

<h3>[the_title]</h3>
<p>
	<?php _e( 'Displays or returns the title of the current post. This tag must be within The Loop. If the post is protected or private, this will be noted by the words "Protected: " or "Private: " prepended to the title.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Usage', 'pressabl_admin_lang' ); ?></h4>
<code>&lt;h2&gt;[the_title]&lt;/h2&gt;</code>

<h3>[siteinfo]</h3>
<p>
	<?php _e( 'Displays information about your site, mostly gathered from the information you supply in your User Profile and General Options from the WordPress Administration panels ("Settings" -> "General"). It can be used anywhere within a page template. This always prints a result to the browser.', 'pressabl_admin_lang' ); ?>
</p>
<h4><?php _e( 'Parameters', 'pressabl_admin_lang' ); ?></h4>
<p>
	<?php _e( 'The "condition" attribute may be used with siteinfo. Below are examples of the output from the various "condition" values:', 'pressabl_admin_lang' ); ?>
</p>
<ul>
	<li><strong>name</strong> - Testpilot</li>
	<li><strong>description</strong> - Just another PixoPoint site</li>
	<li><strong>admin_email</strong> - admin@example</li>
	<li><strong>url</strong> - http://example/home</li>
	<li><strong>wpurl</strong> - http://example/home/wp</li>
	<li><strong>atom_url</strong> - http://example/home/feed/atom</li>
	<li><strong>rss2_url</strong> - http://example/home/feed</li>
	<li><strong>rss_url</strong> - http://example/home/feed/rss</li>
	<li><strong>pingback_url</strong> - http://example/home/wp/xmlrpc.php</li>
	<li><strong>rdf_url</strong> - http://example/home/feed/rdf</li>
	<li><strong>comments_atom_url</strong> - http://example/home/comments/feed/atom</li>
	<li><strong>comments_rss2_url</strong> - http://example/home/comments/feed</li>
</ul>

<h3>[widget]</h3>
<p>
	<?php _e( 'Loads a widget area if activated in the "Extras" tab.', 'pressabl_admin_lang' ); ?>
</p>

</div>
<?php }

/**
 * Parse shortcodes in text widgets
 * From "Allow Shortcodes in Text Widgets v1.0" (http://www.codeandreload.com/wp-plugins/allow-shortcodes-in-text-widgets/) by Robert Wise (http://www.codeandreload.com/)
 * @since 0.1
 */
function pressabl_parseshortcode_in_text_widget(){
	add_filter( 'widget_text', 'do_shortcode' );
	add_filter( 'widget_text', 'convert_smilies' );
}
add_action( 'init', 'pressabl_parseshortcode_in_text_widget' );

/**
 * Tell WordPress to expect a custom menu order for links menu item removal
 * @since 0.1
 */
 function pressabl_links_menu_order(){
	return true;
}
add_filter( 'custom_menu_order', 'pressabl_links_menu_order' );

/**
 * Erase menu items for links menu item removal
 * @since 0.1
 */
function pressabl_links_remove( $menu_order ){
	global $menu;

	foreach ( $menu as $mkey => $m ) {
		$key = array_search( 'link-manager.php', $m );

		if ( $key )
			unset( $menu[$mkey] );
	}

	return $menu_order;
}
add_filter( 'menu_order', 'pressabl_links_remove' );

/**
 * Adds admin menu for links menu item removal
 * @since 0.1
 */
function pressabl_links_options() {

	// Links menu item
	$page = add_dashboard_page(
		'Links',
		'Links',
		'administrator',
		'pressabl_links_page',
		'pressabl_links_redirect'
	);

	// Feedbacks - Grunion Contact Form
	$page = add_dashboard_page(
		'Feedbacks',
		'Feedbacks',
		'administrator',
		'pressabl_feedbacks_page',
		'pressabl_feedbacks_redirect'
	);
}
add_action( 'admin_menu', 'pressabl_links_options' );

/**
 * Admin menu page redirect for links menu item removal
 * @since 0.1
 */
function pressabl_links_redirect() {
	//header( 'HTTP/1.1 301 Moved Permanently' );
	//header( 'Location: ' . admin_url() . 'link-manager.php' );
	//die;
	echo '<meta http-equiv="refresh" content="0;url=' . admin_url() . 'link-manager.php">';
}

/**
 * Admin menu page redirect for Feedback menu item removal
 * @since 0.1
 */
function pressabl_feedbacks_redirect() {
	echo '<meta http-equiv="refresh" content="0;url=' . admin_url() . 'edit.php?post_type=feedback">';
}


/**
 * Loading extra scripts
 * @since 0.1
 */
function pressabl_extra_scripts() {

	// Dropdown menus jQuery script
	if ( 'on' == pressabl_admin_get_option( 'script_menu' ) )
		wp_enqueue_script( 'menu', PRESSABL_ADMIN_URL . 'scripts/menu.js', array( 'jquery' ), 1.0, true );

	// Anything slider jQuery plugin
	if ( 'on' == pressabl_admin_get_option( 'script_anythingslider' ) ) {
		wp_enqueue_script(
			'easing',
			PRESSABL_ADMIN_URL . 'scripts/jquery.easing.1.2.js',
			array( 'jquery' ),
			1.2,
			true
		);
		wp_enqueue_script(
			'anythingslider',
			PRESSABL_ADMIN_URL . 'scripts/jquery.anythingslider.js',
			array( 'jquery' ),
			1.0,
			true
		);
		wp_enqueue_script(
			'pixopoint_sp_init',
			PRESSABL_ADMIN_URL . 'scripts/anythingslider.init.js',
			array( 'jquery' ),
			1.0,
			true
		);
	}
}
add_action( 'wp_print_scripts', 'pressabl_extra_scripts' );
