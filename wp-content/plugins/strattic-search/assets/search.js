(function () {

	window.addEventListener(
		'load',
		function () {

			var fuse = new Fuse(data, strattic_search_settings);
			var template = wp.template( 'strattic-search-template' );
			var results = fuse.search( get_search_string() );

			var template = document.getElementById( 'tmpl-strattic-search-results-template' ).innerHTML;
			var results_html = document.getElementById("strattic-search-results");
			if (Object.keys(results).length > 0){

				results.sort(function(a,b){return new Date(b.rdate) - new Date(a.rdate) });

				var i = -1;
				for (i in results) {
					if (results.hasOwnProperty(i)) {

						var result = results[i];

						result.number = ( parseInt( i ) + 1 );
						result.url = strattic_home_url + result.path;

						results_html.innerHTML += Mustache.render( template, result );
					}
				}

			} else {

				var result = new Object();
				result.search_string = get_search_text();

				results_html.innerHTML = Mustache.render( template, result );

			}
		}
	);


	function get_search_string() {
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			if (pair[0] == 's') {
				return pair[1];
			}
		}
	}

	function get_search_text() {
		 return decodeURIComponent(( get_search_string() +'').replace(/\+/g, '%20'));
	}

})();