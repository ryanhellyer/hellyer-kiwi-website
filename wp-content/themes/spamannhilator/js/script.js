
(function () {

	window.addEventListener(
		'load',
		function (){
			if ( null !== document.getElementById( 'spamannhilator-redirects' ) ) {
				load_table();
			}

		}
	);

	/**
	 * Handle clicks.
	 */
	window.addEventListener(
		'click',
		function (e){

			// Save the data
			if (
				'spamannhilator-submit-1' === e.target.id
				||
				'spamannhilator-submit-2' === e.target.id
			) {
				e.preventDefault();

				// Handle CSS animation
				var redirects = document.getElementById( 'spamannhilator-redirects' );
				redirects.classList.toggle( 'transition' );

				// Save the data
				save();

			}

			// Delete a row
			if ( 'delete' === e.target.className) {
				e.preventDefault();

				delete_row( e );

			}

			// Edit slug
			if ( 'edit-slug' === e.target.className) {
				e.preventDefault();

				e.target.nextElementSibling.style.display = 'inline';
				e.target.previousElementSibling.style.display = 'none';
				e.target.style.display = 'none';

			}

		}
	);

	/**
	 * Load user certificates for specific webinar.
	 */
	function load_table() { /* number_of_certificates, webinar_id ) {*/

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {

			if (this.readyState == 4 && this.status == 200) {

				data = JSON.parse( this.responseText );
				data.reverse();

				var tbody = document.getElementById( 'spamannhilator-redirects' );
				var template = document.getElementById( 'tmpl-spamannhilator-table' ).innerHTML;

				tbody.innerHTML = '';

				// Add table rows
				var i = -1;
				for (i in data) {
					if (data.hasOwnProperty(i)) {

						data[i].number = ( parseInt( i ) + 1 );
						tbody.innerHTML = tbody.innerHTML + Mustache.render( template, data[i] );

					}
				}

				// Add empty table row
				var empty_data = {
					'id':           '',
					'number':       ( parseInt( i ) + 2 ),
					'slug':         '',
					'redirect_url': '',
				};
				tbody.innerHTML = tbody.innerHTML + Mustache.render( template, empty_data );

				// Handle CSS animation
				var redirects = document.getElementById( 'spamannhilator-redirects' );
				redirects.classList.toggle( 'transition' );

			}
		};

		var url = spamannhilator_home_url + "/wp-json/spamannhilator/v1/get?user_id=" + spamannhilator_user_id;
		xhttp.open( 'GET', url, true );
		xhttp.setRequestHeader( 'X-WP-Nonce', spamannhilator_nonce );
		xhttp.send();

	}

	/**
	 * Save the results.
	 */
	function save( form_data ) {

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {

			if ( this.readyState == 4 && this.status == 200 ) {
				load_table();
			}

		};

		var ids           = document.getElementsByName( 'spamannhilator-id[]' );
		var slugs         = document.getElementsByName( 'spamannhilator-slug[]' );
		var redirect_urls = document.getElementsByName( 'spamannhilator-redirect-url[]' );

		var form_data = new FormData();

		var data = [];
		for (var i in slugs) {
			if (slugs.hasOwnProperty(i)) {

				data = {
					'id':           ids[i].value,
					'slug':         slugs[i].value,
					'redirect_url': redirect_urls[i].value
				};
				data = JSON.stringify( data );
				form_data.append( i, data );

			}
		}

		//form_data.append( 'groucho', 'marx' );

		var url = spamannhilator_home_url + "/wp-json/spamannhilator/v1/save?user_id=" + spamannhilator_user_id;
		xhttp.open( 'POST', url, true );
		xhttp.setRequestHeader( 'X-WP-Nonce', spamannhilator_nonce );
		xhttp.send( form_data );
	}

	/**
	 * Delete a row.
	 */
	function delete_row( e ) {

		var xhttp = new XMLHttpRequest();
		xhttp.onreadystatechange = function() {

			if (this.readyState == 4 && this.status == 200) {

				data = JSON.parse( this.responseText );

				// Remove the item from display
				if ( false !== data ) {

				var redirects = document.getElementById( 'spamannhilator-redirects' );
					var item_to_be_deleted = e.target.parentNode.parentNode;
					item_to_be_deleted.remove();
				}

			}
		};

		var id = e.target.dataset.id;
		var url = spamannhilator_home_url + "/wp-json/spamannhilator/v1/delete?user_id=" + spamannhilator_user_id + "&id=" + id;
		xhttp.open( 'GET', url, true );
		xhttp.setRequestHeader( 'X-WP-Nonce', spamannhilator_nonce );
		xhttp.send();
	}

})();