<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package PixoPoint
 * @since PixoPoint 1.0
 */
?>

</div><!-- #main .site-main -->

<footer id="site-footer" role="contentinfo">
	<div id="site-info">
		Copyright &copy; <?php bloginfo( 'name' ); ?> <?php echo date( 'Y' ); ?>.
		 | 
		WordPress theme by <a href="http://geek.ryanhellyer.net/" title="Ryan Hellyer">Ryan Hellyer</a>.
	</div><!-- #site-info -->
</footer><!-- #colophon .site-footer -->

<?php wp_footer(); ?>

</body>
</html>