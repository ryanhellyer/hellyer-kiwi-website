function strattic_start_publishing() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4

			if (xmlhttp.status == 200) {
			} else if (xmlhttp.status == 400) {
				alert('There was an error 400');
			}
		}
	};

	xmlhttp.open('GET', strattic_ajax['url'] + 'stage1', true);
	xmlhttp.send();

	var update = setInterval(strattic_query_status, 1000);
	function strattic_query_status() {
		p = document.getElementById('progress')
		s = document.getElementById('status')

		var xhReq = new XMLHttpRequest();
		xhReq.open('GET', strattic_ajax['url'] + 'redis', false);
		xhReq.send(null);

		var serverResponse = xhReq.responseText;
		s.innerHTML = serverResponse+'%'
		p.value = serverResponse;

		if (serverResponse == 100){
			document.getElementById('finalMsg').innerHTML = strattic_final_message;
		}
		else {
			document.getElementById('finalMsg').innerHTML = '';
		}
	}
}