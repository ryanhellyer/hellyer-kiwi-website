jQuery(function($){

// Scroll to top
$("#to-top").click(function(){
	$("html, body").animate({ scrollTop:0}, "slow");
});

// Main menu button anchor links
$(".menu-item a").click(function(){
	var pageid = $(this).attr("rel");
	var scrollto = $("#post-"+pageid).offset().top;
	$("html, body").animate({scrollTop:scrollto },"slow");
	return false;
});

// Plus/minus buttons on pages
$(".plus-button").click(function(){
	var pageid = $(this).attr("rel");
	$("#read-more-"+pageid).slideDown();
	$("#plus-button-"+pageid).css("display","none");
	$("#minus-button-"+pageid).css("display","block");

	return false;
});
$(".minus-button").click(function(){
	var pageid = $(this).attr("rel");
	$("#read-more-"+pageid).slideUp();
	$("#minus-button-"+pageid).css("display","none");
	$("#plus-button-"+pageid).css("display","block");
	return false;
});

// More gap hover
$(".more-gap-button").hover(function(){
	var pageid = $(this).attr("rel");
	$("#more-gap-"+pageid).slideDown();
});
$(".more-gap-button").mouseout(function(){
	var pageid = $(this).attr("rel");
	var readmore = $("#read-more-"+pageid).css("display");
	if ( readmore == "none" ) {
		$("#more-gap-"+pageid).slideUp();
	}
});

});
