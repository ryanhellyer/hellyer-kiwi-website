<?php
/**
 * The template for displaying search forms
 *
 * @package Arousing Audio
 * @since Arousing Audio 1.0
 */
?>
<form method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="s" class="screen-reader-text"><?php esc_html_e( 'Search', 'arousingaudio' ); ?></label>
	<input type="text" id="s" class="field" name="s" placeholder="<?php esc_attr_e( 'Search', 'arousingaudio' ); ?>" />
	<input type="submit" class="submit" name="submit" value="<?php esc_attr_e( 'Search', 'arousingaudio' ); ?>" />
</form>
