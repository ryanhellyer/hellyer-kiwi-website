<?php
/*
Plugin Name: Client-proof Visual Editor
Plugin URI: https://wordpress.org/plugins/client-proof-visual-editor/
Version: 1.6
Author: Hugo Baeta
Author URI: http://hugobaeta.com
Description: Simple, option-less, plugin to make TinyMCE - the WordPress Visual Editor - easier for clients and n00bs.
*/

/**
 * Filter TinyMCE settings
 *
 * @param  array $settings Array of TinyMCE settings.
 *
 * @return array           New settings array.
 */
function clientproof_visual_editor( $settings ) {

	// What goes into the 'formatselect' list.
	$settings['block_formats'] = 'Header 2=h2;Header 3=h3;Header 4=h4;Paragraph=p;Code=code';

	// What goes into the toolbars. Add 'wp_adv' to get the Toolbar toggle button back.
	$settings['toolbar1'] = 'bold,italic,strikethrough,formatselect,bullist,numlist,blockquote,link,unlink,hr,wp_more,fullscreen';
	$settings['toolbar2'] = '';
	$settings['toolbar3'] = '';
	$settings['toolbar4'] = '';

	// Clear most formatting when pasting text directly in the editor.
	$settings['paste_as_text'] = 'true';

	return $settings;
}

add_filter( 'tiny_mce_before_init', 'clientproof_visual_editor' );
