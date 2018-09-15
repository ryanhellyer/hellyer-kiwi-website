if (
	window.location.href === 'http://members.iracing.com/membersite/member/SeriesRaceResults.do?season=2052'
) {
	console.log('sending message');
	chrome.runtime.sendMessage({text: "download csvs"});
}