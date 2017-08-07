<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after.
 *
 * @package SRC Theme
 * @since SRC Theme 1.0
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

<?php

if ( is_user_logged_in() ) {
	$url = home_url();
	$text = __( 'Rules', 'src' );
} else {
	$url = home_url( '/register/' );
	$text = __( 'Sign Up', 'src' );
}

?>
	<a class="button" href="<?php echo esc_url( $url ); ?>">
		<?php esc_html_e( 'Rules', 'src' ); ?> <span><?php esc_html( $text ); ?></span>
	</a><!-- .sign-up -->

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

</body>
</html>