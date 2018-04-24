<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
 
$app_id = '2067951300118888';
$app_secret = '0e07f473027e7b0df8682e6b7a45724b';

$fb = new Facebook\Facebook([
	'app_id'                => $app_id,
	'app_secret'            => $app_secret,
	'default_graph_version' => 'v2.12',
]);
