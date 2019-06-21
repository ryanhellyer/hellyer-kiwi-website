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
		if ( ! isset( $_GET['strattic-ajax'] ) || ! isset( $_GET['nonce'] ) ) {
			return;
		}

		// Nonce security check
		if ( ! wp_verify_nonce( $_GET['nonce'], 'strattic' ) && ! defined( 'STRATTIC_TEST' ) ) {
			wp_die( 'Failed nonce check' );
		}

		// Permissions security check
		if ( ! current_user_can( $this->permissions ) ) {
			wp_die( 'Error: Insufficient permission' );
		}

		if ( 'deploy' === $_GET['strattic-ajax'] ) {
			add_action( 'admin_init', array( $this, 'ajax_deploy' ) );
		}

		if ( 'status' === $_GET['strattic-ajax'] ) {
			add_action( 'admin_init', array( $this, 'ajax_status' ) );
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
			$result['message'] = $error;

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

		$this->sync_static_files( $distribution_id );
	
		set_transient( 'strattic-distribution-type', $distribution_type, $this->deploy_timeout );
	}

	/**
	 * Syncing static files.
	 *
	 * @param  int  $distribution_id  The distribution ID
	 */
	public function sync_static_files( $distribution_id ) {

		$this->log_data( 'S3 bucket syncing started' );
		set_transient( 'strattic-status', 'first-stage-syncing', $this->deploy_timeout );

		// Work out what the distribution type is for this ID
		$distributions = $this->get_distribution_info();
		foreach ( $distributions as $key => $distribution ) {

			if ( isset( $distribution['type'] ) && isset( $distribution['id'] ) && $distribution_id === $distribution['id'] ) {
				$distribution_type = $distribution['type'];
			}

		}

		// Run bash script
		$command = "/var/strattic/static-files/sync.sh {$distribution_type}  2>&1 | tee -a /var/log/static-files.log 2>/dev/null >/dev/null & echo $!" ;
		$this->log_data( 'S3 bucket syncing command: ' . $command );
		exec( $command, $output );

		$this->log_data( 'S3 bucket syncing completed: ' . implode( '' , $output ) );
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

		if ( ! get_transient( 'strattic-status' ) ) {
			$args = array(
				'deploying' => false,
				'status'    => false,
			);
			if ( isset( $response['body']['result']['created'] ) && 0 === $response['body']['result']['created'] ) {
				$args['error'] = 'site_creating';
			} else if ( isset( $response['body']['result']['updated'] ) && 0 === $response['body']['result']['updated'] ) {
				$args['error'] = 'site_updating';
			}

			echo json_encode( $args );
			die();
		}

		$data = array(
			'deploying'         => $deploying,
			'jobId'             => $job_id,
			'distributionId'    => $distribution_id,
			'status'            => $status,
		);
		if ( isset( $response['body']['result']['created'] ) && 0 === $response['body']['result']['created'] ) {
			$data['error'] = 'site_creating';
		} else if ( isset( $response['body']['result']['updated'] ) && 0 === $response['body']['result']['updated'] ) {
			$data['error'] = 'site_updating';
		}

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
				$syncCompletedPath = '/var/strattic/site/.system/sync/' . get_transient( 'strattic-distribution-type' );
				if ( file_exists( $syncCompletedPath ) ) {
					set_transient( 'strattic-status', 'publishing-completed', $this->deploy_timeout );
				}
				break;

			case 'publishing-completed':
				delete_transient( 'strattic-status' );
				delete_transient( 'strattic-job-id' );
				delete_transient( 'strattic-distribution-id' );
				delete_transient( 'strattic-distribution-type' );
				delete_transient( 'strattic-is-full-publish' );
				delete_transient( 'strattic-advanced-params' );
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
