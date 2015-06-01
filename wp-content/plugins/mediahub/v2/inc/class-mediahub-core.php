<?php

class MediaHub_Core {

	const MAX_POSTS_PER_PAGE = '100';
	const META_KEY           = 'mediahub_note_id';

	/**
	 * This is the function that gets the content from the API
	 *
	 * @param  string $get JSON to call
	 * @param  string $query Optional Query to get
	 * @return object Result
	 */
	protected function mediahub_request( $get, $query = '', $type = 'GET' ) {

		// Checking that keys are added before attempting to retrieve data from API
		$api_keys = get_option( 'mhca_api_key' );
		if (
			( ! isset( $api_keys['mediahub_api_url'] ) || '' == $api_keys['mediahub_api_url'] )
			&&
			( ! isset( $api_keys['mediahub_api_openkey'] ) || '' == $api_keys['mediahub_api_openkey'] )
			&&
			( ! isset( $api_keys['mediahub_api_secretkey'] ) || '' == $api_keys['mediahub_api_secretkey'] )
		) {
			return array();
		}

		$api_settings = get_option( 'mhca_api_key', false );

		// Set authorization key
		if ( isset( $api_settings['mediahub_api_openkey'] ) ) {
			$auth_key = $api_settings['mediahub_api_openkey'];
		} else {
			$auth_key = '';
		}

		// Set secret key
		if ( isset( $api_settings['mediahub_api_secretkey'] ) ) {
			$secret = $api_settings['mediahub_api_secretkey'];
		} else {
			$secret = '';
		}

		// Set API URL
		if ( isset( $api_settings['mediahub_api_url'] ) ) {
			$api_url = $api_settings['mediahub_api_url'];
		} else {
			$api_url = '';
		}

		$environment_id = '1375';

		$time = time();
		$nonce = md5( microtime() . mt_rand() );

		$authHash = hash_hmac( "sha512", $get . $time . $nonce, $secret );

		$auth = 'auth_key=' . $auth_key . '&auth_hash=' . $authHash . '&auth_nonce=' . $nonce . '&auth_time=' . $time. '&environment_id=' . $environment_id;

		// Do request (GET and POST treated differently)
		$url = $api_url . $get . '?' . $query . $auth . '&limit=50';
		if ( 'GET' == $type ) {
			$result = wp_remote_get( $url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );
		} else {
			$result = wp_remote_post(
				$url,
				array(
					'method'      => 'POST',
					'timeout'     => 45,
					'redirection' => 5,
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(),
					'body'        => '',
					'cookies'     => array()
				)
			);

		}

		// If error, just return the result
		if ( is_wp_error( $result ) ) {
			$result->success = 0;
			return $result;
		}

		// Grab and decoded the result data
		$result = $result['body'];
		$decoded_result = json_decode( $result );

		return $decoded_result;
	}

}
