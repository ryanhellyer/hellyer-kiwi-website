(function () {

	window.addEventListener(
		'load',
		function (){
//			var tab_2 = document.getElementById("tab-2");
//			tab_2.style.display = 'block';

			set_standings_sidebars();
			gallery_fields();



//			<script>document.write('<style>.tabberlive {display: block;}</style>');</script>


		}
	);


	/**
	 * Removes all option elements in select box.
	 */
	function remove_all_options(sel) {
		var len, groups, par;

		len = sel.options.length;
		for (var i=len; i; i--) {
			par = sel.options[i-1].parentNode;
			par.removeChild( sel.options[i-1] );
		}
	}

	/**
	 * Modify the gallery fields.
	 */
	function gallery_fields() {

		var undiecar_season_field = document.getElementById('undiecar-season');
		undiecar_season_field.addEventListener(
			"change",
			function(e) {
				var xhttp = new XMLHttpRequest();

				xhttp.onreadystatechange = function() {

					if ( "" !== this.response ) {
						var undiecar_events = JSON.parse( this.response );

						var undiecar_events_field = document.getElementById('undiecar-event');

						// Remove all existing options
						remove_all_options(undiecar_events_field, true);

						var opt = document.createElement('option');
						opt.value = '';
						opt.innerHTML = 'Uknown';
						undiecar_events_field.appendChild(opt);

						// Add some options
						var number_of_events = undiecar_events.length;
						for (var i = 0; i < number_of_events; i++) {
							var opt = document.createElement('option');
							opt.value = undiecar_events[i].ID;
							opt.innerHTML = undiecar_events[i].post_title;
							undiecar_events_field.appendChild(opt);
						}

					}
				}

				var selected_index = e.target.selectedIndex;
				var season_id = e.target[selected_index].value;

				xhttp.open('GET', undiecar_home_url + '/wp-json/undiecar/v1/events_in_season?season_id=' + season_id, true);
				xhttp.send();
			}
		);

	}



	/**
	 * Handle clicks.
	 */
	window.addEventListener(
		'click',
		function (e){
			handle_clicks(e);
		}
	);
	window.addEventListener(
		'touchstart', /* handling iOS devices */
		function (e){
			handle_clicks(e);
		}
	);

	function handle_clicks(e) {

		if ( 'NAV' === e.target.tagName && 'main-menu-wrapper' === e.target.id ) {
			var menu = e.target.children[0];
			menu.classList.toggle('menu-open');

			e.preventDefault();
			e.stopPropagation()

		} else if ( 'A' !== e.target.tagName ) {
			var menu = document.getElementById( 'main-menu' );
			menu.classList.remove('menu-open');

			e.preventDefault();
			e.stopPropagation()

		}

		// If 'Another Driver' button is clicked
		if ( 'another-driver' === e.target.id ) {

			var undiecar_driver_field = document.createElement('input');
			undiecar_driver_field.type = 'text';
			undiecar_driver_field.name = 'undiecar-driver[]';
			var element = document.getElementById('undiercar-driver-form-input-fields');
			element.appendChild(undiecar_driver_field);

			e.preventDefault();
			e.stopPropagation()
		}

	}

	window.addEventListener("scroll", function() {
		var featured_news = document.getElementById("featured-news");
		var scroll_from_top = window.scrollY || window.pageYOffset || document.body.scrollTop;

		if ( null !== featured_news ) {
			featured_news.style.backgroundPosition = 'center ' + 0.5 * scroll_from_top + 'px';
		}

	});

	window.addEventListener("resize", function() {
//		set_featured_news_height();
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
