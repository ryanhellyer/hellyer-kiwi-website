<?php

/**
 * Schedule.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Schedule extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
//		add_shortcode( 'src-schedule', array( $this, 'schedule' ) );

	}

	/**
	 * Output the schedule as a table.
	 *
	 * @param  string  $content    The post content
	 * @return string  The modified post content
	 */
	public function schedule( $content ) {

		$columns = array(
			'Num'        => true,
			'Event'      => true,
			'Season'     => true,
			'FP1'        => false,
//			'FP2'        => false,
			'Qualifying' => false,
			'Race 1'     => false,
			'Race 2'     => false,
			'Race 3'     => false,
		);

		// Get all events from that season
		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$events = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$date = get_post_meta( get_the_ID(), 'date', true );

				if ( $date < time() && ! isset( $_GET['past'] ) ) {
					continue;
				} else if ( $date > time() && isset( $_GET['past'] ) ) {
					continue;
				}

				$events[$date]['id']    = get_the_ID();
				$events[$date]['season'] = get_post_meta( get_the_ID(), 'season', true );
				$events[$date]['track'] = get_post_meta( get_the_ID(), 'track', true );
				$events[$date]['title'] = get_the_title();

				foreach ( $this->event_types() as $name => $desc ) {
					$time = get_post_meta( get_the_ID(), 'event_' . sanitize_title( $name ) . '_timestamp', true );
					if ( '' !== $time ) {

						if ( 'FP1' === $name ) {
							$columns['FP1'] = true;
//						} else if ( 'FP2' === $name ) {
//							$columns['FP2'] = true;
						} else if ( 'Qualifying' === $name ) {
							$columns['Qualifying'] = true;
						} else if ( 'Race 1' === $name ) {
							$columns['Race 1'] = true;
						} else if ( 'Race 2' === $name ) {
							$columns['Race 2'] = true;
						} else if ( 'Race 3' === $name ) {
							$columns['Race 3'] = true;
						}

						$events[$date][sanitize_title( $name ) . '_timestamp'] = get_post_meta( get_the_ID(), 'event_' . sanitize_title( $name ) . '_timestamp', true );
					}

				}

			}

		}

		krsort( $events );

		if ( isset( $_GET['past'] ) ) {
			$title = __( 'Past Events', 'src' );
		} else {
			$title = __( 'Upcoming Events Schedule', 'src' );
		}

		$html = '<h3>' . esc_html( $title ) . '</h3>';
		$html .= '<table>';

		// Create the THEAD
		$html .= '<thead><tr>';
		foreach ( $columns as $label => $column ) {

			// Only load the columns being used
			if ( true === $column ) {
				$html .= '<th>' . esc_html( $label ) . '</th>';
			}

		}
		$html .= '</tr></thead>';

		$html .= '<tbody>';
		$count = 0;
		foreach ( $events as $date => $event ) {
			$count++;
			$formatted_date = date( get_option( 'date_format' ), $date );

			$html .= '<tr>';

			// Only load the columns being used
			foreach ( $columns as $label => $column ) {

				if ( true === $column ) {

					$text = '';
					if ( 'Num' == $label ) {
						$text = $count;
					} else if ( 'FP1' == $label ) {
						if ( isset( $event['fp1_timestamp'] ) ) {
							$text = esc_html( $event['fp1_timestamp'] );
						}
					} else if ( 'Event' == $label ) {
						if ( isset( $event['track'] ) ) {
							$text = '<a href="' . esc_url( get_permalink( $event['id'] ) ) . '">' . esc_html( get_the_title( $event['track'] ) ) . '</a>';
						}
					} else if ( 'Season' == $label ) {
						if ( isset( $event['season'] ) ) {
							$text = '<a href="' . esc_url( get_permalink( $event['season'] ) ) . '">' . esc_html( get_the_title( $event['season'] ) ) . '</a>';
						}
//					} else if ( 'FP2' == $label ) {
//						if ( isset( $event['fp2_timestamp'] ) ) {
//							$text = esc_html( $event['fp2_timestamp'] );
//						}
					} if ( 'Qualifying' == $label ) {
						if ( isset( $event['qualifying_timestamp'] ) ) {
							$text = $event['qualifying_timestamp'] . '<span>' . esc_html( $formatted_date ) . '</span>';
						}
					} if ( 'Race 1' == $label ) {
						if ( isset( $event['race-1_timestamp'] ) ) {
							$text = esc_html( $event['race-1_timestamp'] ) . '<span>' . esc_html( $formatted_date ) . '</span>';
						}
					} if ( 'Race 2' == $label ) {
						if ( isset( $event['race-2_timestamp'] ) ) {
							$text = esc_html( $event['race-2_timestamp'] ) . '<span>' . esc_html( $formatted_date ) . '</span>';
						}
					} if ( 'Race 3' == $label ) {
						if ( isset( $event['race-3_timestamp'] ) ) {
							$text = esc_html( $event['race-3_timestamp'] ) . '<span>' . esc_html( $formatted_date ) . '</span>';
						}
					}

					$html .= '<td>' . $text /* do not escape */ . '</td>';

				}

			}

			$html .= '</tr>';

		}
		$html .= '</tbody>';

		$html .= '</table>';

		wp_reset_query();


		if ( isset( $_GET['past'] ) ) {
			$html .= '<a class="highlighted-link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '">' . __( 'See upcoming events', 'src' ) . '</a>';
		} else {
			$html .= '<a class="highlighted-link" href="' . esc_url( get_permalink( get_the_ID() ) ) . '?past">' . __( 'See previous events', 'src' ) . '</a>';
		}

		$content .= $html;

		return $content;
	}

}
