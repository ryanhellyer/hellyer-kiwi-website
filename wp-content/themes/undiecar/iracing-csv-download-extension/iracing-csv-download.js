// Add session IDs to console log
setInterval(collect_the_ids, 5000);
function collect_the_ids() {
	var collected_ids = '';


	function ShowResults(value, index, ar) {
		var href = value.href;

		var includes = href.includes("javascript:launchEventResult(");
		if ( true === includes ) {
			href = href.replace("javascript:launchEventResult(", "");
			href = href.replace(")", "");

			var bits = href.split(",");
			var session_id = bits[0];
			collected_ids = collected_ids + ',' + session_id;

		}

	}

	var link_tags = document.getElementsByTagName('A');
	link_tags = Array.prototype.slice.call(link_tags)
	link_tags.forEach(ShowResults);

	console.log( collected_ids );
}



if (
	window.location.href === 'https://members.iracing.com/membersite/member/SeriesRaceResults.do?season=2052'
) {
	console.log('sending message');
	chrome.runtime.sendMessage({text: "download csvs"});
}
