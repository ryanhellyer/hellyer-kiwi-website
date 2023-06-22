window.addEventListener(
	'load',
	function( event ) {

		/**
		 * Do front page specific stuff.
		 */
		if ( document.body.classList.contains( 'front-page' ) ) {

			make_header_longer();
			window.addEventListener( 'resize', function( event ) {
				make_header_longer();
			} );

		}

		/**
		 * Make header longer if the content in it is longer than the window height.
		 */
		function make_header_longer() {
			const header_internals = document.querySelector( '#header .container' );
			const window_height    = isNaN( window.innerHeight) ? window.clientHeight : window.innerHeight;
			if ( window_height < header_internals.offsetHeight ) {
				const header = document.getElementById( 'header' );
				header.style.height = ( header_internals.offsetHeight + 80 ) + 'px';
			}
		}

	}
);
