<?php

/**
 * Strattic 404 template.
 * Can be overridden by creating an actual page at this URL.
 * 
 * @copyright Copyright (c), Strattic
 * @since 1.3
 */
class Strattic_404 {

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Add hooks
		add_filter( 'the_posts', array( $this, 'generate_fake_page' ), -10 );
	}

	/**
	 * Create a fake page called "fake"
	 *
	 * $fake_slug can be modified to match whatever string is required
	 *
	 *
	 * @param   object  $posts  Original posts object
	 * @global  object  $wp     The main WordPress object
	 * @global  object  $wp     The main WordPress query object
	 * @return  object  $posts  Modified posts object
	 */
	public function generate_fake_page( $posts ) {
		global $wp, $wp_query;

		if ( ! defined( 'FAKE_PAGE' ) && ( strtolower( $wp->request ) == '404.html' ) ) {

			// stop interferring with other $posts arrays on this page (only works if the sidebar is rendered *after* the main page)
			define( 'FAKE_PAGE', true );

			// create a fake virtual page
			$post = new stdClass;
			$post->post_author    = 1;
			$post->post_name      = '404 Error';
			$post->guid           = esc_url( home_url() . '/404.html' );
			$post->post_title     = esc_html__( '404 Error: This page could not be found', 'strattic' );
			$post->post_content   = '';
			$post->ID             = 0;
			$post->post_type      = 'page';
			$post->post_status    = 'static';
			$post->comment_status = 'closed';
			$post->ping_status    = 'open';
			$post->comment_count  = 0;
			$post->post_date      = current_time( 'mysql' );
			$post->post_date_gmt  = current_time( 'mysql', 1 );
			$posts                = NULL;

			$posts[]              = $post;

			// make wpQuery believe this is a real page too
			$wp_query->is_page             = false;
			$wp_query->is_singular         = false;
			$wp_query->is_home             = false;
			$wp_query->is_archive          = false;
			$wp_query->is_category         = false;
			unset( $wp_query->query[ 'error' ] );
			$wp_query->query_vars[ 'error' ] = '';
			$wp_query->is_404 = true;

			// Yes, this overrides a 404 pages response code with a 200 - this is so that the publishing system won't reject it due to being a 404 error
			header( 'HTTP/1.1 200 OK' );

		}

		return $posts;
	}

}
