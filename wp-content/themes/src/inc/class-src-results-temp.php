<?php

/**
 * Results.
 * Temporary system until final version is ready.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Results_Temp extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_shortcode( 'src-results', array( $this, 'results_shortcode' ) );
	}

	/**
	 * Results.
	 */
	public function results_shortcode( $args ) {

		$args2 = array();
		if ( isset( $args['class'] ) ) {
			$args2['Class'] = $args['class'];
		}

		// Get order into array
		$order = null;
		if ( isset( $args['order'] ) ) {
			$order = explode( ',', $args['order'] );
		}

		// Set orderby
		if ( isset( $args['orderby'] ) ) {
			$orderby = $args['orderby'];
		} else {
			$orderby = 'Pts';
		}

		$results = $this->get_results(
			'results',
			$orderby,
			true,
			$order
		);

		$content = '
				<table>';

		foreach( $results as $row_number => $row ) {

			if ( 0 === $row_number ) {

				$content .= '
					 <thead>
						<tr>';

				foreach( $row as $column => $label ) {

					// Bail out if on number, as this is a race and not needed for driver listings
					if ( is_numeric( $column ) ) {
						continue;
					}

					$content .= '<th>' . esc_html( $column ) . '</th>';
				}

				$content .= '
						</tr>
					</thead>';
			}

			// 
			$content .= '<tr>';
			foreach( $row as $column => $label ) {

				// Bail out if on number, as this is a race and not needed for driver listings
				if ( is_numeric( $column ) ) {
					continue;
				}

				// We ignore two decimal place numbers, as we may need to designate fractions for when dead heats cause problems
				if ( is_numeric ( $label ) ) {
					$label = round( $label, 1 );
				}

				 $content .= '<td>' . esc_html( $label ) . '</td>';
			}
			$content .= '</tr>';

		}

		$content .= '
			</table>';

		return $content;
	}

	/**
	 * Get the results.
	 *
	 * @param  string  $season    The season of the data being accessed
	 * @param  string  $sort_by   The columns to sort by
	 * @param  bool    $reverse   true if results should be reversed
	 * @param  array   $order     Order of values. If not set, provides defaults.
	 */
	private function get_results( $season, $sort_by = null, $reverse, $order = null ) {

		$raw_results = file_get_contents( dirname( __FILE__ ) . '/' . $season . '.csv' );
		$raw_results = explode( "\n", $raw_results );

		// Iterate through each result
		$results = array();
		foreach ( $raw_results as $key => $raw_result ) {
			$raw_result = explode( ',', $raw_result );

			// First row only used to get keys for columns
			if ( 0 === $key ) {
				$columns = $raw_result;
			} else {

				// Process each row
				$result = array();
				$remove = false;
				$points = $am_points = 0;
				foreach ( $raw_result as $col_number => $data ) {
					foreach ( $columns as $n => $label ) {
						if ( $n === $col_number ) {
							$label = trim( $label );
							$data = trim( $data );
							$result[$label] = $data;

							// If label is numeric, then we know it's a race result
							if ( is_numeric( $label ) ) {

								$exploded_data = explode( '#', $data );
								if ( isset( $exploded_data[1] ) ) {
									$am_points = $am_points + $exploded_data[1]; // Add up AM points for each race
								}

								$points = $points + $exploded_data[0]; // Add up points for each race

							}

						}
					}

				}

				// Add points to array (they're not in by default as we still needed to calculate them)
				$result['Pts'] = $points;
				$result['AM Pts'] = $am_points;

				if ( false === $remove ) {
					$results[$key] = $result;
				}

			}

		}

		// Sort rows in the order requested
		if ( null != $sort_by ) {
			$this->sort_by = $sort_by;
			if ( true === $reverse ) {
				usort( $results, array( $this, 'sort_callback_reverse' ) );
			} else {
				usort( $results, array( $this, 'sort_callback' ) );
			}
		}

		// Sort columns (and remove those not specified)
		if ( false != $order ) {
			$old_results = $results;
			foreach ( $old_results as $row_number => $row ) {

				unset( $results[$row_number] );
				foreach ( $order as $x => $column ) {
					if ( isset( $row[$column] ) ) {
						$results[$row_number][$column] = $row[$column];
					}
				}


			}

		}

		// Finally, return the results
		return $results;
	}

	function sort_callback( $a, $b ) {
		return $a[ $this->sort_by ] - $b[ $this->sort_by ];
	}

	function sort_callback_reverse( $a, $b ) {
		return $b[ $this->sort_by ] - $a[ $this->sort_by ];
	}

}
