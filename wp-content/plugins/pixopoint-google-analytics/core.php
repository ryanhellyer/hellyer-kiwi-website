<?php
/*
	Registers settings, adds a new submenu in the admin panel and adds options to array
	@since 0.1

	PixoPoint Google Analytics plugin for WordPress
	Copyright (c) 2009 PixoPoint Web Development

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/


/**
 * Do not continue processing since file was called directly
 * @since 0.1
 */
if ( !defined( 'ABSPATH' ) )
	return;


/**
 * Serve analytics javascript tracker code
 * @since 0.1
 */
function pixopoint_google_analytics_head() {

echo "\n" . PIXOPOINT_GOOGLE_ANALYTICS_AD . '
<script type="text/javascript">' . "
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '" . pixopoint_google_analytics_get_option( 'network' ) . "']);
	_gaq.push(['_setDomainName', 'none']);";
	// _gaq.push(['_setDomainName', '." . pixopoint_google_analytics_get_option( 'domain' ) . "']);
	echo "
	_gaq.push(['_trackPageview']);";

	// Individual site admin specific code
	if ( pixopoint_google_analytics_get_option( 'site' ) ) {
		echo "
	_gaq.push(['b._setAccount', '" . pixopoint_google_analytics_get_option( 'site' ) . "']);
	_gaq.push(['b._trackPageview']);";
	}

	echo "
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>\n";

}
add_action( 'wp_head', 'pixopoint_google_analytics_head' );

