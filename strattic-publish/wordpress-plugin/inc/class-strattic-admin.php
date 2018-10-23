<?php

/**
 * Strattic admin page
 * 
 * @copyright Copyright (c), Strattic
 * @since 1.1
 */
class Strattic_Admin extends Strattic_Core {

	/**
	 * Class constructor
	 */
	public function __construct() {
//add_action( 'admin_init', array( 'Strattic_Form_Processing', 'send' ) );

		add_action( 'admin_menu',            array( $this, 'add_menu_item' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) );
		add_action( 'admin_bar_menu',        array( $this, 'add_toolbar_link' ), 500 );
		add_action( 'init',                  array( $this, 'ajax' ) );
		add_action( 'admin_head',            array( $this, 'remove_menu_item' ) );

		// Add filters
		add_filter( 'parent_file', array( $this, 'highlight_menu' ) );

	}

	/**
	 * Remove menu item.
	 */
	public function remove_menu_item() {

		if (
			is_plugin_active( 'strattic/strattic.php' )
			&&
			! $this->is_uber_admin()
		) {
			remove_menu_page( 'strattic' );
		}

	}

	/**
	 * Adding the Strattic menu item.
	 */
	public function add_menu_item() {

		add_menu_page(
			'Strattic',
			'Strattic',
			'manage_options',
			'strattic',
			array( $this, 'admin_page' ),
			'none',
			1
		);

	}

	/**
	 * Admin page content.
	 */
	public function admin_page(){

		// If deployment was completed, then just start from scratch again
		$message = trim( shell_exec( 'redis-cli get message' ) );
		if ( 'complete' === $message ) {
			shell_exec( 'redis-cli del message' );
			shell_exec( 'redis-cli del percentage' );
		}

		$deployment_settings = get_option( 'strattic-deployment-settings' );
		$deployment_type = '';
		if ( isset( $deployment_settings[ 'deployment-type' ] ) ) {
			$deployment_type = $deployment_settings[ 'deployment-type' ];
		}

		$deploying = false;
		if ( isset( $deployment_settings[ 'deploying' ] ) ) {
			$deploying = true;
		}

		?>

		<div class="wrap">

			<h1><?php esc_html_e( 'Strattic - Deploy your site', 'strattic' ); ?></h1>

			<?php do_action( 'strattic-after-title' ); ?>

			<p>

				<select id="strattic-deployment-type" name="strattic-deployment-type">
					<option <?php selected( $deployment_type, '' ); ?> disabled="disabled"><?php esc_html_e( 'Choose a deployment type', 'strattic' ); ?></option>
					<option <?php selected( $deployment_type, 'live' ); ?> value="live" data-url="<?php echo esc_url( $this->get_admin_setting( 'live-url' ) ); ?>" data-description="<?php esc_html_e( 'Publishes a static version of your live site.', 'strattic' ); ?>"><?php esc_html_e( 'Live', 'strattic' ); ?></option>
					<option <?php selected( $deployment_type, 'test' ); ?> value="test" data-url="<?php echo esc_url( $this->get_admin_setting( 'test-url' ) ); ?>" data-description="<?php esc_html_e( 'Allows you to test how your static site will look on a test domain before deploying your changes to your live site.', 'strattic' ); ?>"><?php esc_html_e( 'Test', 'strattic' ); ?></option><?php

					// Show extra deployment option for Strattic admins
					if ( $this->is_uber_admin() ) {
						?>

					<option <?php selected( $deployment_type, 'dev' ); ?> value="dev" data-url="<?php echo esc_url( $this->get_admin_setting( 'dev-url' ) ); ?>" data-description="<?php esc_html_e( 'Publishes to the dev site specified under advanced settings - this is only visible to internal Strattic admins', 'strattic' ); ?>">Dev</option><?php
					}

					?>
				</select>

			</p>

			<p class="description" id="strattic-deployment-type-description">&nbsp;</p>

			<p>
				<input class="button button-primary" <?php

				if ( true === $deploying ) {
					echo 'disabled="disabled" ';
				}

				?>id="strattic-deploy" type="button" value="<?php esc_html_e( 'Deploy', 'strattic' ); ?>" />
			</p>

			<progress id="strattic-progress" value="0" max="100"> </progress>
			<span id="strattic-status"></span>

			<p id="strattic-message"></p>

			<p>

				<a id="strattic-site-link" href="<?php echo esc_url( $this->get_admin_setting( 'live-url' ) ); ?>">
					<?php esc_html_e( 'Visit site', 'strattic' ); ?> (<span><?php echo esc_url( $this->get_admin_setting( 'live-url' ) ); ?></span>) &raquo;
				</a>
			</p>

			<a id="strattic-advanced-settings-button" href="#">
				<?php esc_html_e( 'Advanced settings', 'strattic' ); ?> &raquo;
			</a>

			<div id="strattic-advanced-settings">

				<?php $this->the_horizontal_menu(); ?>

				<p id="strattic-log-wrapper">
					<?php esc_html_e( 'File log', 'strattic' ); ?>
					<span id="strattic-time-estimate"></span>
					<textarea id="strattic-log" disabled="yes"></textarea>
				</p>

			</div>

		</div><?php
	}

