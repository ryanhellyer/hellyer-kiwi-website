<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Page List</title>
<style>
body {
	font-family: Arial, sans-serif;
	margin: 20px;
	background-color: #f4f4f4;
}
ul {
	list-style: none;
	padding: 0;
}
li {
	background-color: #fff;
	margin: 8px 0;
	padding: 12px;
	border-radius: 4px;
	box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
li div {
	display: none;
}
li div.passwordEntered {
	display: block;
}
a {
	text-decoration: none;
	color: #007bff;
}
a:hover {
	text-decoration: underline;
}
</style>
</head>
<body>

<h1>Encrypted Pages</h1>

<ul>

<?php

function escAttr($attr) {
	//@todo make this escape attributes.
	return $attr;
}
function escHtml($html) {
	//@todo make this escape HTML.
	return $html;
}

$dirPath = dirname(__FILE__).'/encrypted/';
if ($handle = opendir($dirPath)) {
	while (false !== ($entry = readdir($handle))) {
		if (pathinfo($entry, PATHINFO_EXTENSION) === 'data') {
			$pages[] = basename($entry, '.data');
		}
	}
	closedir($handle);
}

foreach ($pages as $page) {
	if ($contents = file_get_contents($dirPath . $page . '.data')) {
		echo "\t<li>\n\t\t" . escHtml($page) . "\n\t\t" . '<input type="password" value="">' . "\n\t\t<div>" . escHtml($contents) . "</div><div></div>\n\t</li>\n";

	}
}
?></ul>

<script src="./script.js"></script>

</body>
</html>