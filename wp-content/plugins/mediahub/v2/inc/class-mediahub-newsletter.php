<?php

class MediaHub_Newsletter extends MediaHub_Core {

	private $args;

	/**
	 * Class constructor.
	 */
	function __construct() {

		$this->args = array(
			'email'           => __( 'Email address', 'mediahub' ),
			'preposition'     => __( 'Preposition', 'mediahub' ),
			'firstname'       => __( 'First name', 'mediahub' ),
			'prefix_lastname' => __( 'Prefix last name', 'mediahub' ),
			'lastname'        => __( 'Last name', 'mediahub' ),
		);

		add_action( 'init', array( $this, 'send_newsletter_details_to_api' ) );
		add_shortcode( 'mediahub_newsletter', array( $this, 'shortcode' ) );
	}

	/**
	 * Add the newsletter form shortcode.
	 * 
	 * @param    array   $args   The shortcode arguments
	 * @return   string          The form HTML
	 */
	public function shortcode( $args ) {
		$content = '
		<form method="post" action="">';

		// Loop through each possible argument
		foreach( $this->args as $arg => $label ) {

			// Check if possible argument is in those selected
			if ( in_array ( $arg , $args ) ) {

				if ( 'email' == $arg ) {
					$type = 'email';
				} else {
					$type = 'text';
				}

				$content .= '
				<p>
					<label>' . $label . '</label>
					<input name="' . esc_attr( 'mediahub_newsletter[' . $arg . ']' ) . '" type="' . esc_attr( $type ) . '" value="" />
				</p>';
			}
		}

		$content .= '
			<input type="submit" value="' . __( 'Submit' ). '" />
		</form>';

		return $content;
	}

	/**
	 * Send the newsletter submission to the API.
	 */
	public function send_newsletter_details_to_api() {

		if ( ! isset( $_POST['mediahub_newsletter'] ) ) {
			return;
		}

		// Loop through each field
		$query = '';
		foreach( $_POST['mediahub_newsletter'] as $arg => $value ) {

			// Check field against white list
			if ( array_key_exists( $arg, $this->args ) ) {

				// Sanitize the outputs
				if ( 'email' == $arg ) {
					$result[$arg] = $new_value = sanitize_email( $value );
				} else {
					$result[$arg] = $new_value = wp_kses_post( $value );
				}

				$query .= $arg . '=' . $new_value . '&';
			}
		}

		$api_keys = get_option( 'mhca_api_key' );
		$options = get_option( 'mhca_options' );
		$query .= 'groups=' . $options['mediahub_api_group'] . '&';

		// Do API call
		$this->mediahub_request( 'newsletters/subscribers', $query, 'POST' );

	}

}
new MediaHub_Newsletter;
