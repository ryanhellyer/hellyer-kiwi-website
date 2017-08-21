<?php

/**
 * Member profiles.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Members extends SRC_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'init',             array( $this, 'rewrites' ) );
		add_action( 'init',             array( $this, 'save' ) );
		add_filter( 'template_include', array( $this, 'member_template' ), 99 );
	}

	public function save() {

		if ( isset( $_POST['src_nonce'] ) && wp_verify_nonce( $_POST['src_nonce'], 'src_nonce' ) ) {

// check if actually logged in
// check if user ID matches form info

			echo 'processing';
			die;

		} else if ( isset( $_POST['src_nonce'] ) ) {
			wp_die( '<strong>Error:</strong> Form could not be processed due to a nonce error. You should never have seen this error. Please contact an admin and let them know this occurred and what you were doing when it happened.' );
		}

	}

	/**
	 * Add rewrite rules.
	 */
	public function rewrites() {
		add_rewrite_rule( 'member/([^/]+)/?$', 'index.php?member=$matches[1]', 'top' );
	}

	/**
	 * Set member template.
	 *
	 * @param  string  $template  The template being used
	 * @global object  $user  the current users object
	 * @return string  The new template
	 */
	public function member_template( $template ) {
		global $src_member;

		$member_path = str_replace( 'http://', '', home_url() );
		$member_path = str_replace( 'https://', '', $member_path );
		$member_path = str_replace( $_SERVER['SERVER_NAME'], '', $member_path );
		$member_path = $member_path . '/member/';

		// If path isn't even in the REQUEST_URI, then we aint on a members page
		if ( strpos( $_SERVER['REQUEST_URI'], $member_path ) !== false ) {
			//
		} else {
			return $template;
		}

		$member_slug = str_replace( $member_path, '', $_SERVER['REQUEST_URI'] );
		$member_slug = str_replace( '/', '', $member_slug );
		$src_member = get_user_by( 'login', $member_slug );

		if ( is_object( $src_member ) ) {
			 /**
			 * Prevent WordPress from returning a 404 status
			 */
			global $wp_query;
			$wp_query->is_404 = false;

			$new_template = locate_template( array( 'member.php' ) );
			return $new_template;
		}

		return $template;
	}

}
