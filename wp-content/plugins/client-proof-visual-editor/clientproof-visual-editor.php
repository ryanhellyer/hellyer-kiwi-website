<?php
/*
Plugin Name: Client-proof Visual Editor
Plugin URI: https://wordpress.org/plugins/client-proof-visual-editor/
Version: 1.5
Author: Hugo Baeta
Author URI: http://hugobaeta.com
Description: Simple, option-less, plugin to make TinyMCE - the WordPress Visual Editor - easier for clients and n00bs.
*/

function clientproof_visual_editor( $mceInit ) {
	// What goes into the 'formatselect' list
	$mceInit['block_formats'] = 'Header 2=h2;Header 3=h3;Header 4=h4;Paragraph=p;Code=code';
	
	// What goes into the toolbars. Add 'wp_adv' to get the Toolbar toggle button back
	$mceInit['toolbar1'] = 'bold,italic,strikethrough,formatselect,bullist,numlist,blockquote,link,unlink,hr,wp_more,fullscreen';
	$mceInit['toolbar2'] = '';
	$mceInit['toolbar3'] = '';
	$mceInit['toolbar4'] = '';

	// Clear most formatting when pasting text directly in the editor
	$mceInit['paste_as_text'] = 'true';

	return $mceInit;
}

add_filter('tiny_mce_before_init', 'clientproof_visual_editor');

?>