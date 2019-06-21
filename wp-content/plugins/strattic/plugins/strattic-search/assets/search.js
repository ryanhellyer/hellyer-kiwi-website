(function () {
	var stratticSearchRendered = false
	/**
	 * Do stuff on page load.
	 */
	window.addEventListener(
		'load',
		function () {
			if (stratticSearchRendered) {
				return
			}
			var query = get_search_string()
			if (query && query.length > 0) {
				if (strattic_algolia_app_id && strattic_algolia_search_key) {
					makeAlgoliaQuery(query, handleSearchResults);
				} else {
					makeFuseQuery(query, handleSearchResults);
				}
			} else {
				handleSearchResults([])
			}
			
		}
	);

	/**
	 * Fixing garbled characters.
	 * Uses the He library and a custom list of string replacements (required to convert German and other language specific characters)
	 */
	function fix_garbled_characters( content ) {

		content = he.decode( content );

		var find_replace = {
			'Â' : '-',
			'Ã´' : 'ô',
			'Å¡' : 'š',
			'Â¤' : '¤',
			'Ã¶' : 'ö',
			'Å¢' : 'Þ',
			'Â¦' : '¦',
			'Ã·' : '÷',
			'Å£' : 'þ',
			'Â§' : '§',
			'Ãº' : 'ú',
			'Å¤' : '?',
			'Â¨' : '¨',
			'Ã¼' : 'ü',
			'Å¥' : '?',
			'Â©' : '©',
			'Ã½' : 'ý',
			'Å®' : 'Ù',
			'Â«' : '«',
			'Ä‚' : 'Ã',
			'Å¯' : 'ù',
			'Â¬' : '¬',
			'Äƒ' : 'ã',
			'Å°' : 'Û',
			'Â-' : '-',
			'Ä„' : '¥',
			'Å±' : 'û',
			'Â®' : '®',
			'Ä…' : '¹',
			'Å¹' : '?',
			'Â°' : '°',
			'Ä†' : 'Æ',
			'Åº' : 'Ÿ',
			'Â±' : '±',
			'Ä‡' : 'æ',
			'Å»' : '¯',
			'Â´' : '´',
			'ÄŒ' : 'È',
			'Å¼' : '¿',
			'Âµ' : 'µ',
			'Ä?' : 'è',
			'Å½' : 'Ž',
			'Â¶' : '¶',
			'ÄŽ' : 'Ï',
			'Å¾' : 'ž',
			'Â·' : '·',
			'Ä?' : 'ï',
			'Ë‡' : '¡',
			'Â¸' : '¸',
			'Ä?' : 'Ð',
			'Ë˜' : '¢',
			'Â»' : '»',
			'Ä‘' : 'ð',
			'Ë™' : 'ÿ',
			'Ã?' : 'Á',
			'Ä˜' : 'Ê',
			'Ë›' : '²',
			'Ã‚' : 'Â',
			'Ä™' : 'ê',
			'Ë?' : '½',
			'Ã„' : 'Ä',
			'Äš' : 'Ì',
			'â€“' : '–',
			'Ã‡' : 'Ç',
			'Ä›' : 'ì',
			'â€”' : '—',
			'Ã‰' : 'É',
			'Ä¹' : 'Å',
			'â€˜' : '‘',
			'Ã‹' : 'Ë',
			'Äº' : 'å',
			'â€™' : '’',
			'Ã?' : 'Í',
			'Ä½' : '¼',
			'â€š' : '‚',
			'ÃŽ' : 'Î',
			'Ä¾' : '¾',
			'â€œ' : '“',
			'Ã“' : 'Ó',
			'Å?' : '£',
			'â€?' : '”',
			'Ã”' : 'Ô',
			'Å‚' : '³',
			'â€ž' : '„',
			'Ã–' : 'Ö',
			'Åƒ' : 'Ñ',
			'â€' : '†',
			'Ã—' : '×',
			'Å„' : 'ñ',
			'â€¡' : '‡',
			'Ãš' : 'Ú',
			'Å‡' : 'Ò',
			'â€¢' : '•',
			'Ãœ' : 'Ü',
			'Åˆ' : 'ò',
			'â€¦' : '…',
			'Ã?' : 'Ý',
			'Å?' : 'Õ',
			'â€°' : '‰',
			'ÃŸ' : 'ß',
			'Å‘' : 'õ',
			'€¹' : '‹â',
			'Ã¡' : 'á',
			'Å”' : 'À',
			'â€º' : '›',
			'Ã¢' : 'â',
			'Å•' : 'à',
			'â‚¬' : '€',
			'Ã¤' : 'ä',
			'Å˜' : 'Ø',
			'â„¢' : '™',
			'Ã§' : 'ç',
			'Å™' : 'ø',
			'Ã©' : 'é',
			'Åš' : 'Œ',
			'Ã«' : 'ë',
			'Å›' : 'œ',
			'Ã-' : 'í',
			'Åž' : 'ª',
			'Ã®' : 'î',
			'ÅŸ' : 'º',
			'Ã³' : 'ó',
			'Å' : 'Š',
		};

		content = content.replace(new RegExp("(" + Object.keys(find_replace).map(function(i){return i.replace(/[.?*+^$[\]\\(){}|-]/g, "\\$&")}).join("|") + ")", "g"), function(s){ return find_replace[s]});

		return content;
	}

	/**
	 * Get the raw search string via the URL.
	 */
	function get_search_string() {
		var query = window.location.search.substring(1);
		var vars = query.split('&');
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split('=');
			if (pair[0] == 'q') {
				return pair[1];
			}
		}
	}
	function get_search_extras() {
		var query = window.location.search.substring(1);
		var vars = query.split('&');
		var params = {};
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split('=');
			if (pair[0] != 'q') {
				params[pair[0]] = decodeURIComponent(pair[1]);
			}
		}
		return params;
	}

	function makeFuseQuery (query, callback) {

		if ( typeof Fuse !== 'function' ) {
			return;
		}

		var fuse = new Fuse(strattic_search_data, strattic_search_settings);
		var results = fuse.search(query);
		callback(results)
		return results
	}

	function decodeHtml(html) {
		var txt = document.createElement("textarea");
		txt.innerHTML = html;
		return txt.value;
	}

	function makeAlgoliaQuery (query, callback) {
		const applicationId = strattic_algolia_app_id
		const apiKey = strattic_algolia_search_key
		var client = algoliasearch(applicationId, apiKey, {
			timeout: 4000,
		});
		var index = client.initIndex( strattic_algolia_index );
		var extraParams = get_search_extras();
		
		var queryParams = {
			// filters: 'taxonomy-category:uncategorized'
		}
		if (extraParams) {
			if (extraParams['taxonomy'] && extraParams['terms']) {
				const taxonomy = extraParams['taxonomy']
				let terms = extraParams['terms'].split(',')
				terms = getAllSubTerms(taxonomy, terms)
				let filters = ''
				for (const term of terms) {
					const filter = `taxonomy-${taxonomy}:${term.trim()}`
					if (filters.length > 0) {
						filters = `${filters} OR ${filter}` 
					} else {
						filters = filter
					}
				}
				queryParams.filters = filters
			}
			if (extraParams['pg']) {
				queryParams.page = extraParams['pg']
			}
		}
		index.search(query, queryParams, function (err, content) {

			if ( undefined !== content ) {
				var results = content;
			} else {
				var results = [];
			}

			console.log('results', results)
			callback(results)
		});
	}

	function getAllSubTerms (taxonomy, terms) {
		if (!all_taxes || !all_taxes[taxonomy]) {
			return terms
		}
		const tax_terms = all_taxes[taxonomy]

		for (const term of terms) {
			for (const tax_term in tax_terms) {
				const tax_term_object = tax_terms[tax_term]

				if (tax_term_object.parent_slug == term) {
					terms.push(tax_term_object.slug)
				}
			}		
		}
		
		return terms
	}

	function handleSearchResults (content) {
		const results = content.hits

		var results_html = document.getElementById('strattic-search-results');

		var no_result_found = false;
		if (
			undefined === results
			||
			Object.keys(results).length < 1
		) {
			no_result_found = true;
		}

		if (
			undefined === get_search_text()
			||
			no_result_found === true
			||
			'' === get_search_text()
		) {

			// Show no results
			var result = new Object();
			result.search_string = get_search_text();
			var results_not_found_template = document.getElementById('tmpl-strattic-search-not-found-template').innerHTML;
			results_html.innerHTML = Mustache.render(results_not_found_template, result);

		} else {
			var contentHtml = ''
			// Process results
			results.sort(function (a, b) { return new Date(b.rdate) - new Date(a.rdate) });
			var i = -1;
			for (i in results) {

				if (results.hasOwnProperty(i)) {
					var result = results[i];
					for (const key in result) {
						if(typeof result[key] == "string") result[key] = decodeHtml(result[key])
					}
						// Not on a taxonomy page, so display all
					contentHtml += display_search_result(result, i);

				}

			}
			contentHtml += display_pagination(content)
			var results_template = document.getElementById('tmpl-strattic-search-main-template').innerHTML;
			var args = new Object();
			args.search_page_number = content.page;
			args.search_results_per_page = content.hitsPerPage;
			args.search_pages_count = content.nbPages;
			args.search_results_count = content.nbHits;
			args.search_string = get_search_text();
			args.content = contentHtml;

			var tempDiv = document.createElement('div');
			tempDiv.innerHTML = Mustache.render(results_template, args);

			for (var child of tempDiv.children) {
				results_html.parentNode.insertBefore(child, results_html);
			}
			results_html.parentNode.removeChild(results_html);			
		}
		stratticSearchRendered = true

		renderedEvent = new Event('StratticSearchRendered', {bubbles: true})
		document.dispatchEvent(renderedEvent)
	}

	/**
	 * Display a search result.
	 */
	function display_search_result( result, i ) {
		result.number = ( parseInt( i ) + 1 );
		result.url = strattic_home_url + result.path;
		var results_template = document.getElementById( 'tmpl-strattic-search-results-template' ).innerHTML;


		return Mustache.render( results_template, result );
	}

	function display_pagination( results ) {
		// console.log('results', results)
		if (results.nbPages < 2) return ''
		// result.number = ( parseInt( i ) + 1 );
		// result.url = strattic_home_url + result.path;
		var results_template = document.getElementById( 'tmpl-strattic-search-pagination-template' ).innerHTML;
		var pagination_nav_template, pagination_item_template, pagination_item_current_template, pagination_nav_prev, pagination_nav_next
		const elements = jQuery(results_template);
		// console.log('elements', elements)

		for (const element of elements) {
			if (element && element.id) {
				switch (element.id) {
					case 'pagination-nav':
						pagination_nav_template = element.innerHTML
						break
					case 'pagination-item':
						pagination_item_template = element.innerHTML
						break
					case 'pagination-item-current':
						pagination_item_current_template = element.innerHTML
						break
					case 'pagination-nav-prev':
						pagination_nav_prev = element.innerHTML
						break
					case 'pagination-nav-next':
						pagination_nav_next = element.innerHTML
						break
				}
			}

		}
		
		let navItems = ''
		var extraParams = get_search_extras();

		let baseQueryUrl = '?q=' + results.query
		for (const extraParam in extraParams) {
			if (extraParam != 'pg') {
				baseQueryUrl += '&' + extraParam + '=' + extraParams[extraParam]
			}
		}
		if (results.page > 0) {
			navItems += Mustache.render(pagination_nav_prev, {
				url: baseQueryUrl + '&pg=' + (results.page - 1)
			})
		}
		for (let index = 0; index < results.nbPages; index++) {
			if (index == results.page) {
				navItems += Mustache.render(pagination_item_current_template, {
					url: baseQueryUrl + '&pg=' + (results.page),
					number: (index + 1)
				})
			} else {
				navItems += Mustache.render(pagination_item_template, {
					url: baseQueryUrl + '&pg=' + (index),
					number: (index + 1)
				})
			}
		}
		if (results.page < results.nbPages - 1) {
			navItems += Mustache.render(pagination_nav_next, {
				url: baseQueryUrl + '&pg=' + (results.page + 1)
			})
		}
		
		return Mustache.render(pagination_nav_template, {
			nav: navItems
		} );
	}

	/**
	 * Get the search string in text form, ready for output to the page.
	 */
	function get_search_text() {
		 return decodeURIComponent( ( get_search_string() +'' ).replace( /\+/g, '%20' ) );
	}

})();