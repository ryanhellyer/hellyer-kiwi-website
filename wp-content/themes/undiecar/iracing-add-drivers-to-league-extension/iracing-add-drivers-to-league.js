
function iracing_add_drivers_to_league_post(path, params) {
/*
	var form = document.createElement("form");
	form.setAttribute("method", 'post');
	form.setAttribute("action", path);

	for(var key in params) {
		if(params.hasOwnProperty(key)) {
			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("type", "hidden");
			hiddenField.setAttribute("name", key);
			hiddenField.setAttribute("value", params[key]);

			form.appendChild(hiddenField);
		}
	}

	document.body.appendChild(form);
	form.submit();
*/
}

/**
 * If league request has been sent, then redirect to league page so that we can keep sending more.
 */
if ( window.location.href === 'http://members.iracing.com/membersite/member/SendLeagueRequest' ) {
	chrome.storage.sync.get({
		members_list: '',
		league_id: '',
	}, function(items) {

		if ( '' !== items.league_id ) {
			window.location.href = 'http://members.iracing.com/membersite/member/LeagueView.do?league='+items.league_id
		}

	});
}

// Add data
// Process data
chrome.storage.sync.get({
	members_list: '',
	league_id: '',
}, function(items) {

	if (
		window.location.href === 'http://members.iracing.com/membersite/member/LeagueDirectory.do'
		||
		window.location.href === 'http://members.iracing.com/membersite/member/LeagueView.do?league='+items.league_id
	) {

		var members_list_array = items.members_list.split(",");

		// If empty members list, then bail out
		items.members_list.replace(/\s+/, "") 
		if ( '' === items.members_list ) {
			return items;
		}

		var member_id = members_list_array[0];

		members_list_array.splice( 0, 1 ); // Remove the name
		members_list = members_list_array.join( ',', members_list_array );


		iracing_add_drivers_to_league_post(
			'/membersite/member/SendLeagueRequest',
			{
				custid: items.member_id,
				leagueid: items.league_id
			}
		);

		chrome.storage.sync.set({
			members_list: members_list,
		}, function() {
			// ...
		});

	}

});
