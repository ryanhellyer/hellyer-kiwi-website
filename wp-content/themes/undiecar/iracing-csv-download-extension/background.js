
chrome.runtime.onMessage.addListener(function(message,sender,sendResponse){
	if (message.text == "download csvs") {

		chrome.storage.sync.get({
			subsessions: '',
		}, function(items) {


			// If empty members list, then bail out
			items.subsessions.replace(/\s+/, "") 
			if ( '' === items.subsessions ) {
				return items;
			}

			var subsessions_array = items.subsessions.split(",");
			for ( var i = 0; i < subsessions_array.length; i++ ) {
				var url = 'http://members.iracing.com/membersite/member/GetEventResultsAsCSV?subsessionid=' + subsessions_array[i] + '&simsesnum=0&includeSummary=1';

				setTimeout(
					function(url) {

//alert('url: ' + url);

						chrome.downloads.download({
							url: url // The object URL can be used as download URL
						});

					},
					( 900 * i ),
					url
				);
/*

*/
//						console.log( subsessions_array[i] );

			}

		});

	}
});


function undiecar_get_csv( url ) {

	console.log('URL: ' + url);

//	chrome.downloads.download({
//		url: url // The object URL can be used as download URL
//	});

}
