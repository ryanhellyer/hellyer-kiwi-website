<?php get_header(); ?>
	<div id="contentwrapper">

		<div id="maincontent">
<?php

if ( is_page_template( 'products-list.php' ) ) {
	$products_meta = get_post_meta( $post->ID, 'products', true );
	$products_meta = explode( ',', $products_meta );
	foreach( $products_meta as $value ) {
		$post_nums[] = $value;
	}

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			$excerpt = get_the_excerpt();
			$content = get_the_content();
		}
	}

	if ( isset( $content ) ) {
		echo '<div class="post">';
		echo wpautop( $content );
		echo '</div>';
	}

	$args = array(
		'post_type' => array( 'post', 'page' ),
		'post__in'  => $post_nums,
		'posts_per_page' => -1,
		'orderby'   => 'menu_order',
		'order'     => 'ASC'
	);
	query_posts( $args );
}

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		?>
<div class="post" id="post-<?php the_ID(); ?>">
	<?php
		if ( is_single() || is_page() && ! is_page_template( 'products-list.php' ) ) {
		}
		else { ?>
			<div class="post_thumbnail"><?php
			if ( !has_post_thumbnail() ) { ?>
				<img src="http://www.gravatar.com/avatar/a084125c824d274ff40cb834cf9f544a?default=monsterid&size=100" alt="" />
			<?php }
			else {
				the_post_thumbnail( 'home-post-thumbnail' );
			} ?>
			</div><?php
		}
	?>
	<h2 id="post_title"><?php if ( !is_page() && !is_single() ) : ?><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php endif; ?><?php the_title(); ?><?php if ( !is_page() && !is_single() ) : ?></a><?php endif; ?></h2>
<?php


	if ( ! isset( $content ) ) { ?>
	<p class="post_subheader"><?php if ( !is_page() ) : ?>
		Published <?php the_time( 'F jS, Y' ) ?><?php if ( !is_page() ) { ?> under <?php the_category( ', ' ) ?><?php } ?>
<?php endif; ?>
		<?php edit_post_link( 'Edit', ' // ', '' ); ?>
	</p><?php
	}


	?>

	<div class="postcontent">
		<?php
			if ( is_single() || is_page() ) {
				the_content( ' ... Read more' );
			}
			else {
				the_excerpt( ' ... Read more' );
			}
		?>
	</div>
</div>
<?php

		comments_template();
	}


numeric_pagination_shortcode();



}
	else { ?>
<h2>Not Found</h2>
<p>Sorry, but you are looking for something that isn't here.</p>
<?php } ?>

		</div>




		<div id="sidebar">
		<?php
			// The Sidebar widget area
			dynamic_sidebar('Sidebar');
		?>
		</div>






		<div class="clear"></div>
	</div>

<?php get_footer(); ?>