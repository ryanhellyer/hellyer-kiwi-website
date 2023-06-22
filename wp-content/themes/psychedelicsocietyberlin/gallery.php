<?php

/**
 * Converts the somewhat convoluted web of HTML that WordPress adds to their 
 * default WordPress galleries, into a super simple unordered list.
 * Includes a class for CSS targeting for when the number of columns changes.
 * 
 * This will look odd by default, don't be silly and leave it without some custom CSS ;)
 */
add_shortcode( 'gallery', 'modify_the_wordpress_gallery' );
function modify_the_wordpress_gallery( $attr ) {

	// If no IDs set, then bail out.
	$ids = $attr['ids'];
	if ( empty( $ids ) ) {
		return '';
	} else {
		$ids = explode( ',', $ids );
	}

	// Randomise the order if orderby is set.
	if ( isset( $attr['orderby'] ) ) {
		shuffle( $ids );
	}

	// Get the number of columns.
	$columns = 3;
	if ( isset( $attr['columns'] ) ) {
		$columns = $attr['columns'];
	}

	// Get the type of link.
	$link = 'attachment';
	if ( isset( $attr['link'] ) ) {
		$link = $attr['link'];
	}

	// Get the image thumbnail size.
	$size = 'thumbnail';
	if ( isset( $attr['size'] ) ) {
		$size = $attr['size'];
	}

	// Implement an unordered list.
	$html = '<ul class="gallery gallery-columns-' . absint( $columns ) . '">';

	// Loop through each attachment ID.
	foreach ( $ids as $key => $attachment_id ) {
		$html .= '<li>';

		// Do something if there's meant to be a link.
		if ( 'none' !== $link ) {

			if ( 'file' === $link ) {
				$url = wp_get_attachment_image_src( $attachment_id, 'full' )[0];
			} else {
				$url = wp_get_attachment_url( $attachment_id );
			}

			$html .= '<a href="' . esc_url( $url ) . '">';
		}

		// Output the image.
		$html .= wp_get_attachment_image( $attachment_id, $size );

		// Close link if required.
		if ( 'none' !== $link ) {
			$html .= '</a>';
		}

		// Implement a caption if relevant.
		$caption = wp_get_attachment_caption( $attachment_id );
		if ( '' !== $caption ) {
			$html .= '<p>' . esc_html( $caption ) . '</p>';
		}

		$html .= '</li>';
	}

	// Close the unordered list.
	$html .= '</ul>';

	return $html;
}
