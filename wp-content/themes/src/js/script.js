(function () {

	window.addEventListener(
		'load',
		function (){
			set_featured_news_height();
			set_article_widths();
			set_standings_sidebars();
		}
	);

	/**
	 * Handle clicks.
	 */
	window.addEventListener(
		'click',
		function (e){

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
			}

		}
	);

	window.addEventListener("resize", function() {
		set_featured_news_height();
		set_article_widths();
		set_standings_sidebars();
	});

	function set_featured_news_height() {
		var featured_news = document.getElementById("featured-news");
		featured_news.style.height = ( window.innerHeight * 0.9 ) + "px";
	}

	function set_article_widths() {
		var slide_count, slide_subtraction;

		var sliders = document.getElementsByClassName("slider")

		var i = 0;
		for ( i = sliders.length - 1; i >= 0; i--) {
			slider = sliders[i];

			var inner = slider.getElementsByClassName("slider-inner")[0];
			inner.style.width = "10000%";
			slide_width = slider.clientWidth / slider.dataset.slides;

			var slides = slider.getElementsByClassName("slide");
			slide_count = 0;
			for ( slide_count = slides.length - 1; slide_count >= 0; slide_count--) {
				slide = slides[slide_count];

				if ( window.innerWidth < 768 && slider.dataset.slides < 4) {
					slide.style.width = ( slider.clientWidth * 0.8 ) + "px";
				} else {

					// Dealing with slides that take up double the space
					slide_width_multiplier = 1;
					if ( undefined != slide.dataset.width ) {
						slide_width_multiplier = Number( slide.dataset.width );
					}

					// Subtracting relevant width from each slide (used when margins are present)
					slide_subtraction = 0;
					if ( undefined != slide.dataset.subtract ) {
						slide_subtraction = slide.dataset.subtract;
					}

					// Keeping double slide very big, but others smaller when more than four slides used
					if ( window.innerWidth < 768 && slider.dataset.slides > 4 ) {
						if ( undefined != slide.dataset.width && slide.dataset.width > 1 ) {
							slide.style.width = ( slider.clientWidth * 0.8 ) + "px";
						} else {
							slide.style.width = ( ( slide_width * 1.5 ) - ( slide_subtraction / 3 ) ) + "px";						
						}
					} else {
						slide.style.width = slide_width_multiplier * ( slide_width - ( slide_subtraction / 3 ) ) + "px";						
					}

				}

			}

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