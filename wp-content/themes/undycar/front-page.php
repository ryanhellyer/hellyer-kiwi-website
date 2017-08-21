<?php
/**
 * Front page template file.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */

get_header();


?>

<section id="latest-news">
	<header>
		<h2>Latest news</h2>
	</header>

	<?php echo src_news( 4 ); ?>

	<a href="<?php echo esc_url( home_url() . '/news/' ); ?>" class="highlighted-link">See more news</a>

</section><!-- #latest-news -->

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
			$event_date = get_post_meta( $event_id, 'event_date', true );

			$track_id = get_post_meta( $event_id, 'track', true );
echo "\n\ntrack id: " . $track_id . "\n";
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
echo 'get_the_ID(): ' . get_the_ID() . "\n\";
					$track_logo = get_post_meta( get_the_ID(), 'logo_id', true );
					$track_name = get_the_title( get_the_ID() );
					$track_type_slug = get_post_meta( get_the_ID(), 'track_type', true );
					$track_type = src_get_track_types()[$track_type_slug];

				}

			}

			$event_array[$event_date] = array(
				'event_id'        => $event_id,
				'track_logo'      => $track_logo,
				'track_name'      => $track_name,
				'track_type_slug' => $track_type_slug,
				'track_type'      => $track_type,
				'event_date'      => $event_date,
			);

		}
	}

	ksort( $event_array );

	$count = 0;
	foreach ( $event_array as $time_stamp => $event ) {
		$count++;
		if ( $count > 5 ) {
			continue;
		}

		$image_url = wp_get_attachment_image_src( $event['track_logo'], 'src-logo' );
		$image_url = $image_url[0];

		?>

		<li class="<?php echo esc_attr( 'post-' . $count ); ?>">
			<a href="<?php echo esc_url( get_the_permalink( $event['event_id'] ) ); ?>">
				<img src="<?php echo esc_url( $image_url ); ?>" />
				<h3 class="screen-reader-text"><?php echo esc_html( $event['track_name'] ); ?></h3>
				<?php

				echo esc_html( $event['track_type'] );

				$month = date( 'M', $event['event_date'] );
				$day_of_month = date( 'd', $event['event_date'] );

				?>

				<date>
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

	<a href="<?php echo esc_url( get_permalink( get_option( 'src-current-season' ) ) ); ?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(<?php echo esc_url( get_template_directory_uri() . '/images/long2.png' ); ?>);">
		<h2>New Season</h2>
		<p>Free with iRacing. Fixed setups provided for each track.</p>
	</a>

	<div id="standings">
		<h3><?php esc_html_e( 'Drivers Championship', 'src' ); ?></h3>
		<table>
			<col width="13%">
			<col width="25%">
			<col width="7%">
			<col width="10%">
			<col width="20%">
			<col width="25%">
			<thead>
				<tr>
					<th>Pos</th>
					<th>Name</th>
					<th>Nat</th>
					<th>Num</th>
					<th></th>
					<th>Pts</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td><a href="#">Paul Rosanski</a></td>
					<td>23</td>
					<td>DEU</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>89</td>
				</tr>
				<tr>
					<td>2</td>
					<td><a href="#">Ryan Hellyer</a></td>
					<td>27</td>
					<td>NZL</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>27</td>
				</tr>
				<tr>
					<td>3</td>
					<td><a href="#">Paul Rosanski</a></td>
					<td>23</td>
					<td>DEU</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>89</td>
				</tr>
				<tr>
					<td>4</td>
					<td><a href="#">Ryan Hellyer</a></td>
					<td>27</td>
					<td>NZL</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>27</td>
				</tr>
				<tr>
					<td>5</td>
					<td><a href="#">Paul Rosanski</a></td>
					<td>23</td>
					<td>DEU</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>89</td>
				</tr>
				<tr>
					<td>6</td>
					<td><a href="#">Ryan Hellyer</a></td>
					<td>27</td>
					<td>NZL</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>27</td>
				</tr>
				<tr>
					<td>7</td>
					<td><a href="#">Paul Rosanski</a></td>
					<td>23</td>
					<td>DEU</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>89</td>
				</tr>
				<tr>
					<td>8</td>
					<td><a href="#">Ryan Hellyer</a></td>
					<td>27</td>
					<td>NZL</td>
					<td><img src="http://dev-hellyer.kiwi/wp-content/themes/undycar/images/car1.png" /></td>
					<td>27</td>
				</tr>
			</tbody>
		</table>

		<a href="<?php echo esc_url( home_url() . '/championship/' ); ?>" class="highlighted-link">See all championship standings</a>

	</div>

	<a href="<?php echo esc_url( home_url( '/rules/' ) ); ?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(<?php echo esc_url( get_template_directory_uri() . '/images/long1.png' ); ?>);">
		<h2>Rules</h2>
		<p>Minimal rules maximum fun</p>
	</a>

</section><!-- #results -->

<?php

get_footer();