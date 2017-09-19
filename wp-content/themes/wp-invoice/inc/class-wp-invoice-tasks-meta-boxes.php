<?php

/**
 * Tasks meta boxes.
 * Repeateable meta boxes.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class WP_Invoice_Tasks_Meta_Boxes extends WP_Invoice_Core {

	const TASKS_KEY = '_tasks';

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {
		parent::__construct();

		// Add to hooks
		add_action( 'admin_footer',  array( $this, 'scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'tasks', // ID
			__( 'Tasks', 'plugin-slug' ), // Title
			array(
				$this,
				'admin_page', // Callback to method to display HTML
			),
			self::INVOICE_POST_TYPE, // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'default'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Enqueuing required scripts.
	 */
	public function enqueue_scripts() {
		
		if (
			(
				( strpos( $_SERVER[ 'REQUEST_URI' ], 'post-new.php') !== false )
				||
				( strpos( $_SERVER[ 'REQUEST_URI' ], 'post.php') !== false )
			)
			&&
			isset( $_GET['post_type'] ) && 'invoice' == $_GET['post_type']
		) {
			wp_enqueue_script( 'jquery-ui-sortable' );
		}

	}

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		?>
		<div class="wrap">

			<form method="post" action="options.php">

				<div id="add-rows" class="form-table"><?php

				// Grab options array and output a new row for each setting
				$options = get_post_meta( get_the_ID(), self::META_KEY, true );
				if ( isset( $options[self::TASKS_KEY] ) ) {
					$options = $options[self::TASKS_KEY];
				} else {
					$options = array();
				}

				if ( is_array( $options ) ) {
					foreach( $options as $key => $value ) {
						echo $this->get_row( $value );
					}
				}

				// Add a new row by default
				echo $this->get_row();
				?>

				</div>

				<input type="button" id="add-new-row" value="<?php _e( 'Add new row', 'plugin-slug' ); ?>" />

				<input type="hidden" id="<?php echo self::META_KEY; ?>-nonce" name="<?php echo self::META_KEY; ?>-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>">
			</form>

		</div><?php
	}

	/**
	 * Get a single table row.
	 * 
	 * @param  string  $value  Option value
	 * @return string  The table row HTML
	 */
	public function get_row( $value = '' ) {

		// Ensuring values are set
		foreach ( $this->possible_keys as $key => $field ) {
			if ( ! isset( $value[ $key ] ) ) {
				$value[ $key ] = '';
			}
		}

		// Create the required HTML
		$row_html = '<p class="sortable">';

		foreach ( $this->possible_keys as $key => $field ) {
			$label = $field['label'];
			$type  = $field['type'];

			if ( 'textarea' == $type ) {
				$row_html .= '<textarea type="text" name="' . esc_attr( self::META_KEY ) . '_' . $key . '[]">' . esc_textarea( $value[ $key ] ) . '</textarea>';
			} elseif ( 'number' == $type ) {
				$row_html .= '<input step="0.01" type="' . esc_attr( $type ) . '" name="' . esc_attr( self::META_KEY ) . '_' . $key . '[]" value="' . esc_attr( $value[ $key ] ) . '" />';
			} else {
				$row_html .= '<input type="' . esc_attr( $type ) . '" name="' . esc_attr( self::META_KEY ) . '_' . $key . '[]" value="' . esc_attr( $value[ $key ] ) . '" />';
			}
			$row_html .= '<label>' . esc_html( $label ) . '</label><br />';
		}

		$row_html .= '</p>';

		// Strip out white space
		$row_html = str_replace( '  ', '', $row_html );
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
	public function meta_boxes_save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST[self::META_KEY . '-nonce'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST[self::META_KEY . '-nonce'], __FILE__ ) ) {
				return;
			}
//print_r( $_POST );
//echo "\n\n\n...............\n\n\n";
			foreach ( $this->possible_keys as $key => $label ) {
				if ( isset( $_POST[ self::META_KEY . '_' . $key ] ) ) {
					$data[ $key ] = $_POST[ self::META_KEY . '_' . $key ];
				} else {
					$data[ $key ] = '';
				}
			}

			// Sanitize the data
			$result = $this->sanitize( $data );

			// Save data inside existing array
			$data = get_post_meta( $post_id, self::META_KEY, true );
			$data[ self::TASKS_KEY ] = $result;
			update_post_meta( $post_id, self::META_KEY, $data );
		}

	}

	/**
	 * Output scripts into the footer.
	 * This is not best practice, but is implemented like this here to ensure that it can fit into a single file.
	 */
	public function scripts() {

		if (
			(
				( strpos( $_SERVER[ 'REQUEST_URI' ], 'post-new.php') !== false )
				||
				( strpos( $_SERVER[ 'REQUEST_URI' ], 'post.php') !== false )
			)
			&&
			'invoice' == get_post_type()
		) {
		?>
<style>
.read-more-text {
	display: none;
}
.sortable .toggle {
	display: inline !important;
}
</style>
		<script>

			jQuery(function($){ 

				// Create the required HTML (this should be added inline via wp_localize_script() once JS is abstracted into external file)
				var html = '<?php echo $this->get_row( '' ); ?>';

				// Add a remove button
				$('.sortable').append('<br /><input type="button" class="remove-setting" value="remove" />');
				$('.remove-setting').click(function () {
					$(this).parent().remove();   
				});

				// Add read more button
				$('.sortable').append('<input type="button" class="read-more" value="read more" />');
				$('.read-more-text').css('display','none');
				$(".read-more").click(function(){
					$(this).parent().find('.read-more-text').toggleClass('toggle');
				});

				// Add a fresh row on clicking the add row button
				$( "#add-new-row" ).click(function() {
					$( "#add-rows" ).append( html );
				});

				// Allow for resorting rows
				$('#add-rows').sortable({
					axis: "y", // Limit to only moving on the Y-axis
				});

			});

		</script><?php
		}
	}

}
new WP_Invoice_Tasks_Meta_Boxes;
