/**
 * Generate shortcodes.
 */
(function () {

	window.addEventListener(
		'DOMContentLoaded',
		function (){
			generate_shortcode();
		}
	);

	window.addEventListener(
		'click',
		function (e) {

			if ( null !== document.getElementById('pushpress-schedule-shortcode') ) {
				generate_shortcode();
			}

		}
	);

	function generate_shortcode() {

		var shortcode = '[pushpress-schedule';

		if ( null !== document.getElementById('pushpress-class-type').value ) {
			var class_type = document.getElementById('pushpress-class-type').value;
			if ( '' !== class_type ) {
				shortcode = shortcode + ' class_type="'+class_type+'"';
			}
		}
		if ( null !== document.getElementById('pushpress-calendar-type').value ) {
			var calendar_type = document.getElementById('pushpress-calendar-type').value;
			if ( '' !== calendar_type ) {
				shortcode = shortcode + ' calendar_type="'+calendar_type+'"';
			}
		}
		/*
		if ( null !== document.getElementById('pushpress-post-code').value ) {
			var post_code = document.getElementById('pushpress-post-code').value;
			if ( '' !== post_code ) {
				shortcode = shortcode + ' post_code="'+post_code+'"';
			}
		}
		*/
		if ( null !== document.getElementById('pushpress-coach').value ) {
			var coach = document.getElementById('pushpress-coach').value;
			if ( '' !== coach ) {
				shortcode = shortcode + ' coach="'+coach+'"';
			}
		}
		if ( null !== document.getElementById('pushpress-length').value ) {
			var length = document.getElementById('pushpress-length').value;
			if ( '' !== length ) {
				shortcode = shortcode + ' length="'+length+'"';
			}
		}

		shortcode = shortcode + ']';

		document.getElementById('pushpress-schedule-shortcode').innerHTML = shortcode;

	}

})();
