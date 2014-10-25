<?php
/**
 * The front page template.
 *
 * This is the main template used to display the pages on the home page.
 * Unlike regular themes, this theme displays your static pages directly
 * on your front page and does not feature a regular static page template.
 *
 * @package WordPress
 * @subpackage Lasse_Super
 */


/**
 * Load header
 */
get_header();


/**
 * Load main content area
 */
foreach( lassesuper_menu() as $item ) {
	$page = get_post( lassesuper_id( $item ) );

	/**
	 * Determining page template used
	 * If no template present, then must be non-page so bail out
	 */
	$custom_fields = get_post_custom_values( '_wp_page_template', lassesuper_id( $item ) );

	$page_template = str_replace( '.php', '', $custom_fields[0] );
	if ( 'page' != $page->post_type || lassesuper_id( $item ) == get_option( 'page_for_posts' ) )
		continue;

	$content = $page->post_content;
	if ( strpos( $content, '<!--more-->' ) ) {
		$content = str_replace( '<!--more-->', "
<div class='more-gap-button' rel='" . $page->ID . "'><!--more-->
	<a href='#' id='plus-button-" . $page->ID. "' class='plus-button' rel='" . $page->ID. "'>+</a>
	<a href='#' id='minus-button-" . $page->ID. "' class='minus-button' rel='" . $page->ID. "'>-</a>
</div>
<div class='more-gap-block' id='more-gap-" . $page->ID . "'></div>
<div class='read-more' id='read-more-" . $page->ID. "'>\n\n", $content );
		$content = $content . "\n\n</div>";
	}

	/**
	 * Add more gap button for hover click template.
	 */
	if ( 'page-templates/page-hover-click' == $page_template ) {
		if ( get_post_meta( $page->ID, 'big' ) )
			$more = "<span class='big'>" . get_post_meta( $page->ID, 'big', true ) . "</span>";
		else
			$more = "<span class='big'>" . __( 'Read more', 'lassesuper' ) . "</span>";
		if ( get_post_meta( $page->ID, 'small' ) )
			$more .= "<span class='small'>" . get_post_meta( $page->ID, 'small', true ) . "</span>";
		else
			$more .= "<span class='small'>" . __( 'Press arrow', 'lassesuper' ) . "</span>";

		$content = str_replace( '<!--more-->', "\n	" . $more, $content );
	}

	/**
	 * Applying filter to the_content.
	 * Adds <p> tags to markdown code in posts
	 */
	$content = apply_filters( 'the_content', $content );

	/**
	 * Create edit page link
	 */
	$edit_page ='<span class="edit-link"> <a href="' . admin_url( '/post.php?post=' . $page->ID . '&#038;action=edit' ) . '">(Edit)</a></span>';

	/**
	 * Grab subheading custom field if present
	 */
	$subheading = get_post_meta( $page->ID, 'subheading' );
	if ( !isset( $subheading[0] ) )
		$subheading = '';

	if ( isset( $subheading[0] ) )
		$subheading = $subheading[0];

	// Let them edit the page
	if ( current_user_can( 'edit_pages' ) )
		$subheading .= $edit_page;

	/**
	 * Display the page content for "big image" template
	 */
	$styles = '';
	$background_styles = '';
	if ( 'page-templates/page-big-image' == $page_template ) :
	$post_thumbnail_id = get_post_thumbnail_id( $page->ID );
	$image_url = wp_get_attachment_image_src( $post_thumbnail_id, 'large', true );
	$image_url = $image_url[0];
	$styles = ' style="background-image:url(' . $image_url . ');"';

	// Background image
	$post_thumbnail_id = get_post_meta( $page->ID, 'page_secondary-image_thumbnail_id', true );
	if ( $post_thumbnail_id ) {
		$post_thumbnail_url = wp_get_attachment_image_src( $post_thumbnail_id, 'page_secondary-image_thumbnail_id', true );
		$post_thumbnail_url = $post_thumbnail_url[0];
		$background_styles = ' style="background-image:url(' . $post_thumbnail_url . ');background-repeat:repeat"';
	}

	endif;

	/**
	 * Display the article
	 */
	echo '
<article id="post-' . $page->ID. '" class="post ' . $page_template . '"' . $background_styles . '>
	<div class="wrapper"' . $styles . '>';
		$styles = ''; // Resetting styles

		/**
		 * Display the page content for "big image" template
		 */
		if ( 'page-templates/page-big-image' == $page_template ) :
		echo '
		<blockquote ' .
		lassesuper_dimensions(
			$page->ID, array(
				'width' => 'width',
				'top'   => 'padding-top',
				'left'  => 'padding-left',
			)
		)
		. 'id="page-' . $page->ID . '">&ldquo;' . $page->post_content . '&rdquo;' . $edit_page . '</blockquote>';

		/**
		 * Display the page content for regular templates
		 */
		else :
		echo '
		<header>
			<h2 id="page-' . $page->ID . '" class="entry-title"><a href="#page-' . $page->ID . '">' . $page->post_title . '</a></h2>';


		// Display subheading if it exists
		if ( $subheading )
			echo "\n		<p class='subheading'>" . $subheading . '</p>';

		echo '
		</header>

		<div class="' . $page_template . ' page-content" ' .
		lassesuper_dimensions(
			$page->ID, array(
				'width' => 'width',
				'top'   => 'margin-top',
				'left'  => 'margin-left',
			)
		) . '>' .
		$content .
		'</div>
	</div>';

	endif;


	echo '
</article><!-- #post-' . $page->ID . ' -->';
}


/**
 * Load footer
 */
get_footer();

