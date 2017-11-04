<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php wp_title( '|' ); ?></title>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style.css" media="screen" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1, user-scalable=no" />
	<!--[if lt IE 9]><script src="<?php echo get_template_directory_uri(); ?>/scripts/html5.js" type="text/javascript"></script><![endif]-->
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<header id="header_wrapper">
	<div id="header" class="wrapper">
		<h1>
			<a href="<?php bloginfo( 'url' ); ?>" title="<?php bloginfo( 'name' ); ?> | <?php bloginfo( 'description' ); ?>">
				Ryan
				<span>Hellyer</span>
			</a>
		</h1>
		<p>
			WordPress geekiness.<br />
			Plugins, code tips and more!
		</p>
		<img src="<?php echo get_template_directory_ur(); ?>/images/ryan-cut-small.png" />
	</div>
</header>

<nav id="nav_wrapper">
	<?php
	if ( ! is_page( DROPDOWNGEN_PAGEID ) ) { ?>
	<div id="nav">
		<?php wp_nav_menu( array( 'theme_location' => 'primary', 'sort_column' => 'menu_order', 'container_class' => 'wrapper menu-header' ) ); ?>
	</div>
	<?php } else {
		pixopoint_cssgeneratormenu();
	} ?>
</nav>

<div id="wrapper" class="wrapper">
