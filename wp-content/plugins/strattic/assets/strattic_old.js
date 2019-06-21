(function () {

	var waitingForStatus = false;
	var waitingForDeploymentRequest = false;
	var previousPercentage = 100; // Set to 100 so that if page is freshly loaded and result is 100%, that we don't immediately show a confusing completed() message.

	/**
	 * Look for clicks.
	 */
	window.addEventListener(
		'click',
		function ( e ) {

			// Completed box
			if ( e.target.classList.contains( 'strattic-close' ) ) {
				tb_remove();
			} else {
				// For testing the completed box ...
				//completed();
			}


			// Publish button
			if (
				e.target.classList.contains( 'strattic-publish' )
				||
				(
					undefined !== e.target.parentElement
					&&
					( 'classList' in e.target.parentElement ) // Checking that method exists before using it
					&&
					e.target.parentElement.classList.contains( 'strattic-publish' )
				)
				||
				( undefined !== e.target.parentElement.parentElement && e.target.parentElement.parentElement.classList.contains( 'strattic-publish' ) )
			) {

				// Stop the user click following the href
				e.preventDefault();

				// Get distribution ID from admin bar click
				if ( undefined !== e.target.rel ) {
					var distributionData = e.target.rel.split( '|' );
					var distributionId = distributionData[ 0 ];
					var distributionType = distributionData[ 1 ];
				}

				// Get distribution ID from publish button click
				if ( undefined !== e.target.dataset.distributionId ) {
					var distributionId = e.target.dataset.distributionId;
					var distributionType = e.target.dataset.distributionType;
				}

				// If distribution ID set, then we're ready to publish ...
				if ( undefined !== distributionId ) {
					setCookie( 'strattic-distribution-id', distributionId );
					setCookie( 'strattic-distribution-type', distributionType );

					var xmlhttp = new XMLHttpRequest();
					xmlhttp.onreadystatechange = function() {

						if ( xmlhttp.readyState == XMLHttpRequest.DONE ) {
							var serverResponse = JSON.parse( xmlhttp.responseText );

							if ( 'site_creating' === serverResponse.message ) {
								displayError( strattic_strings.siteCreating );
							} else if (
								1 === serverResponse.success
								&&
								200 === xmlhttp.status
							) {

								waitingForDeploymentRequest = waitingForStatus = false;

							} else {

								displayError( serverResponse.message );

								// set as no longer publishing
								setPublishButtons( 'false' );

							}

						}
					};

					// Set as publishing
					setPublishButtons( 'true' );

					// Set at at the beginning
					setCookie( 'strattic-percentage', 0 );

					// Make adminbar show publishing text
					adminBarProgress();

					waitingForDeploymentRequest = 1;
					waitingForStatus = true;
					xmlhttp.open( 'GET', strattic_ajax[ 'url' ] + 'deploy&distribution_id=' + distributionId + '&nonce=' + strattic_ajax[ 'nonce' ], true );

					xmlhttp.send();
				}

			}

		},
		false
	);

	/**
	 */
	window.addEventListener(
		'load',
		function () {

			getStatus();
			setInterval( getStatus, 3000 );

		}
	);

	/**
	 * Get the publication status.
	 */
	function getStatus() {

		updateAllTheThingz(); // We update the page straight away so that the page updates quickly on loading rather than waiting for an AJAX request to complete.

		// AJAX request
		var xhReq = new XMLHttpRequest();
		xhReq.onreadystatechange = function() {

			if ( xhReq.readyState == XMLHttpRequest.DONE ) {

				var serverResponse = xhReq.responseText;
				try {
					var data = JSON.parse( serverResponse );

					if ( 'undefined' !== data.percentage ) {
						var percentage = data.percentage;
					}
					if ( 'undefined' !== data.distributionType ) {
						var distributionType = data.distributionType;
					}
					if ( 'undefined' !== data.distributionId ) {
						var distributionId = data.distributionId;
					}
					if ( 'undefined' !== data.estimatedTime ) {
						var estimatedTime = data.estimatedTime;
					}
					var publishing = data.deploying;
					var message = data.message;
					var responseMessage = data.responseMessage;
					if ( 'undefined' !== data.jobId ) {
						var jobId = data.jobId;
					}

					// Publication is now complete ... 
					if (
						'undefined' !== publishing && 'undefined' !== percentage
						&&
						false === publishing && 100 === percentage
					) {

						// If percentage hasn't changed, then it's
						if ( percentage === previousPercentage ) {
							// Do nothing since this job has already been completed in this browser
						} else {
							completed( jobId );
						}

					} else {

						// Stash the percentage
						setCookie( 'strattic-percentage', percentage );

					}

					if ( 'undefined' !== percentage ) {
						previousPercentage = percentage; // Stashing current percentage for checking next time (to confirm if it recently completed)
					}

					// Only set the distribution t
					if (
						'undefined' !== waitingForDeploymentRequest
						&&
						'undefined' !== distributionType
						&&
						false === waitingForDeploymentRequest
					) {
						setCookie( 'strattic-distribution-type', distributionType );
					}

					console.log(
						'previousPercentage:'+previousPercentage+"\n"+
						'percentage:'+getPercentage()+"\n"+
						'distributionType:'+distributionType+"\n"+
						'distributionId:'+distributionId+"\n"+
						'estimatedTime:'+estimatedTime+"\n"+
						'publishing:'+publishing+"\n"+
						'message:'+message+"\n"+
						'responseMessage: '+responseMessage+"\n"+
						'jobId:'+jobId
					);

					// Set cookies
					if ( true === waitingForDeploymentRequest ) {
						setCookie( 'strattic-publishing', true ); // Required in case the status returns the last data before the deployment request has been processed
					} else {
						setCookie( 'strattic-publishing', publishing );
					}

					updateAllTheThingz();

				} catch (e) {
					console.log( 'Invalid JSON response from server' );
				}

				waitingForStatus = false;
			}

		}

		// Only run AJAX request if last one has completed and is ready to go again
		if ( false === waitingForStatus || true ) {
			waitingForStatus = true;
			var distributionId = getCookie( 'strattic-distribution-id' );
			var url = strattic_ajax[ 'url' ] + 'status&distribution_id=' + distributionId + '&nonce=' + strattic_ajax[ 'nonce' ];
			xhReq.open( 'GET', url, true );
			xhReq.send();
		}

	}

	/**
	 * Fire up process for when the publish is complete.
	 *
	 * @param   int   jobId  The job ID being completed
	 */
	function completed( jobId ) {

		setCookie( 'strattic-last-job-completed', jobId );
		setCookie( 'strattic-percentage', 0 );
		setCookie( 'strattic-publishing', 'false' );

		// Load WordPress modal dialog box

		var links = document.getElementsByClassName( 'strattic-completed-distribution' );
		for ( var i = 0; i < links.length; i++ ) {
			var link = links[ i ];

			if ( link.dataset.id === getCookie( 'strattic-distribution-id' ) ) {
				link.style.display = 'block';
			} else {
				link.style.display = 'none';
			}

		}

		tb_show( '', 'filename?TB_inline?&width=325&height=273&inlineId=strattic-completed' );

	}

	/**
	 * Update all the thingz on the page!
	 */
	function updateAllTheThingz() {
		progressBarProgress();
		adminBarProgress();
		setPublishButtons();
	}

	/**
	 * Calculate percentage.
	 * This includes a fudge factor to accommodate the time before the percentage bar being ready.
	 * The percentage bar needs to move even when the user is just waiting for the data to be sent 
	 *     for processing. The fudge factor here accommodates this requirement.
	 */
	function getPercentage() {
		var percentage = parseInt(getCookie( 'strattic-percentage' )) || 0;

		percentage = parseInt( percentage ); // Avoid decimals
		percentage = String( percentage ); // Convert to string to keep HTML escaper happy

		return percentage;
	}

	/**
	 * Display error message.
	 */
	function displayError( message ) {
		console.log( 'error: ' + message );
		var error_message = document.getElementById( 'strattic-error-message' );
		error_message.style.display = 'block';
		error_message.innerHTML = escapeHtml( message );
	}

	/**
	 * Enable publish buttons.
	 */
	function setPublishButtons( publishing ) {

		// Set cookie if publishing var is set
		if ( undefined !== publishing ) {
			setCookie( 'strattic-publishing', publishing );
		}

		// Iterate through each publishing button
		var publishButtons = document.getElementsByClassName( 'strattic-publish' );
		for ( var i = 0; i < publishButtons.length; i++ ) {
			var publishButton = publishButtons[ i ];
			// Disable or enable based on publishing cookie
			if ( 'true' === getCookie( 'strattic-publishing' ) ) {
				publishButton.disabled = 'disabled';

				// Admin bar sub tag - make unclickable
				if ( undefined !== publishButton.childNodes[ 0 ] ) {
					publishButton.childNodes[ 0 ].style.pointerEvents = 'none';
				}

			} else {
				publishButton.disabled = '';

				// Admin bar sub tag - make clickable
				if ( undefined !== publishButton.childNodes[ 0 ] ) {
					publishButton.childNodes[ 0 ].style.pointerEvents = '';
				}

			}

		}

	}

	/**
	 * Progress bar progress.
	 */
	function progressBarProgress() {
		var currentDistributionId = getCookie( 'strattic-distribution-id' );

		var progressBars = document.getElementsByClassName( 'strattic-progress' );
		for ( var i = 0; i < progressBars.length; i++ ) {
			var progressBar = progressBars[ i ];
			var distributionId = progressBar.dataset.id;
			var progressBarNumber = document.getElementById( 'strattic-progress-number-' + distributionId );


			// Hide progress bar if we're not publishing at the moment
			if ( distributionId === currentDistributionId && 'true' === getCookie( 'strattic-publishing' ) ) {

				progressBar.style.display = 'block';

				// Set percentage on progress bar
				if ( null !== progressBar ) {
					progressBar.value = Math.round(getPercentage());
					progressBarNumber.innerHTML = escapeHtml( getPercentage() ) + '%';
				}
			} else {
				progressBar.style.display = 'none';
				progressBarNumber.innerHTML = '';
			}

		}

	}

	/**
	 * Admin bar progress.
	 */
	function adminBarProgress() {

		var progressBar = document.getElementById( 'wp-admin-bar-strattic' );
		if ( null === progressBar ) {
			return; // Bail out if admin progress bar doesn't exist (happens if no distribution ID's are found)
		}

		for ( var i = 0; i < progressBar.childNodes.length; i++ ) {
			if ( 'ab-item' === progressBar.childNodes[ i ].className ) {
				var progressBarLink = progressBar.childNodes[ i ];
				break;
			}
		}

		if ( 'false' === getCookie( 'strattic-publishing' ) ) {

			// We aren't publishing, so just reset and leave...
			progressBarLink.style.backgroundImage = 'linear-gradient(to left, #931922 100%, #e94f3c 0%)';
			progressBarLink.innerHTML = '<span class="ab-icon"></span> ' + strattic_strings.publish;
			progressBarLink.style.backgroundImage = 'linear-gradient(to right, #e94f3c 100%, #931922 0%)';

		} else if ( 'true' === getCookie( 'strattic-publishing' ) && getPercentage() >= 0 ) {

			// Use different text for test distribution type
			if ( 'test' === getCookie( 'strattic-distribution-type' ) ) {
				var publishing_text = strattic_strings.publishingTest;
			} else {
				var publishing_text = strattic_strings.publishing;
			}

			progressBarLink.innerHTML = '<span class="ab-icon"></span> ' + publishing_text + ' ' + escapeHtml( getPercentage() ) + '%';

			// Show progress via colou
			if ( getPercentage() < 50 ) {
				progressBarLink.style.backgroundImage = 'linear-gradient(to left, #931922 ' + ( 100 - getPercentage() ) + '%, #e94f3c ' + getPercentage() + '%)';
			} else {
				progressBarLink.style.backgroundImage = 'linear-gradient(to right, #e94f3c ' + getPercentage() + '%, #931922 ' + ( 100 - getPercentage() ) + '%)';
			}

		} else {

			progressBarLink.innerHTML = '<span class="ab-icon"></span> ' + strattic_strings.publish;
			progressBarLink.style.backgroundImage = 'linear-gradient(to right, #e94f3c 100%, #931922 0%)';

		}

	}


	function setCookie( name, value, days ) {
		var expires;
		if ( days ) {
			var date = new Date();
			date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
			expires = '; expires=' + date.toGMTString();
		} else {
			expires = '';
		}
		document.cookie = name + '=' + value + expires + '; path=/';
	}

	function getCookie( cname ) {
		var name = cname + '=';
		var ca = document.cookie.split( ';' );
		var i;
		var c;
		for ( i=0; i<ca.length; i+= 1) {
			c = ca[i];

			while (c.charAt(0)==" ") {
				c = c.substring(1);
			}

			if (c.indexOf(name) == 0) {
				return c.substring(name.length, c.length);
			}
		}
		return "";
	}

	function eraseCookie( name ) {
		setCookie( name, '', -1 );
	}

	function escapeHtml( unsafe ) {
		return unsafe
		.replace(/&/g, "&amp;")
		.replace(/</g, "&lt;")
		.replace(/>/g, "&gt;")
		.replace(/"/g, "&quot;")
		.replace(/'/g, "&#039;");
	}

})();