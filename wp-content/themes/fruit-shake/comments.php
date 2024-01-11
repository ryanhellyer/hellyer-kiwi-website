<?php
/**
 * @package Fruit Shake
 */
?>
	<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<div class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'fruit-shake' ); ?></div>
	</div><!-- .comments -->
	<?php return;
		endif;
	?>

	<?php // You can start editing here -- including this comment! ?>

	<?php if ( have_comments() ) : ?>
		<h2 id="comments-title">
			<?php
				printf( _n( 'One Response', '%1$s Responses', get_comments_number(), 'fruit-shake' ),
					number_format_i18n( get_comments_number() ) );
			?>
		</h2>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-above">
			<h1 class="section-heading"><?php _e( 'Comment navigation', 'fruit-shake' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'fruit-shake' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'fruit-shake' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

		<ol class="commentlist">
			<?php wp_list_comments( array( 'callback' => 'fruit_shake_comment' ) ); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav-below">
			<h1 class="section-heading"><?php _e( 'Comment navigation', 'fruit-shake' ); ?></h1>
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'fruit-shake' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'fruit-shake' ) ); ?></div>
		</nav>
		<?php endif; // check for comment navigation ?>

	<?php endif; //end if have_comments ?>

	<?php
	// If comments are closed and there are comments, let's leave a little note, shall we?
	if ( ! comments_open() && '0' != get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'fruit-shake' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->