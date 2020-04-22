<?php

/**
 * Strattic admin page AJAX.
 *
 * @copyright Copyright (c), Strattic
 * @since 2.0
 */
class Strattic_AJAX extends Strattic_Core {

	private $deploy_timeout = 5 * HOUR_IN_SECONDS;

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init' ), 5 );
	}

	/**
	 * Initialise the hooks.
	 * This isn't done in the constructor due to wp_verify_nonce not having loaded in time.
	 */
	public function init() {

		// Check we're meant to be process AJAX first
		if ( ! isset( $_REQUEST['strattic-ajax'] ) || ! isset( $_REQUEST['nonce'] ) ) {
			return;
		}

		// Nonce security check
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'strattic' ) && ! defined( 'STRATTIC_TEST' ) ) {
			wp_die( 'Failed nonce check' );
		}

		// Permissions security check
		if ( ! current_user_can( $this->permissions ) ) {
			wp_die( 'Error: Insufficient permission' );
		}

		// Storing the status timestamp (used for calculating when to send alerts when status is stuck)
		add_filter( 'pre_set_transient_strattic-status', function( $status ) {
			set_transient( 'strattic-status-timestamp', time(), $this->deploy_timeout );
			return $status;
		});

		if ( 'deploy' === $_REQUEST['strattic-ajax'] ) {
			add_action( 'admin_init', array( $this, 'ajax_deploy' ) );
		}

		if ( 'status' === $_REQUEST['strattic-ajax'] ) {
			add_action( 'admin_init', array( $this, 'ajax_status' ) );
		}

		if( 'reset_publish' === $_REQUEST['strattic-ajax'] ) {
			$result = $this->clear_all_publish_values();
			die( json_encode( ['result' => $result ] ) );
		}

	}

	/**
	 * Make an API call to a new scrape job for this website
	 *
	 * @param int $distribution_id
	 * @param string $stage (first or second)
	 * @return object API response (object containing body)
	 */
	public function make_scrape_request( $distribution_id, $stage = 'first' ) {
		$site_id = $this->get_current_site_strattic_id();
		$path = 'sites/' . $site_id . '/publish';

		$advanced_params_transient = get_transient( 'strattic-advanced-params' );
		$advanced_params = json_decode( $advanced_params_transient, true );

		$body = array(
			'siteDistributionId'   => absint( $distribution_id ),
			'stage'                => $stage,
			'maxConcurrentWorkers' => $advanced_params['maxConcurrentWorkers'] ?: 8,
			'workerBatchSize'      => $advanced_params['workerBatchSize'] ?: 10,
			'addUrlsBatchSize'     => $advanced_params['addUrlsBatchSize'] ?: 1000,
		);
		$response = $this->make_api_request( $path, 'POST', $body );

		return $response;
	}


	/**
	 * Execute deployment on AJAX request.
	 *
	 */
	public function ajax_deploy() {
		// Bail out now if no distribution type is set
		if ( ! isset( $_GET['distribution_id'] ) ) {
			die( 'Strattic error: No distribution id provided' );
		}

		$distribution_id = intval( $_GET['distribution_id'] );
		$this->setup_publish( $distribution_id );

		// set_transient( 'strattic-status', 'first-stage-beginning', $this->deploy_timeout );

		$this->log_data( 'Deployment requested' );


		// $site_id = $this->get_current_site_strattic_id();

		// $this->log_data( 'First stage paths acquired' );

		// set_transient( 'strattic-first-stage-paths', $paths, DAY_IN_SECONDS );
		set_transient( 'strattic-status', 'first-stage-requesting', $this->deploy_timeout );

		$response = $this->make_scrape_request( $distribution_id, 'first' );

		$result = array();

		// Send error message if no job ID set
		if ( ! isset( $response['body']['result']['id'] ) ) {

			if ( method_exists( $response, 'get_error_message' ) ) {
				$error = $response->get_error_message();
			} else {
				// $error = 'First Stage Error: no job ID';
				$error = $response;
				set_transient( 'strattic-status', 'first-stage-failed', $this->deploy_timeout );
			}

			$result['success'] = 0;
			$result['message'] = $error;

			$this->log_data( 'Request for first stage paths failed' );

			// set_transient( 'strattic-status', 'first-stage-failed', $this->deploy_timeout );

		} else {
			$job_id = absint( $response['body']['result']['id'] );

			$this->log_data( 'Request for first stage paths, job ID = ' . $job_id );
			set_transient( 'strattic-job-id', $job_id, $this->deploy_timeout );
			set_transient( 'strattic-status', 'first-stage-publishing', $this->deploy_timeout );

			$result['success'] = 1;
			$result['distributionId'] = absint( $distribution_id );
			$result['jobId'] = $job_id;

		}
		header( 'Content-Type: application/json' );

		die( json_encode( $result ) );
	}

	public function api_deploy() {
		// Bail out now if no distribution type is set
		if ( ! isset( $_GET['distribution_id'] ) ) {
			die( 'Strattic error: No distribution id provided' );
		}

		if ( ! isset( $_GET['job_id'] ) ) {
			die( 'Strattic error: No job id provided' );
		}

		$distribution_id = intval( $_GET['distribution_id'] );
		$job_id = intval( $_GET['job_id'] );

		$this->setup_publish($distribution_id);
		set_transient( 'strattic-job-id', $job_id, $this->deploy_timeout );
		set_transient( 'strattic-status', 'second-stage-publishing', $this->deploy_timeout );

		$result = array();
		$result['success'] = 1;

		ini_set('display_errors','Off');
		ini_set('error_reporting', E_ALL );
		status_header( 200 );
		header( 'Content-Type: application/json' );

		die( json_encode( $result ) );
	}

	public function setup_publish( $distribution_id ) {
		do_action( 'strattic_deployment' );
		do_action( 'strattic_update_search_results' );
		
		if ( get_transient( 'strattic-status' ) && get_transient( 'strattic-distribution-id' ) != $distribution_id ) {
			die(
				json_encode(
					array(
						'success' => 0,
						'error' => 'Another job in progress for a different distribution.',
					)
				)
			);
		}

		delete_transient( 'strattic-job-id' );
		set_transient( 'strattic-distribution-id', $distribution_id, DAY_IN_SECONDS );

		if ( isset( $_REQUEST['fullPublish'] ) && 'true' == $_REQUEST['fullPublish'] ) {
			set_transient( 'strattic-is-full-publish', 1, $this->deploy_timeout );
		}

		// Set advanced options (found in advanced section of plugin admin page)
		delete_transient( 'strattic-advanced-params' );
		if (
			isset( $_REQUEST['maxConcurrentWorkers'] )
			&&
			isset( $_REQUEST['workerBatchSize'] )
			&&
			isset( $_REQUEST['addUrlsBatchSize'] )
		) {
			$advanced_params = json_encode(
				array(
					'maxConcurrentWorkers' => $max_concurrent_workers,
					'workerBatchSize'      => $worker_batch_size,
					'addUrlsBatchSize'     => $add_urls_batch_size,
				)
			);
			set_transient( 'strattic-advanced-params', $advanced_params, $this->deploy_timeout );
		}

		$this->log_data( "Starting a deployment. \n" );
		set_transient( 'strattic-distribution-type', $this->get_distribution_type( $distribution_id ), $this->deploy_timeout );

		set_transient( 'strattic-status', 'first-stage-syncing', $this->deploy_timeout );
		$this->sync_static_files();
	
	}


	/**
	 * Syncing static files.
	 *
	 * @param  int  $distribution_id  The distribution ID
	 */
	public function sync_static_files( $retry_count = 0 ) {

		$this->log_data( 'S3 bucket syncing started. retry: ' . $retry_count );

		$distribution_type = get_transient( 'strattic-distribution-type' );

		// Run bash script
		$command = "/var/strattic/static-files/sync.sh {$distribution_type}  2>&1 | tee -a /var/log/static-files.log 2>/dev/null >/dev/null & echo $!" ;
		$this->log_data( 'S3 bucket syncing command: ' . $command );
		exec( $command, $output );
		$ps_output = shell_exec('ps -aux');
		$this->log_data( 'S3 bucket syncing ps aux: ' . $ps_output );
		sleep(1);
		$ls_output = shell_exec('ls -ali /var/strattic/site/.system/sync/');
		$this->log_data( 'S3 bucket syncing ls ali: ' . $ls_output );

		$tail_output = shell_exec('tail -n 100 /var/log/static-files.log');
		$this->log_data( 'S3 bucket syncing tail log: ' . $tail_output );

		$this->log_data( 'S3 bucket syncing started: ' . implode( '' , $output ) );
		$this->check_sync_static_files_retry($retry_count);
	}

	public function check_sync_static_files_retry( $retry_count = 0 ) {
		$syncing_complete = $this->syncing_complete();
		$syncing_busy = $this->syncing_busy();
		$this->log_data( 'S3 bucket syncing status: completed: '. $syncing_complete. ' busy: '. $syncing_busy);
		$ls_output = shell_exec('ls -ali /var/strattic/site/.system/sync/');
		$this->log_data( 'S3 bucket syncing ls ali: ' . $ls_output );
		if (!$syncing_complete && !$syncing_busy && $retry_count < 5) {
			$this->sync_static_files(++$retry_count);
		}
	}


	/**
	 * Execute deployment on AJAX request.
	 */
	public function deploy_second_stage( $distribution_id ) {

		// Bail out now if no distribution type is set
		if ( ! isset( $distribution_id ) ) {
			die( 'Strattic error: No distribution id provided' );
		}

		$this->log_data( 'Second stage beginning' );

		// Need to turn on full publish for the second stage
		set_transient( 'strattic-is-full-publish', 1, $this->deploy_timeout );

		// Get list of URLs
		$this->log_data( 'Second stage paths acquired' );

		$response = $this->make_scrape_request( $distribution_id, 'second' );
		// Send error message if no job ID set
		if ( ! isset( $response['body']['result']['id'] ) ) {

			if ( method_exists( $response, 'get_error_message' ) ) {
				$error = $response->get_error_message();
			} else {
				$error = 'Unknown error: no job ID';
			}

			$this->log_data( 'Request for second stage failed: ' . json_encode( $response ) );

			// set_transient( 'strattic-status', 'first-stage-failed', $this->deploy_timeout );

		} else {
			$job_id = absint( $response['body']['result']['id'] );

			$this->log_data( 'Request for second stage paths, job ID = ' . $job_id );
			set_transient( 'strattic-job-id', $job_id, $this->deploy_timeout );
			set_transient( 'strattic-status', 'second-stage-publishing', DAY_IN_SECONDS );
		}

		$this->log_data( 'Second stage publishing beginning' );

	}

	public function get_job_status( $job_id ) {
		if ( ! $job_id ) {
			return false;
		}
		$response = $this->make_api_request( 'scrapejob/' . absint( $job_id ), 'GET', array() );

		//look up "status" here

		$progress = '';
		$status = '';
		if ( isset( $response['body']['result'] ) ) {
			$result = $response['body']['result'];

			$progress = $result['progress'];

			if ( isset( $result['status'] ) ) {
				$job_status = $result['status'];
				if ( 'failed' === $job_status || 'cancelled' === $job_status) {
					if ( 'second-stage-publishing' === get_transient( 'strattic-status' ) ) {
						set_transient( 'strattic-status', 'second-stage-failed', DAY_IN_SECONDS );
					} else {
						set_transient( 'strattic-status', 'first-stage-failed', DAY_IN_SECONDS );
					}
				}
				else if (
					'completed' === $job_status
					||
					( 'false' === $job_status && 0 === absint( $job_id ) ) // Catering for situations in which the job status never gets to 'completed' and puts the site into an eternal 'second-stage'completed' mode.
				) {
					$deploying = false;
					if ( 'second-stage-publishing' === get_transient( 'strattic-status' ) ) {
						set_transient( 'strattic-status', 'second-stage-completed', DAY_IN_SECONDS );
					} else {
						set_transient( 'strattic-status', 'first-stage-completed', DAY_IN_SECONDS );
					}
				}
			}

			$distribution_type = '';
			if ( isset( $result['siteDistribution']['type'] ) ) {
				$distribution_type = $result['siteDistribution']['type'];
			}

			$distribution_id = '';
			if ( isset( $result['siteDistributionId'] ) ) {
				$distribution_id = $result['siteDistributionId'];
			}
		}

		$response_message = '';
		if ( isset( $response['message'] ) ) {
			$response_message = esc_html( $response['message'] );
		}

		return array_merge(
			array(
				'response' => $response_message,
				'status' => $job_status,
			), (array) $progress
		);
	}

	public function syncing_busy() {
		$syncBusyPath = '/var/strattic/site/.system/sync/.syncing';
		return file_exists( $syncBusyPath );
	}

	public function syncing_complete() {
		$distribution_type = get_transient( 'strattic-distribution-type' );
		if ( ! $distribution_type ) {
			return false;
		}
		$syncCompletedPath = '/var/strattic/site/.system/sync/' . $distribution_type;
		return file_exists( $syncCompletedPath );
	}


	/**
	 * Clear all the values in the database that have to do with the publish process
	 */
	public function clear_all_publish_values()
	{
		$success = true; 
		$strattic_transients = [ 'status', 'job-id', 'distribution-id', 'distribution-type', 'is-full-publish', 'advanced-params' ];
	
		foreach( $strattic_transients as $transient_name )
		{
			$deleted = delete_transient( 'strattic-' . $transient_name);
			if( ! $deleted) $success = false;
		}
		return $success;
	}

	/**
	 * Get publication status via AJAX request.
	 */
	public function ajax_status() {
		header( 'Content-Type: application/json' );

		// We are in the middle of a publish
		$deploying = true;
		$job_id = absint( get_transient( 'strattic-job-id' ) );
		$distribution_id = absint( get_transient( 'strattic-distribution-id' ) );
		$status = get_transient( 'strattic-status' );

		$response = $this->make_api_request( 'sites/current' );
		$data = array(
			'deploying' => false,
			'status'    => false,
		);
		if ( isset( $response['body']['result']['created'] ) && 0 === $response['body']['result']['created'] ) {
			$data['error'] = 'site_creating';
		} else if ( isset( $response['body']['result']['updated'] ) && 0 === $response['body']['result']['updated'] ) {
			$data['error'] = 'site_updating';
		} else if ( isset( $response['body']['result']['runningStatus'] ) && 'running' != $response['body']['result']['runningStatus'] ){
			$data['error'] = 'site_not_running';
		}

		if ( ! get_transient( 'strattic-status' ) ) {
			echo json_encode( $data );
			die();
		}

		$data['deploying'] = $deploying;
		$data['jobId'] = $job_id;
		$data['distributionId'] = $distribution_id;
		$data['status'] = $status;

		switch ( $status ) {

			case 'first-stage-completed':
				set_transient( 'strattic-status', 'second-stage-requesting', $this->deploy_timeout );
				$this->deploy_second_stage( $distribution_id );
				break;

			case 'first-stage-failed':
			case 'second-stage-failed':
				delete_transient( 'strattic-status' );
				delete_transient( 'strattic-job-id' );
				delete_transient( 'strattic-distribution-id' );
				break;

			case 'second-stage-completed':
				set_transient( 'strattic-status', 'syncing-images', $this->deploy_timeout );
				break;

			case 'syncing-images':
				if ( $this->syncing_complete() ) {
					set_transient( 'strattic-status', 'publishing-completed', $this->deploy_timeout );
				} else {
					$this->check_sync_static_files_retry();
				}
				break;

			case 'publishing-completed':
				$this->clear_all_publish_values();
				break;

			case 'first-stage-publishing':
			case 'second-stage-publishing':
				$data['job_progress'] = $this->get_job_status( $job_id );

				// Hack to deal with situations in which the job ID is zero, but the publication isn't listed as complete yet
				if ( false === $data['job_progress'] ) {
					set_transient( 'strattic-status', 'publishing-completed' );
				}

				break;

		}

		echo json_encode( $data );
		die;

	}

}
