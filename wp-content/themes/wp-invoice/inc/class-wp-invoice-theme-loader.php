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
		$terms = get_terms( array(
			'taxonomy'   => self::CLIENT_TAXONOMY,
			'hide_empty' => false,
		) );
		$client_name = $terms[0]->name;

		// Get website string
		$website = $data[ '_invoice_to_website' ];
		$website = str_replace( 'https://', '', $website );
		$website = str_replace( 'http://', '', $website );


		// Combining data from tasks of multiple days
		$tasks = array();
		$tasks = $data[ '_tasks' ];
		foreach ( $tasks as $key => $task ) {

			$hash = md5( $task[ 'title' ] . $task[ 'description' ] );

			foreach ( $tasks as $key2 => $task2 ) {

				if (
					$hash == md5( $task2[ 'title' ] . $task2[ 'description' ] )
					&&
					$key != $key2
				) {

					if ( isset( $task[ 'hours' ] ) && isset( $tasks[ $key2 ][ 'hours' ] ) ) {
						$new_tasks[ $hash ][ 'hours' ] = $task[ 'hours' ] + $tasks[ $key2 ][ 'hours' ];
					}

					if ( isset( $task[ 'amount' ] ) && isset( $tasks[ $key2 ][ 'amount' ] ) ) {
						$new_tasks[ $hash ][ 'amount' ] = $task[ 'amount' ] + $tasks[ $key2 ][ 'amount' ];
					}

				}

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

		}
		$tasks = $data[ '_tasks' ] = $new_tasks;



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
