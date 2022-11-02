<?php
/**
 * Template Name: New Media RSS Feed
 */

header( 'Content-Type: ' . feed_content_type( 'rss-http' ) . '; charset=' . get_option( 'blog_charset' ), true );

?>
<?xml version="1.0" encoding="<?php echo esc_attr( get_option( 'blog_charset' ) ); ?>"?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php do_action( 'rss2_ns' ); ?>>
<channel>
	<title><?php bloginfo_rss( 'name' ); ?> - New media feed</title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss( 'url' ) ?></link>
	<description><?php bloginfo_rss( 'description' ) ?></description>
	<lastBuildDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ); ?></lastBuildDate>
	<language><?php echo get_option( 'rss_language' ); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action( 'rss2_head' ); ?>
	<?php

	// Loop through each team
	$media_query = new WP_Query( array(
		'post_type'      => 'attachment',
		'post_status'    => 'any',
		'posts_per_page' => 30,
		'no_found_rows'  => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'orderby' => 'ID',
		'order'   => 'DESC',
	) );
	if ( $media_query->have_posts() ) {
		$count = 0;
		while ( $media_query->have_posts() ) {
			$media_query->the_post();
			$mime_type = get_post_mime_type( get_the_ID() );

			$parent_id = wp_get_post_parent_id( get_the_ID() );
			if (
				'event' === get_post_type( $parent_id )
				&&
				strpos( $mime_type, 'image') !== false
			) {
				$count++;
				if ( 10 > $count ) {
					?>
		<item>
			<title><?php 
			if ( '' !== get_the_title() ) {
				the_title();
			} else {
				echo esc_html__( 'New image', 'src' );
			}
			?></title>
			<link><?php the_permalink(); ?></link>
			<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_the_date( 'U' ), false ); ?></pubDate>
			<description><![CDATA[<?php the_excerpt(); ?>]]></description>
			<content:encoded><![CDATA[<?php the_content(); ?>]]></content:encoded>
			<?php do_action( 'rss2_item' ); ?>
		</item><?php
				}
			}

		}
	}

?>
</channel>
</rss>