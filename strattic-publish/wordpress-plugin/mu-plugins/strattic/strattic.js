/**
 * Updates the publication status on the main admin page.
 */
(function () {

	// This is the intitial delay time
	var timeout = 1000;

	// Fudge factor (in seconds) until the server side script has collected all the URLs together
	var fudge_factor = 2;

	// Text for when URLs are being acquired
	var text_urls_being_acquired = 'We are acquiring URLs to deployment from your site'

	/**
	 * When the page loads, we grab the current status immediately.
	 */
	window.addEventListener(
		'load',
		function (){

			// Set the status to be updated again shortly
			get_status();
			setInterval(get_status, timeout);

		}
	);

	/**
	 * Look for clicks.
	 */
	window.addEventListener(
		'click',
		function (e){

			// If deploy button clicked.

			if ('strattic-advanced-settings-button' === e.target.id) {
				document.getElementById( 'strattic-advanced-settings' ).classList.toggle('strattic-visible');
			} else 	if ('strattic-deploy' === e.target.id) {

				// Disable the deploy button if not already pressed, otherwise give error sayin publication is still happening
				if (  e.target.classList.contains( 'disabled' ) ) {
					alert( 'Please wait until the current publication has completed before deployment.' );
				} else {
					e.target.classList.toggle( 'disabled' );
					get_status(); // Need to fire this to trigger the fudge factor immediately
				}

				var xmlhttp = new XMLHttpRequest();
				xmlhttp.onreadystatechange = function() {
					if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4

						if (xmlhttp.status == 200) {
							console.log( '200 received' );
						} else if (xmlhttp.status == 400) {
							console.log( '400 received' );
						} else {
							console.log( 'other error received' );
						}
					}
				};

				// Grab selected options field
				var deployment_type_tag = document.getElementById( 'strattic-deployment-type' );
				var deployment_type;
				for (var i = 0; i < deployment_type_tag.length; i++) {

					// we're on the selected options field, then update the data for that
					if ( true === deployment_type_tag[ i ].selected ) {
						deployment_type = deployment_type_tag[ i ].value;
					}

				}

				xmlhttp.open('GET', strattic_ajax['url'] + 'deploy&deployment_type='+deployment_type+'&nonce='+strattic_ajax['nonce'], true);

				xmlhttp.send();
			}

		}
	);

	/**
	 * Get the publication status.
	 */
	function get_status() {

		var progress_bar = document.getElementById('strattic-progress')
		var progress_number = document.getElementById('strattic-status')
		var message_box = document.getElementById('strattic-message');
		var log_box = document.getElementById('strattic-log');
		var log_box_wrapper = document.getElementById('strattic-log-wrapper');
		var deploy_button = document.getElementById( 'strattic-deploy' );
		var site_link = document.getElementById( 'strattic-site-link' );

		// Add fudge factor immediately, to avoid waiting for AJAX processing
		if ( deploy_button.classList.contains( 'disabled' ) ) {

			if  ( 0 === progress_bar.value && '' === progress_number.innerHTML ) {
				progress_number.innerHTML = '0%'
				progress_bar.value = 0;

				message_box.innerHTML = text_urls_being_acquired;

			} else if  ( fudge_factor > progress_bar.value ) {
				progress_bar.value = progress_bar.value + 1;
				progress_number.innerHTML = progress_bar.value+'%'
			} else if  ( '' === progress_bar.value ) {
				deploy_button.classList.toggle( 'disabled' );
			}

		}

		// AJAX request
		var xhReq = new XMLHttpRequest();
		xhReq.onreadystatechange = function() {
			if (xhReq.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4

				var serverResponse = xhReq.responseText;
				var data = JSON.parse( serverResponse );

				var percentage = data.percentage;
				var deployment_type = data.deployment_type;
				var estimated_time = data.estimated_time;
				var log = data.log;
				var deploying = data.deploying;
				var message = data.message;

				message_box.innerHTML = message;

				// Grab selected options field
				var deployment_type_tag = document.getElementById( 'strattic-deployment-type' );
				for (var i = 0; i < deployment_type_tag.length; i++) {

					// we're on the selected options field, then update the data for that
					if ( true === deployment_type_tag[ i ].selected ) {

						var description = deployment_type_tag[ i ].dataset.description;
						var site_url = deployment_type_tag[ i ].dataset.url;

						var deployment_type_description = document.getElementById( 'strattic-deployment-type-description' );
						deployment_type_description.innerHTML = description;

						var site_link = document.getElementById( 'strattic-site-link' );
						site_link.childNodes[1].innerHTML = site_url;

					}

				}

				// Making sure correct things are displaying when deploying
				if ( true === deploying ) {

					// Display the bits.
					progress_bar.style.visibility = 'visible';
					deploy_button.classList.add( 'disabled' );
					message_box.style.display = 'block';
					site_link.style.display = 'block';

					log_box_wrapper.style.display = 'block'; // Unhide the log box once it's being used

					// We add a fudge factor to the percentage bar, so that it looks like something is happening while the server is still requesting the list of URLs from WordPress
					fudged_percentage = fudge_factor + ( ( 100 - fudge_factor ) * ( percentage / 100 ) );

					fudged_percentage = Math.round( fudged_percentage );
					fudged_percentage = parseInt( fudged_percentage );
					if ( 100 < fudged_percentage ) {
						fudged_percentage = 100;
					}

					progress_number.innerHTML = fudged_percentage+'%'
					progress_bar.value = fudged_percentage;

					log_box.innerHTML = log_box.innerHTML + log;

				} else {
					// Display the bits.
					progress_bar.style.visibility = 'hidden';
					deploy_button.classList.remove( 'disabled' );
					message_box.style.display = 'none';
					site_link.style.display = 'none';
				}

			}

		}
		xhReq.open('GET', strattic_ajax['url'] + 'redis&nonce='+strattic_ajax['nonce'], true);

		xhReq.send();

	}

})();