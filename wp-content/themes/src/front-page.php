<?php
/**
 * Front page template file.
 *
 * @package SRC Theme
 * @since SRC Theme 1.0
 */

get_header();

if ( is_super_admin() ) {
	get_template_part( 'templates/content-home' );
}

get_footer();