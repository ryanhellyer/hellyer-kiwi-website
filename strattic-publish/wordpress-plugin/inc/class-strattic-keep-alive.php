<?php

/**
 * Keeps Strattic session alive by sporadically pinging the Strattic API.
 * 
 * @copyright Copyright (c), Strattic
 * @since 2.0
 */
class Strattic_Keep_Alive {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'keep_alive' ) );

	}

	/**
	 * Ping Strattic API after one minute.
	 */
	public function keep_alive() {

		$last_report_time = get_transient( 'strattic-keep-alive-last-report-time' );

		// If checked more than a minute ago, then ping the server
		if (  ( $last_report_time + MINUTE_IN_SECONDS ) < time() ) {
			set_transient( 'strattic-keep-alive-last-report-time', time() );

// The following URL should be set as the API request URL.
			file_get_contents( 'https://www.strattic.com/' );

		}

		return;
	}

}
