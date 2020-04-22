<?php

/**
 * Provides end-point for Strattic to detect when alerts need to be sent.
 * 
 * @copyright Copyright © Strattic
 */
class Strattic_Alerts {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		if ( '/strattic-status/' === $_SERVER['REQUEST_URI'] ) {
			add_action( 'template_redirect', array( $this, 'json_dump' ) );
		}

	}

	/**
	 * Dump the required status information out as JSON.
	 */
	public function json_dump() {

		header( 'Content-Type: application/json' );

		$advanced_params = get_transient( 'strattic-advanced-params' );
		$status = get_transient( 'strattic-status' );
		if ( false === $status ) {
			$status = 'not publishing'; // This is to ensure that it is obvious from viewing the API end-point that the site is not publishing. We can't add a 'not publishing' status, since those are only stored temporarily.
		}

		$array = array(
			'status'                 => esc_html( $status ),
			'status_timestamp'       => esc_html( get_transient( 'strattic-status-timestamp' ) ),
			'job_id'                 => esc_html( get_transient( 'strattic-job-id' ) ),
			'distribution_id'        => esc_html( get_transient( 'strattic-distribution-id' ) ),
			'is_full_publish'        => esc_html( get_transient( 'strattic-is-full-publish' ) ),
			'max_concurrent_workers' =>  ( isset( $advanced_params['maxConcurrentWorkers'] ) ? $advanced_params['maxConcurrentWorkers'] : '' ),
			'worker_batch_size'      =>  ( isset( $advanced_params['workerBatchSize'] ) ? $advanced_params['workerBatchSize'] : '' ),
			'add_urls_batch_size'    =>  ( isset( $advanced_params['addUrlsBatchSize'] ) ? $advanced_params['addUrlsBatchSize'] : '' ),
		);

		die( json_encode( $array ) );
	}

}
