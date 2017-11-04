<?php
/*

Plugin Name: Metronet Embed Google Plus
Plugin URI: http://www.metronet.no/
Description: Easily embed Google plus posts into your pages
Author: Metronet / Ryan Hellyer
Version: 1.0.1
Author URI: http://www.metronet.no/

Copyright (c) 2013 Metronet


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/



/**
 * Embed Google Plus posts
 * 
 * @copyright Copyright (c), Metronet
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryan@metronet.no>
 * @since 1.0
 */
class Metronet_Embed_Google_Plus {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_shortcode( 'googleplus', array( $this, 'shortcode' ) );

		wp_embed_register_handler(
			'googleplus',
			'/https:\/\/plus\.google\.com\/[0-9]+\/posts\//i',
			array( $this, 'oembed' )
		);
	}

	/**
	 * Add oEmbed support
	 */
	public function oembed( $matches, $attr, $url, $rawattr ) {
		$string = $this->embed_code( $url );
		return apply_filters( 'embed_google_plus', $string, $matches, $attr, $url, $rawattr );
	}

	/**
	 * Javascript
	 */
	public function javascript() {
		?>
<script>
(function() {
	var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	po.src = 'https://apis.google.com/js/plusone.js?onload=onLoadCallback';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
})();
</script><?php
	}

	/**
	 * Shortcode
	 *
	 * @param string $atts Array containing the embed URL
	 * @return string The embedcode HTML
	 */
	public function shortcode( $atts ){
		extract( $atts );
		if ( ! isset( $url ) )
			$url = $atts[0];

		$string = $this->embed_code( $url );
		return $string;
	}

	/**
	 * Facebook embed post shortcode
	 *
	 * @param string $url The URL to be embedded
	 * @return string The Facebook embedcode HTML
	 */
	public function embed_code( $url ){

		// Add Javascript here, because if HTML is 
		add_action( 'wp_footer', array( $this, 'javascript'  ) );

		$string = '<div class="g-post" data-href="' . $url . '"></div>';
	return $string;
	}

}
new Metronet_Embed_Google_Plus;
