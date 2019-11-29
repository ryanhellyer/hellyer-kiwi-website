<?php
/*
Plugin Name: Yearly Archive and Stats Shortcode
Description: Provides <code>[yearly_archive]</code> and [yearly_archive_stats] shortcodes
Version: 1.2.4
Author: Kaspars Dambis
*/


add_shortcode( 'yearly_archive_stats', 'yas_stats' );

function yas_stats() {

	$maybe_cache = get_transient( 'yearly_archive_stats_render' );

	if ( ! empty( $maybe_cache ) )
		return $maybe_cache;

	// Count totals
	$post_count = wp_count_posts();
	$comment_count = wp_count_comments();
	$first_post = get_posts( array( 'posts_per_page' => 1, 'order' => 'ASC' ) );
	$first_post_date = strtotime( $first_post[0]->post_date );
	$time_between_posts = ( time() - strtotime( $first_post[0]->post_date ) ) / $post_count->publish;

	// Count words
	$all_posts = get_posts( array( 'posts_per_page' => -1 ) );
	$word_count = array();
	$week_stats = array();
	$img_stats = array();
	foreach ( $all_posts as $post ) {
		$week_stats[ date( 'l', strtotime( $post->post_date ) ) ] += 1;
		$word_count[] = str_word_count( strip_tags( $post->post_content ) );
		$img_stats[] = substr_count( $post->post_content, '<img ' ); 
	}
	arsort( $week_stats );
	$week_stats = array_keys( $week_stats );
	$word_count_total = array_sum( $word_count );

	$stats_render = sprintf(
		'In %s since %s I have written %d blog posts mostly on %s and %s (one post per %s on average) which have received %d comments.
		Posts are composed of %d words and %d images in total with %s words and %s per post on average which would be a book of %d pages.',
		human_time_diff( $first_post_date ),
		date( 'F n, Y', $first_post_date ),
		$post_count->publish,
		$week_stats[0] . 's',
		$week_stats[1] . 's',
		human_time_diff( time() - $time_between_posts ),
		$comment_count->approved,
		$word_count_total,
		array_sum( $img_stats ),
		intval( $word_count_total / $post_count->publish ),
		sprintf( _n( '%d image', '%d images', ceil( array_sum( $img_stats ) / $post_count->publish ) ), ceil( array_sum( $img_stats ) / $post_count->publish ) ),
		intval( $word_count_total / 250 )
	);

	set_transient( 'yearly_archive_stats_render', $stats_render, 24*60*60 );

	return $stats_render;

}


add_shortcode( 'yearly_archive', 'yas_shortcode' );

function yas_shortcode( $args ) {

	$maybe_cache = get_transient( 'yearly_archive' );

	if ( ! empty( $maybe_cache ) )
		return $maybe_cache;

	$posts = get_posts( array( 'posts_per_page' => -1 ) );

	if ( empty( $posts ) )
		return;

	$archive = array();
	$render = array();

	foreach ( $posts as $post )
		$archive[ date( 'Y', strtotime( $post->post_date ) ) ][] = sprintf(
			'<li>
				<span class="date" title="%s">%s</span>
				<a href="%s" class="link">%s</a>
			</li>',
			esc_attr( date( 'r', strtotime( $post->post_date ) ) ),
			esc_html( date( 'M j Y', strtotime( $post->post_date ) ) ),
			get_permalink( $post ),
			apply_filters( 'the_title', $post->post_title )
		);

	foreach ( $archive as $year => $year_posts )
		$render[] = sprintf(
			'<h2>%s</h2>
			<ul>%s</ul>',
			$year,
			implode( "\n", $year_posts )
		);

	$html = sprintf(
		'<div class="yearly-archive">%s</div>',
		implode( "\n", $render )
	);

	set_transient( 'yearly_archive', $html, 60 * 60 * 24 );

	return $html;
}
