var template = wp.template( 'strattic-search-template' ); // uses script tag ID minus "tmpl-"

var res="";

function getQueryVariable(variable) {
	var query = window.location.search.substring(1);
	var vars = query.split("&");
	for (var i=0;i<vars.length;i++) {
		var pair = vars[i].split("=");
		if (pair[0] == variable) {
			return pair[1];
		}
	}
	console.log('Query Variable ' + variable + ' not found');
}

function urldecode(str) {
	 return decodeURIComponent((str+'').replace(/\+/g, '%20'));
}

function fSearch() {
	var result = fuse.search(search_string)

	var count= Object.keys(result).length;
	var stxt = urldecode(search_string);
	if (Object.keys(result).length > 0){
		document.getElementById("strattic-search-title").textContent="Search Results for: "+ stxt;
		result.sort(function(a,b){return new Date(b.rdate) - new Date(a.rdate) });
		for (i = 0; i < Object.keys(result).length; i++) {

			var template_data = {
				title: result[i].title,
				text: "some text goes here",
				url: result[i].url,
				readmore: 'the readmore'
			};
			var html = template( template_data );
			res = res.concat(html);

		}
	}
	else {
		document.getElementById("strattic-search-title").textContent="No results found for: "+ stxt;
	}

	document.getElementById("strattic-search-results").innerHTML=res;
}
var search_string = getQueryVariable("s");
/*
console.log(strattic_search_settings);
strattic_search_settings = {
	"minMatchCharLength":"1",
	"shouldSort":"true",
	"keys":[
		"title",
		"author.firstName"
	],
	"location":"0",
	"threshold":"0",
	"distance":"100",
	"maxPatternLength":"32"
};
*/
console.log(strattic_search_settings);

var fuse = new Fuse(data, strattic_search_settings)
var result = fuse.search( 'a' );

fSearch(search_string)
