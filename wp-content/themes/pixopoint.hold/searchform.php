<?php
/**
 * The template for displaying search forms
 *
 * @package PixoPoint
 * @since PixoPoint 1.1
 */
?>
<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="s" class="assistive-text"><?php _e( 'Search', 'pixopoint' ); ?></label>
	<input type="text" id="s" class="field" name="s" placeholder="<?php esc_attr_e( 'Search', 'pixopoint' ); ?>" />
	<input type="submit" class="submit" name="submit" value="<?php esc_attr_e( 'Search', 'pixopoint' ); ?>" />
</form>
