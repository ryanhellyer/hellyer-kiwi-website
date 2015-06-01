<?php

class MediaHub_Comments extends MediaHub_Core {

	/**
	 * Class constructor.
	 */
	function __construct() {

		add_filter( 'preprocess_comment' , array( $this, 'preprocess_comment_handler' ) );

	}

	/**
	 * Check if comment is on a MediaHub post. If it is, tell MediaHub about the comment.
	 * 
	 * @param  [type] $comment_data [description]
	 * @return [type]               [description]
	 */
	public function preprocess_comment_handler( $comment_data ) {
		$post_id = absint( $comment_data['comment_post_ID'] );
		$meta = get_post_meta( $post_id, self::META_KEY, true );
		if ( '' != $meta ) {
			// Do API call here
			$query = 'content=' . $comment_data['comment_content'] . '&';
			$query .= 'anonymous_name=' . $comment_data['comment_author'] . '&';
			$query .= 'email=' . $comment_data['comment_author_email'] . '&';
			$this->mediahub_request( 'articles/' . $post_id . '/comment', $query, 'POST' );
		}

		return $comment_data;
	}

}
new MediaHub_Comments;
