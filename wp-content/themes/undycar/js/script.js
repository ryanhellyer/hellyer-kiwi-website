(function () {

	window.addEventListener(
		'load',
		function (){
			set_featured_news_height();
			set_standings_sidebars();
		}
	);

	/**
	 * Handle clicks.
	 */
	window.addEventListener(
		'click',
		function (e){

			// Applications topic - transfers custom selections to a normal topic submission
			if ( 'bbp_topic_submit' === e.target.id ) {

				var src_drive_name  = document.getElementById( 'src-driver-name' );
				var src_team_name   = document.getElementById( 'src-team-name' );
				var src_vehicle     = document.getElementById( 'src-vehicle' );
				var src_description = document.getElementById( 'src-description' );
				var src_number      = document.getElementById( 'src-number' );

				var src_topic_title = document.getElementById( 'bbp_topic_title' );
				var src_topic_textarea = document.getElementById( 'bbp_topic_content' );
				src_topic_title.value = 'driver name';
				src_topic_textarea.innerHTML = 'Team name: ' + src_team_name.value + "\n\nVehicle: " + src_vehicle.value + "\n\nDescription: " + src_description.value + "\n\nNumber: " + src_number.value;

				//e.preventDefault();
				//e.stopPropagation();
			}

			// Menu button click
			var main_menu_wrap = document.getElementById( 'main-menu-wrap' );
			if (
				'main-menu-wrap' === e.target.id
				&&
				'open' != main_menu_wrap.className
			) {
				main_menu_wrap.classList.add('open');
			} else if (
				'open' === main_menu_wrap.className
				||
				'main-menu-wrap' === e.target.id
			) {
				main_menu_wrap.classList.remove('open');
			} else if (
				'add-a-photo' === e.target.id
			) {
				var gallery_uploader = document.getElementById( 'gallery-uploader' );
				document.getElementById( 'gallery-uploader' ).className = 'clicked';

				e.preventDefault()
			}

		}
	);

	window.addEventListener("scroll", function() {
		var featured_news = document.getElementById("featured-news");
		var scroll_from_top = window.scrollY || window.pageYOffset || document.body.scrollTop;

		if ( null !== featured_news ) {
			featured_news.style.backgroundPosition = 'center ' + 0.5 * scroll_from_top + 'px';
		}

	});

	window.addEventListener("resize", function() {
		set_featured_news_height();
		set_standings_sidebars();
	});

	// add keydown event listener
	var realtrek_position = pink27_position = konami_position = 0;
	document.addEventListener('keydown', function(e) {

		// a key map of allowed keys
		var allowedKeys = {
			37: 'left',
			38: 'up',
			39: 'right',
			40: 'down',
			48: '0',
			49: '1',
			50: '2',
			51: '3',
			52: '4',
			53: '5',
			54: '6',
			55: '7',
			56: '8',
			57: '9',
			65: 'a',
			66: 'b',
			67: 'c',
			68: 'd',
			69: 'e',
			70: 'f',
			71: 'g',
			72: 'h',
			73: 'i',
			74: 'j',
			75: 'k',
			76: 'l',
			77: 'm',
			78: 'n',
			79: 'o',
			80: 'p',
			81: 'q',
			82: 'r',
			83: 's',
			84: 't',
			85: 'u',
			86: 'v',
			87: 'w',
			88: 'x',
			89: 'y',
			90: 'z',
		};

		// Konami code
		var code = ['up', 'up', 'down', 'down', 'left', 'right', 'left', 'right', 'b', 'a'];
		var key = allowedKeys[e.keyCode];
		var requiredKey = code[konami_position];
		if (key == requiredKey) {
			konami_position++;
			if (konami_position == code.length) {
				window.location = "https://www.youtube.com/watch?v=-IJIa-OFN0s";
			}
		} else {
			konami_position = 0;
		}

		// "pink27" code
		var code = ['p','i','n','k','2','7'];
		var key = allowedKeys[e.keyCode];
		var requiredKey = code[pink27_position];
		if (key == requiredKey) {
			pink27_position++;
			if (pink27_position == code.length) {
				window.location = "https://www.youtube.com/watch?v=20zmyPSeXkM";
			}
		} else {
			pink27_position = 0;
		}

		// "realtrek" code
		var code = ['r','e','a','l','t','r','e','k'];
		var key = allowedKeys[e.keyCode];
		var requiredKey = code[realtrek_position];
		if (key == requiredKey) {
			realtrek_position++;
			if (realtrek_position == code.length) {
				window.location = "http://vid.pr0gramm.com/2017/06/08/6ea70e427f5ad989.mp4";
			}
		} else {
			realtrek_position = 0;
		}

	});

	function set_featured_news_height() {
return;
		var featured_news = document.getElementById("featured-news");

		if ( null !== featured_news ) {
			featured_news.style.height = ( window.innerHeight * 0.5 ) + "px";
		}
	}

	function set_standings_sidebars() {

		var sidebars = document.getElementsByClassName("other-race");
		var count = 0;
		for ( count = sidebars.length - 1; count >= 0; count--) {
			sidebar = sidebars[count];
			sidebar.style.height = document.getElementById("standings").clientHeight + "px";
		}

	}

})();
