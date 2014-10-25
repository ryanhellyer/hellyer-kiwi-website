/**
 * Relink script
 */
( function( $ ) {

	// Flag to check if a user changed something
	var documentModified = false;

	// View classes
	var editView = '.modal-relink-edit';
	var editButton = '.open-relink-edit';

	/**
	 * Constructor
	 */
	function _init() {
		_register_components();
	}

	/**
	 * Get posts and insert them into the posts view
	 */
	function _get_posts() {
		_request_posts.call( this )
			.done( _insert_posts.bind( this ) );
	}

	/**
	 * Get linked posts and insert them into the linked posts view
	 */
	function _get_linked_posts() {
		var scope = $( editView );

		_request_linked_posts.call( this )
			.done( _insert_linked_posts.bind( scope ) );
	}

	/**
	 * Remove post from the linked posts view. Provides instant feedback
	 *  which gets overwritten by server response (lag compensation)
	 */
	function _remove_linked_post() {
		var scope = $( editView );

		// Instant feedback
		$( this ).hide();

		_request_remove_linked_post.call( this )
			.done( _insert_linked_posts.bind( scope ) );
	}

	/**
	 * Add a post relation and update the linked posts view
	 */
	function _add_linked_post() {
		var scope = $( editView ),
			blog_name = '',
			clone,
			clone_html;

		_request_add_linked_post.call( this )
			.done( _insert_linked_posts.bind( scope ) );
	}

	/**
	 * Trigger a request to retrieve all posts based on the search string and chosen blog
	 * @return promise
	 */
	function _request_posts() {
		return mlp.helper.request( 'GET', 'mlp_relink_editor_get_posts', {
			blog_id: $( 'select[name="blog-list"] option:selected', this ).val(),
			search: $( 'input[name="search-posts"]', this ).val(),
			source_post_id: $( 'input[name="post_ID"]' ).val()
		});
	}

	/**
	 * Trigger a request to retrieve all linked posts based on the source post id
	 * @return promise
	 */
	function _request_linked_posts() {
		return mlp.helper.request( 'GET', 'mlp_relink_editor_get_linked_posts', {
			source_post_id: $( 'input[name="post_ID"]' ).val()
		});
	}

	/**
	 * Trigger a request to remove a post relation
	 * @return promise
	 */
	function _request_remove_linked_post() {
		var $this = $( this );

		// Detect modification
		documentModified = true;

		return mlp.helper.request( 'POST', 'mlp_relink_editor_remove_linked_post', {
			source_blog_id: $( 'input[name="blog_ID"]' ).val(),
			source_post_id: $( 'input[name="post_ID"]' ).val(),
			blog_id: $this.data( 'blog_id' ),
			post_id: $this.data( 'post_id' )
		});
	}

	/**
	 * Trigger a request to add a post relation
	 * @return promise
	 */
	function _request_add_linked_post() {
		var $this = $( this );

		// Detect modification
		documentModified = true;

		return mlp.helper.request( 'POST', 'mlp_relink_editor_add_linked_post', {
			source_blog_id: $( 'input[name="blog_ID"]' ).val(),
			source_post_id: $( 'input[name="post_ID"]' ).val(),
			blog_id: $this.data( 'blog_id' ),
			post_id: $this.data( 'post_id' )
		});
	}

	/**
	 * Update the DOM with a set of posts
	 */
	function _insert_posts( posts ) {
		mlp.helper.insert.call( this, 'ul.posts', posts, function( post ) {
			$( this )
				.data({
					blog_id: post.blog_id,
					post_id: post.post_id
				})
				.html( post.title );
		});
	}

	/**
	 * Update the DOM with a set of linked posts
	 */
	function _insert_linked_posts( posts ) {
		mlp.helper.insert.call( this, 'ul.linked-posts', posts, function( post ) {
			$( this )
				.data({
					blog_id: post.blog_id,
					post_id: post.post_id
				})
				.html( post.blog_name + ' | ' + post.title );
		});
	}

	/**
	 * Wrapper for registering the modal overlay and common events
	 */
	function _register_components() {
		_register_modal();
		_register_events();
	}

	/**
	 * jQuery UI Dialog Modal implementation
	 */
	function _register_modal() {

		var $editView = $( editView );

		$editView.dialog({
			'dialogClass': 'wp-dialog',
			'modal': true,
			'autoOpen': false,
			'width': 500,
			'closeOnEscape': true,
			'close': function() {
				// Refresh on close
				if ( documentModified && confirm( l18n['refresh-required'] ) ) {
					location.reload();
				}
			},
			'buttons': [{
				'text': 'close',
				'class': 'button',
				'click': function() {
					$( this ).dialog( 'close' );
				}
			}]
		});

		$( editButton ).click( function( event ) {
			event.preventDefault();
			$editView.dialog( 'open' );
			documentModified = false;
		});
	}

	/**
	 * Bind common events
	 */
	function _register_events() {

		var scope = $( editView );

		$( 'select[name="blog-list"]' )
			.on( 'change', _get_posts.bind( scope ) );

		$( 'input[name="search-posts"]' )
			.on( 'keyup', _get_posts.bind( scope ) );

		$( editButton )
			.on( 'click', _get_linked_posts.bind( scope ) );

		$( scope )
			.on( 'click', 'ul.linked-posts > li', _remove_linked_post );

		$( scope )
			.on( 'click', 'ul.posts > li', _add_linked_post );

	}

	$.fn.ready( _init );
})( jQuery );