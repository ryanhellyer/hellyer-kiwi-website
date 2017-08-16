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

<?php
// Load main loop
query_posts( array( 'posts_per_page' => 4 ) );
if ( have_posts() ) {

	// Start of the Loop
	while ( have_posts() ) {
		the_post();

		?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<a href="<?php the_permalink(); ?>">
				<img src="<?php echo esc_url( get_the_post_thumbnail_url() ); ?>" />
				<date><?php echo get_the_date( get_option( 'date_format' ) ); ?></date>
				<p><?php the_title(); ?></p>
			</a>
		</article><?php
	}

}

?>

		<a href="#" onclick="alert('Link does not work yet!')" class="highlighted-link">See more news</a>

</section><!-- #latest-news -->

<section id="schedule">
	<ul><?php

	$query = new WP_Query( array(
		'post_type'      => 'event',
		'post_status'    => 'future',
		'posts_per_page' => 5,
		'no_found_rows'  => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'fields'         => 'ids'
	) );

	if ( $query->have_posts() ) {
		$count = 0;
		while ( $query->have_posts() ) {
			$query->the_post();
			$count++;

			$event_id = get_the_ID();

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

					$track_logo = get_the_post_thumbnail_url();
					$track_name = get_the_title( get_the_ID() );
					$track_type_slug = get_post_meta( get_the_ID(), 'track_type', true );
					$track_type = src_get_track_types()[$track_type_slug];

				}

			}

			?>

			<li class="<?php echo esc_attr( 'post-' . $count ); ?>">
				<div>
					<img src="<?php echo esc_url( $track_logo ); ?>" />
					<h3 class="screen-reader-text"><?php echo esc_html( $track_name ); ?></h3>
					<?php

					echo esc_html( $track_type );

					$month = get_the_date( 'M', $event_id );
					$day_of_month = get_the_date( 'd', $event_id );
					?>

					<date>
						<span><?php echo esc_html( $day_of_month ); ?></span>
						<?php echo esc_html( $month ); ?>

					</date>
				</div>
			</li><?php

		}
	}

	?>

	</ul>
</section><!-- #schedule -->

<section id="results">

	<a href="<?php echo esc_url( home_url( '/car/' ) ); ?>" class="other-race" style="background-image: linear-gradient( rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3) ), url(<?php echo esc_url( get_template_directory_uri() . '/images/long2.png' ); ?>);">
		<h2>Dallara DW12</h2>
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