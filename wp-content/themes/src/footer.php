<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after.
 *
 * @package Hellish Simplicity
 * @since Hellish Simplicity 1.1
 */
?>

</main><!-- #main -->


<footer id="site-footer" role="contentinfo">

	<p>
		&copy; <?php echo date( 'Y' ); ?> <?php esc_html_e( get_bloginfo( 'name', 'display' ) ); ?>. Website by <a title="Ryan Hellyer" href="https://geek.hellyer.kiwi/">Ryan Hellyer</a>.
	</p>

	<ul id="social-icons">
		<li><a href="https://twitter.com/"><span>Twitter</span></a></li>
		<li><a href="https://youtube.com/"><span>YouTube</span></a></li>
		<li><a href="https://facebook.com/"><span>Facebook</span></a></li>
	</ul><!-- #social-icons -->

	<ul id="footer-menu">
		<li><a href="#">Contact</a></li>
		<li><a href="#">Legal Notice</a></li>
		<li><a href="#">About</a></li>
	</ul><!-- #footer-menu -->

</footer><!-- #site-footer -->

<?php wp_footer(); ?>

</body>
</html>