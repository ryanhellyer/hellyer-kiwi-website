
// Add data
if ( window.location.href === 'http://members.iracing.com/jforum/pm/send.page' ) {

	// Process data
	chrome.storage.sync.get({
		members_list: '',
		subject: '',
		message: '',
		delay: '',
	}, function(items) {

		var members_list_array = items.members_list.split(",");

		// If empty members list, then bail out
		items.members_list.replace(/\s+/, "") 
		if ( '' === items.members_list ) {
			return items;
		}

		var member_name = members_list_array[0];

		var random_time_between_2_and_10_secs = 1000 + ( Math.random() * 3000 );

		setTimeout( function() {

			var shortened_name = member_name.split( ' ' );
			shortened_name = shortened_name[0];

			// Cater to weird names
			if ( 'A' === shortened_name ) {
				shortened_name = 'A J';
			}

			// Process shortcodes
			var message = items.message;
			message = message.replace( '[NAME]', shortened_name );

			document.getElementsByName('toUsername' )[0].value = member_name;
			document.getElementsByName('subject')[0].value = items.subject;
			document.getElementsByName('message')[0].value = message;

			members_list_array.splice( member_name, 1 ); // Remove the name
			members_list = members_list_array.join( ',', members_list_array );

			chrome.storage.sync.set({
				members_list: members_list,
			}, function() {
				// ...
			});

		}, items.delay * 1000 );

	});

	// Check every 1 second if the 
	setInterval( function () {

		if ( '' != document.getElementsByName('toUsername' )[0].value ) {
//			var submit_button = document.getElementById('btnSubmit').click();
		}

	}, 1000);

}

// Redirect back
if ( window.location.href === 'http://members.iracing.com/jforum/jforum.page' ) {
	window.location.href = 'http://members.iracing.com/jforum/pm/send.page';
}
