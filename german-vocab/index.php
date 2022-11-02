<?php
//EDITED UP TO 835 SO FAR ... 

declare( strict_types = 1 );

#require( '../../google-config.php' );

if (
	'test-strattic.io' !== $_SERVER['HTTP_HOST']
	&&
	'geek.hellyer.kiwi' !== $_SERVER['HTTP_HOST']
) {
	return;
}

/**
 * Escape string of text.
 * Needs some work ... 
 * 
 * @param string $html The string to escape.
 * @return string The sanitised string. 
 */
function escape_string( $html ) {
	return stripslashes( strip_tags( $html ) );
}


// Get sanitised vocab list.
$json  = file_get_contents( 'german-vocab.json' );
$array = json_decode( $json );
$vocab = array();
foreach ( $array as $key1 => $words ) {
	foreach ( $words as $key2 => $word ) {
		$vocab[ $key1 ][ $key2 ] = escape_string( $word );
	}
}
$json = json_encode( $vocab, JSON_UNESCAPED_UNICODE ); // Unescaped unicode is important to handle umlauts.

/**
 * Generate MP3s from vocab list.
 */
if ( isset( $_GET['generate_mp3s'] ) ) {
	foreach ( $vocab as $key => $words ) {
		foreach ( $words as $lang => $word ) {
			$file_name = md5( $word ) . '.mp3';
			if ( file_exists( 'audio/' . $file_name ) ) {
				continue;
			}

			$command = <<<COMMAND
curl -H 'X-Goog-Api-Key: {{google_api_key}}' \
	-H 'Content-Type: application/json; charset=utf-8' \
	--data '{
		"audioConfig": {
			"audioEncoding": "LINEAR16",
			"pitch": 0,
			"speakingRate": 1
		},
		"input": {
			"text": "{{word}}"
		},
		"voice": {
			"languageCode": "{{lang_code}}",
			"name": "{{lang_name}}",
			"ssmlGender":"{{gender}}"
		},
		"audioConfig":{
			"audioEncoding":"MP3"
		}
	}' 'https://texttospeech.googleapis.com/v1beta1/text:synthesize' > tmp1.txt
COMMAND;
			$command = str_replace( '{{word}}', $word, $command );
			$command = str_replace( '{{google_api_key}}', $google_api_key, $command );

			if ( 'de' === $lang ) {
				$lang_code = 'de-DE';
				$lang_name = 'de-DE-Standard-A';
				$gender    = 'FEMALE';
			} else {
				$lang_code = 'en-US';
				$lang_name = 'en-US-Wavenet-D';
				$gender    = 'MALE';
			}
			$command = str_replace( '{{lang_code}}', $lang_code, $command );
			$command = str_replace( '{{lang_name}}', $lang_name, $command );
			$command = str_replace( '{{gender}}', $gender, $command );
//echo $command;die;

			shell_exec( $command );

			$command = "
				cat tmp1.txt | grep 'audioContent' | \
				sed 's|audioContent| |' | tr -d '\n \":{},' > tmp2.txt && \
				base64 tmp2.txt --decode > audio/" . $file_name . " && \
				rm tmp1.txt && \
				rm tmp2.txt";
			shell_exec( $command );
echo $command;
echo $lang . "\n";
echo $word . "\n";
echo $file_name;
echo "\n\n";
//die;
		}

	}

	die( 'DONE' );
}

?><!DOCTYPE html>
<html lang="en-NZ">
<head>
<meta charset="UTF-8" />
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' />
<style>
h1 {
	width: 96%;
	margin: 15% 2% 0 2%;
	line-height: 1.1em;
	font-size: 250%;
	text-align: center;
	font-family: sans-serif;
}
#back {
	display: block;
	position: fixed;
	bottom: 0;
	width: 20%;
	left: 40%;
	height: 50px;
	text-indent: -999em;
	background: #CC0000;
	border: 1px solid #660000;
	border-bottom: none;
	border-top-left-radius: 50px;
	border-top-right-radius: 50px;
}
#forward {
	display: block;
	position: fixed;
	bottom: 0;
	width: 20%;
	left: 70%;
	height: 50px;
	text-indent: -999em;
	background: #0000CC;
	border: 1px solid #000066;
	border-bottom: none;
	border-top-left-radius: 50px;
	border-top-right-radius: 50px;
}
#time-delay {
	display: block;
	position: fixed;
	bottom: 0;
	width: 30%;
	left: 5%;
	height: 50px;
	text-indent: -999em;
}

