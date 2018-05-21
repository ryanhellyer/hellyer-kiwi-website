<?php
/**
 * The footer template file.
 *
 * @package Cuckoo Nord
 * @since Cuckoo Nord 1.0
 */
?>

</main>

<footer id="footer" class="wrapper">
	<p>
		Copyright &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>.

		<?php the_privacy_policy_link(); ?>
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>