<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */
?>

</main><!-- #main -->


<footer id="site-footer" role="contentinfo">

	<p>
		&copy; <?php echo date( 'Y' ); ?> <a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( get_bloginfo( 'name', 'display' ) ); ?></a>.
		<br />
		Website by <a title="Ryan Hellyer" href="https://geek.hellyer.kiwi/">Ryan Hellyer</a>.
	</p>

	<ul id="social-icons"><?php

		wp_nav_menu(
			array(
				'theme_location' => 'social-links',
				'container'      => '',
				'items_wrap'     => '%3$s',
			)
		);

		?>
	</ul><!-- #social-icons -->

	<ul id="footer-menu"><?php

		wp_nav_menu(
			array(
				'theme_location' => 'footer',
				'container'      => '',
				'items_wrap'     => '%3$s',
			)
		);

		?>
	</ul><!-- #footer-menu -->

</footer><!-- #site-footer -->

<?php wp_footer(); ?>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-2481610-13', 'auto');
  ga('send', 'pageview');

</script>

</body>
</html>