#time-delay:hover,
#back:hover,
#forward:hover {
	cursor: pointer;
}

#which-question {
	position: fixed;
	top: 2%;
	left: 2%;
	font-size: 150%;
	text-align: center;
	font-family: sans-serif;
}
small {
	font-size: 60%;
}
#audio {
	display: none;
}
</style>
</head>
<body>

<h1 id="question-text"></h1>
<button id="back">Back</button>
<button id="forward">Forward</button>
<div id="which-question">4 en</div>

<input type="range" id="time-delay" min="1" value="5" max="20" step="1">

<audio id="audio" controls>
	<source src="" type="audio/mpeg">
	Your browser does not support the audio element.
</audio>

<script>

const timestamp = Date.now();
let time_delay = 5;

window.onload=function() {
	const words = <?php echo escape_string( $json ); ?>

	let language       = 'de';
	let question_text  = document.getElementById( 'question-text' );
	let which_question = document.getElementById( 'which-question' );
	let number         = 0;
	if ( null !== get_query_var( 'question' ) ) {
		number = get_query_var( 'question' );
	}

	set_question();
//	wait_a_while( 5 )

	/**
	 * Wait a while before moving to the next question.
	 * 
	 * @param int time_delay The time in seconds to wait.
	 */
	function wait_a_while( time_delay ) {
		console.log( 'time_delay: ' + time_delay );
		console.log( 'timestamp: ' + Math.floor( ( Date.now() - timestamp ) ) / 1000 );

		setTimeout(
			function() {
				forward();
			},
			time_delay * 1000
		);
	}

	/**
	 * Handle click events.
	 */
	window.addEventListener(
		'click',
		function( e ) {

			if ( 'back' === e.target.id ) {
				back();
			} else if ( 'forward' === e.target.id ) {
				forward();
			}
		}
	);

	/**
	 * Handle click events.
	 */
	window.addEventListener(
		'click',
		function( e ) {

			if ( 'time-delay' === e.target.id ) {
				time_delay = e.target.value;
				console.log( time_delay );
//				forward();
			}
		}
	);

	/**
	 * Move back a question.
	 */
	function back() {

		// Bail out if on first question.
		if ( 0 === number ) {
			return;
		}

		language = 'de';
		number = number - 1;
		window.history.pushState(
			'object or string',
			'',
			'/german-vocab/?question=' + number
		);

		set_question();
	}

	/**
	 * Move forward a question.
	 */
	function forward() {

		// If clicking next and already on english, then switch to next question.
		if ( 'en' === language ) {
			language   = 'de';

			number++;
			window.history.pushState(
				'object or string',
				'',
				'/german-vocab/?question=' + number
			);
		} else {
			language   = 'en';
		}

		set_question();
		play_audio();
		wait_a_while( time_delay );
	}

	/**
	 * Set the question on screen.
	 */
	function set_question() {
		question = words[ number ];
		question_text.innerHTML = question[ language ];
		which_question.innerHTML = number + ' ' + language;

		if ( 'en' === language ) {
			question_text.innerHTML = question_text.innerHTML + '<br /><small>(' + question['de'] + ')</small>';
		}
	}

	/**
	 * Play the audio.
	 */
	function play_audio() {
		question = words[ number ];
//wait_a_while( 20 );

		let audio_tag = document.getElementById( 'audio' );
		audio_tag.src = '/german-vocab/audio/' + MD5( question[ language ] ) + '.mp3';
		audio_tag.play(); 

		let promise = audio_tag.play();
		if ( promise !== undefined ) {
			promise.then(_ => {
				// Success!
			} ).catch( error => {
				console.log( 'Error: Access of MP3 file failed' );
			} );
		}

	}

	/**
	 * Get a query variable.
	 */
	function get_query_var( param ) {
		let qs = window.location.search.substring( 1 );
		let v  = qs.split( '&' );
		for ( let i = 0; i < v.length; i++ ) {
			let p = v[i].split( '=' );
			if ( p['0'] == param ) {
				return p['1'];
			}
		}

		return null;
	}

}