	/**
	 * Add admin script.
	 *
	 * @param  string  The admin page being accessed
	 */
	public function admin_script( $page ) {

		// Bail out now if not on Strattic admin page
		if ( 'toplevel_page_strattic' !== $page ) {
			return;
		}


		wp_enqueue_script(
			'strattic-admin',
			STRATTIC_ASSETS . 'strattic.js',
			array(),
			STRATTIC_VERSION
		);

		/*
		wp_localize_script(
			'strattic-admin',
			'strattic_final_message',
			esc_html__( 'Stage 1 deployment is complete. An end to end process will continue in the background. You will receive an email when the final deployment stage completes. Please wait to deploy further changes until after you receive the email to ensure that your changes are deployed completely.', 'strattic' )
		);
		*/

		wp_localize_script(
			'strattic-admin',
			'strattic_ajax',
			array(
				'url' => admin_url( '?strattic-ajax=' ),
				'nonce' => wp_create_nonce('strattic'),
			)
		);

	}

	/**
	 * Add custom styling for admin panel.
	 */
	public function admin_style() {

		wp_enqueue_style(
			'strattic-admin',
			STRATTIC_ASSETS . 'strattic.css',
			array(),
			STRATTIC_VERSION
		);

	}

	/**
	 * Add toolbar link.
	 */
	public function add_toolbar_link( $admin_bar ) {

		$admin_bar->add_menu( array(
			'id'    => 'strattic',
			'title' => '<span class="ab-icon"></span> Strattic',
			'href'  => esc_url( admin_url( 'admin.php?page=strattic' ) ),
			'meta'  => array(
				'title' => 'Strattic',
			),
		) );

	}

	/**
	 * Highlight the main menu item when on submenu.
	 *
	 * @global  string  $plugin_page  The plugin page slug
	 */
	function highlight_menu( $file ) {
		global $plugin_page;

		if (
			'strattic-search' == $plugin_page
			||
			'discovered-links' == $plugin_page
			||
			'manual-links' == $plugin_page
		) {
			$plugin_page = 'strattic';
		}

		return $file;
	}

