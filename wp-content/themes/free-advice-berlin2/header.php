<?php
/**
 * The header template file.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
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


<header>

	<div class="wrapper">

		<a id="facebook-group" href="https://ryan.hellyer.kiwi/">Free Advice about Berlin</a>

		<a id="title" href="https://ryan.hellyer.kiwi/" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<h1>
				Advice about Berlin
			</h1>
			<p>
				The former resources for a Berlin based Facebook group
			</p>
		</a>

	</div>

</header>

<div class="wrapper">
