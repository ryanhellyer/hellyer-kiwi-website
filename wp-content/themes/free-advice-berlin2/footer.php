<?php
/**
 * The footer template file.
 *
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
?>

	<footer>
		<p>
			Former resources of a Berlin Facebook group.

			<span class="alignright">
				<a href="https://ryan.hellyer.kiwi/contact/">Contact</a>
				 | 
				<a href="<?php echo esc_url( home_url( '/legal-notice/' ) ); ?>"><?php _e( 'Legal Notice', 'free-advice-berlin' ) ?></a>
				|
				<a href="https://ryan.hellyer.kiwi/privacy-policy/">Privacy policy</a>
			</span>
		</p>
	</footer>

</div>

<?php wp_footer(); ?>
</body>
</html>
