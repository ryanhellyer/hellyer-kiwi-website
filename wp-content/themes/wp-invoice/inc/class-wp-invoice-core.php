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

	const VERSION_NUMBER = '1.0';
	const THEME_NAME     = 'wp-invoice';
	const META_KEY       = '_wp_invoice';
	const INVOICE_POST_TYPE      = 'invoice';

	var $possible_keys;
	var $fields;

	public function __construct() {

		$this->fields = array(
			'invoice_no'         => array(
				'label' => __( 'Invoice number', 'plugin-slug' ),
				'type'  => 'text',
			),
			'invoice_to_name' => array(
				'label' => __( 'Name', 'plugin-slug' ),
				'type'  => 'text',
			),
			'invoice_to_details' => array(
				'label' => __( 'Details', 'plugin-slug' ),
				'type'  => 'textarea',
			),
			'invoice_to_website' => array(
				'label' => __( 'Website', 'plugin-slug' ),
				'type'  => 'url',
			),
			'currency' => array(
				'label' => __( 'Currency', 'plugin-slug' ),
				'type'  => 'text',
			),
			'due_date' => array(
				'label' => __( 'Due date', 'plugin-slug' ),
				'type'  => 'date',
			),
			'paid'       => array(
				'label' => __( 'Paid?', 'plugin-slug' ),
				'type'  => 'text',
			),
		);

		$this->possible_keys = array(
			'title' => __( 'Title', 'plugin-slug' ),
			'description' => __( 'Description', 'plugin-slug' ),
			'completed_date' => __( 'Completed date', 'plugin-slug' ),
			'hours' => __( 'Hours', 'plugin-slug' ),
			'amount' => __( 'Amount owed', 'plugin-slug' ),
		);

	}

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

			// Ignore if key doesn't exist - BROKEN! THIS SHOULD CHECK IF KEY IS IN ARRAY, NOT IF VALUE IS IN ARRAY
			if ( ! in_array( $key, $this->possible_keys ) ) {
//				continue;
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
