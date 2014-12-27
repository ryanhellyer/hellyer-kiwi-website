<?php
/**
 * Theme template
 *
 * @package Comic Glot
 * @since Comic Glot 1.0
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<title><?php wp_title( '|', true, 'right' ); ?></title>
<?php do_action( 'comic_glot_head' ); ?>
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

	<button class="button" onclick='mySwipe.prev()'><?php _e( 'Previous', 'comic-glot' ); ?></button> 
	<button class="button" onclick='mySwipe.next()'><?php _e( 'Next', 'comic-glot' ); ?></button>

</div>

<p>
	<?php the_title(); ?>
	<script>

	// Let user know if page served from cache or not
	var appCache = window.applicationCache;
	if(0!=appCache.status && 2!=appCache.status){
		document.write(' <small>(<?php _e( 'served from the browser cache', 'comic-glot' ); ?>)</small>');
	}

	// Check if a new cache is available on page load.
	window.addEventListener('load', function(e) {

		window.applicationCache.addEventListener('updateready', function(e) {
			if (window.applicationCache.status == window.applicationCache.UPDATEREADY) {
				// Browser downloaded a new app cache.
				if (confirm('A new version of this site is available. Load it?')) {
					window.location.reload();
				}
			}
		}, false);

	}, false);

	</script>
</p>

<?php

}
else {
	?>
	<p>No results found sorry</p>
	<?php
}


do_action( 'comic_glot_footer' );

?>
</body>
</html>