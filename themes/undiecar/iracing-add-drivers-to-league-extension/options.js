
function members_list_save_options() {
	var members_list = document.getElementById('members_list').value;
	var league_id = document.getElementById('league_id').value;

	chrome.storage.sync.set({
		members_list: members_list,
		league_id: league_id,
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

			// Process data
			chrome.storage.sync.get({
				members_list: '',
				league_id: '',
			}, function(items) {
				document.getElementById('members_list').innerHTML = items.members_list;
				document.getElementById('league_id').innerHTML = items.league_id;

				var members_list_array = items.members_list.split(",");

				for (i = 0; i < members_list_array.length; i++) { 

					members_list_array.splice( name_to_remove, 1 ); // Remove the name

					members_list = members_list_array.join( ',', members_list_array );

				}

				console.log( members_list );

			});

		}
	);

})();
