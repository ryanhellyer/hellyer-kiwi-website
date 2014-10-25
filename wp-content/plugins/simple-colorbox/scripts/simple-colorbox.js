jQuery(function($){
	$(".colorbox").colorbox({
		iframe:true,
	});

	$(window).resize(function() {
		simple_colorbox_resize();
	});
	simple_colorbox_resize();

	function simple_colorbox_resize() {
		if ( $(window).width() < 640) {
			$( "#colorbox" ).addClass( "mobile-width" );
			$("a[href$='jpg'],a[href$='jpeg'],a[href$='png'],a[href$='bmp'],a[href$='gif'],a[href$='JPG'],a[href$='JPEG'],a[href$='PNG'],a[href$='BMP'],a[href$='GIF']").colorbox({
				maxWidth:'100%',
				maxHeight:'100%',
			});
		} else {
			$( "#colorbox" ).removeClass( "mobile-width" );
			$("a[href$='jpg'],a[href$='jpeg'],a[href$='png'],a[href$='bmp'],a[href$='gif'],a[href$='JPG'],a[href$='JPEG'],a[href$='PNG'],a[href$='BMP'],a[href$='GIF']").colorbox({
				maxWidth:colorbox.width+'%',
				maxHeight:colorbox.height+'%',
			});
		}
	}

});

jQuery.extend(jQuery.colorbox.settings, {
	transition: colorbox.transition,
	speed: colorbox.speed,
	href: colorbox.href,
	title: colorbox.title,
	rel: colorbox.rel,
	scalePhotos: colorbox.scalephotos,
	scrolling: colorbox.scrolling,
	opacity: colorbox.opacity,
	open: colorbox.open,
	returnFocus: colorbox.returnfocus,
	trapFocus: colorbox.trapfocus,
	fastIframe: colorbox.fastiframe,
	preloading: colorbox.preloading,
	overlayClose: colorbox.overlayclose,
	escKey: colorbox.esckey,
	arrowKey: colorbox.arrowkey,
	loop: colorbox.loop,
	data: colorbox.data,
	className: colorbox.classname,
	fadeOut: colorbox.fadeout,
	closeButton: colorbox.closebutton,
	current: colorbox.current,
	previous: colorbox.previous,
	next: colorbox.next,
	close: colorbox.close,
	xhrerror: colorbox.xhrerror,
	imgerror: colorbox.imgerror,
	iframe: colorbox.iframe,
	inline: colorbox.inline,
	html: colorbox.html,
	photo: colorbox.photo,
	/*
	width: colorbox.width+'%',
	height: colorbox.height+'%',
	*/
	innerWidth: colorbox.innerwidth+'%',
	innerHeight: colorbox.innerheight+'%',
	initialWidth: colorbox.initialwidth+'%',
	initialHeight: colorbox.initialheight+'%',
	maxWidth: colorbox.maxwidth+'%',
	maxHeight: colorbox.maxheight+'%',
	slideshow: colorbox.slideshow,
	slideshowSpeed: colorbox.slideshowspeed,
	slideshowAuto: colorbox.slideshowauto,
	slideshowStart: colorbox.slideshowstart,
	slideshowStop: colorbox.slideshowstop,
	fixed: colorbox.fixed,
	/*
	top: colorbox.top,
	bottom: colorbox.bottom,
	left: colorbox.left,
	right: colorbox.right,
	*/
	reposition: colorbox.reposition,
	retinaImage: colorbox.retinaimage,
	retinaUrl: colorbox.retinaurl,
	retinaSuffix: colorbox.retinasuffix,
	onOpen: colorbox.onopen,
	onLoad: colorbox.onload,
	onComplete: colorbox.oncomplete,
	onCleanup: colorbox.oncleanup,
	onClosed: colorbox.onclosed,
});
