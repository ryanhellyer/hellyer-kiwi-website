<?php

/**
 * Add Meta Box to "page" post type
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Arctic_Fox_Admin {
	
	/*
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'admin_init',     array( $this, 'meta_boxes_save' ) );
	}
	
	/**
	 * Add admin metabox for thumbnail chooser
	 */
	public function add_metaboxes() {
		add_meta_box(
			'arctic-fox-format', // ID
			__( 'Format', 'arctic-fox' ), // Title
			array(
				$this,
				'format_meta_box', // Callback to method to display HTML
			),
			'page', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
		add_meta_box(
			'arctic-fox-heading', // ID
			__( 'Heading', 'arctic-fox' ), // Title
			array(
				$this,
				'heading_meta_box', // Callback to method to display HTML
			),
			'page', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
		add_meta_box(
			'arctic-fox-subheading', // ID
			__( 'Sub-heading', 'arctic-fox' ), // Title
			array(
				$this,
				'subheading_meta_box', // Callback to method to display HTML
			),
			'page', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
		add_meta_box(
			'arctic-fox-more', // ID
			__( 'More link', 'arctic-fox' ), // Title
			array(
				$this,
				'more_meta_box', // Callback to method to display HTML
			),
			'page', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the sub-heading meta box
	 */
	public function more_meta_box() {
		global $post;

		// Get post ID
		if ( isset( $_GET['post'] ) )
			$post_ID = (int) $_GET['post'];
		else
			$post_ID = '';

		// Output input fields
		?>
		<p>
			<input type="text" name="_more_big" id="_more_big" value="<?php echo esc_attr( get_post_meta( $post_ID, '_more_big', true ) ); ?>" />
			<label for="_more"><?php _e( 'Big text', 'arctic-fox' ); ?></label>
		</p>
		<p>
			<input type="text" name="_more_small" id="_more_small" value="<?php echo esc_attr( get_post_meta( $post_ID, '_more_small', true ) ); ?>" />
			<label for="_more_small"><?php _e( 'Small text', 'arctic-fox' ); ?></label>
		</p>
		<p>
			<select name="_more_character" id="_more_character"><?php
				$character_options = array(
					'plus'         => '+&#47;-',
					'single-arrow' => '&uarr;&#47;&darr;',
					'double-arrow' => '&uArr;&#47;&dArr;',
					'wedge'        => '&and;&#47;&or;',
				);
				foreach( $character_options as $key => $value ) {
					if ( $key == get_post_meta( $post_ID, '_more_character', true ) ) {
						$selected = ' selected="selected"';
					} else {
						$selected = '';
					}
					echo '<option' . $selected . ' value="' . $key . '">' . $value . '</option>';
				}
				?>
			</select><!--
			<?php //echo ; ?>" />
			-->
			<label for="_more_character"><?php _e( 'Character', 'arctic-fox' ); ?></label>
		</p><?php
	}

	/**
	 * Output the sub-heading meta box
	 */
	public function subheading_meta_box() {
		global $post;

		// Get post ID
		if ( isset( $_GET['post'] ) )
			$post_ID = (int) $_GET['post'];
		else
			$post_ID = '';

		// Output input fields
		?>
		<p>
			<input type="text" name="_subheading" id="_subheading" value="<?php echo esc_attr( get_post_meta( $post_ID, '_subheading', true ) ); ?>" />
			<label for="_subheading"><?php _e( 'Sub-heading', 'arctic-fox' ); ?></label>
		</p><?php
	}

	/**
	 * Output the format meta box
	 */
	public function format_meta_box() {
		global $post;

		// Get post ID
		if ( isset( $_GET['post'] ) )
			$post_ID = (int) $_GET['post'];
		else
			$post_ID = '';

		// Get column width
		$_column_width = get_post_meta( $post_ID, '_column_width', true );
		if ( '' == $_column_width ) {
			$_column_width = 'wide';
		}

		// Output input fields
		?>
		<p>
			<input type="radio" name="_column_width" id="_column_width_wide" value="wide" <?php checked( $_column_width, 'wide' ); ?> />
			<label for="_column_width_wide"><?php _e( 'Wide', 'arctic-fox' ); ?></label>
		</p>
		<p>
			<input type="radio" name="_column_width" id="_column_width_skinny" value="skinny" <?php checked( $_column_width, 'skinny' ); ?> />
			<label for="_column_width_skinny"><?php _e( 'Skinny', 'arctic-fox' ); ?></label>
		</p><?php
	}

	/**
	 * Output the format meta box
	 */
	public function heading_meta_box() {
		global $post;

		// Get post ID
		if ( isset( $_GET['post'] ) )
			$post_ID = (int) $_GET['post'];
		else
			$post_ID = '';

		// Get column width
		$_heading = get_post_meta( $post_ID, '_heading', true );

		// Output input fields
		?>
		<input type="hidden" name="_heading_submitted" value="yep" />
		<p>
			<input type="checkbox" name="_heading" id="_heading" value="on" <?php checked( $_heading, 'on' ); ?> />
			<label for="_heading"><?php _e( 'Display heading?', 'arctic-fox' ); ?></label>
		</p><?php
	}

	/**
	 * Save opening times meta box data
	 */
	public function meta_boxes_save() {

		// Only process if the form has actually been submitted
		if (
			isset( $_POST['_wpnonce'] ) &&
			isset( $_POST['post_ID'] )
		) {

			// Do nonce security check
			wp_verify_nonce( '_wpnonce', $_POST['_wpnonce'] );

			// Grab post ID
			$post_ID = (int) $_POST['post_ID'];

			// Sanitizing and store data
			if ( isset( $_POST['_more_big'] ) ) {
				$_more_big = esc_html( $_POST['_more_big'] ); // Sanitise data input
				update_post_meta( $post_ID, '_more_big', $_more_big ); // Store the data
			}

			// Sanitizing and store data
			if ( isset( $_POST['_more_character'] ) ) {
				$_more_character = esc_html( $_POST['_more_character'] ); // Sanitise data input
				update_post_meta( $post_ID, '_more_character', $_more_character ); // Store the data
			}

			// Sanitizing and store data
			if ( isset( $_POST['_more_small'] ) ) {
				$_more_small = esc_html( $_POST['_more_small'] ); // Sanitise data input
				update_post_meta( $post_ID, '_more_small', $_more_small ); // Store the data
			}

			// Sanitizing and store data
			if ( isset( $_POST['_column_width'] ) ) {
				$_column_width = esc_html( $_POST['_column_width'] ); // Sanitise data input
				update_post_meta( $post_ID, '_column_width', $_column_width ); // Store the data
			}

			// Sanitizing and store data
			if ( isset( $_POST['_heading_submitted'] ) ) {
				if ( isset( $_POST['_heading'] ) )
					$_heading = 'on';
				else
					$heading = '';
				update_post_meta( $post_ID, '_heading', $_heading ); // Store the data
			}

			// Sanitizing and store data
			if ( isset( $_POST['_subheading'] ) ) {
				$_subheading = esc_html( $_POST['_subheading'] );
				update_post_meta( $post_ID, '_subheading', $_subheading ); // Store the data
			}
		}

	}

}
new Arctic_Fox_Admin;
