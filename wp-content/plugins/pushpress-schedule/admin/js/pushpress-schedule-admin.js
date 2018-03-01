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
		function (e){

			// Check if clicking on form
			if (
				'pushpress-schedule-shortcode-generator' === e.target.parentNode.parentNode.parentNode.parentNode.parentNode.id 
				||
				'pushpress-schedule-shortcode-generator-submit' === e.target.id
			) {
				generate_shortcode();
			}

		}
	);

	function generate_shortcode() {

		var shortcode = '[pushpress_schedule';

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
		if ( null !== document.getElementById('pushpress-post-code').value ) {
			var post_code = document.getElementById('pushpress-post-code').value;
			if ( '' !== post_code ) {
				shortcode = shortcode + ' post_code="'+post_code+'"';
			}
		}
		if ( null !== document.getElementById('pushpress-coach').value ) {
			var coach = document.getElementById('pushpress-coach').value;
			if ( '' !== coach ) {
				shortcode = shortcode + ' coach="'+coach+'"';
			}
		}
		if ( null !== document.getElementById('pushpress-day').value ) {
			var day = document.getElementById('pushpress-day').value;
			if ( '' !== day ) {
				shortcode = shortcode + ' day="'+day+'"';
			}
		}
		if ( null !== document.getElementById('pushpress-week').value ) {
			var week = document.getElementById('pushpress-week').value;
			if ( '' !== week ) {
				shortcode = shortcode + ' week="'+week+'"';
			}
		}
		if ( null !== document.getElementById('pushpress-month').value ) {
			var month = document.getElementById('pushpress-month').value;
			if ( '' !== month ) {
				shortcode = shortcode + ' month="'+month+'"';
			}
		}

		shortcode = shortcode + ']';

		document.getElementById('pushpress-schedule-shortcode').innerHTML = shortcode;

	}

})();
