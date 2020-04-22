<?php

/**
 * Disable features.
 *
 * @copyright Copyright (c) 2018, Strattic
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Strattic_Disable_Features extends Strattic_Core {

	const SETTINGS_OPTION = 'strattic-disable-features';

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {

		// Add hooks
		add_action( 'admin_init',        array( $this, 'register_settings' ) );
		add_action( 'strattic_settings', array( $this, 'admin_page' ), 25 );
		add_action( 'init',              array( $this, 'disable_attachment_pages' ) );

	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {

		$b = 'strattic';
		add_settings_section(
			self::SETTINGS_OPTION,
			esc_html__( 'Disable Features', 'strattic' ),
			null,
			$b
		);
		add_settings_field(
			self::SETTINGS_OPTION,
			esc_html__( 'Disable Features field', 'strattic' ),
			array( $this, 'sanitize' ),
			$b,
			self::SETTINGS_OPTION
		);
		register_setting(
			'strattic-settings',       // The settings group name
			self::SETTINGS_OPTION,     // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);

	}

	/**
	 * Output array of available settings.
	 *
	 * @return array  The available settings
	 */
	private function get_available_settings() {

		$settings = array(
			'archive-pagination' => array(
				'title'       => esc_html__( 'Archive pagination', 'strattic' ),
				'description' => esc_html__( 'Disables all archive pagination pages.', 'strattic' ),
			),
			'attachment-pages' => array(
				'title'       => esc_html__( 'Attachment pages', 'strattic' ),
				'description' => esc_html__( 'Disables attachment pages.', 'strattic' ),
			),
			'rss-feeds' => array(
				'title'       => esc_html__( 'RSS Feeds', 'strattic' ),
				'description' => esc_html__( 'Disables all RSS feeds.', 'strattic' ),
			),
			'rss2-feeds' => array(
				'title'       => esc_html__( 'RSS2 Feeds', 'strattic' ),
				'description' => esc_html__( 'Disables all RSS2 feeds.', 'strattic' ),
			),
			'rdf-feeds' => array(
				'title'       => esc_html__( 'RDF Feeds', 'strattic' ),
				'description' => esc_html__( 'Disables all RDF feeds.', 'strattic' ),
			),
			'atom-feeds' => array(
				'title'       => esc_html__( 'Atom Feeds', 'strattic' ),
				'description' => esc_html__( 'Disables all Atom feeds.', 'strattic' ),
			),
			'embed-pages' => array(
				'title'       => esc_html__( 'Embed pages', 'strattic' ),
				'description' => esc_html__( 'Disables all embed pages.', 'strattic' ),
			),
		);

		return $settings;
	}

	/**
	 * Output the admin page.
	 *
	 * @global  string  $title  The page title set by add_submenu_page()
	 */
	public function admin_page() {

		echo '<h2>' . esc_html__( 'Disable Features', 'strattic' ) . '</h2>';

		echo '<table class="form-table">';
		foreach ( $this->get_available_settings() as $slug => $setting ) {

			echo '

			<tr>
				<th scope="row">' . esc_html( $setting[ 'title' ] ) . '</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text"><span>' . esc_html( $setting[ 'title' ] ) . '</span></legend>
						<label for="search-on">
							<input
								' . checked( $this->get_option( $slug ), 'on', false ) . ' 
								name="' . esc_attr( 'strattic-disable-features[' . $slug . ']' ) . '" 
								id="' . esc_attr( $setting[ 'description' ] ) . '" 
								type="checkbox" 
								value="on" 
								class="regular-text"
							/>
						</label>
					</fieldset>
				</td>
			</tr>';
		}
		echo '</table>';

	}

	/**
	 * Sanitize the page or product ID.
	 *
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {
		$output = array();

		if ( isset( $input ) ) {

			// Loop through each bit of data.
			foreach ( (array) $input as $key => $value ) {
				// Sanitize input data
				$sanitized_key   = esc_html( $key );
				$sanitized_value = esc_html( $value );

				$output[ $sanitized_key ] = $sanitized_value;

			}
		}

		flush_rewrite_rules();

		// Return the sanitized data
		return $output;
	}

	/**
	 * Disable the attachment pages.
	 * Code was developed from from https://gschoppe.com/wordpress/disable-attachment-pages/.
	 */
	public function disable_attachment_pages() {

		if ( 'on' === $this->get_option( 'attachment-pages' ) ) {
			add_filter( 'rewrite_rules_array',     array( $this, 'remove_attachment_rewrites' ) );
			add_filter( 'request',                 array( $this, 'remove_attachment_query_var' ) );
			add_filter( 'attachment_link'  ,       array( $this, 'change_attachment_link_to_file' ), 10, 2 );
			add_filter( 'register_post_type_args', array( $this, 'make_attachments_non_queryable' ), 10, 2 );
		}

	}

	/**
	 * Remove the attachment page rewrites.
	 *
	 * @param  array  $rules  The rewrite rules
	 * @return array  The modified rewrite rules
	 */
	public function remove_attachment_rewrites( $rules ) {
		foreach ( $rules as $pattern => $rewrite ) {
			if ( preg_match( '/([\?&]attachment=\$matches\[)/', $rewrite ) ) {
				unset( $rules[$pattern] );
			}
		}
		return $rules;
	}

	/**
	 * Remove the attachment page query var.
	 * Used when permalinks are not turned on.
	 *
	 * @param  array  $vars  The available page query variables
	 * @return array  The available page query variables without attachments
	 */
	public function remove_attachment_query_var( $vars ) {
		if ( ! empty( $vars['attachment'] ) ) {
			$vars['page'] = '';
			$vars['name'] = $vars['attachment'];
			unset( $vars['attachment'] );
		}

		return $vars;
	}

	/**
	 * Make attachments non-queryable.
	 * This ensures that attachment pages can not be viewed.
	 * This does nothing currently, but will when WordPress standardizes attachments as a post type.
	 *
	 * @param  array  $args       The post-type arguments 
	 * @param  string $post_type  The post-type
	 * @return array  The modified post-type arguments
	 */
	public function make_attachments_non_queryable( $args, $post_type ) {

		if ( $post_type === 'attachment' ) {
			$args['public'] = false;
			$args['publicly_queryable'] = false;
		}

		return $args;
	}

	/**
	 * Change the attachment link to the attachment file.
	 *
	 * @param  string  $url      The attachment page URL
	 * @param  string  $post_id  The attachment page URL
	 * @return string  The modified post URL
	 */
	public function change_attachment_link_to_file( $url, $post_id ) {

		$attachment_url = wp_get_attachment_url( $post_id );
		if ( ! empty( $attachment_url ) ) {
			return $attachment_url;
		}

		return $url;
	}

	/**
	 * Get the option data.
	 * Provides legacy backwards support for if data was stored as its own option previously.
	 *
	 * @param  string  $option   The requested setting
	 * @return string  The option
	 */
	public function get_option( $option ) {
		$disable_settings = get_option( self::SETTINGS_OPTION );

		// Legacy support - tries to data from it's own option if none found in main settings array
		if ( ! isset( $disable_settings[$option] ) ) {

			if ( false !== get_option( $option ) ) {
				$disable_settings[$option] = get_option( $option );				
			} else {
				return null; // No data was found, so just give up
			}

		}

		return $disable_settings[$option];
	}

}
