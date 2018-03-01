<?php

class Pushpress_Schedule_Model {

	function __construct() {
		
	}

	public function get_client() { 

		$secret_code = get_option( 'wp-pushpress-secret-code' );
		if ( strlen( trim( $secret_code ) ) ) {
			try {
				if( !$client = get_transient( 'pp_client_info' ) ) {
					PushpressApi::setApiKey($secret_code);
					PushpressApi::setHost( PUSHPRESS_HOST );
					PushpressApi::setApiVersion( PUSHPRESS_API_VERSION );
					$client = Pushpress_Client::retrieve( 'self' );
					set_transient( 'pp_client_info', $client, 300 ); // 5m cache   
				} 
			}
			catch ( Exception $e ) { 
				$client = Pushpress_Client::retrieve( 'self' );
			}
			return $client;
		}

		return null;
	}

}
