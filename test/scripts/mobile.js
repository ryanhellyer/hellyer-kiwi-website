jQuery(document).ready(function($){
	if ( $(window).width() < 800) {
		$('body').addClass('mobile');
	}
	else {
		$('body').removeClass('mobile');		
	}
});