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
			<?php _e( 'Official website of the <a href="https://www.facebook.com/groups/FreeAdviceBerlin/">Free Advice Berlin Facebook group</a>.' ); ?>

			<span class="alignright">
				<a href="<?php echo esc_url( home_url( '/house-rules/' ) ); ?>">House Rules</a>
				 | 
				<a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About</a>
				 | 
				<a href="<?php echo esc_url( home_url( '/legal-notice/' ) ); ?>"><?php _e( 'Legal Notice', 'free-advice-berlin' ) ?></a>
			</span>
		</p>
	</footer>

</div>

<?php wp_footer(); ?>
</body>
</html>