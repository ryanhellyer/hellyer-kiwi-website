<?php

/**
 * Automatically add Facebook Open Graph meta tags.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Free_Advice_Berlin_Facebook_Opengraph {

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		add_filter( 'language_attributes', array( $this, 'add_opengraph_doctype' ) );
		add_action( 'wp_head', array( $this, 'opengraph_tags' ), 5 );
	}

	/**
	 * Adding the Open Graph in the Language Attributes.
	 *
	 * @param  string  $output
	 * @return string
	 */
	function add_opengraph_doctype( $output ) {
		return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
	}

	/**
	 * Output the Facebook Open Graph tags.
	 */
	public function opengraph_tags() {

		if ( is_singular() ) {

			$excerpt = get_post_field( 'post_excerpt', get_the_ID() );
			if ( '' == $excerpt ) {
				$content = get_post_field( 'post_content', get_the_ID() );
				$excerpt = wp_trim_words( $content, 20 );
			}

			echo '
<meta property="og:locale" content="' . esc_attr( get_locale() ) . '" />
<meta property="og:type" content="article" />
<meta property="og:title" content="' . esc_attr( get_the_title() . ' - ' . get_bloginfo( 'name' ) ) . '"/>
<meta property="og:description" content="' . esc_attr( strip_tags( $excerpt ) ) . '" />
<meta property="og:url" content="' . esc_attr( get_permalink() ) . '"/>
<meta property="og:site_name" content="' . esc_attr( get_bloginfo( 'name' ) ) . '" />
';

			if ( has_post_thumbnail( get_the_ID() )) {
				$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'medium' );
				echo '
<meta property="og:image" content="' . esc_attr( $thumbnail_src[0] ) . '"/>
';
			}

echo "\n";

		}

	}
}
