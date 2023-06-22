<?php
/*
Plugin Name: Secure Admin
Plugin URI: http://geek.ryanhellyer.net/
Description: Used on secure.hellyer.kiwi

Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.ryanhellyer.net/

Copyright 2014 Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

*/

add_action( 'page_submitbox_misc_actions' , 'wpse118970_change_visibility_metabox_value' );
function wpse118970_change_visibility_metabox_value(){
    global $post;
    if ($post->post_type != 'page')
        return;
    $post->post_password = '';
    $visibility = 'private';
    $visibility_trans = __('Private');
    ?>
    <script type="text/javascript">
        (function($){
            try {
                $('#page-visibility-display').text('<?php echo $visibility_trans; ?>');
                $('#hidden-page-visibility').val('<?php echo $visibility; ?>');
                $('#visibility-radio-<?php echo $visibility; ?>').attr('checked', true);
            } catch(err){}
        }) (jQuery);
    </script>
    <?php
}

/**
 * Ryans Secure Admin
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Ryans_Secure_Admin {

	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks
	 */
	public function __construct() {

		// Disable things via definitions or filters
//		define( 'WP_POST_REVISIONS', false ); // We don't want post revisions in case we accidentally stash something important
//		define( 'AUTOSAVE_INTERVAL', 1000 * 60 *60 * 60 * 24 * 365 ); // Auto save once per thousand years
		add_filter( 'user_can_richedit' , function() {return false;} , 50 ); // Disable the visual editor since it goofs up encrypted shit

		// Add action hooks
		add_action( 'template_redirect',          array( $this, 'redirect_from_frontend' ) );
		add_action( 'admin_menu',                 array( $this, 'remove_menus' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_links' ) );
		add_action( 'admin_menu',                 array( $this, 'remove_meta_boxes' ) );
		add_action( 'admin_head',                 array( $this, 'css' ) );
		add_action( 'do_meta_boxes',              array( $this, 'remove_featured_image_box' ) );
		add_action( 'admin_menu',                 array( $this, 'remove_page_attribute_meta_box' ) );
	}

	/**
	 * Redirect all users away from front end of the site
	 */
	public function redirect_from_frontend() {
		wp_redirect( admin_url(), 302 );
		exit;
	}

	/**
	 * Hide stuff with CSS
	 */
	public function css() {
		echo '
		<style>
		#wp-admin-bar-site-name {
			display: none;
		}
		</style>';
	}

	/**
	 * Remove admin bar menus
	 *
	 * @global array $wp_admin_bar
	 */
	public function remove_admin_bar_links() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'comments' );
		$wp_admin_bar->remove_menu( 'new-content' );
		$wp_admin_bar->remove_menu( 'blog-6-n' );
		$wp_admin_bar->remove_menu( 'blog-6-c' );

	}
	
	/**
	 * Remove meta boxes
	*/
	public function remove_meta_boxes() {

		// List of meta boxes
		$meta_boxes = array(
			'commentsdiv',
			'trackbacksdiv',
			'postcustom',
//			'postexcerpt',
			'commentstatusdiv',
			'commentsdiv',
		);

		// Removing the meta boxes
		foreach( $meta_boxes as $box ) {
			remove_meta_box(
				$box, // ID of meta box to remove
				'page', // Post type
				'normal' // Context
			);
		}
	
	}

	/**
	 * Remove menus
	 * Redirect dashboard
	 */
	public function remove_menus () {

		// List of items to remove
		$restricted_sub_level = array(
			'options-general.php'             => 'TOP',
			'themes.php'                      => 'TOP',
			'users.php'                       => 'TOP',
			'index.php'                       => 'TOP',
			'my-sites.php'                    => 'index.php',
			'edit-tags.php?taxonomy=category' =>'edit.php', // This doesn't actually do anything since posts aren't present, but left here so that you can see how to remove sub menus if needed in your own projects
			'edit.php'                        => 'TOP',
			'edit-comments.php'               => 'TOP',
			'tools.php'                       => 'TOP',
			'link-manager.php'                => 'TOP',
		);
		foreach( $restricted_sub_level as $page => $top ) {
	
			// If a top leve page, then remove whole block
			if ( 'TOP' == $top )
				remove_menu_page( $page );
			else
				remove_submenu_page( $top, $page );
	
		}
	
		// Redirect from dashboard to edit pages - Thanks to WP Engineer for this code snippet ... http://wpengineer.com/redirects-to-another-page-in-wordpress-backend/
		if ( preg_match( '#wp-admin/?(index.php)?$#', esc_url( $_SERVER['REQUEST_URI'] ) ) )
			wp_redirect( admin_url( 'edit.php?post_type=page' ) );
	
	}

	/**
	 * Remove the featured image meta box
	 */
	public function remove_featured_image_box() {
		remove_meta_box( 'postimagediv', 'page', 'side' );
	}

	/**
	 * Remove page attribute meta box
	 */
	public function remove_page_attribute_meta_box() {
		remove_meta_box( 'pageparentdiv', 'page', 'normal' );
	}

}
new Ryans_Secure_Admin();
