/**
 * Helper methods for the relinking feature
 *
 * @return {object} `request` and `insert` methods
 */
mlp = window.mlp || {};
mlp.helper = ( function( $ ) {

	/**
	 * Trigger a server request
	 *
	 * @param  {string} method  either 'GET' or 'POST'
	 * @param  {string} action  server identifier
	 * @param  {object} params  additional data to add to the request
	 * @return promise
	 */
	function request( method, action, params ) {

		var defaults = {
				action: action,
				_mlp_relink_editor_nonce: $( '#_mlp_relink_editor_nonce' ).val()
			},
			data = $.extend( {}, defaults, params );

		return $.ajax({
			url: ajaxurl,
			method: method,
			dataType: 'json',
			data: data
		});
	}

	/**
	 * DOM insertion abstraction
	 *
	 * @param  {string}   destination  View in which to to insert
	 * @param  {array}    posts        List of posts
	 * @param  {Function} interceptor  Method to modify (intercept) a single post
	 * @return promise
	 */
	function insert( destination, posts, interceptor ) {

		var $posts = $( destination, this ),
			postsLen = posts.length,
			fragment = document.createDocumentFragment(),
			i;

		$posts.removeClass( 'loading' );

		for ( i = 0; i < postsLen; i++ ) {
			var option = document.createElement( 'li' );
			interceptor.call( option, posts[ i ] );
			fragment.appendChild( option );
		}

		$posts
			.html( fragment );
	}

	return {
		request: request,
		insert: insert
	};

})( jQuery );

// Function.prototype.bind Polyfill
Function.prototype.bind=(function(){}).bind||function(b){if(typeof this!=="function"){throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");}function c(){}var a=[].slice,f=a.call(arguments,1),e=this,d=function(){return e.apply(this instanceof c?this:b||window,f.concat(a.call(arguments)));};c.prototype=this.prototype;d.prototype=new c();return d;};