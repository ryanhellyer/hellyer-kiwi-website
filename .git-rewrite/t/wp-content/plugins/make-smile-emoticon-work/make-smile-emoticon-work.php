<?php
/*
Plugin Name: Make Smile Emoticon Work
Plugin URI: https://geek.hellyer.kiwi/plugins/
Description: Makes the smile emoticon work now that it's been converted to an emoji. For some reason :) doesn't work, but :D does. Only tested in Chrome; maybe other browsers have better or worse emoji support.
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
 * Filter the content to make smile emoticon work correctly.
 *
 * @param   string   $content   The post content
 * @return  Tthe modified content
 */
function make_smile_emoticon_work( $content ) {
	$content = str_replace( ':)', ':D', $content );

	return $content;
}
add_action( 'the_content', 'make_smile_emoticon_work', 1 );
