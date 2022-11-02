<?php

/**
 * Post cards.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class SRC_Post_Cards {

	/**
	 * Class constructor
	 * Adds all the methods to appropriate hooks
	 */
	public function __construct() {

		if (
			! isset( $_GET['postcard'] )
			||
			! isset( $_GET['url'] )
			||
			! isset( $_GET['text'] )
		) {
			return;
		}

		$this->post_card();
	}

	public function post_card() {
		$fontsize = 120;
		if ( isset( $_GET['fontsize'] ) ) {
			$fontsize = absint( $_GET['fontsize'] );
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
<style>
#undiecar-postcard {
	position: absolute;
	left: 0;
	top: 0;
	width: 1800px;
	height: 1280px;
	background: #e43146;
	overflow: hidden;
}
#site-header {
	background: #202020;
	width: 1800px;
	position: absolute;
	left: 0;
	top: 0;
	height: 180px;
}
#site-header h1 {
	line-height: 180px;
	height: 180px;
	text-indent: 340px;
	font-size: 120px;
	background-size: 283px;
	border-left: 50px solid #e43146;
}
#featured-news {
	position: absolute;
	left: 0;
	top: 110px;
	height: 1100px !important;
}
</style>
</head>
<body class="member page">

<!-- IMAGE MUST BE 1920X1100 px -->

<div id="undiecar-postcard">
	<header id="site-header" role="banner">
		<h1><a href="https://undiecar.com/" title="Undiecar Championship">Undiecar Championship</a></h1>
	</header><!-- #site-header -->

	<section id="featured-news" style="background-image: url(<?php echo esc_url( $_GET['url'] ); ?>)">
	</section><!-- #featured-news -->

	<div style="
		position: absolute;
		left: 0;
		top: 1100px;
		font-size: <?php echo esc_html( $fontsize ); ?>px;
		color: #fff;
		width: 1800px;
		text-align: center;
		font-family: arial, serif;
		font-weight: 600;
		letter-spacing: -3px;
		text-shadow: 0 0 50px #000;
	">
		<?php echo esc_html( $_GET['text'] ); ?> 
	</div>

</div>

</body>
</html><?php

		die;

	}
}