/*
soundFile.appendChild(src);

//Load the audio tag
//It auto plays as a fallback
soundFile.load();
soundFile.volume = 0.000000;
soundFile.play();

//Plays the sound
function play() {
   //Set the current time for the audio file to the beginning
   soundFile.currentTime = 0.01;
   soundFile.volume = volume;

   //Due to a bug in Firefox, the audio needs to be played after a delay
   setTimeout(function(){soundFile.play();},1);
}


window.onload=function() {

	// Need to 

	setTimeout(
		function() {

			//Create the audio tag
			let audio_tag = document.getElementById( 'audio' );
			audio_tag.src = '/german-vocab/' + MD5( 'xxx' ) + '.mp3';
			//audio_tag.load()
			audio_tag.play(); 

			let promise = audio_tag.play();
			if (promise !== undefined) {
				promise.then(_ => {
					// Success!
				}).catch(error => {
					console.log( 'Error: Access of MP3 file failed' );
				});
			}


		},
		3000
	);

}
*/

/**
 * MD5 generating function.
 */
var MD5 = function (string) {

	function RotateLeft(lValue, iShiftBits) {
		return (lValue<<iShiftBits) | (lValue>>>(32-iShiftBits));
	}

	function AddUnsigned(lX,lY) {
		var lX4,lY4,lX8,lY8,lResult;
		lX8 = (lX & 0x80000000);
		lY8 = (lY & 0x80000000);
		lX4 = (lX & 0x40000000);
		lY4 = (lY & 0x40000000);
		lResult = (lX & 0x3FFFFFFF)+(lY & 0x3FFFFFFF);
		if (lX4 & lY4) {
			return (lResult ^ 0x80000000 ^ lX8 ^ lY8);
		}
		if (lX4 | lY4) {
			if (lResult & 0x40000000) {
				return (lResult ^ 0xC0000000 ^ lX8 ^ lY8);
			} else {
				return (lResult ^ 0x40000000 ^ lX8 ^ lY8);
			}
		} else {
			   return (lResult ^ lX8 ^ lY8);
		}
	}

	function F(x,y,z) { return (x & y) | ((~x) & z); }
	function G(x,y,z) { return (x & z) | (y & (~z)); }
	function H(x,y,z) { return (x ^ y ^ z); }
	function I(x,y,z) { return (y ^ (x | (~z))); }

	function FF(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(F(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};

	function GG(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(G(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};

	function HH(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(H(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};

	function II(a,b,c,d,x,s,ac) {
		a = AddUnsigned(a, AddUnsigned(AddUnsigned(I(b, c, d), x), ac));
		return AddUnsigned(RotateLeft(a, s), b);
	};

	function ConvertToWordArray(string) {
		var lWordCount;
		var lMessageLength = string.length;
		var lNumberOfWords_temp1=lMessageLength + 8;
		var lNumberOfWords_temp2=(lNumberOfWords_temp1-(lNumberOfWords_temp1 % 64))/64;
		var lNumberOfWords = (lNumberOfWords_temp2+1)*16;
		var lWordArray=Array(lNumberOfWords-1);
		var lBytePosition = 0;
		var lByteCount = 0;
		while ( lByteCount < lMessageLength ) {
			lWordCount = (lByteCount-(lByteCount % 4))/4;
			lBytePosition = (lByteCount % 4)*8;
			lWordArray[lWordCount] = (lWordArray[lWordCount] | (string.charCodeAt(lByteCount)<<lBytePosition));
			lByteCount++;
		}
		lWordCount = (lByteCount-(lByteCount % 4))/4;
		lBytePosition = (lByteCount % 4)*8;
		lWordArray[lWordCount] = lWordArray[lWordCount] | (0x80<<lBytePosition);
		lWordArray[lNumberOfWords-2] = lMessageLength<<3;
		lWordArray[lNumberOfWords-1] = lMessageLength>>>29;
		return lWordArray;
	};

	function WordToHex(lValue) {
		var WordToHexValue="",WordToHexValue_temp="",lByte,lCount;
		for (lCount = 0;lCount<=3;lCount++) {
			lByte = (lValue>>>(lCount*8)) & 255;
			WordToHexValue_temp = "0" + lByte.toString(16);
			WordToHexValue = WordToHexValue + WordToHexValue_temp.substr(WordToHexValue_temp.length-2,2);
		}
		return WordToHexValue;
	};

	function Utf8Encode(string) {
		string = string.replace(/\r\n/g,"\n");
		var utftext = "";

		for (var n = 0; n < string.length; n++) {

			var c = string.charCodeAt(n);

			if (c < 128) {
				utftext += String.fromCharCode(c);
			}
			else if((c > 127) && (c < 2048)) {
				utftext += String.fromCharCode((c >> 6) | 192);
				utftext += String.fromCharCode((c & 63) | 128);
			}
			else {
				utftext += String.fromCharCode((c >> 12) | 224);
				utftext += String.fromCharCode(((c >> 6) & 63) | 128);
				utftext += String.fromCharCode((c & 63) | 128);
			}

		}

		return utftext;
	};

	var x=Array();
	var k,AA,BB,CC,DD,a,b,c,d;
	var S11=7, S12=12, S13=17, S14=22;
	var S21=5, S22=9 , S23=14, S24=20;
	var S31=4, S32=11, S33=16, S34=23;
	var S41=6, S42=10, S43=15, S44=21;

	string = Utf8Encode(string);

	x = ConvertToWordArray(string);

	a = 0x67452301; b = 0xEFCDAB89; c = 0x98BADCFE; d = 0x10325476;

	for (k=0;k<x.length;k+=16) {
		AA=a; BB=b; CC=c; DD=d;
		a=FF(a,b,c,d,x[k+0], S11,0xD76AA478);
		d=FF(d,a,b,c,x[k+1], S12,0xE8C7B756);
		c=FF(c,d,a,b,x[k+2], S13,0x242070DB);
		b=FF(b,c,d,a,x[k+3], S14,0xC1BDCEEE);
		a=FF(a,b,c,d,x[k+4], S11,0xF57C0FAF);
		d=FF(d,a,b,c,x[k+5], S12,0x4787C62A);
		c=FF(c,d,a,b,x[k+6], S13,0xA8304613);
		b=FF(b,c,d,a,x[k+7], S14,0xFD469501);
		a=FF(a,b,c,d,x[k+8], S11,0x698098D8);
		d=FF(d,a,b,c,x[k+9], S12,0x8B44F7AF);
		c=FF(c,d,a,b,x[k+10],S13,0xFFFF5BB1);
		b=FF(b,c,d,a,x[k+11],S14,0x895CD7BE);
		a=FF(a,b,c,d,x[k+12],S11,0x6B901122);
		d=FF(d,a,b,c,x[k+13],S12,0xFD987193);
		c=FF(c,d,a,b,x[k+14],S13,0xA679438E);
		b=FF(b,c,d,a,x[k+15],S14,0x49B40821);
		a=GG(a,b,c,d,x[k+1], S21,0xF61E2562);
		d=GG(d,a,b,c,x[k+6], S22,0xC040B340);
		c=GG(c,d,a,b,x[k+11],S23,0x265E5A51);
		b=GG(b,c,d,a,x[k+0], S24,0xE9B6C7AA);
		a=GG(a,b,c,d,x[k+5], S21,0xD62F105D);
		d=GG(d,a,b,c,x[k+10],S22,0x2441453);
		c=GG(c,d,a,b,x[k+15],S23,0xD8A1E681);
		b=GG(b,c,d,a,x[k+4], S24,0xE7D3FBC8);
		a=GG(a,b,c,d,x[k+9], S21,0x21E1CDE6);
		d=GG(d,a,b,c,x[k+14],S22,0xC33707D6);
		c=GG(c,d,a,b,x[k+3], S23,0xF4D50D87);
		b=GG(b,c,d,a,x[k+8], S24,0x455A14ED);
		a=GG(a,b,c,d,x[k+13],S21,0xA9E3E905);
		d=GG(d,a,b,c,x[k+2], S22,0xFCEFA3F8);
		c=GG(c,d,a,b,x[k+7], S23,0x676F02D9);
		b=GG(b,c,d,a,x[k+12],S24,0x8D2A4C8A);
		a=HH(a,b,c,d,x[k+5], S31,0xFFFA3942);
		d=HH(d,a,b,c,x[k+8], S32,0x8771F681);
		c=HH(c,d,a,b,x[k+11],S33,0x6D9D6122);
		b=HH(b,c,d,a,x[k+14],S34,0xFDE5380C);
		a=HH(a,b,c,d,x[k+1], S31,0xA4BEEA44);
		d=HH(d,a,b,c,x[k+4], S32,0x4BDECFA9);
		c=HH(c,d,a,b,x[k+7], S33,0xF6BB4B60);
		b=HH(b,c,d,a,x[k+10],S34,0xBEBFBC70);
		a=HH(a,b,c,d,x[k+13],S31,0x289B7EC6);
		d=HH(d,a,b,c,x[k+0], S32,0xEAA127FA);
		c=HH(c,d,a,b,x[k+3], S33,0xD4EF3085);
		b=HH(b,c,d,a,x[k+6], S34,0x4881D05);
		a=HH(a,b,c,d,x[k+9], S31,0xD9D4D039);
		d=HH(d,a,b,c,x[k+12],S32,0xE6DB99E5);
		c=HH(c,d,a,b,x[k+15],S33,0x1FA27CF8);
		b=HH(b,c,d,a,x[k+2], S34,0xC4AC5665);
		a=II(a,b,c,d,x[k+0], S41,0xF4292244);
		d=II(d,a,b,c,x[k+7], S42,0x432AFF97);
		c=II(c,d,a,b,x[k+14],S43,0xAB9423A7);
		b=II(b,c,d,a,x[k+5], S44,0xFC93A039);
		a=II(a,b,c,d,x[k+12],S41,0x655B59C3);
		d=II(d,a,b,c,x[k+3], S42,0x8F0CCC92);
		c=II(c,d,a,b,x[k+10],S43,0xFFEFF47D);
		b=II(b,c,d,a,x[k+1], S44,0x85845DD1);
		a=II(a,b,c,d,x[k+8], S41,0x6FA87E4F);
		d=II(d,a,b,c,x[k+15],S42,0xFE2CE6E0);
		c=II(c,d,a,b,x[k+6], S43,0xA3014314);
		b=II(b,c,d,a,x[k+13],S44,0x4E0811A1);
		a=II(a,b,c,d,x[k+4], S41,0xF7537E82);
		d=II(d,a,b,c,x[k+11],S42,0xBD3AF235);
		c=II(c,d,a,b,x[k+2], S43,0x2AD7D2BB);
		b=II(b,c,d,a,x[k+9], S44,0xEB86D391);
		a=AddUnsigned(a,AA);
		b=AddUnsigned(b,BB);
		c=AddUnsigned(c,CC);
		d=AddUnsigned(d,DD);
	}

	var temp = WordToHex(a)+WordToHex(b)+WordToHex(c)+WordToHex(d);

	return temp.toLowerCase();
}

</script>

</body>
</html>
<!--
https://cloud.google.com/text-to-speech/docs/voices
-->
