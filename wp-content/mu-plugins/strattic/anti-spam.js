/**
 * Load anti-spam payload.
 */
(function () {

	/**
	 * Decrypt the anti-spam payload.
	 */
	window.addEventListener(
		'load',
		function (){

			var blob_value;
			var input_fields = get_input_fields();
			var i;
			for (i = 0; i < input_fields.length; i++) {

				decrypted_value = base64_decode( strattic_anti_spam );

				input_fields[i].value = strattic_anti_spam + ':' + decrypted_value;
			}

		}
	);

	/**
	 * Get the required input fields from the page.
	 */
	function get_input_fields() {
		var the_input_fields = [];
		var count = 0;
		var input_fields = document.getElementsByTagName('input');
		for (var i = 0, n = input_fields.length; i < n; i++) {

			if ( 'strattic-anti-spam' === input_fields[i].getAttribute('name') ) {

				the_input_fields[count] = input_fields[i];

				count++
			}

		}
		return the_input_fields;
	}

	/**
	 * Library for base64 decoding.
	 * This works in older browsers.
	 * Modified from http://ntt.cc/2008/01/19/base64-encoder-decoder-with-javascript.html
	 */
	function base64_decode(input) {

		var keyStr = "ABCDEFGHIJKLMNOP" +
			"QRSTUVWXYZabcdef" +
			"ghijklmnopqrstuv" +
			"wxyz0123456789+/" +
			"=";

		var output = "";
		var chr1, chr2, chr3 = "";
		var enc1, enc2, enc3, enc4 = "";
		var i = 0;

		// remove all characters that are not A-Z, a-z, 0-9, +, /, or =
		var base64test = /[^A-Za-z0-9\+\/\=]/g;
		input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

		do {
			enc1 = keyStr.indexOf(input.charAt(i++));
			enc2 = keyStr.indexOf(input.charAt(i++));
			enc3 = keyStr.indexOf(input.charAt(i++));
			enc4 = keyStr.indexOf(input.charAt(i++));

			chr1 = (enc1 << 2) | (enc2 >> 4);
			chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
			chr3 = ((enc3 & 3) << 6) | enc4;

			output = output + String.fromCharCode(chr1);

			if (enc3 != 64) {
				output = output + String.fromCharCode(chr2);
			}
			if (enc4 != 64) {
				output = output + String.fromCharCode(chr3);
			}

			chr1 = chr2 = chr3 = "";
			enc1 = enc2 = enc3 = enc4 = "";

		} while (i < input.length);

		return unescape(output);
	}

})();