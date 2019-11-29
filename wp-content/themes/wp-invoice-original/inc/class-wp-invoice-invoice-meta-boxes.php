<?php

/**
 * Add Meta Boxes.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class WP_Invoice_Invoice_Meta_Boxes extends WP_Invoice_Core {

	var $fields;

	/*
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
	}

	/**
	 * Add admin metabox.
	 */
	public function add_metaboxes() {

		add_meta_box(
			'invoice-metabox', // ID
			__( 'Data', 'plugin-slug' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			self::INVOICE_POST_TYPE, // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

		add_meta_box(
			'link-to-invoice-metabox', // ID
			__( 'Link to invoice', 'plugin-slug' ), // Title
			array(
				$this,
				'link_to_invoice', // Callback to method to display HTML
			),
			self::INVOICE_POST_TYPE, // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);

	}

	public function link_to_invoice() {
		?>
		<strong>
			<a href="<?php the_permalink(); ?>">
				<?php the_permalink(); ?>
			</a>
		</strong><?php
	}

	/**
	 * Invoice to meta box.
	 */
	public function meta_box() {

		foreach( $this->fields as $key => $field ) {

			$data = get_post_meta( get_the_ID(), self::META_KEY, true );
			if ( isset( $data[ '_' . $key ] ) ) {
				$value = $data[ '_' . $key ];
			} else {
				$value = '';
			}

			?>

			<p>
				<label for="_<?php echo $key; ?>"><strong><?php echo $field[ 'label' ]; ?></strong></label>
				<br /><?php

				if ( 'textarea' == $field['type'] ) {
					echo '<textarea name="' . esc_attr( '_' . $key ) . '" id="' . esc_attr( '_' . $key ) . '">' . esc_textarea( $value ) . '</textarea>';

				} elseif ( 'checkbox' == $field['type'] ) {
					echo '<input type="' . esc_attr( $field[ 'type' ] ) . '" name="' . esc_attr( '_' . $key ) . '" id="' . esc_attr( '_' . $key ) . '" ' . checked( 'on', $value, false ) . ' />';
				} elseif ( 'number' == $field['type'] ) {
					echo '<input step="0.1" type="' . esc_attr( $field[ 'type' ] ) . '" name="' . esc_attr( '_' . $key ) . '" id="' . esc_attr( '_' . $key ) . '" value="' . esc_attr( $value ) . '" />';
				} else {
					echo '<input type="' . esc_attr( $field[ 'type' ] ) . '" name="' . esc_attr( '_' . $key ) . '" id="' . esc_attr( '_' . $key ) . '" value="' . esc_attr( $value ) . '" />';
				}

				?>
			</p><?php
		}

	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		if ( ! wp_is_post_revision( $post_id ) ){
		
			// unhook this function so it doesn't loop infinitely
			remove_action( 'save_post', array( $this, 'meta_boxes_save' ) );

			// Only save if correct post data sent
			if ( isset( $_POST[self::META_KEY . '-nonce'] ) ) {
				// Do nonce security check
				$file = dirname( __FILE__ ) . '/class-wp-invoice-tasks-meta-boxes.php';
				if ( ! wp_verify_nonce( $_POST[self::META_KEY . '-nonce'], $file ) ) {
					return;
				}

			}

			$data = get_post_meta( get_the_ID(), self::META_KEY, true );
			foreach( $this->fields as $key => $field ) {
				if ( isset( $_POST[ '_' . $key ] ) ) {
					$data[ '_' . $key ] = $_POST[ '_' . $key ];
				} else {
					$data[ '_' . $key ] = '';
				}
			}

			// Get client name for adding to post-title
			if ( isset( $_POST[ 'tax_input' ][ 'client' ][0] ) ) {

				// If new submission, then grab directly from $_POST, otherwise pull name from term via ID (which is found in $_POST when not new)
				$client_name = $_POST[ 'tax_input' ][ 'client' ][0];
				if ( is_numeric( $client_name ) ) {
					$term = get_term( $client_name, self::CLIENT_TAXONOMY );
					$client_name = $term->name;
				}

			}

			// Updating the invoices slug
			$args = array(
				'ID'          => $post_id,
				'post_name'   => sanitize_title( $data[ '_invoice_no' ] ),
				'post_title'  => wp_kses_post( $client_name . ': ' . $data [ '_invoice_no' ] ),
			);
			wp_update_post( $args );

			// re-hook this function
			add_action( 'save_post', array( $this, 'meta_boxes_save' ) );

			// Save the data
			update_post_meta( $post_id, self::META_KEY, $data );

		}

	}

}
new WP_Invoice_Invoice_Meta_Boxes;
