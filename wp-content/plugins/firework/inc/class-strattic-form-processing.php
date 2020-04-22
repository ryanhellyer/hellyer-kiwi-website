<?php

/**
 * Handing form processing.
 * 
 * @copyright Copyright (c), Strattic
 * @since version 2.0
 */
class Strattic_Form_Processing {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Hooks
		add_action( 'init',       array( $this, 'modify_php_file_1' ) );
		add_action( 'init',       array( $this, 'modify_php_file_2' ) );
		add_action( 'admin_head', array( $this, 'hide_mail_2' ) );

		// Filters
		add_filter( 'wpcf7_form_action_url',    array( $this, 'action_url_filter' ) );
		add_filter( 'wpcf7_form_hidden_fields', array( $this, 'add_extra_fields' ) );
		add_filter( 'wpcf7_api_settings',       array( $this, 'modify_cf7_vars' ) );

	}

	/**
	 * Modify a file.
	 *
	 * @param  string  $old_contents   The original file content before modification
	 * @param  string  $new_contents   The new file content after modification
	 */
	public function modify_file( $old_contents, $new_contents, $file_path ) {

		// Only write the file if the content has changed
		if ( $old_contents !== $new_contents && file_exists( $file_path ) ) {
			file_put_contents( $file_path, $new_contents );
		}

	}

	/**
	 * Physically modify the Contact Form 7 PHP file.
	 */
	public function modify_php_file_1() {

		$file_path = WP_PLUGIN_DIR . '/contact-form-7/includes/contact-form.php';

		// Bail out if file doesn't exist
		if ( ! file_exists( $file_path ) ) {
			return;
		}

		$old_contents = file_get_contents( $file_path );

		// Modify line to show Strattic URL
		$old_string = '$hidden_fields += (array) apply_filters(
			\'wpcf7_form_hidden_fields\', array() );';

		$new_string = '$hidden_fields = (array) apply_filters( \'wpcf7_form_hidden_fields\', $hidden_fields );';

		$new_contents = str_replace( $old_string, $new_string, $old_contents );
		$this->modify_file( $old_contents, $new_contents, $file_path );
	}

	/**
	 * Physically modify the Contact Form 7 PHP file.
	 */
	public function modify_php_file_2() {

		$file_path = WP_PLUGIN_DIR . '/contact-form-7/includes/controller.php';

		// Bail out if file doesn't exist
		if ( ! file_exists( $file_path ) ) {
			return;
		}

		$old_contents = file_get_contents( $file_path );

		// Modify line to show Strattic URL
		$start_old_string = '
	$wpcf7 = array(
		\'apiSettings\' => array(
			\'root\' => esc_url_raw( rest_url( \'contact-form-7/v1\' ) ),
			\'namespace\' => \'contact-form-7/v1\',
		),
	);

';

		$end_old_string = '	if ( defined( \'WP_CACHE\' ) and WP_CACHE ) {';

		$new_contents = str_replace( $start_old_string . $end_old_string, $start_old_string . '	$wpcf7 = apply_filters( \'wpcf7_api_settings\', $wpcf7 );' . "\n\n" . $end_old_string, $old_contents );

		$this->modify_file( $old_contents, $new_contents, $file_path );
	}

	/**
	 * Our API curently only supports sending mail to a single place.
	 * This code hides the mail_2 section within the Contact Form 7 admin page.
	 * We are only hiding it from view, because there are no suitable hooks to properly remove it with.
	 */
	public function hide_mail_2() {
		if ( isset( $_GET[ 'page' ] ) && 'wpcf7' === $_GET[ 'page' ] ) {
			echo '<style>#wpcf7-mail-2{display:none}</style>';
		}

	}

	/**
	 * Action URL filter.
	 */
	public function action_url_filter( $url ) {
		return STRATTIC_FORM_SUBMISSION_ENDPOINT;
	}

	/**
	 *  Add extra form fields.
	 */
	public function add_extra_fields( $fields ) {

		$form_id = null;
		if ( isset( $fields[ '_wpcf7' ] ) ) {
			$form_id = $fields[ '_wpcf7' ];
		}

		$contact_forms = WPCF7_ContactForm::find();
		foreach ( $contact_forms as $key => $contact_form ) {
			if ( $form_id === $contact_form->id() ) {
				$properties = $contact_form->get_properties();

				$fields[ 'strattic_form_processor' ] = array();
				$fields[ 'strattic_form_processor' ][ 'mail' ] = $properties[ 'mail' ];
				if ( $properties[ 'mail_2' ][ 'active' ] == '1' ) {
					$fields[ 'strattic_form_processor' ][ 'mail_2' ] = $properties[ 'mail_2' ];
				}

				$fields[ 'strattic_form_processor' ][ 'template' ] = $properties[ 'form' ];
				$fields[ 'strattic_form_processor' ][ 'messages' ] = $properties[ 'messages' ];
			}

		}

		if ( isset( $fields[ 'strattic_form_processor' ] ) ) {
			$fields[ 'strattic_form_processor' ] = $this->encrypt( json_encode( $fields[ 'strattic_form_processor' ] ) );
		}

		return $fields;
	}

	/**
	 * Encrypt the data.
	 * Currently only does test encryption with base64 encoding.
	 *
	 * @access  private
	 * @param   string   $input   String to be encrypted
	 * @return  string   The encrypted string
	 */
	private function encrypt( $input ) {

		// Escape angled brackets around email addresses - required as data was being scrambled during the encoding and then processing later on the server - note: general HTML escaping does not work here
		$input = str_replace( '<', '&lt;', $input );
		$input = str_replace( '>', '&gt;', $input );

		return base64_encode( $input );
	}

	/**
	 * Modify the CF7 JS variables.
	 * Specifically, we need to alter the root URL for AJAX requests.
	 *
	 * @param   array  $wpcf7   The Contact Form 7 variables
	 * @return  array  The modified Contact Form 7 variables
	 */
	public function modify_cf7_vars( $wpcf7 ) {

		$wpcf7[ 'apiSettings' ][ 'root' ] = STRATTIC_FORM_SUBMISSION_ENDPOINT;

		return $wpcf7;
	}

}
