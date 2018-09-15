chrome.runtime.onMessage.addListener(function(message,sender,sendResponse){
	if (message.text == "download csvs") {

		chrome.storage.sync.get({
			subsessions: '',
		}, function(items) {

		//alert('pooper');

//			if (
//				window.location.href === 'http://members.iracing.com/membersite/member/SeriesRaceResults.do?season=2052'
//			) {

				// If empty members list, then bail out
				items.subsessions.replace(/\s+/, "") 
				if ( '' === items.subsessions ) {
					return items;
				}

				var subsessions_array = items.subsessions.split(",");
				for ( var i = 0; i < subsessions_array.length; i++ ) {
					var url = 'http://members.iracing.com/membersite/member/GetEventResultsAsCSV?subsessionid=' + subsessions_array[i] + '&simsesnum=0&includeSummary=1';

//					var url = 'http://members.iracing.com/membersite/member/GetEventResultsAsCSV?subsessionid=23201224&simsesnum=0&includeSummary=1';
					chrome.downloads.download({
						url: url // The object URL can be used as download URL
					});

		//			alert( url );
		console.log( subsessions_array[i] );
		//		    window.open( url, '_self' );

				}

//			}

		});

	}
});

/*
chrome.runtime.onMessage.addListener(function(message,sender,sendResponse){
	if (message.text == "download csvs") {
		console.log('message received');

		var url = 'http://members.iracing.com/membersite/member/GetEventResultsAsCSV?subsessionid=23201224&simsesnum=0&includeSummary=1';
		chrome.downloads.download({
			url: url // The object URL can be used as download URL
		});

	}
});
*/
