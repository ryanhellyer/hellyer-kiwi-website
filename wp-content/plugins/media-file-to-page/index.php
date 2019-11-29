<?php
/*
Plugin Name: Media File to Attachment page converter
Plugin URI: http://geek.ryanhellyer.net/
Description: Media File to Attachment page converter. DO NOT RUN THIS ON A LIVE WEBSITE!
Author: ryanhellyer
Author URI: http://geek.ryanhellyer.net/
Version: 1.0
License: GPL version 2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/


function media_file_to_page() {

	// Bail out now if GET variable not set
	if ( ! isset( $_GET['media_file_to_page'] ) ) {
		return;
	}

	// This may explode since I'm attempting to load ALL posts in one hit - this should be done iteratively to avoid shit exploding on us
	$posts = get_posts(
		array(
			'posts_per_page' => -1,
		)
	);
	foreach ( $posts as $post ) {
		$content = $post->post_content;
		$content = explode( '<a href="', $content );

		$attachments = get_posts( 
			array(
				'post_type'      => 'attachment',
				'posts_per_page' => -1,
				'post_parent'    => $post->ID,
			)
		);
		if ( $attachments ) {

			// Sort the content into chunks
			foreach( $content as $key => &$chunk ) {
				$thumb = false;

				// Get the URL you want to change
				$chunks = explode( '"', $chunk );
				$url = $chunks[0];

				// Cater for thumbnails
				$thumb_maybe = $url;
				$thumb_end = explode( '-', $thumb_maybe );
				$count = count( $thumb_end );

				if ( isset( $thumb_end[$count-1] ) ) {
					$thumb_bit = explode( '.', $thumb_end[$count-1] ); // eg: 680x510
					$thumb_bit = $thumb_bit[0];
					$thumb_bits = explode( 'x', $thumb_bit );

					// If the bits match the pattern we expect, then use them
					if ( ( isset( $thumb_bits[0] ) && is_numeric( $thumb_bits[0] ) ) && ( isset( $thumb_bits[1] ) && is_numeric( $thumb_bits[1] ) ) ) {
						$thumb_size = $thumb_bits[0] . 'x' . $thumb_bits[1];
						$thumb = true;
					}
				}

				// Loop through the attachments
				foreach ( $attachments as $attachment ) {

					// Convert the URL to use the attachment page
					if ( true == $thumb ) {
						$url = str_replace(
							$url,
							get_attachment_link( $attachment->ID ),
							$url
						);
					}

					$url = str_replace(
						wp_get_attachment_url( $attachment->ID ),
						get_attachment_link( $attachment->ID ),
						$url
					);

				}

				$chunks[0] = $url;
				$chunk = implode( '"', $chunks );

			}
		}
		$content = implode( '<a href="', $content );

		// Update the post
		$edited_post = array(
			'ID'           => $post->ID,
			'post_content' => $content,
		);
		wp_update_post( $edited_post );

	}

	die( 'DONE' );
}
add_action( 'template_redirect', 'media_file_to_page' );
add_action( 'admin_init', 'media_file_to_page' );
