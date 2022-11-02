/**
 * The Strattic Widgets JS
 *
 * @package Strattic Widgets
 * @author Strattic
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link https://www.strattic.com/
 */

window.addEventListener(
	'load',
	function( event ) {
		xhttp = new XMLHttpRequest();

		xhttp.onreadystatechange = function () {
			if ( this.readyState == 4 ) {
				if ( this.status == 200 ) {
					const ajax_areas   = document.getElementsByClassName( 'strattic-ajax-area' );

					const number_of_ajax_areas = ajax_areas.length;
					for ( let i = 0; i < number_of_ajax_areas; i++ ) {
						const ajax_block = ajax_areas[ 0 ];
						const ajax_area  = ajax_block.dataset.ajaxArea;
						const response   = JSON.parse( this.responseText );

						if ( undefined !== response[ ajax_area ] ) {
							ajax_block.outerHTML = response[ ajax_area ];
						}

					}
				}
			}
		};
		xhttp.open( 'GET', '/strattic-ajax-area/', true );
		xhttp.send();
	}
);
