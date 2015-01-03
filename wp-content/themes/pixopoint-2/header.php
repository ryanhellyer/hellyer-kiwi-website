<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php wp_title( '|' ); ?></title>
	<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/style24.css" media="screen" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1, user-scalable=no" />
	<!--[if lt IE 9]><script src="http://files.ryanhellyer.net/wp-content/themes/hellish-simplicity/scripts/html5.js" type="text/javascript"></script><![endif]-->
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
                <?php
if ( is_ssl() ) {
        $http = 'https://hellyer.kiwi';
} else {
        $http = 'http:////files.ryanhellyer.net';
}

?>
                <img src="<?php echo $http; ?>/wp-content/themes/pixopoint-2/images/ryan-cut-small.png" />
	</div>
</header>

<nav id="nav_wrapper">
	<?php
	if ( defined( 'DROPDOWNGEN_PAGEID' ) ) {
		if ( ! is_page( DROPDOWNGEN_PAGEID ) ) { ?>
		<div id="nav">
			<?php 
/*
if ( false === ( $menu = get_transient( 'menu' ) ) ) {
	ob_start();

	wp_nav_menu( array( 'theme_location' => 'primary', 'sort_column' => 'menu_order', 'container_class' => 'wrapper menu-header' ) ); ?>

	$menu = ob_get_contents();
	ob_end_clean();
	set_transient( 'menu', $menu, 30 );
}
echo $menu;
*/
?>

		</div>
		<?php } else {
			pixopoint_cssgeneratormenu();
		}
	} else {
			?>
		<div id="nav">
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'sort_column' => 'menu_order', 'container_class' => 'wrapper menu-header' ) ); ?>
		</div><?php
	}
	?>
</nav>

<div id="wrapper" class="wrapper">
