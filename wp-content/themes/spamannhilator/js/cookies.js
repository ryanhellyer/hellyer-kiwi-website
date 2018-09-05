(function () {

	window.addEventListener(
		'load',
		function (){

			cookie_notice();
		}
	);

	/**
	 * Handle clicks.
	 */
	window.addEventListener(
		'click',
		function (e){

			// Accept cookies by clicking on the banner
			if (
				'cookie-notice' === e.target.id
				||
				'close-cookie-notice' === e.target.id
			) {
				accept_cookies();
			}

		}
	);

	/**
	 * Generate cookie notice.
	 */
	function cookie_notice() {

		// Only show cookie notice if cookie not already accepted
		if ( 'yes' !== get_cookie( 'cookie_accepted' ) ) {

			var cookie_notice = document.getElementById( 'cookie-notice' );
			cookie_notice.classList = '';
			cookie_notice.innerHTML = cookie_notice_text;

			/**
			 * After a set period of time, we assume they have accepted cookies.
			 * This system is based on advice from https://privacypolicies.com/blog/eu-cookie-law/
			 */
			setTimeout(
				function() {

					accept_cookies();

				},
				10000
			);

		}

	}

	/**
	 * Users has accepted the cookies.
	 */
	function accept_cookies() {

		// Hide the cookie notice
		var cookie_notice = document.getElementById( 'cookie-notice' );
		cookie_notice.classList = 'hidden';

		// Create cookie
		var date = new Date();
		var year_in_milliseconds = ( 10 * 365 * 24 * 60 * 60 * 1000 );
		date.setTime( date.getTime() + year_in_milliseconds );
		var expires = date.toUTCString();
		document.cookie = 'cookie_accepted=yes expires=' + expires; 

	}


	/**
	 * Get cookie.
	 *
	 * @param  string   name   The cookie name
	 */
	function get_cookie( name ) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) {
				var cookie = c.substring(nameEQ.length,c.length);
				var split_cookie = cookie.split( ' ' );
				return split_cookie[ 0 ];
			}
		}
		return null;
	}

})();