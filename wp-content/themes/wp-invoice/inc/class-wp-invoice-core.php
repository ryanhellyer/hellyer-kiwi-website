<?php

/**
 * WP Invoice core class.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class WP_Invoice_Core {

	const META_KEY    = '_wp_invoice';
	const POST_TYPE    = 'invoice';

	var $possible_keys = array(
		'title',
		'description',
		'due_date',
		'amount',
	);

	/**
	 * Sanitize the data.
	 *
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {

		// Loop through each bit of data
		$output = array();
		foreach( $input as $key => $values ) {

			// Ignore if key doesn't exist
			if ( ! in_array( $key, $this->possible_keys ) ) {
				continue;
			}

			foreach ( $values as $number => $value ) {

				// Sanitize input data
				$sanitized_value = wp_kses_post( $value );

				// If value contains content, then save it
				if ( '' != $sanitized_value ) {
					$output[ $number ][ $key ] = $sanitized_value;
				}

			}

		}

		// Return the sanitized data
		return $output;
	}

}
