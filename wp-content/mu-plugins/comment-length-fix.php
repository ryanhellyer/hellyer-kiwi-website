<?php
/**
 * This protects against the security flaw in WordPress reported
 * on April 27'th ish
 */

add_filter( 'preprocess_comment', 'nyt_preprocess_comment' );

function nyt_preprocess_comment($comment) {
    if ( strlen( $comment['comment_content'] ) > 5000 ) {
        wp_die('Comment is too long.');
    }
    return $comment;
}
