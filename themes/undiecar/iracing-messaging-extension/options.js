
function members_list_save_options() {
	var members_list = document.getElementById('members_list').value;
	var subject = document.getElementById('subject').value;
	var message = document.getElementById('message').value;
	var delay = document.getElementById('delay').value;


	chrome.storage.sync.set({
		members_list: members_list,
		subject: subject,
		message: message,
		delay: delay,
	}, function() {
		// Update status to let user know options were saved.
		var status = document.getElementById('status');
		status.textContent = 'Settings saved.';
		setTimeout(function() {
			status.textContent = '';
		}, 750);
	});
}
document.getElementById('save').addEventListener('click', members_list_save_options);


(function () {

	document.addEventListener(
		'DOMContentLoaded',
		function (){

			// Add names to textarea
			chrome.storage.sync.get({
				members_list: '',
				subject: '',
				message: '',
				delay: '',
			}, function(items) {
				document.getElementById('members_list').innerHTML = items.members_list;
				document.getElementById('subject').innerHTML = items.subject;
				document.getElementById('message').innerHTML = items.message;
				document.getElementById('delay').innerHTML = items.delay;
			});

			// Process data
			chrome.storage.sync.get({
				members_list: '',
			}, function(items) {

				var name_to_remove = 'Ryan Hellyer';
				var members_list_array = items.members_list.split(",");

				for (i = 0; i < members_list_array.length; i++) { 

					if ( name_to_remove === members_list_array[i] ) {
						 members_list_array.splice( name_to_remove, 1 ); // Remove the name
					}

					members_list = members_list_array.join( ',', members_list_array );

				}

				console.log( members_list );

			});

		}
	);

})();
