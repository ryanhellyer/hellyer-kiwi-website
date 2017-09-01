(function () {

	window.addEventListener(
		'load',
		function (){
			set_standings_sidebars();
		}
	);

	/**
	 * Handle clicks.
	 */
	window.addEventListener(
		'click',
		function (e){

			if ( 'NAV' === e.target.tagName && 'main-menu-wrapper' === e.target.id) {
				var menu = e.target.children[0];
				menu.classList.toggle('menu-open');
			} else if ( 'A' !== e.target.tagName ) {
				var menu = document.getElementById( 'main-menu' );
				menu.classList.remove('menu-open');
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

	function set_standings_sidebars() {

		var sidebars = document.getElementsByClassName("other-race");
		var count = 0;
		for ( count = sidebars.length - 1; count >= 0; count--) {
			sidebar = sidebars[count];
			sidebar.style.height = document.getElementById("standings").clientHeight + "px";
		}

	}

})();
