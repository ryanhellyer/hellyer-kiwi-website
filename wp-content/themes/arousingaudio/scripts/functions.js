
/**
 * Change the audio volume.
 *
 * @param  int  volume  The volume level to change to (from 0 and 100)
 */
function changeVolume( volume ) {
console.log( 'volume change: ' + volume );
	// Set audio volume and displayed value
	volumeValue.innerHTML = volume;

	// Display mute icon when volume at zero
	if ( 0 == volume ) {
		mute.className = "muted icon-button";
	} else {
		mute.className = "icon-button";
	}

	audioPlayer.volume = ( volume / 100 );

	// Set volume control position
	var width = volumeControl.clientWidth;
	var span = volumeControl.childNodes[1];
	var setting = ( ( volume / 100 ) * width ) - span.clientWidth;

	span.style.left = setting + "px";

}

/**
 * Change the audio player time stamp.
 *
 * @param  int  percentage_complete  The time completed in percentage
 * param   bool callback true if callback
 */
function changePlayerTimeStamp( percentage_complete, callback = true ) {

	// Set duration time
	durationTime.innerHTML = audioPlayer.duration;

	// Set current time
	var time = Math.floor( audioPlayer.currentTime * 10 );
	currentTime.innerHTML = time / 10;

	// Set time elapsed line length
	var lineLength = ( percentage_complete / 100 ) * timeControl.clientWidth;
	timeElapsedLine.style.width = lineLength + "px";

	// Set audio player time stamp - need to check this this resets the player time constantly, which causes audio glitches
	if ( true == callback ) {
		audioPlayer.currentTime = ( percentage_complete / 100 ) * audioPlayer.duration;
	}

}

function getAudioData( slug, item ) {
	var i = 0;
	for ( i = 0; i < audio_posts.length; i++) {
		if ( slug == audio_posts[ i ][ 'slug' ] ) {
			return audio_posts[ i ][ item ];
		}
	}
}

/**
 * Load an audio file.
 *
 * @param  string  audioFile  An audio file to load
 */
function loadAudioFile( audioFile, pause = false ) {

	var fileLocation = audioFileDir + audioFile + ".mp3";

	set_local_storage( 'current-audio', audioFile );

	if ( audioPlayer.src != fileLocation ) {

		// Set audio player SRC
		audioPlayer.pause(); // Need to pause it or we get errors on changing SRC
		audioPlayer.setAttribute( 'src', fileLocation );

		if ( true == pause ) {
			audioPlayer.pause();
			play.className = "paused icon-button";
		} else {
			audioPlayer.play();			
			play.className = "icon-button";
		}

		// Set track description
		for (i = 0; i < trackDescription.childNodes.length; i++) { 

			trackDescription.style.display = "block";
			trackDescription.href = home_url + "/" + audioFile;

			if ( "H2" == trackDescription.childNodes[i][ 'tagName' ] ) {
				trackDescription.childNodes[i].innerHTML = getAudioData( audioFile, 'title' );
			} else if ( "P" == trackDescription.childNodes[i][ 'tagName' ] ) {
				trackDescription.childNodes[i].innerHTML = getAudioData( audioFile, 'excerpt' );
			}

		}

		// Set ratings
		thumbsUp.innerHTML = thumbs_up = getAudioData( audioFile, 'thumbs_up' );
		thumbsDown.innerHTML = thumbs_down = getAudioData( audioFile, 'thumbs_down' );

	}

}

/**
 * Rating AJAX request.
 * Sent when user clicks thumbs up or thumbs down button.
 *
 * @param  string  rating  up or down
 */
function rating_ajax_request(rating) {

	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			if('Rating successful!'==xhttp.responseText){

				// Storing data
				if ( "down" == rating ) {
					thumbsDown.innerHTML = parseInt( thumbsDown.innerHTML ) + 1;
				} else if ( "up" == rating ) {
					thumbsUp.innerHTML = parseInt( thumbsUp.innerHTML ) + 1;
				}


// SHOULD STORE WHICH PAGE THE USER LIKED OR DISLIKED HERE

			}
		}
	};

	// Get current audio ID

	var i = 0;
	for ( i = 0; i < audio_posts.length; i++) {
		slug = audio_posts[ i ][ 'slug' ];

		if ( audioPlayer.src == audioFileDir + slug + ".mp3" ) {
			var audio_id = audio_posts[ i ][ 'id' ];
		}

	}

	xhttp.open('POST', home_url+'?rating-'+rating+'='+audio_id, true);
	xhttp.send();
}

/**
 * Get local storage item.
 */
function get_local_storage( item ) {
	return localStorage.getItem( item );
}

/**
 * Set local storage item.
 */
function set_local_storage( item, value ) {
	return localStorage.setItem( item, value );
}

/**
 * Resize.
 */
function arousingaudio_resize() {

	width = window.innerWidth || document.body.clientWidth;

	// Mobile device specific stuff
	if ( 760 > width ) {

		// Setting width on header menu to allow horizontal scrolling on touch devices
		var uls = headerNav.getElementsByTagName( "UL" );
		ul = uls[0];
		ul.style.width = width + "px";

	}

	// If volume displayed, then reset it's volume
	if ( document.readyState === 'complete' ) {

		var volumeDisplay = volumeWrapper.currentStyle ? volumeWrapper.currentStyle.display : getComputedStyle(volumeWrapper, null).display;
		if ( "none" === volumeDisplay ) {
			changeVolume( 100 ); // Set volume to 100% when on mobile
		} else {
			changeVolume( audioPlayer.volume * 100 );
		}
	}

	// Forcing main content below the header
	main.style.marginTop    = mainHeader.offsetHeight + "px";
	main.style.marginBottom = mainFooter.offsetHeight + "px";

}
