<?php

/**
 * Strattic admin page
 *
 * @copyright Copyright (c), Strattic
 * @since 1.1
 */
class Strattic_Admin extends Strattic_Core {

	private $strattic_strings = array();
	private $gutenberg_notices = array();

	/**
	 * Class constructor
	 */
	public function __construct() {

		$this->gutenberg_notices = array(
			'performing_updates' => esc_html__( 'Strattic is performing background updates and cannot be published at the moment. We’ll be finished shortly.', 'strattic' ),
			'site_publishing'    => esc_html__( 'Your site is publishing! Note that any changes you make during this time could get published.', 'strattic' ),
		);

		$this->strattic_strings = array(
			'publish'           => esc_html__( 'Strattic Publish', 'strattic' ),
			'publishing'        => esc_html__( 'Publishing…', 'strattic' ),
			'publishingTest'    => esc_html__( 'Publishing test…', 'strattic' ),
			'siteCreating'      => esc_html__( 'Sorry, but this site is still being created. Please check back in 30 mins to begin publishing.', 'strattic' ),
			'busyToolTip'       => esc_html__( 'Almost ready for you to click me', 'strattic' ),
		);

		add_action( 'admin_menu',            array( $this, 'add_menu_item' ) ); 
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) ); 
		add_action( 'admin_footer',          array( $this, 'completed_box' ) ); 
		add_action( 'admin_footer',          array( $this, 'container_down_box' ) ); 
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) ); 
		add_action( 'admin_head',            array( $this, 'generator' ) ); 
		add_action( 'admin_notices',         array( $this, 'admin_notices' ) ); 

		add_action( 'admin_bar_menu',        array( $this, 'add_toolbar_link' ), 500 );
		add_action( 'wp_enqueue_scripts',    array( $this, 'admin_script' ) );
		add_action( 'wp_enqueue_scripts',    array( $this, 'admin_style' ), 500);
	}

	/**
	 * Adding the Strattic menu item.
	 */
	public function add_menu_item() {

		add_menu_page(
			'Strattic',
			'Strattic',
			$this->permissions,
			'strattic',
			array( $this, 'publishing_page' ),
			'none',
			1
		);

		add_submenu_page(
			defined( 'STRATTIC_DEV' ) ? 'strattic' : false, // Only display settings page menu item when dev mode is on
			esc_html__( 'Settings', 'strattic' ),
			esc_html__( 'Settings', 'strattic' ),
			$this->permissions,
			'strattic-settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Admin page content.
	 */
	public function publishing_page() {

		?>

		<input type="hidden" id="status-check" value="ready" />

		<div class="wrap">

			<h1><?php esc_html_e( 'Strattic: Serverless publishing for WordPress', 'strattic' ); ?></h1>

			<?php
			if ( isset( $_GET['advanced'] ) ) {
				?>
				<div class="strattic-advanced">
					<h2><?php esc_html_e( 'Advanced settings:', 'strattic' ); ?></h2>
					<form action="" onsubmit="return false;" id="strattic-advanced">
						<table class="form-table">
							<tr>
s								<th>
									<label for="strattic-concurrentWorkers">
										<?php esc_html_e( 'Concurrent Workers', 'strattic' ); ?>
									</label>
								</th>
								<td>
									<input type="number" name="strattic-concurrentWorkers" id="strattic-concurrentWorkers" value="8"  />
								</td>
							</tr>
							<tr>
								<th>
									<label for="strattic-workerBatchSize">
										<?php esc_html_e( 'Worker Batch Size', 'strattic' ); ?>
									</label>
								</th>
								<td>
									<input type="number" name="strattic-workerBatchSize" id="strattic-workerBatchSize" value="10"  />
								</td>
							</tr>
							<tr>
								<th>
									<label for="strattic-addUrlsBatchSize">
										<?php esc_html_e( 'Add URLs Batch Size', 'strattic' ); ?>
									</label>
								</th>
								<td>
									<input type="number" name="strattic-addUrlsBatchSize" id="strattic-addUrlsBatchSize" value="1000"  />
								</td>
							</tr>
						</table>
					</form>
				</div>
				<?php
			}
			?>

			<?php do_action( 'strattic-after-title' ); ?>

			<p id="strattic-error-message">
				<?php
				if ( '1' === get_transient( 'strattic-refresh-token-fail' ) ) {
					esc_html_e( 'The refresh token has failed to refresh. Please contact support for assistance', 'strattic' );
				}
			?>
			</p>

			<table class="strattic-table strattic-icons wp-list-table widefat fixed striped pages">
			<?php

			$count = 0;
			foreach ( $this->get_distribution_info() as $key => $distribution_info ) {
				$count++;

				$type = $distribution_info['type'];
				$url = $distribution_info['url'];
				$distribution_id = $distribution_info['id'];

				$recent_publications = $this->get_most_recent_publications( $distribution_id );

				?>

				<tr
				<?php
				if ( 0 === $count % 2 ) {
					echo ' class="alternate"';
				}
				?>
				 id="distribution-<?php echo esc_attr( $distribution_id ); ?>">
					<th scope="row">
						<h2><?php
						if ( ! empty( $recent_publications ) ) {
							echo '<a target="_BLANK" href="' . esc_url( $url ) . '">';
						}

						printf( esc_html__( '%s site', 'strattic' ), ucfirst( $type ) );
						?>
						 <span class="icon-share"></span>
						<?php
						if ( ! empty( $recent_publications ) ) {
							echo '</a>';
						}

						echo '</h2>';

						if ( ! empty( $recent_publications ) ) {
						?>

						<p class="site-link">
							<a target="_BLANK" href="<?php echo esc_url( $url ); ?>"><?php echo esc_url( $url ); ?></a>
						</p>
						<?php
						}
						?>

						<?php $publish_text = sprintf( __( '%s Publish' ), ucwords( $type ) )?>
						<div class="strattic-info-box" data-publish-text="<?php echo esc_attr( $publish_text ) ?>">
							<p class="strattic-button-box">
								<a data-distribution-type="<?php echo esc_attr( $type ); ?>" 
								data-distribution-id="<?php echo esc_attr( $distribution_id ); ?>" 
								class="strattic-publish button button-primary" ><?php esc_html_e( 'Publish', 'strattic' ); ?>
									<span class="publish-text" style="display:none"><?php echo esc_html( $publish_text ) ?></span>
								</a>
							</p>

							<p class="strattic-progress-bar" id="<?php echo esc_attr( 'strattic-progress-' . $distribution_id ); ?>">
								<span class="progress-bar-number"></span> (<span class="progress-bar-message"></span>)
								<br />
								<progress data-id="<?php echo esc_attr( $distribution_id ); ?>" class="strattic-progress" value="0" max="100"> </progress>
							</p>

						</div>

					</td>
					<td>

						<?php

						if ( ! empty( $recent_publications ) ) {
							$title = esc_html__( 'Recently Published','strattic' );
						} else {
							$title = esc_html__( 'Never Published','strattic' );
						}

						echo '<h4>' . $title . '</h4>';

						if ( ! empty( $recent_publications ) ) {
							echo '<ul class="recently-published">';
						}
						foreach ( $recent_publications as $key => $publication ) {
							$time = $publication['end_time'];
							if ( 10000000000 < $time ) {
								$time = $time / 10; // Fixing bug when timestamp is out by factor of 10
							}
							$time_difference = sprintf( __( '%s ago', 'strattic' ), human_time_diff( $time, time() ) );

							echo '<li>' . esc_html( $time_difference ) . '</a>';

						}
						if ( ! empty( $recent_publications ) ) {
							echo '</ul>';
						}

						?>

					</td>
				</tr>
				<?php

			}

			?>

			</table>

		</div>

		<form action="" onsubmit="return false;">
			<p>
				<label for="strattic-full-publish">
					<?php esc_html_e( 'Force Full Publish', 'strattic' ); ?>
				</label>
				 &nbsp;
				<input type="checkbox" name="strattic-full-publish" id="strattic-full-publish" />
			</p>
		</form>

		<p>

			<a class="button" target="_blank" href="https://app.<?php echo defined( 'STRATTIC_ENV' ) ? STRATTIC_ENV : ''; ?>strattic.com/"><?php esc_html_e( 'Strattic Dashboard', 'strattic' ); ?></a>
			&nbsp;

			<a class="button" href="https://support.strattic.com" target="_blank"><?php esc_html_e( 'Need help?', 'strattic' ); ?> <span class="icon-share"></a>
		</p>
		<?php

	}

	/**
	 * The settings page.
	 */
	public function settings_page() {

		?>

		<div class="wrap">

			<form method="post" action="options.php">

				<h1><?php esc_html_e( 'Strattic Settings', 'strattic' ); ?></h1>
				<?php

				// Add the admin page content
				do_action( 'strattic_settings' );

				// Adding settings fields for each section
				settings_fields( 'strattic-settings' );

				?>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save', 'strattic' ); ?>" />
				</p>

			</form>

		</div>
		<?php

	}

	/**
	 * Add admin script.
	 *
	 * @param  string  The admin page being accessed
	 */
	public function admin_script() {

		// Permissions check - don't want admin scripts showing up for logged out users
		if ( ! current_user_can( $this->permissions ) ) {
			return;
		}

		add_thickbox(); // Used by the completed box

		wp_enqueue_script(
			'strattic-admin',
			STRATTIC_ASSETS . 'strattic.js',
			array( 'jquery' ),
			STRATTIC_VERSION
		);

		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_style( 'wp-jquery-ui-dialog' );

		wp_localize_script(
			'strattic-admin',
			'strattic',
			array(
				'api_url' => STRATTIC_API_URL,
				'site_id' => $this->get_current_site_strattic_id(),
			)
		);

		wp_localize_script(
			'strattic-admin',
			'strattic_ajax',
			array(
				'url'       => admin_url( '?strattic-ajax=' ),
				'admin_url' => admin_url(),
				'nonce'     => wp_create_nonce( 'strattic' ),
			)
		);

		wp_localize_script(
			'strattic-admin',
			'strattic_strings',
			$this->strattic_strings
		);

		wp_localize_script(
			'strattic-admin',
			'strattic_gutenberg_notices',
			array(
				'performing_updates' => esc_html( $this->gutenberg_notices['performing_updates'] ),
				'site_publishing'    => esc_html( $this->gutenberg_notices['site_publishing'] ),
			)
		);

	}

	/**
	 * Adding completed box to the footer of all pages.
	 * This needs to be on every page incase it is triggered when not in the Strattic admin page.
	 * This is not implemented as an iframe because we need to know how large the containing box is
	 * due to styling changes particular to this dialog box.
	 */
	public function completed_box() {
		?>

<div id="strattic-completed">
	<div id="strattic-completed-inner">
		<div id="strattic-completed-inner-inner">
			<a class="strattic-close strattic-tick-button" href="#">✓</a>
			<div class="success-text">
				<?php esc_html_e( 'Success!', 'strattic' ); ?>
			</div>
			<p>
				<?php esc_html_e( 'Strattic publishing completed.', 'strattic' ); ?>
			</p>
			<?php

			foreach ( $this->get_distribution_info() as $key => $distribution_info ) {

				$type = $distribution_info['type'];
				$url = $distribution_info['url'];
				$distribution_id = $distribution_info['id'];

				echo '
				<a target="_BLANK" href="' . esc_url( $url ) . '" class="strattic-close strattic-button strattic-completed-distribution-link" id="strattic-completed-distribution-link-' . esc_attr( $distribution_id ) . '">' . sprintf( esc_html__( '%s site', 'strattic' ), ucfirst( $type ) ) . ' &gt;</a>';

			}

			?>
		</div>
	</div>
</div><?php
	}

	/**
	 * Adding container down box to the footer of all pages.
	 * This needs to be on every page incase it is triggered when not in the Strattic admin page.
	 */
	public function container_down_box() {

		?>
<div id="strattic-container-down">
	<div id="strattic-container-down-inner">
		<h3><?php esc_html_e( 'Strattic Session expired', 'strattic' ); ?></h3>
		<a class="button" id="strattic-spin-container-up" href="#"><?php esc_html_e( 'Click here to continue Session', 'strattic' ); ?></a>
		<img width="40" height="40" src="<?php echo STRATTIC_ASSETS; ?>loading.gif" />
	</div>
</div><?php
	}

	/**
	 * Add custom styling for admin panel.
	 */
	public function admin_style() {

		// Permissions check - don't want admin styles showing up for logged out users
		if ( ! current_user_can( $this->permissions ) ) {
			return;
		}

		wp_enqueue_style(
			'strattic-admin',
			STRATTIC_ASSETS . 'strattic.css',
			array(),
			STRATTIC_VERSION
		);

	}

	

	/**
	 * Adds generator meta tag.
	 */
	public function generator() {
		?>
		<meta name="generator" content="Strattic <?php echo STRATTIC_VERSION; ?>" />
		<?php
	}

	/**
	 * Prints admin notices, for example, the "curently publishing" notice.
	 */
	public function admin_notices() {

		// Alert them they're publishing - needs to always be shown, but hidden, so that it can be loaded easily via JS
		?>
		<div id="strattic-notice-publishing" class="notice notice-warning" style="display:none">
			<p><?php echo esc_html( $this->gutenberg_notices['site_publishing'] ); ?></p>
		</div>

		<div id="strattic-notice-background" class="notice notice-warning" style="display:none">
			<p><?php echo esc_html( $this->gutenberg_notices['performing_updates'] ); ?></p>
		</div><?php
	}

	/**
	 * Add toolbar link.
	 *
	 * @global  object  $wp_admin_bar   The WordPress admin bar object
	 */
	public function add_toolbar_link( $wp_admin_bar ) {

		// Permissions security check
		if ( ! current_user_can( $this->permissions ) ) {
			return;
		}

		// Get live distribution ID
		$distributions = array();
		$distribution_info = $this->get_distribution_info();
		foreach ( $distribution_info as $key => $distribution ) {

			$type = $distribution['type'];
			if ( 'live' === $type ) {
				$live_distribution_id = $distribution['id'];
				$live_distribution_type = $type;
			} else {
				$distributions[ $type ] = $distribution['id'];
			}
		}
		
		if ( ! isset( $live_distribution_id ) ) {
			return $wp_admin_bar;
		}

		// Add admin bar
		$wp_admin_bar->add_menu(
			array(
				'id'    => 'strattic',
				'title' => '<span class="publish-text"  data-distribution-id="' . $live_distribution_id . '" style="display: none">' . sprintf( esc_html__( '%s Publish', 'strattic' ), ucwords( $live_distribution_type ) ) . '</span>
				<span class="ab-icon"></span> <span class="ab-message">' . esc_html( $this->strattic_strings['publish'] ) . '</span>',
				'href'  => esc_url( admin_url( 'admin.php?page=strattic' ) ),
				'meta'  => array(
					'class' => 'strattic-publish',
					'rel' => $live_distribution_id, // can't find anywhere suitable to store this piece of data, so placed it here
				),
			)
		);

		foreach ( $distributions as $type => $distribution_id ) {

			$wp_admin_bar->add_menu(   //sprintf( esc_html( '%s Publish', 'strattic' ), ucwords( $type ) )
				array(
					'parent' => 'strattic',
					'title'  => esc_html( sprintf( __( '%s publish', 'strattic' ), ucfirst( $type ) ) ) 
						. '<span class="publish-text" data-distribution-id="' . $distribution_id . '" style="display: none">' 
						. sprintf( esc_html( '%s Publish', 'strattic' ), ucwords( $type) ) . '</span>',
					'id'     => esc_attr( 'strattic-' . $type . '-publish' ),
					'href'   => esc_url( admin_url() . 'admin.php?page=strattic&publish=' . $distribution_id ),
					'meta'  => array(
						'class' => 'strattic-publish',
						'rel' => $distribution_id, // can't find anywhere suitable to store this piece of data, so placed it here
					),
				)
			);

		}

	}

}
