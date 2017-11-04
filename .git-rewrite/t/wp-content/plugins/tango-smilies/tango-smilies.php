<?php
/*
Plugin Name: Tango/GNOME Smilies
Plugin URI: http://wordpress.org/extend/plugins/tango-smilies/
Description: Replace the blocky default (GIF) smilies with beautiful Tango/GNOME (PNG) smilies.
Version: 3.3.0.1
Author: Jeff Waugh
Author URI: http://bethesignal.org/
*/

/*
Copyright (C) Jeff Waugh <http://bethesignal.org/>

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/


function tango_smilies_fixurl($text) {
	$siteurl = get_option('siteurl');
	$plugurl = plugin_dir_url(__FILE__);
	$search = sprintf( '#<img src="%s/wp-includes/images/smilies/#', $siteurl );
	$replace = sprintf( '<img src="%stango/', $plugurl );
	return preg_replace( $search, $replace, $text );
}

function tango_smilies_init() {
	global $wpsmiliestrans, $wp_version;

	if ( !isset($wpsmiliestrans) && get_option('use_smilies') ) {
		$wpsmiliestrans = array(
		':mrgreen:' => 'face-monkey.png',
		':neutral:' => 'face-plain.png',
		':twisted:' => 'face-devilish.png',
		  ':arrow:' => 'stock_right.png',
		  ':shock:' => 'face-surprise.png',
		  ':smile:' => 'face-smile.png',
		    ':???:' => 'face-worried.png',
		   ':cool:' => 'face-cool.png',
		   ':evil:' => 'face-devilish.png',
		   ':grin:' => 'face-smile-big.png',
		   ':idea:' => 'idea.png',
		   ':oops:' => 'face-embarrassed.png',
		   ':razz:' => 'face-raspberry.png',
		   # FIXME: is there a better icon for rolling eyes?
		   ':roll:' => 'face-raspberry.png',
		   ':wink:' => 'face-wink.png',
		    ':cry:' => 'face-crying.png',
		    ':eek:' => 'face-surprise.png',
		    ':lol:' => 'face-laugh.png',
		    ':mad:' => 'face-angry.png',
		    ':sad:' => 'face-sad.png',
		      '8-)' => 'face-cool.png',
		      '8-O' => 'face-surprise.png',
		      ':-(' => 'face-sad.png',
		      ':-)' => 'face-smile.png',
		      ':-?' => 'face-worried.png',
		      ':-D' => 'face-smile-big.png',
		      ':-P' => 'face-raspberry.png',
		      ':-o' => 'face-surprise.png',
		      ':-x' => 'face-angry.png',
		      ':-|' => 'face-plain.png',
		      ';-)' => 'face-wink.png',
			 ":-\\" => 'face-uncertain.png',
		       '8)' => 'face-cool.png',
		       '8O' => 'face-surprise.png',
		       ':(' => 'face-sad.png',
		       ':)' => 'face-smile.png',
		       ':?' => 'face-worried.png',
		       ':D' => 'face-smile-big.png',
		       ':P' => 'face-raspberry.png',
		       ':o' => 'face-surprise.png',
		       ':x' => 'face-angry.png',
		       ':|' => 'face-plain.png',
		       ';)' => 'face-wink.png',
			  ":\\" => 'face-uncertain.png',
		      ':!:' => 'exclaim.png',
		      ':?:' => 'question.png',
		// elite tango-smilies extensions
	     ':monkey:' => 'face-monkey.png',
		  ':devil:' => 'face-devilish.png',
		  ':angel:' => 'face-angel.png',
		  ':smirk:' => 'face-smirk.png',
		   ':kiss:' => 'face-kiss.png',
			 'O:-)' => 'face-angel.png',
			  'O:)' => 'face-angel.png',
			 'o:-)' => 'face-angel.png',
			  'o:)' => 'face-angel.png',
		      ';-,' => 'face-smirk.png',
		       ';,' => 'face-smirk.png',
		);

		// can only include smilies with "/" after 2.8.1-beta1 (see #9955)
		if ( version_compare($wp_version, '2.8.1-beta1', '>') ) {
			$wpsmiliestrans += array(
			  ':-/' => 'face-uncertain.png',
			   ':/' => 'face-uncertain.png',
			);
		}

		// Content filters to fix image URLs
		add_filter('the_content', 'tango_smilies_fixurl', 10);
		add_filter('the_excerpt', 'tango_smilies_fixurl', 10);
		add_filter('comment_text', 'tango_smilies_fixurl', 30);
	}
}
add_action('init', 'tango_smilies_init', 1);
