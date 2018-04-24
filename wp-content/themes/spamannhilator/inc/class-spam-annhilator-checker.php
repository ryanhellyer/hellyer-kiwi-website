<?php

/**
 * Spam Annhilator Checker.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Spam Annhilator theme
 * @since Spam Annhilator theme 1.0
 */
class Spam_Annhilator_Checker {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		if ( false !== ( $slug = $this->get_check_slug() ) ) {
			$redirect_url = $this->get_redirect_url( $slug );

			$template = file_get_contents( dirname( dirname( __FILE__ ) ) . '/' . 'checking-template.tpl' );

			$redirect_tag = '';

			$template = str_replace( '{{redirect_url}}', $redirect_url, $template );

			$template = str_replace( '{{template_url}}', esc_url( get_template_directory_uri() ), $template );

			echo $template;

			die;
		}

	}

	/**
	 * Get the slug for a checking page.
	 *
	 * @return bool | string   false | slug if on check page
	 */
	private function get_check_slug() {

		// Get check path
		$path = str_replace( 'http://', '', home_url() );
		$path = str_replace( 'https://', '', $path );
		$path = str_replace( $_SERVER['SERVER_NAME'], '', $path );
		$path = str_replace( $_SERVER['HTTP_HOST'], '', $path );
		$path = $path . '/' . esc_html__( 'check', 'spamannhilator' ) . '/';

		// If path isn't even in the REQUEST_URI, then we aint on a checking page anyway
		if ( strpos( $_SERVER['REQUEST_URI'], $path ) === false ) {
			return false;
		}

		// Calculate the check slug
		$slug = str_replace( $path, '', $_SERVER['REQUEST_URI'] );
		$slug = str_replace( '/', '', $slug );

		if ( '' !== $slug ) {
			return $slug;

		}

		return false;
	}

	/**
	 * Get redirect URL from slug.
	 *
	 * @param  string  $slug   The post slug
	 * @return string  Redirect URL
	 */
	private function get_redirect_url( $slug ) {
		$post = get_page_by_path( $slug, OBJECT, 'check' );
		$post_id = $post->ID;
		$redirect_url = get_post_meta( $post_id, '_redirect_url', true );

		return $redirect_url;
	}

}
