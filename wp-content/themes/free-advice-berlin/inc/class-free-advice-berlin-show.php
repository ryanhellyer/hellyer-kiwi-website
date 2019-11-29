<?php

/**
 * Provide taxonomy for specific taxonomies.
 */
class Free_Advice_Berlin_Show {

	/*
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
	}

	/**
	 * Register the taxonomy.
	 */
	public function register_taxonomy() {
		register_taxonomy(
			'show',
			'page',
			array(
				'public'  => false,
				'show_ui' => false,
			)
		);
	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'show', // ID
			__( 'Show on front page?', 'free-advice-berlin' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			'page', // Post type
			'side', // Context, choose between 'normal', 'advanced', or 'side'
			'high'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the show meta box.
	 */
	public function meta_box() {
		?>

		<p>
			<input type="checkbox" name="_show" id="_show" <?php echo checked( get_post_meta( get_the_ID(), '_show', true ), 1, true ); ?> value="1" />
			<input type="hidden" id="show-nonce" name="show-nonce" value="<?php echo esc_attr( wp_create_nonce( __FILE__ ) ); ?>">
		</p><?php
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Do nonce security check
		if ( ! wp_verify_nonce( $_POST['show-nonce'], __FILE__ ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		// Only save if correct post data sent
		if ( isset( $_POST['_show'] ) && 1 == $_POST['_show']) {
			update_post_meta( $post_id, '_show', 1 );
		} else {
			delete_post_meta( $post_id, '_show' );
		}

	}
 
}
new Free_Advice_Berlin_Show;
