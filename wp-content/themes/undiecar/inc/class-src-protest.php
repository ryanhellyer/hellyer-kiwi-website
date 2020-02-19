<?php

/**
 * Receive protest submissions.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class SRC_Protest {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'process_protest_submission' ) );
		add_shortcode( 'undiecar_protests', array( $this, 'protests_shortcode' ) );

	}

	/**
	 * Protest an individual protest submission.
	 */
	public function process_protest_submission() {

		// Bail out if required form inputs not set.
		if (
			! isset( $_POST['undiecar-protester-name'] )
			||
			! isset( $_POST['undiecar-protestee-names'] )
			||
			! isset( $_POST['undiecar-protest-when'] )
		) {
			return;
		}

		$protester_name          = $_POST['undiecar-protester-name'];
		$protestee_names         = $_POST['undiecar-protestee-names'];
		$protest_when            = $_POST['undiecar-protest-when'];
		$protest_additional_info = $_POST['undiecar-protest-additional-info'];

		$message = 'Protest submission from ' . esc_html( $protester_name ) . "\n";
		$message .= 'Protester name: ' . esc_html( $protester_name ) . "\n";
		$message .= 'Protestee names: ' . esc_html( $protestee_names ) . "\n";
		$message .= 'When: ' . esc_html( $protest_when ) . "\n";
		$message .= 'Additional info: ' . esc_html( $protest_additional_info );

		$response = wp_mail(
			'ryanhellyer@gmail.com,directorjustin62@gmail.com',
			'Undiecar protest submission from ' . esc_html( $protester_name ),
			$message,
			array( 'From: Undiecar Championship <donotreply@undiecar.com>' ),
			$this->get_files()
		);

		if ( true === $response ) {
			define( 'UNDIECAR_PROTEST_SUBMITTED', true );
		} else {
			define( 'UNDIECAR_PROTEST_SUBMITTED', false );
		}
	}

	private function get_files() {
		$attachments = array();

		$number = 0;
		while ( $number < 3 ) {
			$number++;

			$new_location = get_temp_dir() . 'replay-' . $number . '.rpy';
			if ( file_exists( $new_location ) ) {
				unlink( $new_location ); // Delete the file if it exists already.
			}

			if ( isset( $_FILES['undiecar-replay-file' . $number ]['tmp_name'] ) ) {
				$file_size     = $_FILES['undiecar-replay-file' . $number ]['size'];
				$temp_location = $_FILES['undiecar-replay-file' . $number ]['tmp_name'];
				$file_parts    = pathinfo( $_FILES['undiecar-replay-file' . $number ]['name'] );

				if (
					$file_size < ( 10 * 1000 * 1000 ) // Only process if it's smaller than 10 MB.
					&&
					isset( $file_parts['extension'] ) && 'rpy' === $file_parts['extension'] // Only process if it's a .rpy extension.
				)  {
					move_uploaded_file( $temp_location, $new_location );
					$attachments[] = $new_location;
				}

			}

		}

		return $attachments;
	}

	/**
	 * Displays the protests shortcode.
	 */
	public function protests_shortcode() {

		if ( defined( 'UNDIECAR_PROTEST_SUBMITTED' ) && false === UNDIECAR_PROTEST_SUBMITTED ) {
			return '<p class="error">Woops! Something went wrong. Please try again. If this keeps happening, please pester Ryan Hellyer via <a href="https://spamannihilator.com/check/undiecar/">Discord</a> or iRacing private message.</p>
			' . $this->protest_form_html();
		} else if ( defined( 'UNDIECAR_PROTEST_SUBMITTED' ) ) {
			return '<p class="notice">Thanks for submitting a protest!</p>';
		} else {
			return $this->protest_form_html();
		}

	}

	private function protest_form_html() {
		return '
<p>If you feel someone has <a href="https://undiecar.com/rules/">broken a rule</a>, please submit your protest via the form below.</p>

<p>If we need to see the incident, please include a replay highlight (max 20 MB per file) or link to a video showing the incident.</p>

<form action="" method="POST" enctype="multipart/form-data">

	<label for="undiecar-protester-name">Your iRacing name</label>
	<input type="text" name="undiecar-protester-name" id="undiecar-protester-name" value="" required aria-required="true" />

	<label for="undiecar-protestee-names">Name(s) of driver(s) being protested against</label>
	<input type="text" name="undiecar-protestee-names" id="undiecar-protestee-names" value="" required aria-required="true" />

	<label for="undiecar-protest-when">Which race and lap did this occur?</label>
	<input type="text" name="undiecar-protest-when" id="undiecar-protest-when" value="" required aria-required="true" />

	<label for="undiecar-protest-additional-info">Please add any relevant extra information here, including links to Twitch or YouTube videos showing the incident</label>
	<textarea name="undiecar-protest-additional-info" id="undiecar-protest-additional-info"></textarea>

	<label for="undiecar-replay-file1">Add replay files if relevant (10 MB max per file)</label>
	<input type="file" name="undiecar-replay-file1" id="undiecar-replay-file1" accept="video/rpy" />
	<input type="file" name="undiecar-replay-file2" id="undiecar-replay-file2" accept="video/rpy" />
	<input type="file" name="undiecar-replay-file3" id="undiecar-replay-file3" accept="video/rpy" />

	<input class="button" type="submit" value="Submit &#187;" />

</form>

<h3>How protests are handled</h3>
<p>Protests are handled by the <a href="https://undiecar.com/about/">protest administrator</a>, but when the decision is unclear, a combination of all Undiecar admins will be involved. When a conflict of interest occurs or we have a split decision, the case will be referred to <a href="https://undiecar.com/member/alex-skinner/">Alex Skinner</a> who is not actively competing in the league.</p>

<h3>Penalties</h3>
<p>Penalties are determined on a case by case basis, but we have a rough overview of likely penalties on the following page.</p>
<a class="button" href="' . esc_url( home_url( '/penalties/' ) ) . '">Penalties information</a>
';
	}

}
