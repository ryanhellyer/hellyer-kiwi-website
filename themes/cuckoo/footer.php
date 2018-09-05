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
		&copy; <?php printf( esc_html__( 'Copyright %s %s. All rights reserved.', 'cuckoo' ), date( 'Y' ), get_bloginfo( 'name' ) ); ?>
		<?php the_privacy_policy_link(); ?>
	</p>
</footer>

<?php wp_footer(); ?>
</body>
</html>