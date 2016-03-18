jQuery(document).ready(function($){
	if ( $(window).width() < 800) {
		$('body').addClass('mobile');

		var width = $('body').width();
		var height = 0.906666667 * width;

		$('.related-posts article').css( 'height', height+'px' );
		$('#featured-featured-image').css( 'height', window.outerHeight+'px' );

	}
	else {
		$('body').removeClass('mobile');		
	}
});
