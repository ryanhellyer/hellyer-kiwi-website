<?php

/**
 * Core functionalities.
 * Methods used across multiple classes.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */
class SRC_Core {

	/**
	* Event types.
	*
	* @return array
	*/
	protected function event_types() {

		$types = array(
			'FP1'        => 'Free Practice 1',
			'FP2'        => 'Free Practice 1',
			'Qualifying' => 'Qualifying',
			'Race 1'     => 'Race 1',
			'Race 2'     => 'Race 2',
			'Race 3'     => 'Race 3',
		);

		return $types;
	}

	/**
	 * The championship table.
	 *
	 * @param  string  $content   The post content
	 * @param  bool    $bypass    true if bypassing post-type check
	 */
	static function championship( $content, $bypass = false ) {

		if ( 'season' !== get_post_type() && true !== $bypass ) {
			return $content;
		}

		if ( is_front_page() ) {
			$season_id = get_option( 'src-current-season' );
		} else {
			$season_id = get_the_ID();
		}

		// Get all events from that season
		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',

			'meta_key'               => 'season',
			'meta_value'             => $season_id,

			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$stored_results = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$results = get_post_meta( get_the_ID(), '_results', true );
				$results = json_decode( $results, true );

				$points_positions = get_post_meta( $season_id, 'points_positions', true );

				if ( is_array( $results ) ) {
					foreach ( $results as $pos => $result ) {
						$name = $result['name'];
						if ( isset( $points_positions[$pos - 1] ) ) {
							$stored_results[$name] = $points_positions[$pos - 1];
						}
					}
				}

			}
		}
		arsort( $stored_results );
		wp_reset_query();

		if ( array() !== $stored_results ) {
			$content .= '<h3>' . esc_html__( 'Championship', 'src' ) . '</h3>';
			$content .= '<table>';

			$content .= '<thead><tr>';

			$content .= '
				<th>Pos</th>
				<th>Name</th>
				<th>Num</th>
				<th>Nationality</th>
				<th>Pts</th>';
			$content .= '</tr></thead>';

			$content .= '<tbody>';
			$position = 0;
			foreach ( $stored_results as $name => $points ) {
				$position++;
				$content .= '<tr>';

				$content .= '<td>' . esc_html( $position ) . '</td>';
				$content .= '<td><a href="#">' . esc_html( $name ) . '</a></td>';
				$content .= '<td>27</td>';
				$content .= '<td>NZL</td>';
				$content .= '<td>' . esc_html( $points ) . '</td>';

				$content .= '</tr>';
			}
			$content .= '</tbody>';

			$content .= '</table>';
		}

		return $content;
	}

}
