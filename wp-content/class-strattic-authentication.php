<?php

/**
 * Token authentication for connecting with Strattic console.
 * 
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_Authentication extends Strattic_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'transfer_tokens_to_wordpress' ) );

		// Only load authentication if site_auth query var is set
		if ( isset( $_GET['site_auth'] ) ) {

			// Load WordPress if not already loaded
			$wp_load_path = ABSPATH . 'wp-load.php';
			if (
				! function_exists( 'get_option' )
				&&
				file_exists( $wp_load_path )
			) {
				require( $wp_load_path );
			}

			// Check if WordPress is installed
			if ( null == get_option( 'home' ) ) {
				
				// Sending token to file since WordPress is not loaded yet
				$this->receive_token_to_file();

			} else {

				// Storing token in WordPress since WordPress is loaded
				$this->receive_token_to_wp();

			}

			die;

		}

/* Temporary function for testing purposes - this should be removed once the actual authentication system is implemented
 */
 if ( '/strattic-authentication-send/' === $this->get_current_path() ) {add_action( 'template_redirect', array( $this, 'test' ) );}
/* Temporary function for testing purposes - cancels job*/ if ( '/strattic-authentication-cancel/' === $this->get_current_path() ) {add_action( 'template_redirect', array( $this, 'cancel' ) );}
	}

	/**
	 * Transfer tokens to WordPress.
	 * Only transfers if the auth.json file exists.
	 */
	public function transfer_tokens_to_wordpress() {

		$path = ABSPATH . '.auth/auth.json';
		if ( file_exists( $path ) ) {
			$contents = file_get_contents( $path );
			$result = json_decode( $contents, true );

			$id_token      = $result[ 'idToken' ][ 'jwtToken' ];
			$refresh_token = $result[ 'refreshToken' ][ 'token' ];
			$region        = $result[ 'region' ];
			$client_id     = $result[ 'clientId' ];

			$this->save_tokens_in_wordpress( $id_token, $refresh_token, $region, $client_id );

			unlink( $path );
		}

	}

	/**
	 * Saves the tokens within WordPress.
	 */
	private function save_tokens_in_wordpress( $id_token, $refresh_token, $region, $client_id ) {

		$id_token      = esc_html( $id_token );
		$refresh_token = esc_html( $refresh_token );
		$region        = esc_html( $region );
		$client_id     = esc_html( $client_id );

		update_option( 'strattic-id-token', $id_token, false );
		update_option( 'strattic-refresh-token', $refresh_token, false );
		update_option( 'strattic-cognito-region', $region, false );
		update_option( 'strattic-cognito-client-id', $client_id, false );

	}

	/**
	 * Receive the incoming POST request with the token.
	 * Saves to file when WordPress isn't installed yet.
	 */
	public function receive_token_to_file() {
		$rest_json = file_get_contents( 'php://input' );
		$_POST = json_decode( $rest_json, true );
		$response = array();
		header( 'Content-Type: application/json' );

		if (
			isset( $_POST[ 'region' ] ) &&
			isset( $_POST[ 'clientId' ]) &&
			isset( $_POST[ 'idToken' ][ 'jwtToken' ]) &&
			isset( $_POST[ 'refreshToken' ][ 'token' ] )
		) {

			$auth = array();
			$auth[ 'region' ]                  = esc_html( $_POST[ 'region' ] );
			$auth[ 'clientId' ]                = esc_html( $_POST[ 'clientId' ] );
			$auth[ 'idToken' ][ 'jwtToken' ]   = esc_html( $_POST[ 'idToken' ][ 'jwtToken' ] );
			$auth[ 'refreshToken' ][ 'token' ] = esc_html( $_POST[ 'refreshToken' ][ 'token' ] );

			if ( ! is_dir( ABSPATH . '.auth' ) ) {
				mkdir( ABSPATH . '.auth' );
			}

			$authFile = fopen( ABSPATH . '.auth/auth.json', 'w' ) or die( 'Unable to open file!' );
			fwrite( $authFile, json_encode( $auth ) );
			$response[ 'success' ] = 1;
		} else {
			$response[ 'success' ] = 0;
		}
		die( json_encode( $response ) );
	}

	/**
	 * Receive the incoming POST request with the token.
	 */
	public function receive_token_to_wp() {

		// Convert raw JSON data to POST format
		$_POST = json_decode( file_get_contents( 'php://input' ), true );
		$response = array();
		header( 'Content-Type: application/json' );

		// If the tokens exist, save them ...
		if (
			isset( $_POST[ 'idToken' ][ 'jwtToken' ] )
			&&
			isset( $_POST[ 'refreshToken' ][ 'token' ] )
		) {
			$id_token = esc_html( $_POST[ 'idToken' ][ 'jwtToken' ] );
			$refresh_token = esc_html( $_POST[ 'refreshToken' ][ 'token' ] );

			if ( isset( $_POST[ 'region' ] ) ) {
				$region = esc_html( $_POST[ 'region' ] );
			}

			if ( isset( $_POST[ 'clientId' ] ) ) {
				$client_id = esc_html( $_POST[ 'clientId' ] );
			}

			$this->save_tokens_in_wordpress( $id_token, $refresh_token, $region, $client_id );

			$response[ 'success' ] = 1;
		} else {

			// No token was found, so we need to send an error - when new error system is implemented we should log this there
			header( 'HTTP/1.0 400 Bad Request' );
			$response[ 'success' ] = 0;

		}

		die( json_encode( $response ) );

	}



	function cancel() {

		$response = $this->make_api_request( 'sites/current' );
		$job_id = $this->get_most_recent_job_id();

		$url = STRATTIC_API_URL . 'scrapejob/' . $job_id . '/cancel';

		$id_token = $this->get_valid_token();
		$args = array(
			'method'        => 'POST',
			'timeout'       => 30,
			'httpversion'   => '1.1',
			'headers'       => array(
				'content-type'  => 'application/json',
				'authorization' => $id_token,
			),
		);

		$args = $this->set_user_agent( $args );
		$response = wp_remote_request(
			$url,
			$args 
		);

		if ( is_wp_error( $response ) ) {//error handler here
		}

		echo 'URL: ' . $url . "\n\n";
		print_r( $args );
		echo "\n\n\n\n.........................\n\n\n";
		print_r( $response );
		die;
	}

	// This is a temporary function for testing the authentication system.
	// This (and the hook to it) can be removed once the authentication system is operational.
	function test() {
		$body = '{
	"idToken": {
		"jwtToken": "eyJraWQiOiJNMSs0NG40QXBXbldlWlJkY1JreERvcm1LdTVCZVk2ZnUzR3JPU0FiN2JnPSIsImFsZyI6IlJTMjU2In0.eyJzdWIiOiI4MjVlYzBkMi02YzNlLTQyNzgtYWJiMC02YTVmZmYzMTQwNjciLCJhdWQiOiI2YjZlcDhwdGRlbmR0MDZ2ZWdrMXNtbnMyMSIsImNvZ25pdG86Z3JvdXBzIjpbIkFkbWluc0dyb3VwIl0sImV2ZW50X2lkIjoiMjI0NzIzY2MtZTBkYS0xMWU4LWE0MjEtZjNlZTc1NmQxYWUwIiwidG9rZW5fdXNlIjoiaWQiLCJhdXRoX3RpbWUiOjE1NDE0MDg4MTEsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5ldS13ZXN0LTEuYW1hem9uYXdzLmNvbVwvZXUtd2VzdC0xX2lxYm90SkVqeSIsImNvZ25pdG86dXNlcm5hbWUiOiI4MjVlYzBkMi02YzNlLTQyNzgtYWJiMC02YTVmZmYzMTQwNjciLCJleHAiOjE1NDE0MTI0MTEsImlhdCI6MTU0MTQwODgxMSwiZW1haWwiOiJhbWl0YXltb2xrb0BnbWFpbC5jb20ifQ.ge18ReLBa6dcHh7proHubWrhVTy6sTtpuo1uV804yxtkVUEp9APIGRV7_X3betZBVe2148nsQ5rfBkT2xLN5fgLXO-WFMddm2R9V-4XZrr8gLbCFQFyALboa0LkEU59G1h98bWgCX202tCWN_7_rQg98PWTqFBokW7SNX4iW7uQkLgEdp8OyQ1wimhTIIDEcEJu_DIILbVyFs1kbZXmDPZFeAUYwl1EhOSc5k1zj-cQov6glfSF46YXdkgB8TRqewUwucDmA_LJEKZN8q0dg2mtMpbSrVspUsyBKHltr9Lw_tWSZkYf0i02ocUevU3por-9FsR_WxT13V4t3mow7ZQ"
	},
	"refreshToken": {
		"token": "eyJjdHkiOiJKV1QiLCJlbmMiOiJBMjU2R0NNIiwiYWxnIjoiUlNBLU9BRVAifQ.cuQM4rYf28miWEFyGe72ayWxFEq2CjEjQmKF-oAU8NtNPuiBcCZo8G37AtatOZpSq5FOu_b_T54qn-wXGH17LIzFO42sDdV2UmH_IZE8mQpgsnGGgYDzvbyxSZuoiogxlm0skDuUvXGSbMxYyxyDrODxtxp71q1EOqHvaRWXH1kXvFOcyVDdNBBmWSWS953CEYiCWN5lecThXfI3e6SUktBJWCz6RgVcUM5c-wzruop15NI66TxpuTBg8ITGOjeA01ciqwCbxpKe_chyqQno_oTFHFyijpOmpOd1yEW1AGp6h9wgfOPbK-vwby0BVW-AVE2GluNWtOtR51Zc82Q4RA.oH3KWDQo0XD0ry2M.2X2Y3F2-PVeVSQqQGBA6Wg9akT5DPMcO8WLW61XtdueN9rye_kmezsILANFhg38f6qnIjsI0Y1PeD4E9P7OiLo1yGDttSicXD9cWk_6T1SGVdp8YmgkTnl08aXhl9uqcQYRODI23COS_9tLPVQgN72CiHINEMBhCoR-k4ilicGcL6n2Cmnx5MGS1U-Abv_vQk2w4pn41JJxkdhyAKTAPRVe1mHkvXouVzKrAAtv71DIX_7RQnJnTQ-vq79G9Vp3QPKYaWOTcy4Mv9mgpKIGrgmgI2MFHiqSyjy1uaXDk19VIuTfb5aRJxzABUmNpRgie3wmvC2U26xC7vQYDTXEVNKAI3iBmRdYZK8NvSaVLzOR0ZUMACBIwPNFauoJgVDeo_ubX_EipUUE-ofSfK3-aQR_gA2H9rCYSYP_vzBgQtK381vpWAv_90HvMn1H05uJ8_mr18xr0Be_Gtk0qAl2YIE96yIEdT8_W6K7G0Ma3uMQYVbhGSF82CQkhrg5mKT_ZaEAGFolu8wjnIb1siwh0NpvE6JOr-qWPuoezdmZZHh0fpe9D_4Bjpj0YEOCQmKKxHF7ZqyYjFbvI3MCkrxZPtd4ap8iAaQx5dCexZ1jI9FUrqPn0O2_SDmk7HZZ2rufmQ42IHx6siNjSuyWIvgG68OUEVAar8C2QQGROiarpgrMw3vxEztrIRyT_INCAlgUxhgxYh2noJArMxX6kzVJVBCAOQ-8bhgFzjcxr3Yn4PVQD9akPE0KYroIjyzspkMVHd-IryIgWcs0EMk_cYitYtIpvTTqkn8uYvK3SDliNAqAPcQoMg2dQ-IFDC9GFVTMuNsACFcB20Nn9vKL13jkE1QkQ5UvD1x2cb4iBKkMfu3pwK9qpzGiSBDRKWOM5G1eflUkSB9hsV7J8pZqqpjF-EZIumDszZT2ZIyJR1RREjB9tDruVbtusTjdtQax_L6O6pU2J7EtQQyLXEegq2VtOxSjA2emZogI_OyHXISFkbqrRg-jPilOOT65SKF17LV891aMhu38heC3r7psraOD1Wjps0cxDe3Kavkol7VFjg8N-3fekN_W3OATXrw0GzZIbxDFyPMtBrs_E8LOsmV_2JwLbEWHHTYKWvpELRi694G-DIJQuoTIc5iCZA8-QrWuOG-Ya1DzfGw4tQB9keHVBvd5BNNylOj5ZVjN3t54mbG0DjUacUAqYRuJariU2pXq5wA9Ciwe9kfX5kAyVeL8P5-OK7IkQsaRjyW4tR61bTBeaWB7-TW5RcD-2yn1bAUYo376o8SXwth5XS7HRQeP7BZe7VeK9cOYW7eHNTK8_Z5ExsCgN5_NNp-uZyQ.XAZQXIQu9kDsjGQFYtW-Yw"
	},
	"accessToken": {
		"jwtToken": "eyJraWQiOiIzVGJDRndVNmV6bjNEVHNcL1QyTklES3VObmVEVyt6UUlFdXVWblE3XC9LOW89IiwiYWxnIjoiUlMyNTYifQ.eyJzdWIiOiI4MjVlYzBkMi02YzNlLTQyNzgtYWJiMC02YTVmZmYzMTQwNjciLCJjb2duaXRvOmdyb3VwcyI6WyJBZG1pbnNHcm91cCJdLCJldmVudF9pZCI6IjIyNDcyM2NjLWUwZGEtMTFlOC1hNDIxLWYzZWU3NTZkMWFlMCIsInRva2VuX3VzZSI6ImFjY2VzcyIsInNjb3BlIjoiYXdzLmNvZ25pdG8uc2lnbmluLnVzZXIuYWRtaW4iLCJhdXRoX3RpbWUiOjE1NDE0MDg4MTEsImlzcyI6Imh0dHBzOlwvXC9jb2duaXRvLWlkcC5ldS13ZXN0LTEuYW1hem9uYXdzLmNvbVwvZXUtd2VzdC0xX2lxYm90SkVqeSIsImV4cCI6MTU0MTQxMjQxMSwiaWF0IjoxNTQxNDA4ODExLCJqdGkiOiIwZTMyMDlmOC04NDE2LTRhODAtODY1Ny0yODQ4ZGZiMGQ5M2UiLCJjbGllbnRfaWQiOiI2YjZlcDhwdGRlbmR0MDZ2ZWdrMXNtbnMyMSIsInVzZXJuYW1lIjoiODI1ZWMwZDItNmMzZS00Mjc4LWFiYjAtNmE1ZmZmMzE0MDY3In0.GQ1L1sfEkBnDG6leZMEax2B-orZ1KU72XKMZTcSpF4qTcnYJtzLUebf-gAG1GSNjj1bacBT3A-R8noJQIJ_NhvqZE0kq02y4EU9pEF7gFay-50PMgPGOiZdj4R62MR4W38kWtuJKBQl6tY30uPbfpLliYLNXD82zTZtv5iptRIbZ8iyxZLVoPHGl4Gw7QDaXU87mOmrfzjEnCVk2ctLL6LnoF3m_F8nyiy4Lmxernq6OqihkJi2wLHS_6t16Lg1c9qVNk4dIqW8LPBOFE5KegCl6pD3NJ-M7TAaH0yefqMwApNsO_a977UBlWWJXFk2-IxKYJCgMC6Y4LuSsvCLc6Q"
	},
	"clockDrift": 0
}';

		$args = array(
			'method'  => 'POST',
			'timeout' => 120,
			'body'    => $body,
			'headers' => array(
				'content-type' => 'application/json',
			),
		);

		$url = home_url() . '/wp-admin/install.php?site_auth';

		$response = wp_remote_post(
			$url,
			$args
		);
		if ( is_wp_error( $response ) ) {//error handler here
		}
print_r( $response );
die;
	}

}
