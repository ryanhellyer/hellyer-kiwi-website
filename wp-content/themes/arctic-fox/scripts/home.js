jQuery(function($){

	// Scroll to top
	$("#to-top").click(function(){
		$("html, body").animate({ scrollTop:0}, "slow");
	});

	// Main menu button anchor links
	$(".menu-item a").click(function(){
		var pageid = $(this).attr("rel");
		var scrollto = $("#post-"+pageid).offset().top;
		var scrollto = scrollto-'100';

		$("html, body").animate({scrollTop:scrollto },"slow");
		return false;
	});

	// Set default plus/minus buttons
	$(".plus .plus-button").html('+');
	$(".double-arrow .plus-button").html('&dArr;');
	$(".single-arrow .plus-button").html('&darr;');
	$(".wedge .plus-button").html('&or;');

	// Plus/minus buttons on click
	$(".plus-button").click(function(){
		if($(this).closest(".last-post").length) {
			$("html, body").animate({ scrollTop: $(document).height() }, "slow");
		}
		var pageid = $(this).attr("rel");
		$("#read-more-"+pageid).toggle('fast', function() {
			if ($("#read-more-"+pageid).is(":visible") == true) {
				$(".plus #plus-button-"+pageid).html('-');
				$(".double-arrow #plus-button-"+pageid).html('&uArr;');
				$(".single-arrow #plus-button-"+pageid).html('&uarr;');
				$(".wedge #plus-button-"+pageid).html('&and;');
			} else {
				$(".plus #plus-button-"+pageid).html('+');
				$(".double-arrow #plus-button-"+pageid).html('&dArr;');
				$(".single-arrow #plus-button-"+pageid).html('&darr;');
				$(".wedge #plus-button-"+pageid).html('&or;');
			}
		});
		return false;
	});

});
