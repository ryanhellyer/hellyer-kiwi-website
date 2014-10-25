<?php
/*

	Plugin Name: PixoPoint WordPress CSS parser
	Plugin URI: http://pixopoint.com/product/XXXX
	Description: WordPress CSS parser
	Author: PixoPoint Web Development / Ryan Hellyer
	Version: 0.1
	Author URI: http://pixopoint.com/

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
 * [the_id] shortcode
 * @since 0.1
 */
function pixopoint_wp_css_shortcode( $atts ) {
	if ( !isset( $_POST['pixopoint_wp_css'] ) )
		require( 'example.css.php' );
	return '
	<form method="post" action="">
		<textarea style="width:500px;height:350px;" name="pixopoint_wp_css" value="">' . pixopoint_wp_css_validate( $_POST['pixopoint_wp_css'] ) . '</textarea>
		<input type="submit" name="Submit" value="Format CSS" /></p> 
	</form>';
}
add_shortcode( 'pixopoint_wp_css', 'pixopoint_wp_css_shortcode' );

/**
 * CSS validation
 * Much of this code is courtesy of SafeCSS by Automattic and CSSTidy
 * @since 0.1
 */
function pixopoint_wp_css_validate( $css ) {

	// SafeCSS / CSSTidy stuff
	require_once( 'csstidy.php' ); // CSS sanitising gizmo
	safecss_class();
	$csstidy = new csstidy();
	$csstidy->optimise = new safecss( $csstidy );
	$csstidy->set_cfg( 'remove_bslash', false );
	$csstidy->set_cfg( 'compress_colors', false );
	$csstidy->set_cfg( 'compress_font-weight', false );
	$csstidy->set_cfg( 'discard_invalid_properties', true );
	$csstidy->set_cfg( 'merge_selectors', false );

	// Santisation stuff copied from SafeCSS by Automattic
	$css = stripslashes( $css );
	$css = preg_replace( '/\\\\([0-9a-fA-F]{4})/', '\\\\\\\\$1', $prev = $css );
	$css = str_replace( '<=', '&lt;=', $css ); // Some people put weird stuff in their CSS, KSES tends to be greedy
	$css = wp_kses_split( $prev = $css, array(), array() ); // Why KSES instead of strip_tags?  Who knows?
	$css = str_replace( '&gt;', '>', $css ); // kses replaces lone '>' with &gt;
	$css = strip_tags( $css ); // Why both KSES and strip_tags?  Because we just added some '>'.
	$csstidy->parse( $css ); // Parse with CSS Tidy
	$css = $csstidy->print->plain(); // Grab CSS output

	// Beautifying the CSS - This is ugly code, but it works :P
	$css = preg_replace( '/\n/', '', $css ); // Stripping carriage returns from menu
	$css = str_replace( ';', ';
	', $css ); // Add carriage return after ";"
	$css = str_replace( '!important;', ' !important;', $css ); // Adding space back in before !important declaration
	//$css = str_replace( '#suckerfishnav', '.pixopoint', $css ); // Legacy support for CSS generator and older PixoPoint plugins
	$css = str_replace( '
	}', '
}
', $css ); // Remove tab before and carriage return after "}"
	$css = str_replace( '{', '{
	', $css ); // Add carriage return and tab after "{"
	$css = explode( '{', $css ); // The following is hideous code - but it works so will probably remain here until some kind sole offers to rewrite it
	foreach( $css as $piece=>$chunk ) {
		if ( $count == 0 ) {
			$chunk = explode( '}', $chunk );
			$chunk[1] = str_replace( ',', ',
', $chunk[1] );
			$chunk[0] = str_replace( ':', ': ', $chunk[0] ); // Add spaces after colons - needs to be here to avoid messing up pseudo-classes
			//$chunk[0] = str_replace( ',', ',', $chunk[0] ); // Add space after comma - mainly for font-family declarations - doesn't work
			$chunk = implode( '}', $chunk );
			$count = -1;
		}
		$css[$piece] = $chunk;
		$count ++;
	}
	$css = implode( '{', $css );
	$css = str_replace( '}{', '{', $css ); // Nasty hack to fix "{}" code bug
	//echo '<br />THE CSS <br /><textarea style="width:100%;height:700px;">'.$css;echo '</textarea>';die;

	// Store CSS in file
	$uploads_folder = wp_upload_dir();
	if ( '' != $css )
		file_put_contents( $uploads_folder['basedir'] . '/pixopoint_accessible_menu.css', $css ); // Save stylesheet
	elseif ( file_exists( $uploads_folder['basedir'] . '/pixopoint_accessible_menu.css' ) )
		unlink( $uploads_folder['basedir'] . '/pixopoint_accessible_menu.css', $css ); // Delete stylesheet

	// Finally, return CSS
	return $css;
}

