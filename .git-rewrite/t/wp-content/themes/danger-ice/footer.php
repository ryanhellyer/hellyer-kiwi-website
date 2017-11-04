<div id="primary-sidebar" class="sidebar">
		<?php dynamic_sidebar( 'sidebar' ); ?>
	</div>
	<a href="http://www.centreice.co.nz/"><img class="banner" src="<?php echo get_template_directory_uri(); ?>/images/center-ice-ad.jpg" /></a>
	<div id="bottom-widgets">
		<?php dynamic_sidebar( 'footer' ); ?>
	</div>

	<div id="footer">
		<img src="<?php echo get_template_directory_uri(); ?>/images/footer-logo.png" />
		<p>
			Copyright © <a href="http://dunedinicehockey.co.nz/">Dunedin Ice Hockey Association</a> <?php echo date( 'Y' ); ?>.
			<br />
			Website by <a href="http://geek.ryanhellyer.net/">Ryan Hellyer</a>.
		</p>
	</div>

	</div>

</div>

<?php wp_footer(); ?>

</body>
</html>