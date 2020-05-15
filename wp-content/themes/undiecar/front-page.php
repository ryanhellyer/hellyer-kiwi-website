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
		<h2><?php esc_html_e( 'Latest News', 'undiecar' ); ?></h2>
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

			unset( $time );
/*** THIS NEEDS UPGRADED TO WORK WITH THE NUMBER OF RACES, RATHER THAN GUESSING HOW MANY RACES THERE ARE ***/
			foreach ( array( 3, 2, 1 ) as $race ) {
				$race_time = get_post_meta( get_the_ID(), 'event_race-' . $race . '_timestamp', true );
				if ( '' === $race_time ) {
					$race_time = get_post_meta( get_the_ID(), 'race_' . $race . '_time', true );
				}

				if ( '' !== $race_time && ! isset( $time ) ) {
					$exploded_race_time = explode( ':', $race_time );

					$hours_in_seconds = $exploded_race_time[0] * 60 * 60;
					$minutes_in_seconds = $exploded_race_time[1] * 60;
					$time = $hours_in_seconds + $minutes_in_seconds;
				}

			}

			if ( ! isset( $time ) ) {
				continue;
			}

			$event_time = $event_date + $time;
			$event_time = $event_time + ( 2 * HOUR_IN_SECONDS ); // Show on calendar for two hours after it happened
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

		$season_id = get_post_meta( $event['event_id'], 'season', true );
		$season_name = get_the_title( $season_id );
		?>

		<li class="<?php echo esc_attr( 'post-' . $count ); ?>">
			<a href="<?php echo esc_url( get_the_permalink( $event['event_id'] ) ); ?>">
				<img src="<?php echo esc_url( $image_url ); ?>" />
				<h3 class="screen-reader-text"><?php echo esc_html( $event['track_name'] ); ?></h3>
				<?php

				$season_label = get_post_meta( $season_id, 'label', true );

//				if ( 'Special Events' === $season_name ) {
//					esc_html_e( 'Special Event', 'undiecar' );
//					echo '<span class="season-label"> </span>';
//				} else {
//					echo esc_html( $event['track_type'] );
					echo esc_html( get_the_title( $event['event_id'] ) );

					echo '<span class="season-label">' . esc_html( $season_name ) . '</span>';


//				}

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

<?php

/*
$args = array(
	'name'        => 'our-events',
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => 1
);
$our_events = get_posts( $args );

if ( isset( $our_events[0] ) ) {
	
	?>

<section class="latest-items" id="low-down">

	<header>
		<h2><?php esc_html_e( 'Our events', 'undiecar' ); ?></h2>
	</header>

	<?php

	echo apply_filters( 'the_content', $our_events[0]->post_content );

	?>

</section><!-- #low-down -->

<?php } 
*/

?>

<section id="results">

	<a href="<?php

		if ( '' !== get_option( 'next-season' ) ) {
			$season_id = get_option( 'next-season' );
		} else {
			$season_id = get_option( 'current-season' );
		}

		if ( get_option( 'next-season' ) === get_option( 'current-season' ) ) {
			$text = __( 'This season', 'undiecar' );
		} else {
			$text = __( 'Next season', 'undiecar' );
		}

		echo esc_url( get_permalink( $season_id ) );



