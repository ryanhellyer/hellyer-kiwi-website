<?php

class MediaHub_Agenda extends MediaHub_Core {

	/**
	 * Class constructor.
	 */
	function __construct() {

		add_shortcode( 'mediahub_agenda', array( $this, 'agenda_shortcode' ) );
	}


	/**
	 * Displays a list of posts added by MediaHub.
	 *
	 * @return string $event Agenda items
	 */
	function agenda_shortcode( $args ) {

		// WP_Query arguments
		$query_args = array (
			'post_type'              => 'post',
			'ignore_sticky_posts'    => true,
			'meta_key'               => self::META_KEY,
			'orderby'                => 'meta_value',
			'order'                  => 'ASC',
			'meta_query'             => array(
				array(
					'key'       => self::META_KEY,
				),
			),
		);

		// Set the category
		if ( isset( $args['category'] ) ) {
			$query_args['category_name'] = esc_html( $args['category'] );
		}

		// Limit to last days
		if ( isset( $args['days'] ) ) {
			$query_args['days'] = array(
				array(
					'year'  => date( 'y', time() ),
					'month' => date( 'm', time() ),
					'day'   => date( 'd', time() ),
				),
			);
		}

		// Limit to date range
		if ( isset( $args['before_date'] ) && isset( $args['after_date'] ) ) {
			$before_date = explode( ':', $args['before_date'] );
			$after_date  = explode( ':', $args['after_date'] );

			$query_args['date_query'] = array(
				array(
					'after'     => array(
						'year'    => absint( $after_date[2] ),
						'month'   => absint( $after_date[1] ),
						'day'     => absint( $after_date[0] ),
					),
					'before'    => array(
						'year'    => absint( $before_date[2] ),
						'month'   => absint( $before_date[1] ),
						'day'     => absint( $before_date[0] ),
					),
					'inclusive' => true,
				),
			);

		}


		if ( isset( $args['posts_per_page'] ) && is_numeric( $args['posts_per_page'] ) ) {
			if ( $args['posts_per_page'] < self::MAX_POSTS_PER_PAGE ) {
				$query_args['posts_per_page'] = $args['posts_per_page'];
			} else {
				$query_args['posts_per_page'] = self::MAX_POSTS_PER_PAGE;
			}
		}	
//			'posts_per_page'         => self::MAX_POSTS_PER_PAGE,




		// The Query
		$mediahub_events = new WP_Query( $query_args );

		$content = '';

		// Loop through all existing posts made by MediaHub
		if ( $mediahub_events->have_posts() ) {

			while ( $mediahub_events->have_posts() ) {
				$mediahub_events->the_post();

				$content .= '<h2><a href="' . esc_url( get_permalink() ) . '">' . get_the_title() . "</a></h2>\n";

				$start = date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( get_the_ID(), $key = 'mediahub_event_start', $single = true ) ) );
				$end   = date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( get_the_ID(), $key = 'mediahub_event_end', $single = true ) ) );

				$content .= '<p class="event-date">' . $start . ' tot ' . $end . "</p>\n";
				$content .= '<p class="event-excerpt">' . get_the_excerpt() . ' <a class="readmore" href="' . esc_url( get_permalink() ) . '">Lees verder' . "</a></p>\n";
			}
		} else {
			// Message to display when no posts found
			$content = '<p>' . __( 'No current calendar items found', 'mediahub' ) . '</p>';
		}

		// Restore original Post Data
		wp_reset_postdata();

		return $content;
	}

}
new MediaHub_Agenda;
