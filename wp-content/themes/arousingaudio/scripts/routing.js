(function () {

	var stored_pathname;

	/**
	 * Need to set stored pathname so that we don't do an AJAX request on initial page load.
	 */
	window.addEventListener(
		'load',
		function (){
			stored_pathname = location.pathname;
		}
	);

	/**
	 * Add / Update a key-value pair in the URL query parameters.
	 *
	 * @param  uri  The URI
	 * @param  key  The query var key
	 * @param value The query var value
	 * @return  The modified URI
	 */
	function add_query_var_to_uri( uri, key, value ) {

		// remove the hash part before operating on the uri
		var i = uri.indexOf( '#' );
		var hash = i === -1 ? ''  : uri.substr( i );
		uri = i === -1 ? uri : uri.substr( 0, i );

		var re = new RegExp( "([?&])" + key + "=.*?(&|$)", "i" );
		var separator = uri.indexOf( '?' ) !== -1 ? "&" : "?";
		if ( uri.match( re ) ) {
			uri = uri.replace( re, '$1' + key + "=" + value + '$2' );
		} else {
			uri = uri + separator + key + "=" + value;
		}

		return uri + hash;  // finally append the hash as well
	}

	/**
	 * Change address bar URL when links clicked.
	 * Kill links.
	 */
	window.addEventListener(
		'click',
		function ( e ){

			// Get clicked URL
			if (
				undefined != e.target.parentNode.parentNode
				&&
				undefined != e.target.parentNode.parentNode.href
			) {
				var linked_url = e.target.parentNode.parentNode.href;
			} else if ( undefined != e.target.parentNode.href ) {
				var linked_url = e.target.parentNode.href;
			} else if ( undefined != e.target.href ) {
				var linked_url = e.target.href;
			}

			// If clicking on TR pseudo link
			if (
				undefined != e.target.parentNode.attributes
				&&
				undefined != e.target.parentNode.attributes[0]
				&&
				undefined != e.target.parentNode.attributes[0].name
				&&
				"data-href" == e.target.parentNode.attributes[0].name
			) {
				var linked_url = e.target.parentNode.attributes[0].value;
			}

			// If URL has been clicked, then do something
			if ( undefined != linked_url ) {

				// Set URL in browser address bar
				window.history.pushState( null, null, linked_url );

				// Kill link
				e.stopPropagation();
				e.preventDefault();
			}

		}
	);

	/**
	 * Update page if URL changes.
	 */
	setInterval(
		function() {

			// Only do something if URL path has changed (need to check that stored_pathname exists first, as setInterval often fires before window is loaded)
			if ( stored_pathname != location.pathname ) {

				stored_pathname = location.pathname;

				var json_url = add_query_var_to_uri( location.href, "json", "true" )

				// Load new content via AJAX
				var xmlhttp;
				xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ){

						var response = JSON.parse( xmlhttp.responseText );

						// Replace page content
						if ( undefined != response[ 'title' ] && undefined != response[ 'slug' ] ) {
							title.innerHTML    = response[ 'title' ];
							title.style.display = "block";
							document.title     = response[ 'title' ];
						} else {
							title.innerHTML    = "";
							title.style.display = "none";
							document.title     = page_title;
						}

						if ( undefined != response[ 'content' ] ) {
							content.innerHTML  = response[ 'content' ];
						} else {
							content.innerHTML = "";
						}

						// Insert comments as HTML string
						if ( undefined != response[ 'comments' ] ) {
							comments.innerHTML = response[ 'comments' ];
						} else {
							comments.innerHTML = "";
						}

						// Body tag class
						var bodyTag = document.getElementsByTagName( "BODY" );
						if ( "audio" == response.post_type ) {
							canvas.style.display = "block";
							bodyTag[0].className = "single-audio";
						} else if ( "page" == response.post_type ) {
							canvas.style.display = "none";
							bodyTag[0].className = "page";
						} else {
							canvas.style.display = "none";
							bodyTag[0].className = "home";
						}

						// Load new audio file into player
						if ( undefined != response[ 'audio' ] && undefined != response[ 'audio' ] ) {
							loadAudioFile( response[ 'slug' ] );
						}

						// Handle frontend audio uploads (controlled via seperate plugin)
						Frontend_Audio_Uploader();

					}
				}
				xmlhttp.open( "GET", json_url, true );
				xmlhttp.send();

			}

		},
		( 1000 / 30 ) // Second number is FPS
	);

})();
