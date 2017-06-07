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

<?php

if ( is_super_admin() ) {
	?>
<section id="schedule">
	<div class="slider" data-slides="6">
		<ul class="slider-inner">
			<li class="post-1 slide">
				<div>
					<img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/flag1.png" />
					Monaco - PERHAPS REMOVE THE FLAG AND ONLY HAVE BIG DATE, FOLLOWED BY RACE TRACK
					<date>
						<span>28</span>
						May
					</date>
				</div>
			</li>
			<li class="post-2 slide">
				<div>
					<img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/flag2.png" />
					Suzuka
					<date>
						<span>15</span>
						July
					</date>
				</div>
			</li>
			<li class="post-3 double-slide slide" data-width="2">
				<div>
					<img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/flag3.png" />
					Laguna Seca
					<h2>Round 3: Laguna Seca</h2>
					<span>
						Race 2: <date>20:50 CET Saturday July 21 2017</date>
					</span>
					<span>
						Race 1: <date>20:00 CET Saturday July 21 2017</date>
					</span>
					<span>
						Qualifying: <date>19:40 CET Saturday July 21 2017</date>
					</span>
					<span>
						Free Practice 2: <date>20:00 CET Friday July 20 2017</date>
					</span>
					<span>
						Free Practice 1: <date>20:00 CET Friday July 20 2017</date>
					</span>
					</div>
			</li>
			<li class="post-4 slide">
				<div>
					<img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/flag1.png" />
					Bathurst
					<date>
						<span>14</span>
						August
					</date>
					</div>
			</li>
			<li class="post-5 slide">
				<div>
					<img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/flag3.png" />
					Hockenheim
					<date>
						<span>21</span>
						September
					</date>
				</div>
			</li>
			<li class="post-6 slide">
				<div>
					<img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/flag1.png" />
					Bathurst
					<date>
						<span>14</span>
						August
					</date>
				</div>
			</li>
			<li class="post-7 slide">
				<div>
					<img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/flag3.png" />
					Hockenheim
					<date>
						<span>21</span>
						September
					</date>
				</div>
			</li>
		</ul><!-- .slider-inner -->
	</div><!-- .slider -->
</section><!-- #schedule -->

<section id="results">

	<div class="other-race" style="background-image:url(http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/track.jpg">
		<h2>Last Race</h2>
		<p>Round 2<br />Monaco</p>
	</div>

	<div id="standings">
		<h3>Driver Standings</h3>
		<ul>
			<li>Pro Drivers</li>
			<li>Am Drivers</li>
			<li>Teams</li>
		</ul>
		<table>
			<tr>
				<td class="pink"><span>1</span></td>
				<td>Trek</td>
				<td>Ferrari 458</td>
				<td class="car"><img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/pink-car.jpg" /></td>
				<td>949</td>
			</tr>
			<tr>
				<td class="green"><span>2</span></td>
				<td>Paul Rosanski</td>
				<td>Audi R8</td>
				<td class="car"><img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/green-car.jpg" /></td>
				<td>86</td>
			</tr>
			<tr>
				<td class="red"><span>3</span></td>
				<td>Tango Foxx</td>
				<td>BMW M6</td>
				<td class="car"><img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/red-car.jpg" /></td>
				<td>46</td>
			</tr>
			<tr>
				<td class="pink"><span>4</span></td>
				<td>Ryan Hellyer</td>
				<td>Ferrari 458</td>
				<td class="car"><img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/pink-car.jpg" /></td>
				<td>24</td>
			</tr>
			<tr>
				<td class="yellow"><span>5</span></td>
				<td>Speedylu</td>
				<td>Porsche 911</td>
				<td class="car"><img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/yellow-car.jpg" /></td>
				<td>11</td>
			</tr>
			<tr>
				<td class="pink"><span>1</span></td>
				<td>Trek</td>
				<td>Ferrari 458</td>
				<td class="car"><img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/pink-car.jpg" /></td>
				<td>949</td>
			</tr>
			<tr>
				<td class="green"><span>2</span></td>
				<td>Paul Rosanski</td>
				<td>Audi R8</td>
				<td class="car"><img src="http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/green-car.jpg" /></td>
				<td>86</td>
			</tr>
		</table>

		<a href="#" class="highlighted-link">See all championship standings</a>

	</div>

	<div class="other-race" style="background-image:url(http://dev-hellyer.kiwi/bbpress/wp-content/themes/src/images/track.jpg">
		<h2>Next Race</h2>
		<p>Round 4<br />Bathurst</p>
	</div>

</section><!-- #results -->

<?php
}

get_footer();