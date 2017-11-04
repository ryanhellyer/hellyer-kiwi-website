<?php
/**
 * The template for displaying Comments.
 *
 * The area of the page that contains both current comments and the comment form.
 *
 * @package WordPress
 * @subpackage Lasse_Super
 * @since 1.0
 */

?>

<div id="comments">
<?php if ( post_password_required() ) : ?>
	<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'lassesuper' ); ?></p>
</div><!-- #comments -->
<?php
	/* Stop the rest of comments.php from being processed,
	 * but don't kill the script entirely -- we still have
	 * to fully load the template.
	 */
	return;
endif;


/* If comments exist, serve headings, comments etc.
 */
if ( have_comments() ) : ?>
	<h2 id="comments-title"><?php
		printf( _n( 'One thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', get_comments_number(), 'lassesuper' ),
			number_format_i18n( get_comments_number() ), '<span>' . get_the_title() . '</span>' );
		?>
	</h2>

	<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
	<nav id="comment-nav-above">
		<h1 class="assistive-text"><?php _e( 'Comment navigation', 'lassesuper' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'lassesuper' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'lassesuper' ) ); ?></div>
	</nav>
	<?php endif; // check for comment navigation
	
	

	/* Display list of comments.
	 * Note: Does not use a callback, uses default markup instead.
	 */ ?>
	<ol class="commentlist"><?php wp_list_comments(); ?> </ol><?php

		
	/* If there is enough comments for pagination, display comment pagination links
	 */
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
	<nav id="comment-nav-below">
		<h1 class="assistive-text"><?php _e( 'Comment navigation', 'lassesuper' ); ?></h1>
		<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'lassesuper' ) ); ?></div>
		<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'lassesuper' ) ); ?></div>
	</nav>
	<?php endif; // check for comment navigation


	/* If there are no comments and comments are closed, let's leave a little note, shall we?
	 * But we don't want the note on pages or post types that do not support comments.
	 */
	elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
	<p class="nocomments"><?php _e( 'Comments are closed.', 'lassesuper' ); ?></p><?php
	
	endif;

	/* Display the comments form ... 
	 */
	comment_form(); ?>

</div><!-- #comments -->

