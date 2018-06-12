
function strattic_start_publishing() {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4

			if (xmlhttp.status == 200) {
				//document.getElementById("myDiv").innerHTML = xmlhttp.responseText;
			} else if (xmlhttp.status == 400) {
				alert('There was an error 400');
			}
		}
	};

	xmlhttp.open("GET", strattic_plugin_url + 'stage1.php', true);
	xmlhttp.send();

	var update = setInterval(strattic_query_status, 1000);
	function strattic_query_status() {
		p = document.getElementById("progress")
		s = document.getElementById("status")

		var xhReq = new XMLHttpRequest();
		xhReq.open("GET", "/redis.php", false);
		xhReq.send(null);

		var serverResponse = xhReq.responseText;

		s.innerHTML = serverResponse+"%"
		p.value = serverResponse;

		if (serverResponse == 100){
			document.getElementById("finalMsg").innerHTML = strattic_final_message;
		}
		else {
			document.getElementById("finalMsg").innerHTML = "";
		}
	}
}
