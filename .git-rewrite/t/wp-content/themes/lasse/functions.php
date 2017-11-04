<?php
/**
 * Lasse Super functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * @package WordPress
 * @subpackage Lasse_Super
 * @since 1.0
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 960;

/**
 * Load required files
 */
require( 'inc/class-multi-post-thumbnails.php' );
require( 'inc/class-lasse-super-setup.php' );
require( 'inc/theme-functions.php' );

$lassesuper = new Lasse_Super_Setup;
global $lassesuper;

