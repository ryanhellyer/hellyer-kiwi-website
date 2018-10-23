<?php

/**
 * Handing form processing.
 * 
 * @copyright Copyright (c), Strattic
 */
class Strattic_Form_Processing {

	/**
	 * Class constructor
	 */
	static public function send() {

		$contact_forms = WPCF7_ContactForm::find();
		foreach ( $contact_forms as $key => $contact_form ) {

			$id = $contact_form->id();
			$name = $contact_form->name();
			$title = $contact_form->title();
			$locale = $contact_form->locale();
die;
			$properties = $contact_form->properties();
//print_r( $properties );

		}
print_r( $properties );die;
		$response = wp_remote_post(
			STRATTIC_FORM_SUBMISSION_ENDPOINT,
			array(
				'method' => 'POST',
				'body' => array(
					'id'         => $contact_form->id(),
					'name'       => $contact_form->name(),
					'title'      => $contact_form->title(),
					'locale'     => $contact_form->locale(),
					'properties' => $contact_form->properties(),
				),
		    )
		);

		// If the forms don't send, we fire off an email alert
		if ( is_wp_error( $response ) ) {

			$to = STRATTIC_ALERT_EMAIL;
			$subject = 'Strattic error: Contact Form 7 forms not sent';
			$body = 'Error triggered via Strattic_Form_Processing() in the Strattic WordPress plugin.<br /><br />' . esc_html( $response->get_error_message() );
			$headers = array('Content-Type: text/html; charset=UTF-8');

			wp_mail( $to, $subject, $body, $headers );
		}

		die;
	}


}
