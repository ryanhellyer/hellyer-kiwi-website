
<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>

	<?php settings_errors(); ?>

	<form method="post" action="options.php">

		<?php 
			// settings_fields( self::SETTINGS_OPTION);
		?>

		<?php require( 'search-admin-settings.php' ); ?>

		<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e( 'Save', 'strattic' ); ?>" />
		</p>

	</form>

</div><?php
