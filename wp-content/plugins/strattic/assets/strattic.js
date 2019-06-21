(function (wp, $) {

	var distributionId = parseInt(localStorage.getItem('strattic-distribution-id')) || 0; // Global variable
	var notice_performing_updates;
	var notice_publishing;

	var tmpIsPublishing = false;
	jQuery(function( $ ) {

		// When page has finished loading
		checkStatus();

		var distributionId = false;
		$('a.strattic-publish, .strattic-publish a.ab-item').click(function(e){
			if ($(this).data('distribution-id')) {
				distributionId = $(this).data('distribution-id');
			} else if( parseInt($(this).attr('rel')) ) {
				distributionId = parseInt($(this).attr('rel'));
			} else {
				return;
			}

			e.preventDefault();

			if ($('#wp-admin-bar-strattic .ab-item.disabled').length) {
				return;
			}
			
			startDeployment(distributionId);
			setProgressBar(distributionId, 0, 'Starting...');
			tmpIsPublishing = new Date();

		});

		$('#wp-admin-bar-strattic').click( '' );
	});

	var resetProgressBarsButtons = function() {
		$('.strattic-progress-bar').hide();
		$('#wp-admin-bar-strattic > .ab-item').prop('disabled', false)
			.css({
				backgroundImage: ''
			}).removeClass('disabled');
		$('a.strattic-publish').prop('disabled', false).removeClass('disabled');
		$('#strattic-notice-publishing').hide();
		
	}
	var setProgressBar = function(distributionId, percentage, text) {
		resetProgressBarsButtons();
		if (!percentage) percentage = 0;
		var $progressWrap = $('.strattic-progress-bar#strattic-progress-' + distributionId).show();
		$progressWrap.find('progress').val(percentage)
		$progressWrap.find('.progress-bar-number').text(percentage + '%');
		if (text) {
			$progressWrap.find('.progress-bar-message').text(text);
		}

		// Admin menu bar
		$('#wp-admin-bar-strattic > .ab-item').prop('disabled', true).addClass('disabled')
			.css({
				backgroundImage: 'linear-gradient(to right, #e94f3c ' + percentage + '%, #931922 ' + percentage + '%)'
			});

		// Disable buttons
		$('a.strattic-publish').prop('disabled', true).addClass('disabled');

		// Show notice
		$('#strattic-notice-publishing').show();
		if (
			true !== notice_publishing  // Gutenberg version - important to only show once or it will be added multiple times
			&&
			true === isGutenbergActive() // Only show when on Gutenberg pages
		) {
			wp.data.dispatch('core/notices').createNotice(
				'warning',
				strattic_gutenberg_notices['site_publishing'],
				{
					isDismissible: true,
				}
			);
		}
		notice_publishing = true;

	}

	/**
	 * Return true if we are on a Gutenberg page.
	 */
	function isGutenbergActive() {
		return typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined';
	}

	var showSuccessPopup = function(distributionId) {
		$('#strattic-completed .strattic-completed-distribution-link').hide()
			.filter('#strattic-completed-distribution-link-' + distributionId).show();
		tb_show( '', 'filename?TB_inline?&width=325&height=273&inlineId=strattic-completed' );
	}
	
	var checkStatus = function() {

		$.get(strattic_ajax.admin_url, {
			'strattic-ajax': 'status',
			'distribution_id': distributionId,
			'nonce': strattic_ajax.nonce
		}).done(function(response){
			console.log(response);

			// Show error messages if site is updating or creating
			if (
				'site_updating' === response.error 
				||
				'site_creating' === response.error
			) {
				$('#strattic-notice-background').show();
				$('a.strattic-publish').prop('disabled', true).addClass('disabled');
				$('.strattic-publish a').prop('disabled', true).addClass('disabled');
				$('.strattic-publish').prop('disabled', true).addClass('disabled');

				// Add tooltip to each publish button, saying that the site is busy
				var tooltip = '<span class="strattic-tooltip">' + strattic_strings['busyToolTip'] + '</span>';
				var publish_buttons = document.getElementsByClassName('strattic-publish');
				for(var i = 0; i < publish_buttons.length; i++){

					if ( ! publish_buttons[i].innerHTML.includes( strattic_strings['busyToolTip'] ) ) {
						publish_buttons[i].innerHTML = publish_buttons[i].innerHTML + tooltip;
					}

				}

				// Display performing updates notice (if applicable and only once)
				if (
					true !== notice_performing_updates  // Gutenberg version - important to only show once or it will be added multiple times
					&&
					true === isGutenbergActive() // Only show when on Gutenberg pages
				) {
					wp.data.dispatch('core/notices').createNotice(
						'warning',
						strattic_gutenberg_notices['performing_updates'],
						{
							isDismissible: true,
						}
					);
				}
				notice_performing_updates = true;

				tmpIsPublishing = 9999999999999999999999999999999999999; // No documentation was found for this variable, but it needs to be set high to avoid the button becoming immediately disabled again
			} else {
				$('#strattic-notice-background').hide();
				$('a.strattic-publish').prop('enabled', true).removeClass('disabled');
				$('.strattic-publish a').prop('enabled', true).removeClass('disabled');

				// Hide all the "busy" tooltips, since the site is no longer busy
				var tooltips = document.getElementsByClassName('strattic-tooltip');
				for(var i = 0; i < tooltips.length; i++){
					tooltips[i].style.display = 'none';
				}

			}

			if(response.deploying){
				var distributionId = response.distributionId;
				switch(response.status) {

					case 'first-stage-syncing':
						setProgressBar(distributionId, 5, 'Publishing static files…');
						break;

					case 'first-stage-beginning':
						setProgressBar(distributionId, 10, 'Publishing latest changes…');
						break;

					case 'first-stage-requesting':
						setProgressBar(distributionId, 15, 'Publishing latest changes…');
						break;

					case 'first-stage-publishing':
						var rangeMin = 15;
						var rangeMax = 40;
						var relativeProgress = (rangeMax - rangeMin) * (response.job_progress.progress || 0);
						var percentage = Math.round( rangeMin + relativeProgress );
						setProgressBar(distributionId, percentage, 'Publishing latest changes…');
						break;

					case 'first-stage-completed':
						setProgressBar(distributionId, 40, 'Latest changes are published.');
						break;

					case 'second-stage-requesting':
						setProgressBar(distributionId, 50, 'Publishing the rest of your site…');
						break;

					case 'second-stage-publishing':
						var rangeMin = 50;
						var rangeMax = 90;
						var relativeProgress = (rangeMax - rangeMin) * (response.job_progress.progress || 0);
						var percentage = Math.round( rangeMin + relativeProgress );
						setProgressBar(distributionId, percentage, 'Publishing the rest of your site…');
						break;

					case 'second-stage-completed':
						setProgressBar(distributionId, 95, 'Finishing publish...');
						break;

					case 'syncing-images':
						setProgressBar(distributionId, 95, 'Syncing site images.');
						break;

					case 'publishing-completed':
						setProgressBar(distributionId, 100, 'Publishing completed.');
						showSuccessPopup(distributionId);
						tmpIsPublishing = false;
						break;
				}

			} else {
				if ( ! tmpIsPublishing || new Date() - tmpIsPublishing > 20 * 1000 ) {
					resetProgressBarsButtons();
				}
			}
		})
		.always(function(response){
			setTimeout(checkStatus, 1000);
		});
	};

	var startDeployment = function(distributionId) {
		localStorage.setItem('strattic-distribution-id', distributionId)
		var fullPublish = jQuery('#strattic-full-publish').prop('checked');
		var maxConcurrentWorkers = jQuery('#strattic-advanced input#strattic-concurrentWorkers').val();
		var workerBatchSize = jQuery('#strattic-advanced input#strattic-workerBatchSize').val();
		var addUrlsBatchSize = jQuery('#strattic-advanced input#strattic-addUrlsBatchSize').val();
		
		sendDeployCall(distributionId, fullPublish, maxConcurrentWorkers, workerBatchSize, addUrlsBatchSize);
		// setProgressBar(distributionId, 0);
	}
	
	var sendDeployCall = function (distributionId, fullPublish, maxConcurrentWorkers, workerBatchSize, addUrlsBatchSize) {

		$.get(strattic_ajax.admin_url, {
			'strattic-ajax': 'deploy',
			'distribution_id': distributionId,
			'fullPublish': fullPublish,
			'maxConcurrentWorkers': maxConcurrentWorkers,
			'workerBatchSize': workerBatchSize,
			'addUrlsBatchSize': addUrlsBatchSize,
			'nonce': strattic_ajax.nonce
		}, function(response){
			
		});
	};
})(window.wp, jQuery);