<?php
/*
Plugin Name: New User RSS Feed
Plugin URI: https://geek.hellyer.kiwi/plugins/
Description: New User RSS Feed
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
 * New User RSS Feed class.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class New_User_RSS_Feed {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'add_feed' ) );
		add_filter( 'feed_content_type', array( $this, 'rss_content_type' ), 10, 2 );
	}

	/**
	 * Add the feed.
	 */
	public function add_feed(){
		add_feed( 'new_users', array( $this, 'output_template' ) );
	}

	/**
	 * Output the feed template.
	 */
	public function output_template(){
		require( 'feed-template.php' );
		die;
	}

	/*
	 * Filter the type, this hook wil set the correct HTTP header for Content-type.
	 *
	 * @param  string  $content_type  The type of feed content
	 * @param  string  $type          The 
	 */
	public function rss_content_type( $content_type, $feed_name ) {

		// Return rss2 feed content type of on new_users feed
		if ( 'new_users' === $feed_name ) {
			return feed_content_type( 'rss2' );
		}

		return $content_type;
	}

}
new New_User_RSS_Feed;
