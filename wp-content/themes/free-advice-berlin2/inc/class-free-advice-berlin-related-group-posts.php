<?php

/**
 * Related posts on the Facebook group.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
class Free_Advice_Berlin_Related_Group_Posts {

	/**
	 * Set some constants for setting options.
	 */
	const META_KEY = '_related_group_posts';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes',    array( $this, 'add_metabox' ) );
		add_action( 'save_post',         array( $this, 'save' ), 10, 2 );
		add_action( 'admin_footer',      array( $this, 'scripts' ) );
		add_action( 'fab_after_content', array( $this, 'related_posts_html' ), 5 );
	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			self::META_KEY, // ID
			__( 'Related Posts', 'plugin-slug' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'page', // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'low'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the meta box.
	 */
	public function meta_box() {

		echo '
		<p>' . __( 'Please enter URLs from the group Facebook group, which are relevant to this document. These will be displayed beneath the document.', 'free-advice-berlin' ) . '</p>

		<table class="form-table">
			<tbody id="add-rows">';

			// Add the existing rows
			$options = get_post_meta( get_the_ID(), self::META_KEY, true );
			if ( is_array( $options ) ) {
				foreach( $options as $key => $value ) {
					echo $this->get_row( $value );
				}
			}

			// Add a new row by default
			echo $this->get_row();

			echo '
			</body>

		</table>

		<input type="button" id="add-new-row" value="' . __( 'Add new row', 'plugin-slug' ) . '" />
		<input type="hidden" id="related-group-posts-nonce" name="related-group-posts-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">';

	}

	/**
	 * Get a single table row.
	 * 
	 * @param  string  $value  Option value
	 * @return string  The table row HTML
	 */
	public function get_row( $value = '' ) {

		// Create the required HTML
		$row_html = '

			<tr class="sortable-related-posts">
				<td>
					<input class="fab-button" type="text" name="' . esc_attr( self::META_KEY ) . '[]" value="' . esc_attr( $value ) . '" />
				</td>
			</tr>';

		// Strip out white space
		$row_html = str_replace( '	', '', $row_html );
		$row_html = str_replace( "\n", '', $row_html );

		// Return the final HTML
		return $row_html;
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST[self::META_KEY] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['related-group-posts-nonce'], __FILE__ ) ) {
				return;
			}

			// Sanitize the data
			foreach ( $_POST[self::META_KEY] as $key => $value ) {
				$key = absint( $key );
				$value = esc_url( $value );
				if ( '' != $value ) {
					$related_posts[$key] = $value;
				}
			}

			// Store the data
			update_post_meta( $post_id, self::META_KEY, $related_posts );
		}

	}

	/**
	 * Output scripts into the footer.
	 * This is not best practice, but is implemented like this here to ensure that it can fit into a single file.
	 */
	public function scripts() {

		// Bail out if not editing a page
		if ( 
			'post-new.php' != str_replace( dirname( $_SERVER['PHP_SELF'] ) . '/', '', $_SERVER['PHP_SELF'] )
			&&
			! isset( $_GET['post'] )
		) {
			return;
		}

		echo '
		<style>
		.sortable-related-posts .toggle {
			display: inline !important;
		}

		.fab-button {
			width: 100%;
		}

		.form-table td {
			padding: 4px 4px 4px 0;
		}

		#add-new-row {
			margin-top: 20px;
		}
		</style>

		<script>

			jQuery(function($){ 

				/**
				 * Adding some buttons
				 */
				function add_buttons() {

					// Loop through each row
					$( ".sortable-related-posts" ).each(function() {

						// If no input field found with class .remove-setting, then add buttons to the row
						if(!$(this).find(".fab-button").hasClass("remove-setting")) {

							// Add a remove button
							$(this).append(\'<td><input type="button" class="remove-setting" value="&#x2715;" /></td>\');

							// Remove button functionality
							$(".remove-setting").click(function () {
								$(this).parent().parent().remove();
							});

						}

					});

				}

				// Create the required HTML (this should be added inline via wp_localize_script() once JS is abstracted into external file)
				var html = \'' . $this->get_row( '' ) . '\';

				// Add the buttons
				add_buttons();

				// Add a fresh row on clicking the add row button
				$( "#add-new-row" ).click(function() {
					$( "#add-rows" ).append( html ); // Add the new row
					add_buttons(); // Add buttons tot he new row
				});

				// Allow for resorting rows
				$("#add-rows").sortable({
					axis: "y", // Limit to only moving on the Y-axis
				});

 			});

		</script>';
	}

	/**
	 * Output the related Facebook group posts to the document.
	 */
	public function related_posts_html() {

		$related_posts = get_post_meta( get_the_ID(), self::META_KEY, true );
		if ( is_array( $related_posts ) ) {

			echo '
			<h3>' . __( 'Related posts from Facebook', 'free-advice-berlin' ) . '</h3>
			<ol id="related-facebook-posts">';

			foreach( $related_posts as $key => $url ) {
				echo '
				<li>
					<a href="' . esc_url( $url ) . '">
						' . esc_url( $url ) . '
					</a>
				</li>';
			}

			echo '
			</ol>';

		}		

	}

}
