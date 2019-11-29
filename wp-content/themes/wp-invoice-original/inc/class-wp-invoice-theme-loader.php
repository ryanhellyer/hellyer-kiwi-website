<?php

/**
 * WP Invoice Theme Loader.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class WP_Invoice_Theme_Loader extends WP_Invoice_Core {

	public function __construct() {
		parent::__construct();

		// Pull data out
		$data = get_post_meta( get_the_ID(), '_wp_invoice', true );
		foreach ( $this->fields as $key => $x ) {
			if ( ! isset( $data[ '_' . $key ] ) ) {
				$data[ '_' . $key ] = '';
			}			
		}

		// Get client name
		$terms = wp_get_post_terms( get_the_ID(), self::CLIENT_TAXONOMY );
		$client_name = $terms[0]->name;
		$client_description = $terms[0]->description;

		// Get website string
		$website = $data[ '_invoice_to_website' ];
		$website = str_replace( 'https://', '', $website );
		$website = str_replace( 'http://', '', $website );

		// Combining data from tasks of multiple days
		$new_tasks = array();
		$tasks = $data[ '_tasks' ];
		foreach ( $tasks as $key => $task ) {

			// If no description set, then throw error
			if ( ! isset( $task[ 'description' ] ) ) {
				$task[ 'description' ] = '';
			}

			$hash = md5( $task[ 'title' ] . $task[ 'description' ] );

			if ( ! isset( $new_tasks[ $hash ][ 'hours' ] ) ) {
				$new_tasks[ $hash ][ 'hours' ] = 0;
			}

			$new_tasks[ $hash ][ 'hours' ] = $new_tasks[ $hash ][ 'hours' ] + $task[ 'hours' ];

			// Set amount
			if ( isset( $task[ 'amount' ] ) ) {

				if ( ! isset( $new_tasks[ $hash ][ 'amount' ] ) ) {
					$new_tasks[ $hash ][ 'amount' ] = 0;
				}

				// Add up amounts for identical tasks
				$new_tasks[ $hash ][ 'amount' ] = $new_tasks[ $hash ][ 'amount' ] + $task[ 'amount' ];

			} else {

				// Calculate amount based on number of hours
				$new_tasks[ $hash ][ 'amount' ] = $new_tasks[ $hash ][ 'hours' ] * $data[ '_hourly_rate' ];

			}

			// Adding in original keys
			foreach ( $this->possible_keys as $possible_key => $x ) {

				if (
					! isset( $new_tasks[ $hash ][ $possible_key ] )
					&&
					isset( $task[ $possible_key ] )
				) {
					$new_tasks[ $hash ][ $possible_key ] = $task[ $possible_key ];
				}
			}

		}
		$data[ '_tasks' ] = $new_tasks;

		// Calculating the amount
		foreach ( $data[ '_tasks' ] as $key => $task ) {

			if (
				isset( $task[ 'amount' ] )
				&&
				( '' != $task[ 'amount' ] || 0 == $task[ 'amount' ] )
			) {
				// Do nothing
			} else {
				$data[ '_tasks' ][ $key ][ 'amount' ] = $data[ '_hourly_rate' ] * $task[ 'hours' ];

			}

		}

		// Calculating total amount
		$total_amount = 0;
		foreach ( $data[ '_tasks' ] as $key => $task ) {
			if ( isset( $task['amount'] ) ) {
				$total_amount = $total_amount + $task['amount'];
			}
		}

		// Load template
		require( dirname( dirname( __FILE__ ) ) . '/templates/invoice.php' );
	}

}
