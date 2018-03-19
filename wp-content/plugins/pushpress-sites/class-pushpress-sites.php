<?php

class PushPress_Sites extends PushPress_Connect {
//class PushPress_Sites extends Pushpress_Plugin {

	/**
	 * Class constructor.
	 */
	public function __construct(){
		add_action( 'admin_menu',             array( $this, 'admin_pages' ) );

		add_action('wp_dashboard_setup',      array( $this, 'dashboard_setup' ), 999999 );
		add_action( 'init',                   array( $this, 'init_shortode' ), 20 );
		add_action( 'gform_after_save_form',  array( $this,'pushpress_gravity_form_saved' ), 10, 2 );
		add_action( 'gform_after_submission', array( $this,'pushpress_after_gravity_form_submission' ), 10, 2 );
	}

	public function admin_pages(){
		add_submenu_page( 'pushpress', esc_html__( 'Sites', 'pushpress-sites' ), esc_html__( 'PushPress Sites', 'pushpress-sites' ), 'manage-options', 'pushpress-sites', array( $this, 'sites_admin_page' ) );
	}

	public function sites_admin_page() {
		echo '<p>This should display the shortcodes here, but does not. The shortcodes would not load in the original plugin, and so this page could not be completed. Updated: 2018-03-19.</p>';
	}

	/**
	 * Modfying the dashboard setup.
	 */
	public function dashboard_setup() {
		global $wp_meta_boxes;

		foreach ( $wp_meta_boxes['dashboard']['normal']['core'] as $item => $widget ) { 
			remove_meta_box( $item, 'dashboard', 'normal');
		}

		foreach ( $wp_meta_boxes['dashboard']['side']['core'] as $item => $widget ) { 
			remove_meta_box( $item, 'dashboard', 'side');
		}

		// 1st Method - Declaring $wpdb as global and using it to execute an SQL query statement that returns a PHP object
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM pp_sites_posts where post_type = 'pp_dashboards' and post_status = 'publish'", OBJECT );
		if ( is_array( $results ) ) {
			foreach ( $results as $index => $box ) { 
				wp_add_dashboard_widget( $box->post_name, $box->post_title, array( $this, 'render_dashboard_metabox' ),  $box->post_content, array( 'item' => $box ) );
			}
		}

	}

	/**
	 * Output the dashboard widget HTML.
	 *
	 * @param  array  $args  The meta box arguments
	 * @param  array  $post  The main post object
	 * @global $shortcode_tags;
	 */
	public function render_dashboard_metabox( $args, $post ) {
		global $shortcode_tags;
		echo $post['args']['item']->post_content;
	}

	public function init_shortode() {
		$this->subdomain = '';
		$secret_code = get_option( 'wp-pushpress-secret-code' );

		// If secret code exists, then run shortcode
		if ( strlen( trim( $secret_code ) ) ) {
			$this->pushpress_shortcode();
		}

	}

	function pushpress_shortcode() {
		$shortcode = new PushPress_Shortcode( $this->subdomain );
		if ( is_array( $this->listPagesSlug ) ) {
			foreach ( $this->listPagesSlug as $pageSlug ) {
				add_shortcode( $this->prefixShortcodes . $pageSlug , array( $shortcode, $pageSlug ) );
			}
		}
	}

