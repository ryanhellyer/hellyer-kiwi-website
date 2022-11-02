<?php @header("HTTP/1.1 404 Not found", TRUE, 404); ?>
<?php get_header(); ?>

<div class="post">
	<h2>404 Error - not found</h2>
	<div class="postcontent">
		<p>Apologies, but we were unable to find what you were looking for. Perhaps searching will help.</p>
		<form id="error404-searchform" method="get" action="<?php bloginfo('home') ?>">
			<input id="error404-s" name="s" type="text" value="<?php echo wp_specialchars(stripslashes($_GET['s']), true) ?>" size="40" />
			<input id="error404-searchsubmit" name="searchsubmit" type="submit" value="Search" />
		</form>
	</div>
</div>

<?php get_footer(); ?>
