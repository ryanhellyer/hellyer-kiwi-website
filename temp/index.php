<?php

declare(strict_types=1);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('class-encrypted-storage.php');

?><!DOCTYPE html>
	<html lang="en">
	<head>
	<meta charset="UTF-8">
	<title>End2End Encrypted Content</title>
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
	li button,
	li textarea {
		display: none;
	}
	li.decrypted button,
	li.decrypted textarea {
		display: block;
	}
	@keyframes saveStart {
		0% {
			background: #fff;
		}
		100% {
			background: rgba(0,0,255,0.03);
		}
	}
	@keyframes saveEnd {
		0% {
			background: rgba(0,0,255,0.03);
		}
		100% {
			background: #fff;
		}
	}
	.savingStart {
		animation: saveStart 1s ease-in-out forwards;
	}
	.savingEnd {
		animation: saveEnd 1s ease-in-out forwards;
	}
	</style>
</head>
<body>

	<h1>End2End Encrypted Content</h1>

	<ul><?php
		echo $encryptedStorage->listEncryptedFiles();
	?>

	</ul>

	<noscript>This page does not work without JavaScript</noscript>
	<script src="./functions.js"></script>
	<script src="./script.js"></script>
</body>
</html>