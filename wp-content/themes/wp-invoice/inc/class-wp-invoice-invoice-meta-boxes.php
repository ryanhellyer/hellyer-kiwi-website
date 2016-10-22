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

		$this->fields = array(
			'invoice_no'         => array(
				'label' => __( 'Invoice number', 'plugin-slug' ),
				'type'  => 'text',
			),
			'total_amount'       => array(
				'label' => __( 'Total amount', 'plugin-slug' ),
				'type'  => 'text',
			),
			'invoice_to_details' => array(
				'label' => __( 'Details', 'plugin-slug' ),
				'type'  => 'text',
			),
			'invoice_to_website' => array(
				'label' => __( 'Website', 'plugin-slug' ),
				'type'  => 'url',
			),
		);

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
			self::POST_TYPE, // Post type
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
			self::POST_TYPE, // Post type
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
				<br />
				<input type="<?php echo $field[ 'type' ]; ?>" name="_<?php echo $key; ?>" id="_<?php echo $key; ?>" value="<?php echo esc_attr( $value ); ?>" />
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
				}
			}

			$this->sanitize( $data );

			// Save the data
			update_post_meta( $post_id, self::META_KEY, $data );

			// Updating the invoices slug
			$args = array(
				'ID'           => $post_id,
				'post_name'   => sanitize_title( $data[ '_invoice_no' ] ),
			);
			wp_update_post( $args );

			// re-hook this function
			add_action( 'save_post', array( $this, 'meta_boxes_save' ) );

		}

	}

}
new WP_Invoice_Invoice_Meta_Boxes;
