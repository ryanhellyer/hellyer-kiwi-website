/**
 * Set the hero height.
 */
let hero        = document.getElementById( 'hero' );
let hero_height = window.innerHeight * 0.96;

hero.style.height = hero_height + 'px';

window.onscroll = function() {
	var scroll_from_top = window.scrollY || window.pageYOffset || document.body.scrollTop;

	let background_video = document.getElementById( 'background-video' );
	background_video.style.backgroundPosition = 'center ' + 0.5 * scroll_from_top + 'px';
};















window.addEventListener(
	'load',
	function( event ) {
		//
	}
);
