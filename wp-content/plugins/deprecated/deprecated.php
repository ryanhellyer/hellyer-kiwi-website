<?php
/*

Plugin Name: Deprecated
Plugin URI: http://geek.ryanhellyer.net/products/deprecated/
Description: Adds a deprecated message to the post content
Author: Ryan Hellyer
Version: 1.0
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2014 Ryan Hellyer


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/


/**
 * Add Deprecated Meta Box to "post" and "page" post types
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Add_Deprecated_Metabox {

	/*
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
		add_action( 'the_content',    array( $this, 'deprecated_notice' ) );
	}

	/*
	 * Deprecated notice
	 */
	public function deprecated_notice( $content ) {
		if ( 1 == get_post_meta( get_the_ID(), '_deprecated', 1 ) ) {
			$content = '
			<p style="background:#ff9999;border:1px solid #ff0000;padding:0.5em 1em;color:#000;-moz-border-radius:8px;border-radius:8px;">
				Sorry, but there are better ways to do this now. This project has been deprecated and is no longer in development.
				This page will be kept online for archival purposes and in case anyone is still interested in the project despite being no longer maintained.
			</p>' . $content;
		}

		return $content;
	}

	/**
	 * Add admin metabox for thumbnail chooser
	 */
	public function add_metabox() {
		$post_types = array( 'post', 'page' );
		foreach( $post_types as $post_type ) {
			add_meta_box(
				'deprecated-projects', // ID
				__( 'Deprecated post', 'deprecated' ), // Title
				array(
					$this,
					'meta_box', // Callback to method to display HTML
				),
				$post_type, // Post type
				'side', // Context, choose between 'normal', 'advanced', or 'side'
				'low'  // Position, choose between 'high', 'core', 'default' or 'low'
			);
		}
	}

	/**
	 * Output the thumbnail meta box
	 */
	public function meta_box() {
		?>
		<p>
			<?php _e( 'You may set this post as "deprecated", which will add a notice to the top of the page.', 'deprecated' ); ?>
		</p>
		<p>
			<label for="_deprecated"><strong><?php _e( 'This a deprecated post?', 'deprecated' ); ?></strong></label>
			&nbsp;
			<input <?php checked( get_post_meta( get_the_ID(), '_deprecated', 1 ), true ); ?> type="checkbox" name="_deprecated" id="_deprecated" value="1" />
			<input type="hidden" name="_deprecated_nonce" value="<?php echo wp_create_nonce( __FILE__ ); ?>">
		</p><?php
	}

	/**
	 * Save opening times meta box data
	 */
	function meta_boxes_save( $post_id, $post ) {

		// Do nonce security check
		if ( isset( $_POST['_deprecated_nonce'] ) && ! wp_verify_nonce( $_POST['_deprecated_nonce'], __FILE__ ) ) {
			return;
		}

		// Sanitizing data
		if ( isset( $_POST['_deprecated'] ) ) {
			$deprecated = absint( $_POST['_deprecated'] ); // Sanitise data input
			update_post_meta( $post_id, '_deprecated', $deprecated ); // Store the data
		} else {
			delete_post_meta( $post_id, '_deprecated' ); // Remove the data
		}

	}
 
}
new Add_Deprecated_Metabox;
