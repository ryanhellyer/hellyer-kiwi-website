// global wp, codeMirrorSettings

document.addEventListener("DOMContentLoaded", function() {
	let editors = document.getElementsByClassName( 'code-editor' );

	if ( editors.length > 0 ) {
		for( var i = 0; i < editors.length; i++ ) {
			var editor = editors[ i ];
			wp.codeEditor.initialize( editor, codeMirrorSettings );
		}
	}

	let copyLinks = document.querySelectorAll( '[data-copy]' );

	for( var i = 0; i < copyLinks.length; i++ ) {
		var elem = copyLinks[ i ];
		elem.onclick = function( event ) {
			copyTextToClipboard( event.target.getAttribute( 'data-copy' ) );
		};
	}

	let tags    = document.getElementById( 'template-tags' ),
	    tagsTop = tags.offsetTop - 13;

	// When the user scrolls the page, execute myFunction
	window.onscroll = function() {
		if ( window.pageYOffset > tagsTop ) {
			tags.classList.add( 'sticky' );
			tags.style.width = ( tags.parentElement.clientWidth - 24 ) + 'px';
		} else {
			tags.classList.remove( 'sticky' );
			tags.style.width = 'auto';
		}
	};
});

function copyTextToClipboard( text ) {
	var textArea   = document.createElement("textarea");
	textArea.value = text;

	document.body.appendChild(textArea);
	textArea.focus();
	textArea.select();

	document.execCommand('copy');

	document.body.removeChild(textArea);
}