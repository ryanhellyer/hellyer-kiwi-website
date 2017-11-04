<?php

/****
* Simple PHP application for using the Bing Search API
*/
$acctKey = 'V3Zo5BUurZrF5q7OJZlIRQFxFOZ94CFwVVj2mcPq6C8=';
$rootUri = 'https://api.datamarket.azure.com/Bing/Search';

// Read the contents of the .html file into a string.
$contents = file_get_contents('bing_basic.html');
//http://www.bing.com/search?q=wordpress&go=&qs=n&          filt=all&pq=wordpress&sc=8-8&sp=-1&sk=   &first=11&FORM=PERE
//http://www.bing.com/search?q=wordpress&go=&qs=n&form=QBLH&filt=all&pq=wordpress&sc=8-8&sp=-1&sk=

//	$requestUri = 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query=%27' . $_GET['s'] . '+site%3Arandom.ryanhellyer.net%27';
	$requestUri = 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query=%27' . $_GET['s'] . '+site%3Aryanhellyer.net%27&$top=50';

	$requestUri = 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query=%27' . $_GET['s'] . '+site%3Aryanhellyer.net%20-site:ice.ryanhellyer.net%20-site:geek.ryanhellyer.net%20-site:random.ryanhellyer.net%20-site:slapshot.ryanhellyer.net%20-site:tweets.ryanhellyer.net%27&$top=50';

//	$requestUri = 'https://api.datamarket.azure.com/Bing/Search/Web?$format=json&Query=%27' . $_GET['s'] . '+site%3Aryanhellyer.net+-site%3Ageek.ryanhellyer.net%27';
	

	// Encode the credentials and create the stream context.
	$auth = base64_encode("$acctKey:$acctKey");
	$data = array(
		'http' => array(
			'request_fulluri' => true,
			'ignore_errors'   => true, // ignore_errors can help debug – remove for production. This option added in PHP 5.2.10
			'header'          => "Authorization: Basic $auth"
		)
	);
	
	$context = stream_context_create($data);
	
	// Get the response from Bing.
	$response = file_get_contents($requestUri, 0, $context);
	
	// Decode the response.
	$jsonObj = json_decode($response); $resultStr = '';
	// Parse each result according to its metadata type.
	foreach ( $jsonObj->d->results as $value ) {
		switch ($value->__metadata->type) {
			case 'WebResult': $resultStr .= "<a href=\"{$value->Url}\">{$value->Title}</a><p>{$value->Description}</p>";
			break; case 'ImageResult': $resultStr .= "<h4>{$value->Title} ({$value->Width}x{$value->Height}) " . "{$value->FileSize} bytes)</h4>" . "<a href=\"{$value->MediaUrl}\">" . "<img src=\"{$value->Thumbnail->MediaUrl}\"></a><br />"; break;
		}
	}

	// Substitute the results placeholder. Ready to go.
	$contents = str_replace('{RESULTS}', $resultStr, $contents);


echo $contents;
