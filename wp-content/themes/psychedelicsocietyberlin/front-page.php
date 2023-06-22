<?php
/**
 * The main template file.
 *
 * @package Psychedelic Society Berlin
 * @since Psychedelic Society Berlin 1.0
 */

get_header();

?>

<section id="events">
	<div class="container">
		<h1>Activities</h1>
		<?php echo wpautop( wp_kses_post( get_option( 'events-description' ) ) ); ?>
		<ul><?php
		$events_query = new WP_Query( array(
			'post_type' => 'event',
			'posts_per_page' => 3,
			'no_found_rows' => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields' => 'ids',

		) );

		if ( $events_query->have_posts() ) {
			while ( $events_query->have_posts() ) {
				$events_query->the_post();

				?>

			<li>
				<a title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
					<?php 
						$attachment_id   = get_post_thumbnail_id();
						$thumbnail_image = wp_get_attachment_image_src( $attachment_id, 'events-thumb' );
						$medium_image    = wp_get_attachment_image_src( $attachment_id, 'events-double' );
						echo '<img src="' . esc_url( $medium_image[0] ) . '" srcset="' . esc_url( $thumbnail_image[0] ) . ', ' . esc_url( $medium_image[0] ) . ' 2x" alt="' . esc_attr( get_the_title() ) . '" />';
					?>
					<h2>
						<?php the_title(); ?>
					</h2>
					<date><?php 
						echo date( get_option( 'date_format' ) . ' h:m', get_post_meta( get_the_ID(), 'start_time', true ) );
					?> </date>
				</a>
			</li><?php
			}
		}

		?>

		</ul>
		<p>
			<a class="button" href="/events/">See all events</a>
		</p>
	</div>
</section>

<section id="partners">
	<div class="container">
		<h1>Partners</h1>
		<ul><?php

		$partners   = get_post_meta(
			get_option( 'page_on_front' ),
			'partners',
			true
		);

		foreach ( $partners as $key => $partner ) {
			$attachment  = get_post( $partner['attachment'] );
			$logo_small  = wp_get_attachment_image_src( $attachment->ID, 'logo-small' );
			$logo_double = wp_get_attachment_image_src( $attachment->ID, 'logo-double' );

			echo '
				<li>
					<a title="' . esc_attr( $attachment->post_title ) . '" href="' . esc_url( $partner['url'] ) . '">
						<img src="' . esc_url( $logo_double[0] ) . '" srcset="' . esc_url( $logo_small[0] ) . ', ' . esc_url( $logo_double[0] ) . ' 2x" alt="' . esc_attr( get_the_title() ) . '" />
					</a>
				</li>';
		}

		?>
		</ul>
	</div>
</section>

<section id="contact">
	<div class="container"><?php

		$contact_page_id = get_post_meta(
			get_option( 'page_on_front' ),
			'contact-page',
			true
		);

		$contact_query = new WP_Query( array(
			'p'                      => $contact_page_id,
			'post_type'              => 'page',
			'posts_per_page'         => 1,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		) );

		if ( $contact_query->have_posts() ) {
			while ( $contact_query->have_posts() ) {
				$contact_query->the_post();

				?>

				<h1><?php the_title(); ?></h1>
				<div id="contact-page">
					<?php the_content(); ?>
				</div><?php
			}
		}

		?>

		</div>
	</div>
</section>

<?php

get_footer();