/*
	?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(https://undiecar.com/files/tall6.jpg);">
*/
	?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(https://undiecar.com/files/mats-sjoblom-tall.jpg);">
		<h2><?php echo esc_html( $text ); ?></h2>
		<p>
			<?php echo esc_html( get_the_title( $season_id ) ); ?><br />
			Race with free iRacing content. Fixed setups.
		</p>
	</a>

	<div id="standings">

		<?php

/****  MAKE TABBER.JS ONLY LOAD ON FRONT PAGE ****/

		if ( '' === get_option( 'current-season' ) ) {
			$season_id = get_option( 'last-season' );
		} else {
			$season_id = get_option( 'current-season' );
		}


		?>

<!--
		<div class="tabber">

			<div class="tabbertab" id="tab-1">
-->
				<h2><?php echo esc_html( get_the_title( $season_id ) ); ?></h2>
				<?php
					$championship_title = esc_html( get_the_title( $season_id ) );
					echo SRC_Core::championship_summary( '', true, 10, false, false, $season_id );


/*
				?>
<table class="some-list" id="src-championship">
<thead>
<tr>
<th>Pos</th>
<th>Driver</th>
<th>Total</th>
<th>R1</th>
<th>R2</th>
<th>R3</th>
<th>R4</th>
<th>R5</th>
<th>R6</th>
<th>R7</th>
<th>R8</th>
<th>R9</th>
<th>R10</th>
<th>R11</th>
</tr>
</thead>
<tbody>






<tr>
<td>1</td>
<td>Olivier Dean2</td>
<td>91</td>
<td>20</td>
<td>19</td>
<td>14</td>
<td>18</td>
<td> </td>
<td>20</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>2</td>
<td>Justin Hess</td>
<td>85</td>
<td>13</td>
<td>17</td>
<td>18</td>
<td>7</td>
<td>9</td>
<td>21</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>3</td>
<td>Henry Bennett</td>
<td>70</td>
<td>14</td>
<td>11</td>
<td>19</td>
<td>16</td>
<td>1</td>
<td>9</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>4</td>
<td>Thomas Lademann (div 2)</td>
<td>56</td>
<td>17</td>
<td>8</td>
<td>5</td>
<td>12</td>
<td>7</td>
<td>7</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>5</td>
<td>Alejandro Yanes</td>
<td>55</td>
<td> </td>
<td>4</td>
<td>17</td>
<td>15</td>
<td>8</td>
<td>11</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>6</td>
<td>Frank Oosterhuis</td>
<td>49</td>
<td> </td>
<td> </td>
<td>9</td>
<td>20</td>
<td>20</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>7</td>
<td>Nikolay Ladushkin</td>
<td>48</td>
<td> </td>
<td>8</td>
<td>17</td>
<td>23</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>8</td>
<td>Josu Solaguren</td>
<td>48</td>
<td>6</td>
<td>2</td>
<td>12</td>
<td>9</td>
<td>5</td>
<td>14</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>9</td>
<td>Steven Brumfield</td>
<td>47</td>
<td> </td>
<td>15</td>
<td>20</td>
<td>6</td>
<td>6</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>
<tr>
<td>10</td>
<td>Andreas Robertsson</td>
<td>45</td>
<td> </td>
<td> </td>
<td>8</td>
<td>12</td>
<td>18</td>
<td>7</td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
<td> </td>
</tr>





</tbody>
</table>
*/
?>



				<a href="<?php echo esc_url( get_permalink( $season_id ) ); ?>" class="highlighted-link">See full championship standings</a>


<!--
			</div>

			<div class="tabbertab" id="tab-2">

<?php $season_id = 2191; ?>

				<h2><?php echo esc_html( get_the_title( $season_id ) ); ?></h2>
				<?php
					$championship_title = esc_html( get_the_title( $season_id ) );
					echo SRC_Core::championship( '', true, 10, false, false, $season_id );
				?>
				<a href="<?php echo esc_url( get_permalink( $season_id ) ); ?>" class="highlighted-link">See full championship standings</a>
			</div>

		</div>
-->

	</div>

	<a href="<?php echo esc_url( home_url( '/rules/' ) ); ?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(https://undiecar.com/files/tall-1.jpg);">
		<h2>Rules</h2>
		<p>Minimal rules maximum fun</p>
	</a>

</section><!-- #results -->

<section class="latest-items" id="latest-media">
	<header>
		<h2><?php esc_html_e( 'Latest photos and videos', 'undiecar' ); ?></h2>
	</header><?php

	// Get photos
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
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$time_stamp = get_the_date( 'U' );
			$media_posts[ $time_stamp ] = array(
				'post_id'   => get_the_ID(),
				'post_type' => 'attachment',
			);

		}
		wp_reset_query();
	}

	// Get videos
	$args = array(
		'posts_per_page'         => 4,
		'post_type'              => 'video',
		'post_status'            => 'publish',
		'no_found_rows'          => true,  // useful when pagination is not needed.
		'update_post_meta_cache' => false, // useful when post meta will not be utilized.
		'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
		'fields'                 => 'ids'
	);
	$query = new WP_Query( $args );
	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();

			$time_stamp = get_the_date( 'U' );
			$media_posts[ $time_stamp ] = array(
				'post_id'   => get_the_ID(),
				'post_type' => 'video',
			);

		}
		wp_reset_query();
	}

	krsort( $media_posts );

	// Loop through each media post
	$count = 0;
	foreach ( $media_posts as $time_stamp => $post ) {
		$count++;

		// Limit to 4
		if ( $count > 4 ) {
			break;
		}

		$post_id = $post[ 'post_id' ];
		$post_type = $post[ 'post_type' ];

		echo '

		<article id="' . esc_attr( 'post-' . $post_id ) . '">
			<a href="' . esc_attr( get_the_permalink( $post_id ) ) . '">';

		if ( 'video' === $post_type ) {
			echo '
				<img src="' . esc_url( get_the_post_thumbnail_url( $post_id	, 'src-four' ) ) . '" />';
		} else {
			echo '
				' . wp_get_attachment_image( $post_id, 'src-four' );
		}

		echo '
				<date>' . date( get_option( 'date_format' ), $time_stamp ) . '</date>
				<p>' . esc_html( get_the_title( $post_id ) ) . '</p>
			</a>
		</article>';

	}
	?>

	<a href="<?php echo esc_url( home_url() . '/gallery/' ); ?>" class="highlighted-link"><?php esc_html_e( 'See more photos', 'undiecar' ); ?></a>

</section><!-- #latest-media -->

<?php

get_footer();
