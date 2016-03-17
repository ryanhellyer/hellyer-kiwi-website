/**
 * Adding "scrolled" class to body when not at top.
 */
document.addEventListener("scroll", zeilen_scroll);
function zeilen_scroll() {
	var body_class = document.getElementsByTagName('body')[0].className;

	var bodyScrollTop = document.documentElement.scrollTop || document.body.scrollTop;
	if ( 0 < bodyScrollTop ) {
		if (body_class.indexOf('scrolled') ==-1) {
			document.getElementsByTagName('body')[0].className+=' scrolled';
		}

	} else {
		if (body_class.indexOf('scrolled') ==-1) {
//			document.getElementsByTagName('body')[0].className+=' scrolled';
		} else {
			document.getElementsByTagName('body')[0].className = body_class.replace(' scrolled', '');
		}
	}
}