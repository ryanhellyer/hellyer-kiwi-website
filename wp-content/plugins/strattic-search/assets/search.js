(function () {

	/**
	 * Do stuff on page load.
	 */
	window.addEventListener(
		'load',
		function () {

			var results_html = document.getElementById( 'strattic-search-results' );
			var fuse = new Fuse(data, strattic_search_settings);
			var results = fuse.search( get_search_string() );

			if ( Object.keys( results ).length > 0 ) {

				var intro_template = document.getElementById( 'tmpl-strattic-search-intro-template' ).innerHTML;
				var args = new Object();
				args.search_string = get_search_text();
				results_html.innerHTML += Mustache.render( intro_template, args );

				// Process results
				results.sort( function( a, b ) { return new Date( b.rdate ) - new Date( a.rdate ) } );
				var i = -1;
				for ( i in results ) {
					if ( results.hasOwnProperty( i ) ) {
						var result = results[ i ];
						result.number = ( parseInt( i ) + 1 );
						result.url = strattic_home_url + result.path;
						var results_template = document.getElementById( 'tmpl-strattic-search-results-template' ).innerHTML;
						results_html.innerHTML += Mustache.render( results_template, result );
					}
				}

			} else {

				// Show no results
				var result = new Object();
				result.search_string = get_search_text();
				var results_not_found_template = document.getElementById( 'tmpl-strattic-search-not-found-template' ).innerHTML;
				results_html.innerHTML = Mustache.render( results_not_found_template, result );

			}
		}
	);

	/**
	 * Get the raw search string via the URL.
	 */
	function get_search_string() {
		var query = window.location.search.substring( 1 );
		var vars = query.split( '&' );
		for ( var i = 0; i < vars.length; i++ ) {
			var pair = vars[i].split( '=' );
			if ( pair[ 0 ] == 's' ) {
				return pair[ 1 ];
			}
		}
	}

	/**
	 * Get the search string in text form, ready for output to the page.
	 */
	function get_search_text() {
		 return decodeURIComponent( ( get_search_string() +'' ).replace( /\+/g, '%20' ) );
	}

})();