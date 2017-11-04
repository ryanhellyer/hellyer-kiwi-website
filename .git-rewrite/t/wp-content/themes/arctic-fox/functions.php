<?php
/**
 * Arctic Fox functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * @package WordPress
 * @subpackage Arctic_Fox
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
require( 'inc/class-arctic-fox-setup.php' );
require( 'inc/class-arctic-fox-admin.php' );
require( 'inc/theme-functions.php' );

$arcticfox = new Arctic_Fox_Setup;
global $arcticfox;
