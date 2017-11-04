<?php
/**
 * The Legal Notice template file.
 * This file should not be translated.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */


/**
 * Removing home page class on body tag.
 *
 * @param  array  $classes  The body classes
 * @return array  The modified body classes
 */
add_filter( 'body_class', function( $classes ) {
	foreach( $classes as $key => $class ) {
		if ( 'home' == $class ) {
			unset( $classes[$key] );
		}
	}

	return $classes;
} );


get_header();

echo '<article>';
$markdown = new Free_Advice_Berlin_Markdown();
$content = file_get_contents( dirname( __FILE__ ) . '/legal-notice.txt' );
$content = do_shortcode( $content );
echo $markdown->text( $content );
echo '</article>';

get_footer();
