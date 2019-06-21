<?php

/**
 * Strattic Logout.
 * 
 * @copyright Copyright (c), Strattic
 * @since 2.0
 */
class Strattic_Logout extends Strattic_Core {

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );

		add_action( 'strattic_cron_logout', array( $this, 'shutdown_container' ) );
		//wp_unschedule_event( time(), 'strattic_cron_logout' );
		$schedule = wp_get_schedule( 'strattic_cron_logout' );
		if ( '' == $schedule ) {
			wp_schedule_event( time(), 'every_minute', 'strattic_cron_logout' );
		}

		// If not an AJAX page, then store last request time
		if ( ! isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) ) {
			update_option( 'strattic_last_page_load', time() );
		}

	}

	/**
	 * Shutdown the container.
	 */
	public function shutdown_container() {

		// Check if page visited within designated time
		$time = time() - ( 5 * MINUTE_IN_SECONDS );
		if ( $time < get_option( 'strattic_last_page_load' ) ) {

			// Page wasn't visited within designated time, so processed with shutting down the container

			//TEST CODE ... $string = $time . "\n" . get_option( 'strattic_last_page_load' ) . "\n\n";file_put_contents( '/var/www/test-strattic.io/public_html/wp-content/plugins/strattic/test.txt', $string, FILE_APPEND );

			$site_id = $this->get_current_site_strattic_id();
			$response = $this->make_api_request( '/sites/' . $site_id . '/stop', 'POST', '' );

		}

	}

	/**
	 * Adjust the available Cron schedules.
	 */
	public function cron_schedules( $schedules ) {

		$schedules[ 'every_minute' ] = array(
			'interval' => 60,
			'display' => __( 'Once per minute', 'strattic' )
		);

		return $schedules;
	}

}
