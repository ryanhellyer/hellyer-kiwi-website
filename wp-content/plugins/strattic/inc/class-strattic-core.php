<?php

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;

use \Firebase\JWT\JWT;

/**
 * Strattic Core methods.
 *
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_Core {

	const PER_PAGE = 100;
	const MEMORY_LIMIT = 1024;
	const TIME_LIMIT = HOUR_IN_SECONDS;

	protected $permissions = 'publish_posts';

	/**
	 * Get the current page URL.
	 *
	 * @access  protected
	 * @return  string  The URL
	 */
	protected function get_current_url() {

		// Bail out if server variable not set - problem when using WP CLI
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			return '';
		}

		$url = 'http';
		if ( is_ssl() ) {
			$url .= 's';
		}
		$url .= '://';

		if ( '' !== $_SERVER['SERVER_NAME'] && '_' !== $_SERVER['SERVER_NAME'] ) {
			$url .= $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
		} else {
			$url .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}

		return $url;
	}

	/**
	 * Get the current page URL.
	 *
	 * @access  protected
	 * @return  string  The URL path
	 */
	protected function get_current_path() {

		$url = $this->get_current_url();
		$path = str_replace( home_url(), '', $url );

		// Nasty hack to return the API path when $url is having bizarre unexplained issues with string lengths that don't match what they look like they should match. This was a problem on Wadi Digital and needed to be hacked in for emergency purposes and must be fixed at a later date.
		if ( strpos( $url, '/strattic-urls/' ) !== false ) {
			return '/strattic-urls/';
		} else {
			return $path;
		}

		return $path;
	}

	/**
	 * Parse the JWT token.
	 *
	 * @param  string  $jwt   The JSON web token
	 * @return string  The body code
	 */
	protected function parse_jwt( $jwt ) {
		$tks = explode( '.', $jwt );
		if ( count( $tks ) < 3 ) {
			return array();
		}
		list( $headb64, $bodyb64, $cryptob64 ) = $tks;
		// $header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64));
		$body = JWT::jsonDecode( JWT::urlsafeB64Decode( $bodyb64 ) );
		// print_r($header);
		// print_r($body);
		return $body;
	}

	/**
	 * Renew the token.
	 * Legacy system. This is now accessing the data via the $_ENV var.
	 *
	 * @return  string  The refreshed token
	 */
	protected function renew_token() {
		$region = get_option( 'strattic-cognito-region' );
		$client_id = get_option( 'strattic-cognito-client-id' );
		$id_token = get_option( 'strattic-id-token' );
		$refresh_token = get_option( 'strattic-refresh-token' );

		if ( ! ( $region ) || ! ( $client_id ) || ! ( $refresh_token ) ) {
			return false;
		}

		$body = $this->parse_jwt( $id_token );

		$client = new CognitoIdentityProviderClient(
			[
				'version' => '2016-04-18',
				'region' => $region,
				'credentials' => false,
			]
		);
		$args = [
			'AuthFlow' => 'REFRESH_TOKEN_AUTH',
			'AuthParameters' => [
				'REFRESH_TOKEN' => $refresh_token,
			],
			'ClientId' => $client_id,
		];

		/**
		 * Check if token failed recently, if it did then bail out now.
		 * This step is required because the initiateAuth() method serves a fatal
		 *     error when an invalid refresh token is sent. We have not found a
		 *     way to continue the execution after this fatal error yet.
		 */
		$key = 'strattic-refresh-token-fail';
		if ( '1' === get_transient( $key ) ) {
			return;
		}
		set_transient( $key, '1', 10 * MINUTE_IN_SECONDS );

		$result = $client->initiateAuth( $args );

		delete_transient( $key ); // deleting transient used for checking token auth worked

		$authenticationResult = $result->get( 'AuthenticationResult' );
		$id_token = $authenticationResult['IdToken'];
		$id_token = esc_html( $id_token );

		update_option( 'strattic-id-token', $id_token, false );

		return $id_token;
	}

	/**
	 * Get a valid API token.
	 * Legacy system. This is now accessing the data via the $_ENV var.
	 *
	 * @return  string  The ID token
	 */

	protected function get_valid_token() {
		$id_token = get_option( 'strattic-id-token' );
		$body = $this->parse_jwt( $id_token );
		$fiveMinutes = time() + (5 * 60);
		if ( ! isset( $body->exp ) || $body->exp < $fiveMinutes ) {
			$id_token = $this->renew_token();
		}

		return $id_token;
	}

	/**
	 * Make an API request.
	 *
	 * @param  string  $path    The request path
	 * @param  string  $method  GET or POST
	 * @param  array   $params  The paramters for GET requests
	 * @return array
	 */
	protected function make_api_request( $path, $method = 'GET', $params = array() ) {
		$id_token = '';
		$base_url = STRATTIC_API_URL;
		if ( isset( $_ENV['PLUGIN_API_KEY'] ) ) {
			$id_token = $_ENV['PLUGIN_API_KEY'];
			$base_url = STRATTIC_PLUGIN_API_URL;
		} else {
			$id_token = $this->get_valid_token();
		}
		$args = array(
			'method'        => $method,
			'timeout'       => 30,
			'httpversion'   => '1.1',
			'headers'       => array(
				'content-type'  => 'application/json',
				'authorization' => $id_token,
			),
		);
		if ( $method != 'GET' ) {
			$args['body'] = json_encode( $params );
		}

		$args = $this->set_user_agent( $args );

		if ( isset( $_GET['test'] ) ) {
			echo "Request:\n";
			print_r( $base_url . $path );
			echo "\n\n";
			print_r( $args );
			echo "\n\n";
		}

		$response = wp_remote_request(
			$base_url . $path,
			$args
		);

		if ( isset( $_GET['test'] ) ) {
			echo "Response:\n";
			print_r( $response );
			echo "\n\n";
		}

		$result = array();

		if ( is_array( $response ) && isset( $response['response']['message'] ) ) {
			$result['message'] = $response['response']['message'];
		}

		if ( is_array( $response ) && isset( $response['response']['code'] ) ) {
			$result['statusCode'] = $response['response']['code'];
		}

		if ( is_array( $response ) && isset( $response['body'] ) ) {
			$result['body'] = json_decode( $response['body'], true );
		}

		// If error message, store it for later use (show error message in admin panel)
		if (
			is_array( $response )
			&&
			isset( $response['response']['code'] )
			&&
			(
				'site_creating' === $response['response']['code']
				||
				'site_updating' === $response['response']['code']
			)
		) {
			$error_code = esc_html( $response['response']['code'] );
			set_transient( 'strattic-error', $error_code, HOUR_IN_SECONDS );
		} else {
			delete_transient( 'strattic-error' );
		}

		return $result;
	}

	/**
	 * Gets information about the various distrbutions avaialable.
	 *
	 * @access  protected
	 * @return  array  The distribution information
	 */
	protected function get_distribution_url( $type = 'live' ) {
		$url = false;

		foreach ( $this->get_distribution_info() as $key => $distribution ) {

			if ( isset( $distribution['url'] ) && isset( $distribution['type'] ) && $type === $distribution['type'] ) {
				$url = $distribution['url'];
			}
		}

		return $url;
	}

	/**
	 * Get information from the API about the current site.
	 *
	 * @access  protected
	 * @return  array  The distribution information
	 */
	protected function get_current_site_api_data() {
		$key = 'strattic-site-data';
//print_r( $_ENV );
$response = $this->make_api_request( 'sites/current' );
//print_r( $response );die;
		//delete_transient( $key ); // Uncomment this to flush the cache out during testing

		// Grab from cache if recently queried
		if ( false === ( $result = get_transient( $key ) ) ) {
			$response = $this->make_api_request( 'sites/current' );

			// If response is good, store, otherwise serve error
			if ( isset( $response['body']['result'] ) ) {
				$result = $response['body']['result'];
				$result['success'] = 1;
				set_transient( $key, $result, MINUTE_IN_SECONDS );
			} else {
				$result['success'] = 0;
				$result['message'] = 'Error: Strattic API response failure.';
			}
		}

		return $result;
	}

	/**
	 * Get the most recent job ID for the current site.
	 *
	 * @access  protected
	 * @return  int  The job ID
	 */
	protected function get_most_recent_job_id() {
		$response = $this->make_api_request( 'sites/current' );

		if ( ! isset( $response['body']['result']['distributions'] ) ) {
			return false;
		}

		$distributions = $response['body']['result']['distributions'];
		$jobs = array();
		foreach ( $distributions as $key => $distribution ) {
			$distribution_id = $distribution['id'];
			$last_scrape_jobs = $distribution['lastScrapeJobs'];

			foreach ( $last_scrape_jobs as $x => $job ) {
				$job_id = absint( $job['id'] );
				$end_time = strtotime( $job['endTime'] );
				if ( empty( $end_time ) ) {
					$end_time = time() * 10;
				}

				$jobs[] = array(
					'end_time' => $end_time,
					'job_id'   => $job_id,
				);

			}
		}

		// Sort jobs with latest job first
		usort(
			$jobs, function( $a, $b ) {
				if ( $a['end_time'] == $b['end_time'] ) {
					return 0;
				}
				return ($a['end_time'] < $b['end_time']) ? -1 : 1;
			}
		);

		// Grab latest job
		$job_id = null;
		if ( isset( $jobs[0]['job_id'] ) ) {
			$job_id = $jobs[0]['job_id'];
		}

		return $job_id;
	}

	protected function log_data( $string ) {
		$dir = wp_upload_dir();
		$dir = $dir['basedir'];

		$content = "\n" . current_time( 'Y-m-d h:i:s' ) . ': ' . $string;

		file_put_contents( $dir . '/log.strattic', $content, FILE_APPEND );
	}


	/**
	 * Gets information about the various distrbutions available.
	 *
	 * @access  protected
	 * @param   int    $distribution_id   The distribution ID
	 * @return  array  The distribution information
	 */
	protected function get_most_recent_publications( $distribution_id ) {

		$response = $this->make_api_request( 'sites/current' );

		// Bail out now if no distributions found
		if ( ! isset( $response['body']['result']['distributions'] ) ) {
			return array();
		}

		$distributions = $response['body']['result']['distributions'];

		$recent_publications = array();
		foreach ( $distributions as $key => $distribution ) {
			if ( absint( $distribution_id ) == $distribution['id'] ) {
				$last_scrape_jobs = $distribution['lastScrapeJobs'];

				foreach ( $last_scrape_jobs as $x => $job ) {
					$job_id = absint( $job['id'] );
					$start_time = $job['startTime'];
					$end_time = $job['endTime'];
					$status = esc_html( $job['status'] );

					if ( empty( $end_time ) ) {
						$end_time = time();
					}

					$recent_publications[] = array(
						'job_id'     => $job_id,
						'start_time' => $start_time,
						'end_time'   => $end_time,
						'job_id'     => $job_id,
						'status'     => $status,
					);

				}
			}
		}

		// Sort in order of most recent end time
		usort(
			$recent_publications,
			function ( $a, $b ) {
				return $b['end_time'] - $a['end_time'];
			}
		);

		return $recent_publications;
	}

	/**
	 * Gets the timestamp for the most recent publication of this distribution.
	 *
	 * @access  protected
	 * @param   int    $distribution_id   The distribution ID
	 * @return  int  Timestamp
	 */
	protected function get_most_recent_completed_published_timestamp( $distribution_id ) {

		$cache_key = 'strattic-recent-completed-time-' . $distribution_id;

		$timestamp = wp_cache_get( $cache_key );
		if ( false === $timestamp ) {

			$publications = $this->get_most_recent_publications( $distribution_id );

			// Sort in order of most recent end time
			usort(
				$publications,
				function ( $a, $b ) {
					return $a['end_time'] - $b['end_time'];
				}
			);

			$timestamp = 'none';
			foreach ( $publications as $key => $publication ) {

				if ( 'completed' === $publication['status'] ) {
					$timestamp = $publication['end_time'];
					break;
				}
			}

			wp_cache_set( $cache_key, $timestamp );
		}

		return $timestamp;
	}
	/**
	 * Gets the timestamp for the publication before the last one for this distribution.
	 *
	 * @access  protected
	 * @param   int    $distribution_id   The distribution ID
	 * @return  int  The timestamp
	 */
	protected function get_last_relevant_publish_date( $distribution_id ) {

		$publications = $this->get_most_recent_publications( $distribution_id );

		$timestamp = false;
		$timestamp_2 = false;
		foreach ( $publications as $key => $publication ) {

			if ( 'completed' === $publication['status'] ) {
				$timestamp = $timestamp_2;
				$timestamp_2 = $publication['end_time'];
			}
		}

		return $timestamp;
	}

	/**
	 * Get the current sites Strattic ID.
	 *
	 * @access  protected
	 * @return  string  The site ID
	 */
	protected function get_current_site_strattic_id() {

		$site_data = $this->get_current_site_api_data();
		if ( isset( $site_data['id'] ) ) {
			return absint( $site_data['id'] );
		} else {
			return null;
		}

	}

	/**
	 * Is the site currently deploying?
	 * Returns true if the site is still deploying.
	 *
	 * @access  protected
	 * @param   string   $distribution_id   The distribution type to check (usually live or test)
	 * @return  bool
	 */
	protected function is_deploying( $distribution_id ) {

		$site_data = $this->get_current_site_api_data();
		if ( isset( $site_data['distributions'] ) ) {
			$distributions = $site_data['distributions'];
			foreach ( $distributions as $key => $distribution ) {
				if ( isset( $distribution['id'] ) && $distribution['id'] === $distribution_id ) {

					// Check last job status
					if ( isset( $distribution['lastScrapeJobs'][0]['status'] ) ) {

						// If task is completed or cancelled, then it's over and deployment is no longer happening
						if (
							'completed' === $distribution['lastScrapeJobs'][0]['status']
							||
							'cancelled' === $distribution['lastScrapeJobs'][0]['status']
						) {
							$status = $distribution['lastScrapeJobs'][0]['status'];

							return false;
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Gets information about the various distrbutions available.
	 *
	 * @access  protected
	 * @return  array  The distribution information
	 */
	protected function get_distribution_info() {

		$site_data = $this->get_current_site_api_data();
		$distribution_info = array();
		if ( isset( $site_data['distributions'] ) ) {
			foreach ( $site_data['distributions'] as $key => $distribution ) {

				$distribution_info[] = array(
					'url'  => esc_url( 'https://' . $distribution['domainName'] ),
					'type' => esc_html( $distribution['type'] ),
					'id'   => absint( $distribution['id'] ),
				);

			}
		}

		// Put "live" ahead of "test"
		usort(
			$distribution_info, function( $a, $b ) {
				if ( $a['type'] == $b['type'] ) {
					return 0;
				}
				return ($a['type'] < $b['type']) ? -1 : 1;
			}
		);

		return $distribution_info;
	}

	/**
	 * Get live distribution ID.
	 *
	 * @access  protected
	 * @return  array  The live distribution ID
	 */
	protected function get_live_distribution_id() {
		$cache_key = 'strattic-live-distribution-id';

		$live_distribution_id = wp_cache_get( $cache_key );
		if ( false === $live_distribution_id ) {

			$live_distribution_id = null;
			$data = $this->get_distribution_info();
			foreach ( $data as $key => $distribution ) {

				if ( 'live' === $distribution['type'] ) {
					$live_distribution_id = $distribution['id'];
				}
			}

			wp_cache_set( $cache_key, $live_distribution_id );
		}

		return $live_distribution_id;
	}

	/**
	 * True if user is a Strattic admin.
	 */
	protected function is_strattic_admin() {

		// If user can't manage options, then they're not an admin
		if ( ! current_user_can( 'manage_options' ) ) {
			return false;
		}

		$user_id = get_current_user_id();
		$user = get_userdata( $user_id );

		// If user has no email address then they're not a Strattic admin
		if ( ! isset( $user->user_email ) ) {
			return false;
		}

		$user_email = $user->user_email;

		// If user email address is @strattic.com, then they're an admin
		if ( '@strattic.com' === substr( $user_email, -13 ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Set the user agent in remote arguments.
	 *
	 * @return  string  The user agent
	 */
	protected function set_user_agent( $args ) {

		if ( defined( 'STRATTIC_WORDPRESS_USER_AGENT' ) ) {
			$args['headers']['user-agent'] = STRATTIC_WORDPRESS_USER_AGENT;
		}

		return $args;
	}

}