	/**
	 * Process AJAX requests.
	 *
	 * @TODO Implement nonce and user permissions check here
	 */
	public function ajax() {

		// Check we're at the correct URL
		if ( ! isset( $_GET[ 'strattic-ajax'] ) || ! isset( $_GET[ 'nonce'] ) ) {
			return;
		}

		// Nonce security check
		if ( ! wp_verify_nonce( $_GET[ 'nonce'], 'strattic' ) ) {
			wp_die( 'Failed nonce check' );
		}

		// Send request to deploy
		if ( 'deploy' === $_GET[ 'strattic-ajax'] ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );

			$site_directory = get_home_path();

			$deployment_type = esc_html( $_GET[ 'deployment_type' ] );

			$options[ 'deployment-type' ] = $deployment_type;
			$options[ 'deploying' ] = true;

			// Create URL for accessing API
			$url = 'https://apidomain.com/?deployment_type=' . $deployment_type . '&home_url=' . STRATTIC_HOME_URL;

			// If on "dev" deployment type, then override settings
			if ( 'dev' === $deployment_type ) {

				$cloudfront_url = $this->get_admin_setting( 'cloudfront-url' );
				$cloudfront_id  = $this->get_admin_setting( 'cloudfront-id' );
				$s3_bucket      = $this->get_admin_setting( 's3-bucket' );
				$email          = $this->get_admin_setting( 'email' );

				if ( '' != $cloudfront_url ) {
					$options[ 'cloudfront-url' ] = $cloudfront_url;
				}
				if ( '' != $cloudfront_id ) {
					$options[ 'cloudfront-id' ] = $cloudfront_id;
				}
				if ( '' != $s3_bucket ) {
					$options[ 's3-bucket' ] = $s3_bucket;
				}
				if ( '' != $email ) {
					$options[ 'email' ] = $email;
				}

				$url .= '&cloudfront_url=' . $cloudfront_url . '&cloudfront_id=' . $cloudfront_id . '&s3_bucket=' . $s3_bucket . '&email=' . $email;
			}

			update_option( 'strattic-deployment-settings', $options );

			// Test code for accessing raw command
			if ( isset( $_GET['get-command' ] ) ) {
				echo $url;
				die;
			}

			// Send request to deploy
			file_get_contents( $url );
		}

		// Load the redis script
		if ( 'redis' === $_GET[ 'strattic-ajax'] ) {

			$message = trim( shell_exec( 'redis-cli get message' ) );

			if ( 'acquiring-urls' === $message ) {
				$message = esc_html__( 'The URLs to deploy are being acquired.', 'strattic' );
			} else if ( 'acquired-urls' === $message ) {
				$message = esc_html__( 'The URLs have been acquired and are being deployed to your site. You can track their progress via the bar above.', 'strattic' );
			} else if ( 'first-stage-complete' === $message ) {
				$message = esc_html__( 'All updated pages have been deployed. You can track the progress of the remaining pages via the bar above.', 'strattic' );
			} else if ( 'second-stage-complete' === $message ) {
				$message = esc_html__( 'Publishing is complete! A background process is underway for deploying less important content on your website.', 'strattic' );
			} else if ( 'third-stage-complete' === $message ) {
				$message = esc_html__( 'Deployment is complete!', 'strattic' );
			} else if ( 'complete' === $message ) {
				$message = esc_html__( 'Deployment is totally complete!', 'strattic' );
			} else {
				$message = esc_html__( '', 'strattic' );
			}

			// Calculate percentage complete
			$url_num = trim( shell_exec( 'redis-cli get number_of_important_urls' ) );
			$urls_processed = trim( shell_exec( 'redis-cli get number_important_urls_processed' ) );
			if (
				is_numeric( $url_num )
				&&
				is_numeric( $urls_processed )
			) {
				$percentage = round( 100 * ( $urls_processed / $url_num ) );
			} else {
				$percentage = '';
			}

/***************** RANDOM DATA FOR TESTING *****************/
$percentage = STRATTIC_PERCENTAGE;
$message = STRATTIC_MESSAGE;

			// Get most recent deployment type
			$deployment_settings = get_option( 'strattic-deployment-settings' );
			$deployment_type = '';
			if ( isset( $deployment_settings[ 'deployment-type' ] ) ) {
				$deployment_type = $deployment_settings[ 'deployment-type' ];
			}

			// Process the log
			$log = str_replace( STRATTIC_HOME_URL, STRATTIC_CLOUDFRONT_URL, 'SOME LOG DATA SHOULD GO HERE ONCE API IS COMPLETE' );
			if ( '' === $log || empty( $log ) ) {
				$log = '';
			}

			// Output data
			$data = array(
				'message'         => $message,
				'percentage'      => $percentage,
				'log'             => $log,
				'deployment_type' => $deployment_type,
			);

			if ( isset( $deployment_settings[ 'deploying' ] ) ) {
				$data[ 'deploying' ] = true;
			}

			Strattic_Form_Processing::send_contact_forms();

			echo json_encode( $data );
		}

		die;
	}

}
