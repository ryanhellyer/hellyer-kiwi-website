<?php

// Adds support for post thumbnails
add_theme_support( 'post-thumbnails' );
add_theme_support( 'page-thumbnails' );
add_image_size( 'home-post-thumbnail', 100, 100, true );
add_theme_support( 'menus' );
add_post_type_support( 'page', 'excerpt' );


register_nav_menus( array(
	'primary' => 'Primary'
) );


// Resize expert
function new_excerpt_length($length) {
	return 45;
}
add_filter('excerpt_length', 'new_excerpt_length');


// Widgetizes right hand sidebar
register_sidebar(
	array(
		'name' => 'Sidebar',
		'before_widget' => '<div class="widget-wrapper"><div class="widget">',
		'after_widget'  => '</div></div>',
		'before_title'  => '<h3>',
		'after_title'   => '</h3>',
	)
);

// Loads the comments function
function pixopoint_theme_comment( $comment, $args, $depth ) {
$GLOBALS['comment'] = $comment; ?>
<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div class="comment_outer_wrapper" id="comment-<?php comment_ID(); ?>">
		<div class="comment_wrapper" id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
				<?php echo get_avatar( $comment, $size='48' ); ?>
			</div>
			<?php if ( $comment->comment_approved == '0' ) : ?>
			<em><?php _e( 'Your comment is awaiting moderation.' ) ?></em>
			<br />
			<?php endif; ?>

			<div class="comment-meta commentmetadata">
				<?php printf(__('<p class="comment_header"><cite>%s</cite> says:</p>'), get_comment_author_link()) ?>
				<div class="comments">
					<?php comment_text() ?>
				</div>
				<p class="comment_footer">
					<?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?>
					<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">#</a> //
					<?php edit_comment_link(__('Edit'), ' ', ' // ' ) ?>
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ) ?>
				</p>
			</div>
		</div>
	</div>
<?php
}












function numeric_pagination_shortcode( $pages = '', $range = 2 ) {
	echo '<ul id="numeric_pagination">';
	// Beginning of numeric pagination - code developed from the excellent Genesis theme by StudioPress (http://studiopress.com/)

	if( !is_singular() ) : // do nothing

	global $wp_query;

	// Stop execution if there\'s only 1 page
	if( $wp_query->max_num_pages <= 1 ) return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged') ) : 1;
	$max = intval( $wp_query->max_num_pages );

	//	add current page to the array
	if ( $paged >= 1 )
		$links[] = $paged;
	
	//	add the pages around the current page to the array
	if ( $paged >= 3 ) {
		$links[] = $paged - 1; $links[] = $paged - 2;
	}
	if ( ($paged + 2) <= $max ) { 
		$links[] = $paged + 2; $links[] = $paged + 1;
	}

	//	Previous Post Link
	if ( get_previous_posts_link() )
		printf( '<li>%s</li>' . "\n", get_previous_posts_link( __( '&laquo; Previous', 'wppb_lang') ) );

	//	Link to first Page, plus ellipeses, if necessary
	if ( !in_array( 1, $links ) ) {
		if ( $paged == 1 )
			$current = ' class="active"';
		else
			$current = null;
		printf(
			'<li %s><a href="%s">%s</a></li>' . "\n",
			$current,
			get_pagenum_link(1),
			'1'
		);

		if ( !in_array( 2, $links ) )
			echo '<li>&hellip;</li>';
	}

	//	Link to Current page, plus 2 pages in either direction (if necessary).
	sort( $links );
	foreach( (array)$links as $link ) {
		$current = ( $paged == $link ) ? 'class="active"' : '';
		printf(
			'<li %s><a href="%s">%s</a></li>' . "\n",
			$current,
			get_pagenum_link( $link ),
			$link
		);
	}

	//	Link to last Page, plus ellipses, if necessary
	if ( !in_array( $max, $links ) ) {
		if ( !in_array( $max - 1, $links ) )
			echo '<li>&hellip;</li>' . "\n";
		
		$current = ( $paged == $max ) ? 'class="active"' : '';
		printf(
			'<li %s><a href="%s">%s</a></li>' . "\n",
			$current,
			get_pagenum_link( $max ),
			$max
		);
	}

	//	Next Post Link
	if ( get_next_posts_link() )
		printf(
			'<li>%s</li>' . "\n",
			get_next_posts_link( __( 'Next &raquo;', 'wppb_lang' ) ) );
	endif;
	echo '</ul>';

}


/**
 * Proper way to enqueue scripts and styles
 */
function pixopoint_style() {
	wp_enqueue_style( 'pixopoint', get_template_directory_uri() . '/style24.css' );
}

add_action( 'wp_enqueue_scripts', 'pixopoint_style' );

