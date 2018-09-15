
function members_list_save_options() {
	var subsessions = document.getElementById('subsessions').value;


	chrome.storage.sync.set({
		subsessions: subsessions,
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
				subsessions: '',
			}, function(items) {
				document.getElementById('subsessions').innerHTML = items.subsessions;
			});

			// Process data
			chrome.storage.sync.get({
				subsessions: '',
			}, function(items) {

				var subsessions_array = items.subsessions.split(",");

				for (i = 0; i < subsessions_array.length; i++) { 

					subsessions = subsessions_array.join( ',', subsessions_array );

				}

				console.log( subsessions );

			});

		}
	);

})();