	function pushpress_gravity_form_saved( $form, $is_new ) {
		global $wpdb;

		$blog_id = get_current_blog_id();
		$is_main_site = is_main_site( $blog_id );

		if ( ! $is_main_site ) {
			// we should pull this from main site to re-write
			return false;
		}

		$form_notifications = $form['notifications'];
		$form_confirmations = $form['confirmations'];

		$form_meta = json_encode( $form );
		$form_notifications_meta = json_encode( $form_notifications );
		$form_confirmations_meta = json_encode( $form_confirmations );

		$sites = wp_get_sites( array( 'archived' => 0, 'deleted' =>0, 'limit' => 2000 ) );

		foreach ( $sites as $site ) {
			if ( $site['blog_id'] == $blog_id ) {
				continue;
			}

			$current_blog_id = $site['blog_id'];
			$form_table_name = 'pp_sites_' . $current_blog_id. '_rg_form';
			$form_meta_table_name = 'pp_sites_' . $current_blog_id. '_rg_form_meta';

			try {
				$core_form_data = array(
					'id'           => $form['id'],
					'title'        => $form['title'],
					'date_created' => date( 'Y-m-d g:i:s' ),
					'is_active'    => 1,
					'is_trash'      => 0
				);

				$wpdb->replace( $form_table_name, $core_form_data );
			}

			catch ( Exception $e ) {
				// echo "<br><h4 style='color:red;'>Error Updating Form: " . $e->getMessage() . "</h4>";
			}

			try {
				// echo "<br>Updating Form Meta: " . $form_meta_table_name;

				$meta_form_data = array(
					"form_id" => $form['id'],
					"display_meta" => $form_meta,
					"entries_grid_meta" => null,
					"confirmations" => $form_confirmations_meta,
					"notifications" => $form_notifications_meta
				);


				// echo "<bR>Updating Form...";
				// var_dump($core_form_data);
				$wpdb->replace( $form_meta_table_name, $meta_form_data );
				// echo " success!";
				// $wpdb->update( $form_table_name, $data, $where, $format = null, $where_format = null );
			}
			catch ( Exception $e ) {
				// echo "<br><h4 style='color:red;'>Error Updating Form Meta: " . $e->getMessage() . "</h4>";
			}

		}
	}

	function pushpress_after_gravity_form_submission( $entry, $form ) {

		// first thing - check to see if we save to options
		$save_to_options  = false;

		foreach ( $form['fields'] as $field ) {

			$x = $field['label'];
			$x = strtolower($x);
			$x = preg_replace( "/[^a-z0-9_\s-]/", '', $x );
			$x = preg_replace( "/[\s-]+/", ' ', $x );
			$x = preg_replace( "/[\s_]/", '-', $x );

			if ( $x == 'save-to-options' ) {
				$id = $field['id'];
				$v  = $entry[$id];

				if ( isset( $v ) && $v == 1 ) {
					$save_to_options = true;
					break;
				}
			}

			if ( ! $save_to_options ) {
				return;
			}

		}

		foreach ( $form['fields'] as $field ) {
			
			$id = $field['id'];

			try {
				// if this has inputs, need to loop them
				if ( $field['inputs'] ) {
					foreach ( $field['inputs'] as $input ) {
						$f['label'] = $input['label'];
						$f['adminLabel'] = "";

						$e = $entry[ $input['id'] ];
						//$e = $input
						$this->pushpress_save_gravity_formfield_to_option( $f, $e );
					}
					$test = maybe_unserialize( $option['value'] );
				}
				else {
					$e = $entry[$id];
					$unserialized = maybe_unserialize( $e );

					if (is_array($unserialized)) {
						$count = 1;
						$og_label = $field['label'];

						// loop over the data
						foreach ( $unserialized as $unserial ) {
							$base_label = $og_label . ' ' . $count;
							foreach ( $unserial as $key => $value ) {
								$f['label'] = $base_label . ' ' . $key;
								$e = $value;
								$this->pushpress_save_gravity_formfield_to_option( $f, $e );
							}

							$count++;
						}
					}
					else { 
						$f['label'] = $field->label;
						$f['adminLabel'] = $field->adminLabel;

						// $this->pushpress_save_gravity_form_to_option();
						$this->pushpress_save_gravity_formfield_to_option( $f, $e);
					}
				}
			}
			catch ( Exception $e ) { 
				echo '<br>ERROR: ' . esc_html( $e->getMessage() );
			}
		}			
		
	}

	function pushpress_save_gravity_formfield_to_option( $field = null, $value = '' ) {

		if ( $field['adminLabel'] ) {
			$label = $field['adminLabel'];
		}
		else {
			$label = $field['label'];
		}

		$label = strtolower( $label );
		//Make alphanumeric (removes all other characters)
		$label = preg_replace( "/[^a-z0-9_\s-]/", '', $label );
		//Clean up multiple dashes or whitespaces
		$label = preg_replace( "/[\s-]+/", ' ', $label );
		//Convert whitespaces and underscore to dash
		$label = preg_replace( "/[\s_]/", '-', $label );


		$option['option_name'] = 'pp-shortcode-' . $label;
		$option['option_value'] = $value;

		update_option( $option['option_name'], $option['option_value'], true );

	}

}
