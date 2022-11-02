<?php

if ( ! isset( $_GET['url'] ) ) {
	return;
}

?><!DOCTYPE html>
<html lang="en-GB">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width" />
<title>Undiecar Championship postcard</title>
<meta name="robots" content="max-snippet:-1, max-image-preview:large, max-video-preview:-1"/>
<link rel='stylesheet' id='google-open-sans-css'  href='https://fonts.googleapis.com/css?family=Open+Sans%3A400%2C600%2C700%2C800&#038;ver=1.1' type='text/css' media='all' />
<link rel='stylesheet' id='undiecar-css'  href='https://undiecar.com/wp-content/themes/undiecar/css/style.min.css?ver=1.1' type='text/css' media='all' />
</head>
<body class="member page" style="position:absolute;left:0;top:0;width:1920px;height:1280px;">

<header id="site-header" role="banner" style="background:lime;width:1920px;position:absolute;left:0;top:1200px;height:180px;">
	<h1><a href="https://undiecar.com/" title="Undiecar Championship">Undiecar.com</a></h1>
</header><!-- #site-header -->

<section id="featured-news" style="height:1280px;background-image: url(<?php echo esc_url( $_GET['url'] ); ?>)">
</section><!-- #featured-news -->

</body>
</html><?php

die;
