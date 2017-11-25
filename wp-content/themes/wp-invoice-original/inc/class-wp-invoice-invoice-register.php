<?php

/**
 * Register post-type and taxonomy.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class WP_Invoice_Register extends WP_Invoice_Core {

	/*
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'init',           array( $this, 'register_post_type' ) );
		add_action( 'init',           array( $this, 'register_taxonomy' ) );
		add_filter( 'post_type_link', array( $this, 'remove_slug' ), 10, 3 );
		add_action( 'pre_get_posts',  array( $this, 'parse_request' ) );
	}

	/**
	 ** Register post-type.
	 */
	public function register_post_type() {
		$args = array(
			'public'   => true,
			'label'    => 'Invoice',
			'supports' => array(
//				'title',
				'revisions',
			)
		);
		register_post_type( 'invoice', $args );
	}

	/**
	 ** Register taxonomy.
	 */
	public function register_taxonomy() {
		register_taxonomy(
			self::CLIENT_TAXONOMY,
			self::INVOICE_POST_TYPE,
			array(
				'label'        => __( 'Client', 'plugin-slug' ),
				'hierarchical' => false,
				'public'       => false,
				'show_ui'      => true,
			)
		);

	}

	public function remove_slug( $post_link, $post, $leavename ) {

		if ( 'invoice' != $post->post_type || 'publish' != $post->post_status ) {
			return $post_link;
		}

		$post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

		return $post_link;
	}

	public function parse_request( $query ) {

		if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query['page'] ) ) {
			return;
		}

		if ( ! empty( $query->query['name'] ) ) {
			$query->set( 'post_type', array( 'invoice' ) );
		}

	}

}
new WP_Invoice_Register;
