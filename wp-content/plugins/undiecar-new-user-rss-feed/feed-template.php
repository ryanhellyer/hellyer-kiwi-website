<?php
/**
 * Template Name: New User RSS Feed
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
	<title><?php bloginfo_rss( 'name' ); ?> - New user feed</title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss( 'url' ) ?></link>
	<description><?php bloginfo_rss( 'description' ) ?></description>
	<lastBuildDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', get_lastpostmodified( 'GMT' ), false ); ?></lastBuildDate>
	<language><?php echo get_option( 'rss_language' ); ?></language>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>
	<?php do_action( 'rss2_head' ); ?>
	<?php

	$number = 25;
	if ( isset( $_GET['number'] ) ) {
		$number = absint( $_GET['number'] );
	}

	$users = get_users(
		array(
			'orderby' => 'ID',
			'order'   => 'DESC',
			'number'  => $number,
		)
	);

	foreach ( $users as $count => $user ) {
		$member_url = home_url() . '/member/' . $user->data->user_login . '/';

		$content = '
			New user signup by ' . esc_html( $user->data->display_name ) . '.
			Check out their member profile at <a href="' . esc_url( $member_url ) . '">' . esc_url( $member_url ) . '</a>.';

		?>
		<item>
			<title>New user - <?php echo esc_html( $user->data->display_name ); ?></title>
			<link><?php echo esc_url( $member_url ); ?></link>
			<pubDate><?php echo mysql2date( 'D, d M Y H:i:s +0000', esc_html( $user->data->user_registered ), false ); ?></pubDate>
			<description><![CDATA[<?php echo $content; ?>]]></description>
			<content:encoded><![CDATA[<?php echo $content; ?>]]></content:encoded>
			<?php do_action( 'rss2_item' ); ?>
		</item><?php
	}

?>
</channel>
</rss>