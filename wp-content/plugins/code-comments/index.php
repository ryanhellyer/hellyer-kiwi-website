<?php
/*

	Plugin Name: Code Comments
	Plugin URI: http://geek.ryanhellyer.net/products/code-comments/
	Description: A WordPress plugin which automatically encodes everything inside &lt;code&gt; tags.
	Author: Ryan Hellyer / Kaspars Dambis
	Version: 0.4
	Author URI: http://geek.ryanhellyer.net/

	Code entirely based on that of Kaspars Dambis
	http://konstruktors.com/blog/wordpress/1850-automatically-escape-html-entities-of-code-fragments-in-comments/

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.

*/

/**
 * Main filter
 * 
 * @since 0.1
 * @author Kaspars Dambis and Ryan Hellyer <ryanhellyer@gmail.com>
 */
function encode_code_in_comment( $source ) {
	$encoded = preg_replace_callback( '/<code>(.*?)<\/code>/ims',
		create_function(
			'$matches',
			'$matches[1] = preg_replace(
			array("/^[\r|\n]+/i", "/[\r|\n]+$/i"), "",
			$matches[1]);
			return "<code>" . esc_html($matches[1]) . "</code>";'
		),
		$source
	);

	if ( $encoded )
		return $encoded;
	else
		return $source;
}
add_filter( 'pre_comment_content', 'encode_code_in_comment' );
