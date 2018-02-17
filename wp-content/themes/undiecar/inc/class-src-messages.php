<?php

/**
 * Messages.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Messages extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_action( 'init',           array( $this, 'init' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post',      array( $this, 'meta_boxes_save' ), 10, 2 );
	}

	/**
	 * Init.
	 */
	public function init() {

		register_post_type(
			'message',
			array(
				'public'             => true,
				'publicly_queryable' => false,
				'label'              => esc_html__( 'Messages', 'src' ),
				'supports'           => array( 'title', 'thumbnail' ),
				'menu_icon'          => 'dashicons-flag',
			)
		);

		register_post_type(
			'message-chunk',
			array(
				'public'             => true,
				'publicly_queryable' => false,
				'label'              => esc_html__( 'Message chunks', 'src' ),
				'supports'           => array( 'title' ),
				'show_in_menu'           => 'edit.php?post_type=message',
			)
		);

	}

	/**
	 * Add admin metabox.
	 */
	public function add_metabox() {
		add_meta_box(
			'message', // ID
			__( 'Message', 'src' ), // Title
			array(
				$this,
				'meta_box', // Callback to method to display HTML
			),
			array( 'message', 'message-chunk' ), // Post type
			'normal', // Context, choose between 'normal', 'advanced', or 'side'
			'core'  // Position, choose between 'high', 'core', 'default' or 'low'
		);
	}

	/**
	 * Output the message meta box.
	 */
	public function meta_box() {

		$html = '
			<p>
				<textarea style="width:100%;min-height:300px;" name="_message" id="_message">' . esc_textarea( get_post_meta( get_the_ID(), '_message', true ) ) . '</textarea>
			</p>

			<input type="hidden" id="message-nonce" name="message-nonce" value="' . esc_attr( wp_create_nonce( __FILE__ ) ) . '">';


		if ( 'message-chunk' === get_post_type() ) {
			//
		} else {

			$buttons = '<p>';

			$query = new WP_Query( array(
				'posts_per_page'         => 100,
				'post_type'              => 'message-chunk',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			) );
			if ( $query->have_posts() ) {

				while ( $query->have_posts() ) {
					$query->the_post();

					$buttons .= '<span data-message="' . esc_attr( get_post_meta( get_the_ID(), '_message', true ) ) . '" class="add-message-chunk button" id="' . esc_attr( 'chunk-' . get_the_ID() ) . '">' . esc_html( get_the_title( get_the_ID() ) ) . "</span>\n";

				}

				wp_reset_postdata();
			}
			$buttons .= '</p>';

			$html = $buttons . $html;

			$html .= "
<script>
(function () {

	window.addEventListener(
		'load',
		function (){
			//
		}
	);

	/**
	 * Handle clicks.
	 */
	window.addEventListener(
		'click',
		function (e){

			var chunk = e.target.dataset.message + \"\\n\";
			document.getElementById( '_message' ).innerHTML += chunk;

		}
	);

})();

</script>";
		}

		echo $html;
	}

	/**
	 * Save opening times meta box data.
	 *
	 * @param  int     $post_id  The post ID
	 * @param  object  $post     The post object
	 */
	public function meta_boxes_save( $post_id, $post ) {

		// Only save if correct post data sent
		if ( isset( $_POST['_message'] ) ) {

			// Do nonce security check
			if ( ! wp_verify_nonce( $_POST['message-nonce'], __FILE__ ) ) {
				return;
			}

			// Sanitize and store the data
			$_message = wp_kses_post( $_POST['_message'] );
			update_post_meta( $post_id, '_message', $_message );
		}

	}
 
 }
