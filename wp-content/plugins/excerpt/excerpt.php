<?php
/*
Plugin Name: Excerpt
Plugin URI: https://geek.hellyer.kiwi/plugins/
Description: Converts areas which display content, to only display excerpts. Includes home, search and archive pages.
Version: 1.0
Author: Ryan Hellyer
Author URI: https://geek.hellyer.kiwi/
License: GPL2

------------------------------------------------------------------------
Copyright Ryan Hellyer

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA

*/

/**
 * Converts archive, search and home pages to use excerpts instead of the content.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Excerpt_Plugin {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_filter( 'the_content', array( $this, 'convert_to_excerpt' ) );
	}


	/**
	 * Convert the home page to show an excerpt instead of the content.
	 *
	 * Method is developed from this post ... http://www.sean-barton.co.uk/2011/11/getting-the-wordpress-excerpt-outside-of-the-loop/
	 *
	 * @param   string   $content  The post content
	 * @return  string             The post excerpt
	 */
	public function convert_to_excerpt( $content ) {

		// If not home page, then serve content as per normal
		if ( ! is_home() && ! is_archive() && ! is_search() ) {
			return $content;
		}

		global $post;
		if ( ! $excerpt = trim( $post->post_excerpt ) ) {
			$excerpt = $post->post_content;
			$excerpt = strip_shortcodes( $excerpt );
			$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );
			$excerpt = strip_tags( $excerpt );
			$excerpt_length = apply_filters( 'excerpt_length', 55 );
			$excerpt_more = apply_filters( 'excerpt_more', ' ' . '[...]' );

			$words = preg_split( "/[\n\r\t ]+/", $excerpt, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY );
			if ( count( $words ) > $excerpt_length ) {
				array_pop( $words );
				$excerpt = implode( ' ', $words );
				$excerpt = $excerpt . $excerpt_more;
			} else {
				$excerpt = implode( ' ', $words );
			}
			$excerpt = wpautop( $excerpt );
		}

		return $excerpt;
	}

}
new Excerpt_Plugin;
