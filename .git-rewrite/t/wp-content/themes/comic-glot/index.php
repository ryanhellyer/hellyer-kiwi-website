<?php
/**
 * Theme template
 *
 * @package Comic Glot
 * @since Comic Glot 1.0
 */
?><!DOCTYPE html>
<html manifest="<?php echo esc_url( home_url( '?manifest=' . get_the_ID() ) ); ?>">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php

// Load main loop
if ( have_posts() ) {

?>
<div id="mySwipe">
	<div class="swipe-wrap"><?php

	// Start of the Loop
	while ( have_posts() ) {
		the_post();
		the_content();
	}
?>

	</div>
</div>


<div class="buttons">

	<button onclick='mySwipe.prev()'><?php _e( 'Previous', 'comic-glot' ); ?></button> 
	<button onclick='mySwipe.next()'><?php _e( 'Next', 'comic-glot' ); ?></button>

</div>

<p><?php the_title(); ?></p>

<?php

}
else {
	?>
	<p>No results found sorry</p>
	<?php
}


wp_footer();

?>
</body>
</html>