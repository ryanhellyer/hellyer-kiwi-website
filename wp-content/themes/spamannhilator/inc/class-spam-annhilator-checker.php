<?php

if ( isset( $_GET['test'] ) ) {
	$meta_key = 'success';
	$post_id = 124;
	$checked = get_post_meta( $post_id, '_' . $meta_key, true );
	echo $checked;
	die;
}

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

	public $secret_key;
	public $slug = '';

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'rest_api_init',  array( $this, 'register_routes' ) );

		$this->secret_key =  md5( rand() );

		if ( false !== ( $this->slug = $this->get_check_slug() ) ) {

			if ( false === $this->iterate_checked_number( $this->slug, 'loaded' ) ) {

				// No post was found, so just bail out of checking template system and fallback to 404
				return;
			}

			$template = file_get_contents( dirname( dirname( __FILE__ ) ) . '/' . 'checking-template.tpl' );
			$template = $this->modify_template( $template );

			echo $template;
			die;
		}

	}

	/**
	 * Register URL routes for REST API requests.
	 */
	public function register_routes() {

		register_rest_route( 'spamannhilator/v1', '/log', array(
			'methods'  => 'GET',
			'callback' => array( $this, 'log' ),
		) );

	}

	/**
	 * Logging the AJAX request.
	 * eg: /wp-json/spamannhilator/v1/log?slug=code
	 *
	 * @param  array  $request  Request vars
	 * @return string | array
	 */
	public function log( $request ) {

		$request_params = $request->get_query_params();

		foreach ( $request_params as $slug => $code ) {

			$this->iterate_checked_number( $slug, $code );

			return true;
		}

		return false;
	}

	/**
	 * Modify the template.
	 *
	 * @access private
	 * @param  string  The template
	 * @return string  The modified template
	 */
	private function modify_template( $template ) {

		$redirect_url = $this->get_redirect_url( $this->slug );
		$encrypted = $this->CryptoJSAesEncrypt( $this->secret_key, $redirect_url );

		$tags = array(
			'redirect_url' => esc_url( $redirect_url ),
			'home_url'     => esc_url( home_url() ),
			'slug'         => esc_html( $this->slug ),
			'secret_key'   => esc_html( $this->secret_key ),
			'text'         => esc_html( $encrypted ),
			'template_url' => esc_url( get_template_directory_uri() ),
		);

		foreach ( $tags as $tag => $string ) {
			$template = str_replace( '{{' . $tag . '}}', $string, $template );
		}

		return $template;
	}

	/**
	 * Iterate the number of times a URL has been checked.
	 * Possible codes include "success", "failed-to-decrypt", "loaded" and "js-failed".
	 *
	 * @access private
	 * @param  string  $slug  The slug
	 * @param  string  $code  The error/success code
	 * @return bool | string   false if no post found
	 */
	private function iterate_checked_number( $slug, $code ) {

		if ( 'success' === $code ) {
			$meta_key = 'success';
		} else if ( 'failed-to-decrypt' === $code ) {
			$meta_key = 'failed-to-decrypt';
		} else if ( 'js-failed' === $code ) {
			$meta_key = 'js-failed';
		} else if ( 'loaded' === $code ) {
			$meta_key = 'loaded';
		} else {
			return false;
		}

		$post = get_page_by_path( $slug, OBJECT, 'check' );
		if ( isset( $post->ID ) ) {
			$post_id = $post->ID;
			$checked = get_post_meta( $post_id, '_' . $meta_key, true );
			$checked++;
			update_post_meta( $post_id, '_' . $meta_key, $checked );
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Get the slug for a checking page.
	 *
	 * @access private
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

	private function cryptoJsAesDecrypt($passphrase, $jsonString){
		$jsondata = json_decode($jsonString, true);
		$salt = hex2bin($jsondata["s"]);
		$ct = base64_decode($jsondata["ct"]);
		$iv  = hex2bin($jsondata["iv"]);
		$concatedPassphrase = $passphrase.$salt;
		$md5 = array();
		$md5[0] = md5($concatedPassphrase, true);
		$result = $md5[0];
		for ($i = 1; $i < 3; $i++) {
			$md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
			$result .= $md5[$i];
		}
		$key = substr($result, 0, 32);
		$data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
		return json_decode($data, true);
	}

	private function cryptoJsAesEncrypt($passphrase, $value){
		$salt = openssl_random_pseudo_bytes(8);
		$salted = '';
		$dx = '';
		while (strlen($salted) < 48) {
			$dx = md5($dx.$passphrase.$salt, true);
			$salted .= $dx;
		}
		$key = substr($salted, 0, 32);
		$iv  = substr($salted, 32,16);
		$encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
		$data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
		return json_encode($data);
	}

}
