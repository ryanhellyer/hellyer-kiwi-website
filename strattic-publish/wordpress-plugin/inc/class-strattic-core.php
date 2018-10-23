<?php

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

	/**
	 * Get the current page URL.
	 *
	 * @access  protected
	 * @return  string  The URL
	 */
	protected function get_current_url() {

		$url = 'http';
		if ( is_ssl() ) {
			$url .= 's';
		}
		$url .= '://';

		if ( '' !== $_SERVER[ 'SERVER_NAME' ] && '_' !== $_SERVER[ 'SERVER_NAME' ] ) {
			$url .= $_SERVER[ 'SERVER_NAME' ] . $_SERVER[ 'REQUEST_URI' ];
		} else {
			$url .= $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ];
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
if ( strpos( $url, '/strattic-api/') !== false) {
	return '/strattic-api/';
} else {
	return $path;
}


		return $path;
	}


	/**
	 * The menu displayed at the bottom of each admin page.
	 */
	public function the_horizontal_menu() {

		// Bail out if not an uber admin
		if ( ! $this->is_uber_admin() ) {
			return;
		}

		echo '<p>';

		$items = array(
			'strattic'                    => esc_html__( 'Publish', 'strattic' ),
			//'strattic-search-settings' => esc_html__( 'Search', 'strattic' ),
			'manual-links'                => esc_html__( 'Manual links', 'strattic' ),
			'discovered-links'            => esc_html__( 'Discovered links', 'strattic' ),
			'strattic-string-replacement' => esc_html__( 'String Replacement', 'strattic' ),
			'strattic-settings'           => esc_html__( 'Settings', 'strattic' ),
		);

		foreach ( $items as $slug => $title ) {

			if ( isset( $done ) ) {
				echo ' | ';
			}

			echo '<a href="' . esc_url( admin_url( 'admin.php?page=' . $slug ) ) . '">';
			if ( $slug === $_GET[ 'page' ] ) {
				echo '<strong>';
			}

			echo esc_html( $title );

			if ( $slug === $_GET[ 'page' ] ) {
				echo '</strong>';
			}
			echo '</a>';

			$done = true;

		}

		echo '</p>';
	}

	/**
	 * True if user is a Strattic admin.
	 */
	protected function is_uber_admin() {

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
	 * Get an admin setting.
	 *
	 * @return  string  The admin settings.
	 */
	protected function get_admin_setting( $setting ) {

		$options = $this->get_admin_settings();
		if ( isset( $options[ $setting ][ 'value' ] ) ) {
			return $options[ $setting ][ 'value' ];
		}

		return false;
	}

	/**
	 * Get admin settings.
	 *
	 * @return  array  The possible uber admin settings.
	 */
	protected function get_admin_settings() {

		$options = array(
			's3-bucket' => array(
				'text'    => 'S3 Bucket',
				'default' => STRATTIC_S3_BUCKET,
				'type'    => 'text',
			),
			'live-url' => array(
				'text'    => 'Live CloudFront URL',
				'default' => STRATTIC_CLOUDFRONT_URL,
				'type'    => 'text',
			),
			'live-cloudfront-id' => array(
				'text'    => 'Live CloudFront ID',
				'default' => STRATTIC_CLOUDFRONT_ID,
				'type'    => 'text',
			),
			'test-url' => array(
				'text'    => 'Test CloudFront URL',
				'default' => '',
				'type'    => 'text',
			),
			'test-cloudfront-id' => array(
				'text'    => 'Test CloudFront ID',
				'default' => '',
				'type'    => 'text',
			),
			'dev-url' => array(
				'text'    => 'Dev CloudFront URL (internal use only)',
				'default' => '',
				'type'    => 'text',
			),
			'dev-cloudfront-id' => array(
				'text'    => 'Dev CloudFront ID (internal use only)',
				'default' => '',
				'type'    => 'text',
			),
			'email' => array(
				'text'    => 'Email Address',
				'default' => STRATTIC_EMAIL,
				'type'    => 'email',
			),
			'debug' => array(
				'text'    => 'Debug mode',
				'default' => false,
				'type'    => 'checkbox',
			),
		);

		$settings = get_option( 'strattic_settings' );
		foreach ( $options as $key => $values ) {
			$value = '';

			// Get saved value
			if ( isset( $settings[ $key ] ) ) {
				$value = $settings[ $key ];
			}
			$options[ $key ][ 'saved' ] = $value;

			// Get intended value to use (if none found, then uses the default)
			if ( '' !== $value ) {
				$options[ $key ][ 'value' ] = $value;
			} else {
				$options[ $key ][ 'value' ] = $options[ $key ][ 'default' ];
			}

		}

		return $options;
	}

}
