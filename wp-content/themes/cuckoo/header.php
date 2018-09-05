<?php
/**
 * The header template file.
 *
 * @package Cuckoo Nord
 * @since Cuckoo Nord 1.0
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<link rel="profile" href="http://gmpg.org/xfn/11" />
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>


<header class="wrapper">

	<?php

	// Output language selector menu
	wp_nav_menu(
		array(
			'theme_location' => 'language',
			'container'      => 'div',
		)
	);

	?>

	<h1>
		<a id="title" href="<?php echo esc_url( home_url() . '/' ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<?php bloginfo( 'name' ); ?>
		</a>
	</h1>

	<?php

	// Output main header menu
	wp_nav_menu(
		array(
			'theme_location' => 'header',
			'container'      => '',
		)
	);

	?>

</header>

<main id="main" class="wrapper">
