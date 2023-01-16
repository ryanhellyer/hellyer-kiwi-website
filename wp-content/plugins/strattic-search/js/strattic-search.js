window.addEventListener(
	'load',
	function( event ) {

		// Bail out now if not meant to be searching.
		if ( true !== is_search() ) {
			return;
		}

		set_body_classes();
		hide_page_sections();
		set_content_areas();

		setTimeout(
			function() {

				let data = get_local_data();
				if ( null === data ) {
					get_index();
				} else {
					data = JSON.parse( data );
					strattic_search = Object.assign( data, strattic_search );

					strattic_search_engine( strattic_search );

					set_taxonomy_form_fields();
					monitor_form_fields(); // Only start monitoring after index has been collected.
					show_search_page();
				}

			}, 2000
		);

		/**
		 * Get the index.
		 */
		function get_index() {
			const request = new XMLHttpRequest();
			request.open(
				'GET',
				'/strattic-search.json?' + strattic_search.current_date,
				true
			);

			request.setRequestHeader( 'Content-type', 'application/json' );
			request.onreadystatechange = function() {
				if ( request.readyState == 4 && request.status == 200 ) {
					const data = request.responseText;

					strattic_search = Object.assign( JSON.parse( data ), strattic_search );
					strattic_search_engine( strattic_search );

					set_taxonomy_form_fields();
					monitor_form_fields(); // Only start monitoring after index has been collected.
					show_search_page();

					save_local_data( data );
				}
			};

			request.send();
		}

		/**
		 * Set the taxonomy form fields on initial page load.
		 * Populates the form fields based on the initial page URL.
		 */
		function set_taxonomy_form_fields() {
			const taxonomy_data = get_taxonomy_params();
			const taxonomies    = Object.keys( taxonomy_data );
			taxonomies.forEach(
				( taxonomy, strattic_search ) => {
					const fields = document.querySelectorAll( "[data-taxonomy='" + taxonomy + "']" );
					for ( let i = 0; i < fields.length; i++ ) {
						fields[ i ].value = taxonomy_data[ taxonomy ].term_id;
					}
				}
			);
		}

		/**
		 * Sets up event listeners to monitor the various form fields which can be changed.
		 */
		function monitor_form_fields() {
			const fields = get_fields_to_monitor();
			for ( let i = 0; i < fields.length; i++ ) {

				// We use a debounce function to ensure we don't needlessly search during typing.
				fields[ i ].addEventListener(
					'keyup',
					debounce(
						function( e ) {
							search_something( e );
						},
						strattic_search.debounce_timer
					)

				);
				fields[ i ].addEventListener(
					'change',
					debounce(
						function( e ) {
							search_something( e );
						},
						strattic_search.debounce_timer
					)

				);
			}
		}

		/**
		 * Hide redundant page sections.
		 * This is required for some themes which include extra content which is not relevant to the search page.
		 */
		function hide_page_sections() {

			if ( null !== strattic_search.hide_content ) {
				for ( let i = 0; i < strattic_search.hide_content.length; i++ ) {
					const items_to_hide = document.querySelectorAll( strattic_search.hide_content[ i ] );
					if ( null !== items_to_hide ) {
						for ( let i = 0; i < items_to_hide.length; i++ ) {
							const item_to_hide         = items_to_hide[ i ];
							item_to_hide.style.display = 'none';
						}
					}
				}
			}

		}

		/**
		 * Searches something.
		 * Starts searching based on changes to search form inputs.
		 *
		 * @param object The event element (from keypress or click).
		 */
		function search_something( e ) {

			// If no value set, then bail out.
			if ( null === e.target.value ) {
				return;
			}

			set_body_classes();
			hide_page_sections();

			// Get search parameters.
			let search_param = get_search_param();
			if ( 's' === e.target.name ) {
				search_param = e.target.value;
			}

			// Bail out if not searching anything.
			if ( '' === search_param ) {
				return;
			}

			// Get taxonomy terms to search (if applicable).
			let taxonomy_data = get_taxonomy_params();
			let i             = 0;
			let taxonomy      = e.target.getAttribute( 'data-taxonomy' );
			if ( '' !== taxonomy ) {
				taxonomy_data = get_taxonomy_data_from_fields( e );
			}

			show_search_page( search_param, taxonomy_data );
		}

		/**
		 * Get all the search form fields which need to be monitored for change.
		 *
		 * @return array The fields to be monitored.
		 */
		function get_fields_to_monitor() {
			const taxonomy_fields    = document.querySelectorAll( "[data-taxonomy]" );
			const search_text_fields = document.querySelectorAll( '[name="' + strattic_search.name_attr + '"]' );
			const fields             = [...taxonomy_fields,...search_text_fields];

			return fields;
		}

		/**
		 * Display results on the page.
		 *
		 * @param string content The element to to show the search block in.
		 * @param array results The search results.
		 * @param string main_template The main search template which replaces the content.
		 * @param string result_template The template for an individual result.
		 */
		function show_results( content, results, main_template, result_template ) {
			main_template = main_template.replace( '{{search_param}}', get_search_param() );

			let the_results = '';
			for ( let i = 0; i < results.length; i++ ) {
				let result = [];

				result.id              = results[ i ]['id'];
				result.slug            = results[ i ]['slug'];
				result.path            = results[ i ]['path'];
				result.title           = decode_html( results[ i ]['title'] );
				result.content         = decode_html( results[ i ]['content'] );
				result.timestamp       = results[ i ]['timestamp'];
				result.date            = date( strattic_search.date_format, results[ i ]['timestamp'] );
				result.date_yy_mm_dd   = date( "Y-m-d", results[ i ]['timestamp'] );
				result.date_time_h_i_s = date( "H:i:s", results[ i ]['timestamp'] );
				result.date_time_g_i_a = date( "g:i a", results[ i ]['timestamp'] );
				result.modified_date   = results[ i ]['modified_timestamp'];
				result.attachments     = results[ i ]['attachments'];
				result.sticky          = results[ i ]['sticky'];
				result.post_type       = results[ i ]['post_type'];
				result.excerpt         = '{{excerpt}}'; // Temporary cludge as we will inject the excerpt later so that it's HTML is displayed.

				// Taxonomies. Only term ID is stored for each post, so need to extract required information for each one.
				result.taxonomies = get_taxonomy_info_for_post( results[ i ]['term_ids'] );

				// Authors.
				result.author = strattic_search.authors[ results[ i ]['author_id'] ];

				// Render template and inject excerpt.
				let item = Mustache.render( result_template, result )
				item     = item.replace( '{{excerpt}}', decode_html( results[ i ]['excerpt'] ) ); // Temporary cludge to inject the excerpt so that HTML is displayed.

				the_results = the_results + item;
			}

			const rendered_content = main_template.replace( '{{main_content}}', the_results );
			content.innerHTML      = rendered_content;
		}

		/**
		 * Get taxonomy info for a specific post.
		 * 
		 * @param object posts_term_ids The posts term IDs.
		 * @return object The taxonomy data for this post.
		 */
		function get_taxonomy_info_for_post( posts_term_ids ) {
			let taxonomy_data  = [];
			const taxonomies   = strattic_search.taxonomies;
			for ( const [ key, value ] of Object.entries( taxonomies ) ) {
				const taxonomy  = value.taxonomy_data.name;
				const all_terms = value.terms;

				let terms = [];

				for ( let x = 0; x < posts_term_ids.length; x++ ) {
					const term_id = posts_term_ids[ x ].id
					if ( ! is_empty( all_terms ) ) {
						for ( let i = 0; i < all_terms.length; i++ ) {
							if ( posts_term_ids[ x ] === all_terms[ i ].id ) {
								const term_id = all_terms[ i ].id;
								terms = terms.concat( all_terms[ i ] );
							}
						}
					}
				};

				if ( 0 < terms.length ) {
					taxonomy_data[ taxonomy ] = {
						'name': value.taxonomy_data.name,
						'all_items': value.taxonomy_data.labels.all_items,
						'singular_name': value.taxonomy_data.singular_name,
						'terms': terms
					}
				}

			}

			return taxonomy_data;
		}

		/**
		 * Get the search parameter.
		 *
		 * @return string The search query parameter.
		 */
		function get_search_param() {
			const url_params   = new URLSearchParams( window.location.search );
			return url_params.get( strattic_search.name_attr );
		}

		/**
		 * Get the taxonomy parameters.
		 *
		 * @return array The taxonomy parameters.
		 */
		function get_taxonomy_params() {
			const url_params   = new URLSearchParams( window.location.search );

			const taxonomy_index = strattic_search.taxonomies;
			let taxonomy_data    = [];
			const taxonomies     = Object.keys( taxonomy_index );
			taxonomies.forEach(
				( taxonomy, strattic_search ) => {

					let slug = url_params.get( taxonomy );
					if ( null !== slug ) {
						taxonomy_data[ taxonomy ] = {
							term_id: get_term_id( taxonomy, slug ),
							slug: slug
						};
					}

				}
			);

			return taxonomy_data;
		}

		/**
		 * Get the taxonomy data from the HTML fields on the page.
		 * 
		 * @return object The taxonomy data.
		 */
		function get_taxonomy_data_from_fields() {

			// Based on HTML fields.
			const taxonomies  = document.querySelectorAll('[data-taxonomy]');
			let taxonomy_data = [];
			let count         = 0;
			for ( const taxonomy of taxonomies ) {
				let term_id       = taxonomy.value
				let taxonomy_name = taxonomy.getAttribute( 'data-taxonomy' );

				taxonomy_data[ taxonomy_name ] = {
					term_id: parseInt( taxonomy.value ),
					slug: get_term_slug( taxonomy_name, taxonomy.value )
				};

				count++;
			}

			return taxonomy_data;
		}

		/**
		 * Show the search page and run query.
		 *
		 * @param string search_param The search parameter (not accessed from URL in case submitted from form).
		 * @param object taxonomy_data The taxonomy data, including terms.
		 */
		function show_search_page( search_param = get_search_param(), taxonomy_data = get_taxonomy_params() ) {

			// Bail out now if no search param set.
			if ( null === search_param ) {
				return;
			}

			// Get results.
			const raw_results = strattic_search_get_results( search_param );

			const taxonomies  = Object.keys( taxonomy_data );
			let results       = [];
			let result_number = 0;
			for ( let i = 0; i < raw_results.length; i++ ) {
				const id   = raw_results[ i ];
				const post = strattic_search.posts.find( o => o.id === id );

				results[ result_number ] = post;

				// Discard unwanted terms.
				if ( 0 < taxonomies.length ) {
					let keep = false;
					taxonomies.forEach(
						( taxonomy, strattic_search ) => {
							let term_id = taxonomy_data[ taxonomy ].term_id;

							// Loop through each search item and look for a term match.
							let term_results = post['term_ids'];
							for ( x = 0; x < term_results.length; x++ ) {

								// Selected term ID is also in the current search item.
								if ( term_id === term_results[ x ] ) {
									keep = true;
								}

							}

						}
					);
					if ( false === keep ) {
						continue; // If the post doesn't contain one of the selected terms, then ignore it.
					}
				}

				result_number++;
			}

			// Order posts.
			const orderby = strattic_search.orderby;
			const order   = strattic_search.order;
			if ( 'DESC' === order ) {
				results.sort( ( a, b ) => b[ orderby ] - a[ orderby ] );
			} else {
				results.sort( ( a, b ) => a[ orderby ] - b[ orderby ] );
			}

			// Set title and URL in address bar.
			let taxonomy_title = '';
			form_data = get_taxonomy_data_from_fields();
			taxonomies.forEach(
				( taxonomy, strattic_search ) => {
					let term = form_data[ taxonomy ].slug;
					if ( null !== term ) {
						taxonomy_title = taxonomy_title + '&' + taxonomy + '=' + term;
					}
				}
			);

			// Set the new URL.
			let url;
			if ( // If on a designated search page.
				strattic_search.active_pages === window.location.pathname
			) {
				url = strattic_search.home_path + strattic_search.active_pages + '?' + strattic_search.name_attr + '=' + search_param + taxonomy_title; // Uses home_path because home_url does not behave as intended.
			} else {
				url = strattic_search.home_path + '/?' + strattic_search.name_attr + '=' + search_param + taxonomy_title; // Uses home_path because home_url does not behave as intended.
			}

			window.history.pushState( 'object or string', 'Search Results for "' + search_param + '"', url );

			// Get the search templates.
			const templates = strattic_search.templates;
			for ( const selector of Object.keys( templates ) ) {
				const the_templates = templates[ selector ];
				const element       = document.querySelector( selector );

				let template;
				if ( 0 === result_number ) {
					template  = the_templates.no_results_template;
				} else if ( 1 === result_number ) {
					template  = the_templates.single_result_template;
				} else {
					template  = the_templates.multi_results_template;
				}
				template = template.replace( '{{number_of_results}}', result_number );

				// Show results on the page.
				const result_template = the_templates.result_template;
				show_results( element, results, template, result_template );
			}

		}

		/**
		 * Set the pages body classes.
		 */
		function set_body_classes() {
			if ( null !== strattic_search.body_classes ) {
				document.body.className = strattic_search.body_classes;
			}
		}

		/**
		 * Are we searching?
		 *
		 * @return bool True if searching, false if not searching.
		 */
		function is_search() {

			if ( 'all' === strattic_search.active_pages ) {
				return true; // If allowing search on all pages.
			} else if ( // If on a designated search page.
				strattic_search.active_pages === window.location.pathname
			) {
				return true;
			}

			return false;
		}

		/**
		 * Set the content area.
		 */
		function set_content_areas() {

			// Bail out now if not searching for something.
			if ( null === get_search_param() ) {
				return;
			}

			// Loop through each selector and show loading content.
			const templates = strattic_search.templates;
			for ( const selector of Object.keys( templates ) ) {
				const the_templates = templates[ selector ];

				const element  = document.querySelector( selector );
				const loading  = templates[ selector ].loading;
				element.innerHTML = loading;
			}
		}

		/**
		 * Get the taxonomy term slug.
		 * 
		 * @param string taxonomy The taxonomy.
		 * @param int term The taxonomy term.
		 * @return string The term slug.
		 */
		function get_term_slug( taxonomy, term ) {
			term = parseInt( term );

			const terms_index = strattic_search.taxonomies[ taxonomy ].terms;

			if ( is_empty( terms_index ) ) {
				return null; // Terms are empty, so just return null.
			}

			let i = 0;
			while ( i < terms_index.length ) {
				if ( term === terms_index[ i ].id ) {
					return terms_index[ i ].slug;
				}

				i++;
			}

			return null;
		}

		/**
		 * Check if an object is empty or not.
		 *
		 * @param object obj The object to check.
		 * @return bool true if object, false if not an object.
		 */
		function is_empty( obj ) {

			if ( obj == null ) {
				return true;
			}

			if ( obj === undefined ) {
				return true;
			}

			if ( obj.length === 0 ) { 
				return true;
			}

			if ( obj.length > 0 ) {
				return false;
			}

			for ( let key in obj ) {
				if ( obj.hasOwnProperty( prop ) ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Get the taxonomy term slug.
		 * 
		 * @param string taxonomy The taxonomy.
		 * @param int slug The taxonomy term slug.
		 * @return string The term slug.
		 */
		function get_term_id( taxonomy, slug ) {
			const taxonomy_index = strattic_search.taxonomies[ taxonomy ];
			const terms          = taxonomy_index.terms;

			// If no terms, then just drop out.
			if ( is_empty( terms ) ) {
				return null;
			}

			let i = 0;
			while ( i < terms.length ) {
				let thisterm  = terms[ i ];
				let this_id   = terms[ i ].id;
				let this_slug = terms[ i ].slug;

				if ( slug === this_slug ) {
					return this_id;
				}

				i++;
			}

			return null;
		}

		/**
		 * Save the local data.
		 * Avoids needing to repeatedly perform AJAX requests to get the search data.
		 *
		 * @param object data The data to save.
		 */
		function save_local_data( data ) {
			localStorage.setItem( 'strattic_search_data', data );
		}

		/**
		 * Get the local data.
		 * Avoids needing to repeatedly perform AJAX requests to get the search data.
		 */
		function get_local_data() {
			return localStorage.getItem( 'strattic_search_data' );
		}

		/**
		 * Decodes HTML and converts entities back to their real form.
		 *
		 * @param string html The encoded text.
		 * @return string html The decoded text without entities.
		 */
		function decode_html( html ) {
			let txt       = document.createElement( 'textarea' );
			txt.innerHTML = html;

			return txt.value;
		}

		/**
		 * Debounce function.
		 * Adapted from underscore.js
		 * https://github.com/jashkenas/underscore
		 *
		 * @license MIT
		 *
		 * @param string func The function to debounce.
		 * @param int wait How long to wait, in milliseconds.
		 * @param bool int If it's meant to be immediate, then do it now.
		 */
		function debounce( func, wait, immediate ) {
			let timeout;
			return function() {
				const context = this, args = arguments;
				const later = function() {
					timeout = null;
					if ( ! immediate) {
						func.apply( context, args );
					}
				};
				if ( immediate && ! timeout ) {
					func.apply( context, args );
				}

				clearTimeout( timeout );
				timeout = setTimeout( later, wait );
			};
		};

	}
);
