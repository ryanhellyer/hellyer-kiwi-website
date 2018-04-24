<?php

require( 'config.php' );

$url = 'https://spamannihilator.com/facebook/callback.php';

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl( $url, $permissions );

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
