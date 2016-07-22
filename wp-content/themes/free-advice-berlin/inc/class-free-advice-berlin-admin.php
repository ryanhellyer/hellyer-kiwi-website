<?php

/**
 * Admin panel changes.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Free_Advice_Berlin_Admin {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'admin_menu',                 array( $this, 'remove_menus' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_links' ) );
		add_action( 'admin_menu',                 array( $this, 'remove_meta_boxes' ) );
	}

	/**
	 * Remove admin bar menus.
	 *
	 * @global object $wp_admin_bar
	 */
	public function remove_admin_bar_links() {
		global $wp_admin_bar;

		$wp_admin_bar->remove_menu( 'comments' );
		$wp_admin_bar->remove_menu( 'new-content' );
		$wp_admin_bar->remove_menu( 'blog-6-n' );
		$wp_admin_bar->remove_menu( 'blog-6-c' );
	
	}
	
	/**
	 * Remove meta boxes.
	*/
	public function remove_meta_boxes() {

		// List of meta boxes
		$meta_boxes = array(
			'trackbacksdiv',
			'postcustom',
//			'postexcerpt',
			'commentsdiv',
			'revisionsdiv',
			'authordiv',
//			'slugdiv',
		);

		// Hiding ability to turn comments off for non-admins
		if ( ! current_user_can( 'manage_options' ) ) {
			$meta_boxes[] = 'commentstatusdiv';
		}

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
	 * Remove menus.
	 * Redirect dashboard.
	 */
	public function remove_menus () {

		// List of items to remove
		$restricted_sub_level = array(
			'index.php'                       => 'TOP',
			'edit-tags.php?taxonomy=category' =>'edit.php', // This doesn't actually do anything since posts aren't present, but left here so that you can see how to remove sub menus if needed in your own projects
			'edit.php'                        => 'TOP',
			'link-manager.php'                => 'TOP',
		);

		// Allow tools menu for super admins only
		if ( ! is_super_admin() ) {
			$restricted_sub_level['tools.php'] = 'TOP';
		}

		foreach( $restricted_sub_level as $page => $top ) {
	
			// If a top leve page, then remove whole block
			if ( 'TOP' == $top ) {
				remove_menu_page( $page );
			} else {
				remove_submenu_page( $top, $page );
			}
	
		}
	
		// Redirect from dashboard to edit pages - Thanks to WP Engineer for this code snippet ... http://wpengineer.com/redirects-to-another-page-in-wordpress-backend/
		if ( preg_match( '#wp-admin/?(index.php)?$#', esc_url( $_SERVER['REQUEST_URI'] ) ) ) {
			wp_redirect( admin_url( 'edit.php?post_type=page' ) );
		}
	
	}

}
