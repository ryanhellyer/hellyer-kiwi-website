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

		add_action( 'admin_menu',            array( $this, 'add_menu_item' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_script' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_style' ) );
		add_action( 'admin_bar_menu',        array( $this, 'add_toolbar_link' ), 500 );
		add_action( 'init',                  array( $this, 'ajax' ) );

		// Add filters
		add_filter( 'parent_file', array( $this, 'highlight_menu' ) );

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
		?>

		<div class="wrap">

			<h1><?php esc_html_e( 'Strattic - Publish your site', 'strattic' ); ?></h1>
			<p>
				<input class="button button-primary" id="publish" type="button" value="<?php esc_html_e( 'Publish', 'strattic' ); ?>" onclick="strattic_start_publishing();" />
			</p>

			<progress id="progress" value="0" max="100" style="width:900px; height:20px;"> </progress>
			<span id="status"></span>
			<h2 id="finalMsg"></h2>

			<?php $this->the_horizontal_menu(); ?>

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

		wp_localize_script(
			'strattic-admin',
			'strattic_final_message',
			esc_html__( 'Stage 1 publishing is complete. An end to end process will continue in the background. You will receive an email when the final publishing stage completes. Please wait to deploy further changes until after you receive the email to ensure that your changes are deployed completely.', 'strattic' )
		);

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
	 */
	public function ajax() {

		if ( ! isset( $_GET[ 'strattic-ajax'] ) ) {
			return;
		}

		// Load the stage1 script
		if ( 'stage1' === $_GET[ 'strattic-ajax'] ) {
			$output = shell_exec( 'flock -n /tmp/lock1 /usr/local/bin/stage1.sh ' . STRATTIC_CLOUDFRONT_DOMAIN . ' ' . STRATTIC_CLOUDFRONT_ID . ' ' . STRATTIC_STAGE_DOMAIN . ' ' . STRATTIC_S3_BUCKET . ' ' . STRATTIC_EMAIL . ' ' . STRATTIC_PASSWORD );
		}

		// Load the redis script
		if ( 'redis' === $_GET[ 'strattic-ajax'] ) {
			$output = shell_exec( 'redis-cli get progress' );
			echo $output;
		}

		die;
	}

}
