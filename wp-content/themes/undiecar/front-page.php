<?php
/**
 * Front page template file.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */

get_header();

?>

<section class="latest-items" id="latest-news">
	<header>
		<h2>Latest News</h2>
	</header><?php

	// Load main loop
	$news_query = new WP_Query( array(
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'no_found_rows'  => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	) );

	if ( $news_query->have_posts() ) {

		while ( $news_query->have_posts() ) {
			$news_query->the_post();

			echo '

			<article id="' . esc_attr( 'post-' . get_the_ID() ) . '">
				<a href="' . esc_attr( get_the_permalink( get_the_ID() ) ) . '">
					<img src="' . esc_url( get_the_post_thumbnail_url( get_the_ID(), 'src-four' ) ) . '" />
					<date>' . get_the_date( get_option( 'date_format' ) ) . '</date>
					<p>' . esc_html( get_the_title( get_the_ID() ) ) . '</p>
				</a>
			</article>';
		}

	}
	wp_reset_query();

	?>

	<a href="<?php echo esc_url( home_url() . '/news/' ); ?>" class="highlighted-link"><?php esc_html_e( 'See more news', 'undiecar' ); ?></a>

</section><!-- #latest-item -->

<section id="schedule">
	<ul><?php

	$query = new WP_Query( array(
		'post_type'      => 'event',
		'post_status'    => 'publish',
		'posts_per_page' => 100,
		'no_found_rows'  => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'fields'         => 'ids'
	) );
	$event_array = array();
	if ( $query->have_posts() ) {
		$count = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			$count++;

			$event_id = get_the_ID();
			$event_date = get_post_meta( $event_id, 'date', true );

			foreach ( array( 3, 2, 1 ) as $race ) {
				$race_time = get_post_meta( get_the_ID(), 'event_race-' . $race . '_timestamp', true );
				if ( '' !== $race_time && ! isset( $time ) ) {
					$exploded_race_time = explode( ':', $race_time );

					$hours_in_seconds = $exploded_race_time[0] * 60 * 60;
					$minutes_in_seconds = $exploded_race_time[1] * 60;
					$time = $hours_in_seconds + $minutes_in_seconds;
				}

			}
			$event_time = $event_date + $time;

			$track_id = get_post_meta( $event_id, 'track', true );
			$track_query = new WP_Query( array(
				'p'                      => $track_id,
				'post_type'              => 'track',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids'
			) );

			if ( $track_query->have_posts() ) {
				while ( $track_query->have_posts() ) {
					$track_query->the_post();

					$track_logo = get_post_meta( get_the_ID(), 'logo_id', true );
					$track_name = get_the_title( get_the_ID() );
					$track_type_slug = get_post_meta( get_the_ID(), 'track_type', true );
					$track_type = src_get_track_types()[$track_type_slug];
				}

			}

			if ( $event_time > time() ) {

				$event_array[$event_time] = array(
					'event_id'        => $event_id,
					'track_logo'      => $track_logo,
					'track_name'      => $track_name,
					'track_type_slug' => $track_type_slug,
					'track_type'      => $track_type,
					'event_date'      => $event_date,
				);

			}

		}
	}

	ksort( $event_array );

	$count = 0;
	foreach ( $event_array as $time_stamp => $event ) {
		$count++;
		if ( $count > 5 ) {
			continue;
		}

		$image_url = wp_get_attachment_image_src( $event['track_logo'], 'src-four' );
		$image_url = $image_url[0];

		?>

		<li class="<?php echo esc_attr( 'post-' . $count ); ?>">
			<a href="<?php echo esc_url( get_the_permalink( $event['event_id'] ) ); ?>">
				<img src="<?php echo esc_url( $image_url ); ?>" />
				<h3 class="screen-reader-text"><?php echo esc_html( $event['track_name'] ); ?></h3>
				<?php

				$season_id = get_post_meta( $event['event_id'], 'season', true );
				$season_name = get_the_title( $season_id );
				if ( 'Special Events' === $season_name ) {
					echo 'Special Event';
				} else {
					echo esc_html( $event['track_type'] );
				}

				$day_of_week = date( 'D', $event['event_date'] );
				$month = date( 'M', $event['event_date'] );
				$day_of_month = date( 'd', $event['event_date'] );

				?>

				<date>
					<?php echo esc_html( $day_of_week ); ?>
					<span><?php echo esc_html( $day_of_month ); ?></span>
					<?php echo esc_html( $month ); ?>
				</date>
			</a>
		</li><?php
	}

	?>

	</ul>
</section><!-- #schedule -->

<section id="results">

	<a href="<?php

		if ( '' !== get_option( 'next-season' ) ) {
			$season_id = get_option( 'next-season' );
		} else {
			$season_id = get_option( 'current-season' );
		}

		echo esc_url( get_permalink( $season_id ) );

	?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(https://undiecar.com/files/tall6.jpg);">
		<h2>Next Season</h2>
		<p>Free with iRacing. Fixed setups provided for each track.</p>
	</a>

	<div id="standings">
		<?php

		if ( '' === get_option( 'current-season' ) ) {
			$season_id = get_option( 'last-season' );
		} else {
			$season_id = get_option( 'current-season' );
		}

		$championship_title = esc_html( get_the_title( $season_id ) . ' Championship' );
		echo SRC_Core::championship( '', true, 10, $championship_title );

		?>
		<a href="<?php echo esc_url( get_permalink( $season_id ) ); ?>" class="highlighted-link">See full championship standings</a>

	</div>

	<a href="<?php echo esc_url( home_url( '/rules/' ) ); ?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(https://undiecar.com/files/tall7.jpg);">
		<h2>Rules</h2>
		<p>Minimal rules maximum fun</p>
	</a>

</section><!-- #results -->

<section class="latest-items" id="latest-media">
	<header>
		<h2>Latest Photos</h2>
	</header><?php

	// Load main loop
	$args = array(
		'posts_per_page'         => 4,
		'post_type'              => 'attachment',
		'post_status'            => 'inherit',
		'post_mime_type'         => 'image',
		'meta_key'               => 'gallery',
		'no_found_rows'          => true,  // useful when pagination is not needed.
		'update_post_meta_cache' => false, // useful when post meta will not be utilized.
		'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
		'fields'                 => 'ids'
	);
	$query = new WP_Query( $args );
	$undiecar_gallery = '[gallery columns="8" size="thumbnail" ids="';
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			echo '

			<article id="' . esc_attr( 'post-' . get_the_ID() ) . '">
				<a href="' . esc_attr( get_the_permalink( get_the_ID() ) ) . '">
					' . wp_get_attachment_image( get_the_ID(), 'src-four' ) . '
					<date>' . get_the_date( get_option( 'date_format' ) ) . '</date>
					<p>' . esc_html( get_the_title( get_the_ID() ) ) . '</p>
				</a>
			</article>';

		}
		wp_reset_query();
	}

	?>

	<a href="<?php echo esc_url( home_url() . '/gallery/' ); ?>" class="highlighted-link">See more photos</a>

</section><!-- #latest-media -->

<?php

get_footer();
