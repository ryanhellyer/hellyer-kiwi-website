<?php
/**
 * @package Fruit Shake
 */
?>
	<footer id="colophon" role="contentinfo">
		<div id="site-info">
			Copyright &copy; <?php echo date('Y'); ?> Ryan Hellyer
		</div>
	</footer><!-- #colophon -->

	<?php get_template_part( 'post-navigation', 'footer' ); ?>

	<?php
		/* A sidebar in the footer? Yep. You can can customize
		 * your footer with three columns of widgets.
		 */
		if ( ! is_404() )
			get_sidebar( 'footer' );
	?>

	</div><!-- #main -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
