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

		// Calculating total amount
		$total_amount = 0;
		foreach ( $data[ '_tasks' ] as $key => $task ) {
			$total_amount = $total_amount + $task['amount'];
		}

		// Load template
		require( dirname( dirname( __FILE__ ) ) . '/templates/invoice.php' );
	}

}
