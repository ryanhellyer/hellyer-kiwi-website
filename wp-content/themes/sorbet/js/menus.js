jQuery(document).ready(function($) {

	function navMenu() {

		var sidebarToggle = $('#sidebar-toggle');
		var menuToggle = $('#menu-toggle');
		var socialLinksToggle = $('#social-links-toggle');
		var searchToggle = $('#search-toggle');

		var socialLinksNav = $('#social-links-toggle-nav');
		var sidebarNav = $('#sidebar-toggle-nav');
		var searchNav = $('#search-toggle-nav');
		var menuNav = $('#menu-toggle-nav');

		function myToggleClass( $myvar ) {
			if ( $myvar.hasClass( 'active' ) ) {
				$myvar.removeClass( 'active' );
			} else {
				$myvar.addClass('active');
			}
		}

		// Display/hide sidebar
		sidebarToggle.on('click', function() {
			sidebarNav.slideToggle();
			myToggleClass($(this));

			// Remove mejs players from sidebar
			$( '#sidebar-toggle-nav .mejs-container' ).each( function( i, el ) {
				if ( mejs.players[ el.id ] ) {
					mejs.players[ el.id ].remove();
				}
			} );

			socialLinksNav.hide();
			menuNav.hide();
			searchNav.hide();

			searchToggle.removeClass('active');
			menuToggle.removeClass('active');
			socialLinksToggle.removeClass('active');

			if ( ! sidebarNav.hasClass( 'active' ) ) {
				// Re-initialize mediaelement players.
				setTimeout( function() {
					if ( window.wp && window.wp.mediaelement ) {
						window.wp.mediaelement.initialize();
					}
				} );

				// Trigger resize event to display VideoPress player.
				setTimeout( function(){
					if ( typeof( Event ) === 'function' ) {
						window.dispatchEvent( new Event( 'resize' ) );
					} else {
						var event = window.document.createEvent( 'UIEvents' );
						event.initUIEvent( 'resize', true, false, window, 0 );
						window.dispatchEvent( event );
					}
				} );
			}

			$('#sidebar-toggle-nav').resize();
		});
		// Display/hide social links
		socialLinksToggle.on('click', function() {
			socialLinksNav.slideToggle();
			myToggleClass($(this));

			menuNav.hide();
			searchNav.hide();
			sidebarNav.hide();

			searchToggle.removeClass('active');
			menuToggle.removeClass('active');
			sidebarToggle.removeClass('active');
		});
		// Display/hide menu
		menuToggle.on('click', function() {
			menuNav.slideToggle();
			myToggleClass($(this));

			container = document.getElementById( 'site-navigation' );

			// Fix child menus for touch devices.
			function fixMenuTouchTaps( container ) {
				var touchStartFn,
					parentLink = container.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

				if ( 'ontouchstart' in window ) {
					touchStartFn = function( e ) {
						var menuItem = this.parentNode;

						if ( ! menuItem.classList.contains( 'focus' ) ) {
							e.preventDefault();
							for( var i = 0; i < menuItem.parentNode.children.length; ++i ) {
								if ( menuItem === menuItem.parentNode.children[i] ) {
									continue;
								}
								menuItem.parentNode.children[i].classList.remove( 'focus' );
							}
							menuItem.classList.add( 'focus' );
						} else {
							menuItem.classList.remove( 'focus' );
						}
					};

					for ( var i = 0; i < parentLink.length; ++i ) {
						parentLink[i].addEventListener( 'touchstart', touchStartFn, false )
					}
				}
			}

			fixMenuTouchTaps( container );

			searchNav.hide();
			sidebarNav.hide();
			socialLinksNav.hide();

			searchToggle.removeClass('active');
			sidebarToggle.removeClass('active');
			socialLinksToggle.removeClass('active');
		});
		// Display/hide search
		searchToggle.on('click', function() {
			searchNav.slideToggle();
			myToggleClass($(this));

			sidebarNav.hide();
			socialLinksNav.hide();
			menuNav.hide();

			sidebarToggle.removeClass('active');
			menuToggle.removeClass('active');
			socialLinksToggle.removeClass('active');
		});
	}
	$(window).on('load', navMenu);
} );
