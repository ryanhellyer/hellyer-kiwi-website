<?php
/**
 * The front page template.
 *
 * This is the main template used to display the pages on the home page.
 * Unlike regular themes, this theme displays your static pages directly
 * on your front page and does not feature a regular static page template.
 *
 * @package WordPress
 * @subpackage Arctic_Fox
 */


/**
 * Load header
 */
get_header();


/**
 * Load main content area
 */
$arcticfox_menu = arcticfox_menu();
$count = count( $arcticfox_menu );
foreach( $arcticfox_menu as $key => $item ) {
	$page = get_post( arcticfox_id( $item ) );

	$content = $page->post_content;

	// Get column width
	$_column_width = get_post_meta( $page->ID, '_column_width', true );
	if ( '' == $_column_width ) {
		$_column_width = 'wide';
	}

	// Grab data used on main content
	$_more_character = get_post_meta( $page->ID, '_more_character', true );
	if ( '' == $_more_character ) {
		$_more_character = 'wedge';
	}
	$classes = 'post post-' . $key . ' ' . $_column_width . ' ' . $_more_character;
	if ( $key == ( $count - 1 ) ) {
		$classes .= ' last-post';
	}
	if ( has_post_thumbnail( $page->ID ) ) {
		$post_thumbnail_id = get_post_thumbnail_id( $page->ID );
		$image_url = wp_get_attachment_image_src( $post_thumbnail_id, 'large', true );
		$image_url = $image_url[0];
		$styles = ' style="background-image:url(' . $image_url . ');"';
	} else {
		$styles = ' style="background-color: #231f20;"';
	}

	// Set more content
	if ( strpos( $content, '<!--more-->' ) ) {

		// Set text depending on if last or not
		$extra_text = '';
		$_more_big = get_post_meta( $page->ID, '_more_big', true );
		if ( $_more_big )
			$extra_text .= '<span class="big">' . esc_html( $_more_big ) . '</span>';
		$_more_small = get_post_meta( $page->ID, '_more_small', true );
		if ( $_more_small )
			$extra_text .= '<span class="small">' . esc_html( $_more_small ) . '</span>';

		$content = str_replace( '<!--more-->', "
		<div class='more-gap-button' rel='" . $page->ID . "'><!--more-->" . $extra_text . "
			<a href='#' id='plus-button-" . $page->ID . "' class='plus-button' rel='" . $page->ID. "'>&and;</a>
		</div>
		<div class='read-more' id='read-more-" . $page->ID. "'>\n\n", $content );

		$content = $content . "\n\n</div>";
	}
	$content = apply_filters( 'the_content', $content ); // Apply 

	// Display the article
	echo '
	<article id="post-' . $page->ID. '" class="' . $classes . '">
		<div class="article-inner"' . $styles . '>
			<div class="wrapper">
				<header>';
	
					// Heading can be toggled on and off from edit page
					if ( 'on' == get_post_meta( $page->ID, '_heading', true ) ) {
						echo '<h2 id="page-' . $page->ID . '" class="entry-title"><a href="#page-' . $page->ID . '">' . $page->post_title . '</a></h2>';
					}
	
					// Display subheading if it exists
					$_subheading = get_post_meta( $page->ID, '_subheading', true );
					if ( $_subheading )
						echo "\n			<p class='subheading'>" . $_subheading . '</p>';
		
					// Let them edit the page
					if ( current_user_can( 'edit_pages' ) ) {
						echo '<span class="edit-link"> <a href="' . admin_url( '/post.php?post=' . $page->ID . '&#038;action=edit' ) . '">(Edit)</a></span>';
					}
	
				echo '
				</header>
	
				<div class="page-content">' . 
					$content .
				'</div><!-- .page-content -->
			</div><!-- .wrapper -->
		</div><!-- .article-inner -->
	</article><!-- #post-' . $page->ID . ' -->';
}


/**
 * Load footer
 */
get_footer();

