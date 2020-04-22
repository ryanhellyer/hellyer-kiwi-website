<?php

/**
 * Warn about caching plugins.
 * 
 * @copyright Copyright (c), Strattic
 * @since 1.1
 */
class Strattic_Warn_About_Caching_Plugin extends Strattic_Core {

	/**
	 * Class constructor
	 */
	public function __construct() {

		if ( file_exists( WP_CONTENT_DIR . '/advanced-cache.php' ) ) {
			add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
		}

	}

	/**
	 * Display Admin Notice, pointing out the use of a caching plugin.
	 */
	public function display_admin_notice() {
//delete_site_option( 'strattic-ignore-cache-warning' );
		$screen = get_current_screen(); 
		if (
			isset( $screen->base ) && 'toplevel_page_strattic' == $screen->base
			&&
			'yes' !== get_site_option( 'strattic-ignore-cache-warning' )
		) {

			$url = wp_nonce_url( admin_url( 'admin.php?page=strattic&ignore-cache-warning=true' ), 'ignore-cache-notice-nonce' );

			echo '
			<div class="error">
				<p>
					' . esc_html__( 'Your site includes a file at wp-content/advanced-cache.php. This is normally due to the use of a caching plugin, however caching plugins are not required on Strattic. We recommend removing any full page caching functionality from your website.', 'strattic' ) . '
						&nbsp;
						<a class="button" href="' . esc_url( $url ) . '">' . __( 'Hide this message forever', 'strattic'  ) . '</a>
				</p>
			</div>';

		}

	}

	/**
	 * Set the plugin to no longer bug users if user asks not to be.
	 */
	public function set_no_bug() {

		// Bail out if not on correct page
		if (
			isset( $_GET['_wpnonce'] )
			&&
			(
				wp_verify_nonce( $_GET['_wpnonce'], 'ignore-cache-notice-nonce' )
				&&
				is_admin()
				&&
				isset( $_GET[ 'ignore-cache-warning' ] )
				&&
				current_user_can( $this->permissions )
			)
		) {
			add_site_option( 'strattic-ignore-cache-warning', 'yes' );
		}

	}

}
