<?php

/**
 * Simple Facebook Login SDK class.
 * Contains helper functions for working with the Facebook SDK.
 *
 * @todo   abstract complete SDK functionality as 100% separate PHP library
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Simple_Facebook_Login_SDK {

	/**
	 * Loading the Facebook SDK.
	 *
	 * @access protected
	 * @return object  $fb   The Facebook SDK object
	 */
	protected function load_facebook_sdk() {

		$path = dirname( dirname( __FILE__ ) ) . '/vendor/autoload.php';
		require( $path );

		$fb = new Facebook\Facebook([
			'app_id'                => get_option( 'simple-facebook-login-app-id' ),
			'app_secret'            => get_option( 'simple-facebook-login-app-secret' ),
			'default_graph_version' => 'v2.12',
		]);

		return $fb;
	}

	/**
	 * Get the Facebook login URL.
	 *
	 * @access protected
	 * @param  object  $wp  The WP global object
	 * @return string  The Facebook login URL
	 */
	protected function get_facebook_login_url() {
		$fb = $this->load_facebook_sdk();

		$helper = $fb->getRedirectLoginHelper();
		$permissions[] = 'email';
		$login_url = $helper->getLoginUrl( $this->get_callback_url(), $permissions );

		return $login_url;
	}

	/**
	 * Get callback URL.
	 *
	 * @access protected
	 * @global object   $wp  The WP global object
	 * @return string   The callback URL
	 */
	protected function get_callback_url() {
		global $wp;

		$current_url = home_url( $wp->request ) . '/';
		$callback_url = add_query_arg( 'simple-facebook-login', 'callback', $current_url );

		return $callback_url;
	}

	/**
	 * The callback for processing logins when sent back from Facebook.com.
	 *
	 * @access protected
	 * @return object|string  The user node object | error message (if login fails)
	 */
	protected function process_facebook_callback() {
		$fb = $this->load_facebook_sdk();
		$helper = $fb->getRedirectLoginHelper();

		// Fixing state bug
		if ( isset( $_GET['state'] ) ) {
			$state = sanitize_title( $_GET[ 'state' ] );
			$helper->getPersistentDataHandler()->set( 'state', $state );
		}

		// Get access token
		try {
			$access_token = $helper->getAccessToken();
		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			$this->messages[] = 'facebook-graph-error';
		} catch( Facebook\Exceptions\FacebookSDKException $e) {
			$this->messages[] = 'facebook-sdk-error';
		}

		// If not access token set, work out what the error is
		if ( ! isset( $access_token  ) ) {
			if ( $helper->getError() ) {
				$this->messages[] = 'facebook-401';
			} else {
				$this->messages[] = 'facebook-400';
			}
		}

		// The OAuth 2.0 client handler helps us manage access tokens
		$oAuth2Client = $fb->getOAuth2Client();

		// Get the access token metadata from /debug_token
		$token_meta_data = $oAuth2Client->debugToken( $access_token );

		// Validation (these will throw FacebookSDKException's when they fail)
		$token_meta_data->validateAppId( get_option( 'simple-facebook-login-app-id' ) );
		$token_meta_data->validateExpiration();

		// Try to get a long lived access token
		if ( ! $access_token->isLongLived() ) {

			// Exchange a short-lived access token for a long-lived one
			try {
				$access_token = $oAuth2Client->getLongLivedAccessToken( $access_token );
			} catch ( Facebook\Exceptions\FacebookSDKException $e ) {
				$this->messages[] = 'access-token-error';
			}

		}

		// If errors, then bail out now
		if ( ! empty( $this->messages ) ) {
			return null;
		}

		// Get Facebook user
		$fb->setDefaultAccessToken( $access_token );
		$response = $fb->get( '/me?locale=en_US&fields=name,email' );
		$user_node = $response->getGraphUser();

		// If result is not object, then assume user was not found
		if ( ! is_object( $user_node ) ) {
			$this->messages[] = 'facebook-user-not-found';
		}


		return $user_node;
	}

}
