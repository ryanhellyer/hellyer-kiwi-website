jQuery(document).ready(function($) {

	$( "#mobile-menu" ).prepend( '<button id="hamburger" data-clicked="no">&#9776;</button>' );

	// Show menu on clicking hamburger
	$( '#hamburger' ).click( function() {

		var $this = $( "#mobile-menu " );
		$this.toggleClass( 'activated' );
		$this.next( '#mobile-menu' ).slideToggle( 'fast' );

		if ( '✕' != $('#hamburger').text() ) {
			$('#hamburger').text( '✕' );
			$(this).data('clicked', 'yes');
		} else {
			$('#hamburger').text( '☰' );
			$(this).data('clicked', 'no');
		}

	});

	// Show menu on clicking hamburger
	$( '#mobile-menu' ).click( function(e) {
 
		if ( 'mobile-menu' == e.target.id ) {
			window.location.assign(zeilen_home_url)
		}

	});

});