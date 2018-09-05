<?php
/**
 * Front page template file.
 *
 * @package SRC Theme
 * @since SRC Theme 1.0
 */

get_header();


?>

<section id="latest-news">
	<header>
		<h2>The latest in the Seacrest Racing Club</h2>
	</header>

	<div class="slider" data-slides="3" data-subtract="80">
		<div class="slider-inner"><?php

	if ( is_user_logged_in() ) {

		$args = array(
			'post_type'              => 'topic',
			'meta_key'               => '_thumbnail_id',
			'posts_per_page'         => 6,
			'offset'                 => 1,
			'no_found_rows'          => true,  // useful when pagination is not needed.
			'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids'
		);

	} else {

		$args = array(
			'post_type'              => 'topic',
			'meta_key'               => '_thumbnail_id',
			'posts_per_page'         => 6,
			'no_found_rows'          => true,  // useful when pagination is not needed.
			'update_post_meta_cache' => false, // useful when post meta will not be utilized.
			'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
			'fields'                 => 'ids'
		);

	}

	$featured_items = new WP_Query( $args );
	$count = 0;
	if ( $featured_items->have_posts() ) {
		while ( $featured_items->have_posts() ) {
			$featured_items->the_post();
			$count++;

			?>


			<a href="<?php the_permalink(); ?>" class="<?php echo esc_attr( 'slide post-' . $count ); ?>">
				<div class="image" style="background-image:url(<?php echo esc_url( get_the_post_thumbnail_url() ); ?>);"></div>

				<div class="box-text">
					<date><?php the_date(); ?></date>
					<p>
						<?php the_title(); ?>
					</p>
				</div>

			</a><?php

		}
	}

	?>

		</div><!-- .slider-inner -->
	</div><!-- .slider-outer -->
</section><!-- #latest-news -->

<section id="schedule">
	<div class="slider" data-slides="6">
		<ul class="slider-inner"><?php

			$count = 0;
			$season_slug = get_option( 'src-season' );
			foreach ( src_get_events( $season_slug ) as $number => $event ) {
				$count++;

				$timestamp = $event['event_race-1_timestamp'];

				$text1 = $event['track_name'];

				if ( ! isset( $future ) && $timestamp > time() ) {
					$future = true;

					$extra_class = ' double-slide';
					$data_width = ' data-width="2"';

					$text2 = '
					<h2>' . sprintf( esc_html__( 'Round %s: %s' ), $count, $event['track_name'] )  . '</h2>';

					if ( isset( $event['event_qualifying_timestamp'] ) && '' !== $event['event_qualifying_timestamp'] ) {
						$date_qual  = get_date_from_gmt( date( get_option( 'date_format' ), $event['event_qualifying_timestamp'] ), get_option( 'date_format' ) );
						$time_qual  = get_date_from_gmt( date( get_option( 'time_format' ), $event['event_qualifying_timestamp'] ), get_option( 'time_format' ) ) . ' CET';
						$text2 .= '
					<span>
						' . esc_html( 'Qualifying', 'src' ) . ': <date>' . esc_html( $time_qual ) . ', ' . esc_html( $date_qual ) . '</date>
					</span>';
					}

					if ( isset( $event['event_race-1_timestamp'] ) && '' !== $event['event_race-1_timestamp'] ) {
						$date_race1 = get_date_from_gmt( date( get_option( 'date_format' ), $event['event_race-1_timestamp'] ), get_option( 'date_format' ) );
						$time_race1 = get_date_from_gmt( date( get_option( 'time_format' ), $event['event_race-1_timestamp'] ), get_option( 'time_format' ) ) . ' CET';
						$text2 .= '
					<span>
						' . esc_html( 'Race 1', 'src' ) . ': <date>' . esc_html( $time_race1 ) . ', ' . esc_html( $date_race1 ) . '</date>
					</span>';
					}

					if ( isset( $event['event_race-2_timestamp'] ) && '' !== $event['event_race-2_timestamp'] ) {
						$date_race2 = get_date_from_gmt( date( get_option( 'date_format' ), $event['event_race-2_timestamp'] ), get_option( 'date_format' ) );
						$time_race2 = get_date_from_gmt( date( get_option( 'time_format' ), $event['event_race-2_timestamp'] ), get_option( 'time_format' ) ) . ' CET';
						$text2 .= '
					<span>
						' . esc_html( 'Race 2', 'src' ) . ': <date>' . esc_html( $time_race2 ) . ', ' . esc_html( $date_race2 ) . '</date>
					</span>';
					}

				} else {

					$extra_class = '';
					$data_width = '';

					$text2 = '
					<date>
						<span>' . esc_html( date( 'd', $timestamp ) ) . '</span>
						' . esc_html( date( 'M', $timestamp ) ) . '
					</date>';

				}

				$country_code = $event['country'];
				echo '
			<li class="' . esc_attr( 'post-' . $count . ' slide' . $extra_class ) . '"' . $data_width . '>
				<div>
					<img src="' . esc_url( get_template_directory_uri() . '/images/flags/' . strtolower( $country_code ) . '.gif' ) . '" />
					' . esc_html( $text1 ) .

					/* Already escaped */
					$text2

					. '
				</div>
			</li>';

			}


			?>

		</ul><!-- .slider-inner -->
	</div><!-- .slider -->
</section><!-- #schedule -->

<section id="results">

	<a href="<?php echo esc_url( home_url( '/available-cars/' ) ); ?>" class="other-race" style="background-image:url(<?php echo home_url(); ?>/wp-content/themes/src/images/track.jpg">
		<h2>Cars</h2>
		<p>Check out the cars available to drive</p>
	</a>

	<div id="standings">
		<h3><?php esc_html_e( 'Drivers Championship', 'src' ); ?></h3>
		<table><?php

		$season_slug = get_option( 'src-season' );

		$order = array(
			'Username',
			'Nationality',
			'Car',
			'Team',
			'Class',
			'Pts',
		);

		$drivers = src_get_drivers( $season_slug );

		$content = '';
		if ( is_array( $drivers ) ) {
			$content .= '<table>';

			$count = 0;
			foreach ( $drivers as $row_number => $driver ) {
				$count++;

				$username = $driver[0];

				if ( $count < 9 ) {

					$name = esc_html( src_get_display_name_from_username( $username ) );

					$url = src_get_memberurl_from_username( $username );
					if ( false !== $url ) {
						$name = '<a href="' . esc_url( $url ) . '">' . esc_html( $name ) . '</a>';
					}

					$content .= '<tr>';
					$content .= '<td>' . esc_html( $count ) . '</td>';
					$content .= '<td>' . $name /* Escaped already */ . '</td>';
					$content .= '<td>' . esc_html( $driver[3] ) . '</td>';
					$content .= '<td>' . esc_html( src_get_driver_points( $season_slug, $username ) ) . '</td>';

					$content .= '</tr>';

				}

			}
			$content .= '</table>';
		}
		echo $content;

		?>

		</table>

		<a href="<?php echo esc_url( home_url() . '/championship/' ); ?>" class="highlighted-link">See all championship standings</a>

	</div>

	<a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>" class="other-race" style="background-image:url(<?php echo home_url(); ?>/wp-content/themes/src/images/rosanski.jpg">
		<h2>Gallery</h2>
		<p>Checkout out our photos gallery</p>
	</a>

</section><!-- #results -->

<?php

get_footer();