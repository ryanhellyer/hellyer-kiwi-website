<?php
/**
 * Template for displaying Comments.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */

function free_advice_berlin_comment_form_defaults($defaults){
	$defaults['comment_notes_before'] = '';
	return $defaults;
}
add_filter( 'comment_form_defaults', 'free_advice_berlin_comment_form_defaults' );

/**
 * Show pre comments navigation.
 */
function free_advice_berlin_comments_navigation( $id = '' ) {
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) {
		?>
	<nav role="navigation" id="<?php echo esc_attr( $id ); ?>" class="site-navigation comment-navigation">
		<h1 class="assistive-text"><?php esc_html_e( 'Comment navigation', 'free-advice-berlin' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( esc_html__( '&larr; Older Comments', 'free-advice-berlin' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', 'free-advice-berlin' ) ); ?></div>
	</nav><!-- #comment-nav-<?php echo absint( $id ); ?> .site-navigation .comment-navigation --><?php
	}
}


/**
 * Bail out now if the user needs to enter a password.
 */
if ( post_password_required() ) {
	return;
}


/**
 * Showing the discussion button.
 */

do_action( 'fab_before_comments' );

?>

<div id="discussion"><?php

/**
 * Display the main comment form.
 */

comment_form(
	array(
		'title_reply'         => '',
		'comment_notes_after' => '',
		'comment_field'       => '
			<p class="comment-form-comment">
				<label for="comment">' . __( 'Have corrections or improvements? Leave a comment :)' ) . '</label>
				<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
			</p>',
	)
);

?>

<div id="comments">

<?php

/**
 * Display the comments if any exist.
 */
if ( have_comments() ) {

	?>
	<h2><?php _e( 'Discussion', 'free-advice-berlin' ); ?></h2>

	<ol class="commentlist"><?php wp_list_comments(); ?></ol><!-- .commentlist --><?php

}

/**
 * If comments are closed, then leave a notice.
 */
if (
	! comments_open() &&
	'0' != get_comments_number() &&
	post_type_supports( get_post_type(), 'comments' )
) {
	echo '<p class="nocomments">' . esc_html__( 'Comments are closed.', 'free-advice-berlin' ) . '</p>';
}

?>

</div><!-- #comments .comments-area -->
</div><!-- #discussion -->
