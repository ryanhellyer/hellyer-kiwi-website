<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package PixoPoint
 * @since PixoPoint 1.1
 */
?>

</div><!-- #main .site-main -->

<footer id="site-footer" role="contentinfo">
	<div id="site-info">
		<?php _e( 'Copyright', 'pixopoint' ); ?> &copy; <?php bloginfo( 'name' ); ?> <?php echo date( 'Y' ); ?>. 
		<?php _e( 'WordPress theme by', 'pixopoint' ); ?> <a href="http://geek.ryanhellyer.net/" title="<?php _e( 'Ryan Hellyer and Tung Do', 'pixopoint' ); ?>"><?php _e( 'Ryan & Tung', 'pixopoint' ); ?></a>.

		<div class="footer-blocks">
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
			<div></div>
		</div>
	</div><!-- #site-info -->
</footer><!-- #colophon .site-footer -->

<?php wp_footer(); ?>

</body>
</html>