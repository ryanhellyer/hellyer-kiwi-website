
chrome.runtime.onMessage.addListener(function(message,sender,sendResponse){
	if (message.text == "download csvs") {

		chrome.storage.sync.get({
			subsessions: '',
		}, function(items) {


			setTimeout(function() {

				// If empty members list, then bail out
				items.subsessions.replace(/\s+/, "") 
				if ( '' === items.subsessions ) {
					return items;
				}

				var subsessions_array = items.subsessions.split(",");
				for ( var i = 0; i < subsessions_array.length; i++ ) {
					var url = 'http://members.iracing.com/membersite/member/GetEventResultsAsCSV?subsessionid=' + subsessions_array[i] + '&simsesnum=0&includeSummary=1';

//console.log('xxx');

//					var includes = url.includes("csv");
//					if ( true === includes ) {

						chrome.downloads.download({
							url: url // The object URL can be used as download URL
						});

						console.log( subsessions_array[i] );
//					}

				}

			}, 1000 );


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
