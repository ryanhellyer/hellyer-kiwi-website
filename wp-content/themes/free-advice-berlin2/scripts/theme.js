function set_cookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays*24*60*60*1000));
	var expires = 'expires='+d.toUTCString();
	document.cookie = cname + '=' + cvalue + '; ' + expires;
}

function get_cookie(cname) {
	var name = cname + '=';
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	}
	return '';
}




// This is called with the results from from FB.getLoginStatus().
function statusChangeCallback(response) {
	console.log(response);
	// The response object is returned with a status field that lets the
	// app know the current login status of the person.
	// Full docs on the response object can be found in the documentation
	// for FB.getLoginStatus().
	if (response.status === 'connected') {
		// Logged into your app and Facebook.
		testAPI();
		fab_facebook_discussion();
	} else if (response.status === 'not_authorized') {
		// The person is logged into Facebook, but not your app.
	} else {
		// The person is not logged into Facebook, so we're not sure if
		// they are logged into this app or not.
		document.getElementById('facebook-login').style.display = 'block';
	}
}

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
function checkLoginState() {
	FB.getLoginStatus(function(response) {
		statusChangeCallback(response);
	});
}

if ('' != get_cookie('fb_name')){
	fab_facebook_discussion();
}

if ('' == get_cookie('fb_name')){

	window.fbAsyncInit = function() {
		FB.init({
			appId      : '505104076358306',
			cookie     : true,  // enable cookies to allow the server to access 
													// the session
			xfbml      : true,  // parse social plugins on this page
			version    : 'v2.5' // use graph api version 2.5
		});

		// Now that we've initialized the JavaScript SDK, we call 
		// FB.getLoginStatus().  This function gets the state of the
		// person visiting this page and can return one of three states to
		// the callback you provide.  They can be:
		//
		// 1. Logged into your app ('connected')
		// 2. Logged into Facebook, but not your app ('not_authorized')
		// 3. Not logged into Facebook and can't tell if they are logged into
		//    your app or not.
		//
		// These three cases are handled in the callback function.

		FB.getLoginStatus(function(response) {
			statusChangeCallback(response);
		});

	};


	// Load the SDK asynchronously
	(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = '//connect.facebook.net/en_US/sdk.js';
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));

	// Here we run a very simple test of the Graph API after login is
	// successful.  See statusChangeCallback() for when this call is made.
	function testAPI() {
		console.log('Welcome!  Fetching your information.... ');
		FB.api('/me', function(response) {

			set_cookie('fb_name', response.name, 365);
			set_cookie('fb_id', response.id, 365);
			fab_facebook_discussion();
		});
	}
}

function fab_facebook_discussion() {

	var fab_facebook_login = document.getElementById('facebook-login');
	if (fab_facebook_login != null) {
		fab_facebook_login.style.display = 'none';
	}

	var fab_discussion = document.getElementById('discussion');
	if (fab_discussion != null) {
		fab_discussion.style.display = 'block';
	}

	var fab_author = document.getElementById('author');
	if (fab_author != null) {
		fab_author.value = get_cookie( 'fb_name' );
	}
}




document.addEventListener('click', function (e) {

	if(e.target.id == 'thumb-up') {
		fab_ajax_request('up');
	}

	if(e.target.id == 'thumb-down') {
		fab_ajax_request('down');
	}

	if(e.target.id == 'feedback') {
		document.getElementById('discussion').style.display = 'block';
	}

});


/**
 * Load stuff.
 */
document.addEventListener('DOMContentLoaded', fab_loaded );
function fab_loaded() {
	fab_cookie_check();
}

function fab_cookie_check() {
	if(typeof page_id != 'undefined' && 1!=fab_get_cookie('fab_'+page_id)){
		var fab_ratings_html = `
		Did you find this document helpful?
		<span>
			<a id='thumb-up' href='javascript:void(0)'>Yes</a>
			<a id='thumb-down' href='javascript:void(0)'>No</a>
		</span>`;

		var fab_thumbs = document.getElementById('thumbs');
		if (fab_thumbs != null) {
			fab_thumbs.innerHTML = fab_ratings_html;
		}
	}
}


function fab_get_cookie(cname) {
    var name = cname + '=';
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
    }
    return '';
}

function fab_ajax_request(rating) {
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200) {
			if('Rating successful!'==xhttp.responseText){
				document.getElementById('thumbs').innerHTML = 'Thanks for your feedback :)';

				// Storing cookie
				var d = new Date();
				d.setTime(d.getTime() + (365*24*60*60*1000));
				var expires = 'expires='+d.toUTCString();
				document.cookie = 'fab_'+page_id+'=1; '+expires;
			}
		}
	};
	xhttp.open('POST', fab_home_url+'?rating-'+rating+'='+page_id, true);
	xhttp.send();
}
