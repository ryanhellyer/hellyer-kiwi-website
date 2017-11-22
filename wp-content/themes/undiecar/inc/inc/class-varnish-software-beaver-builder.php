<?php

/**
 * Beaver Builder specific code.
 *
 * @copyright Copyright (c), Varnish Software
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Varnish Software
 * @since Varnish Software 1.0
 */
class Varnish_Software_Beaver_Builder {

	/**
	 * Constructor.
	 */
	public function __construct() {

		add_action( 'init',       array( $this, 'load_templates' ) );
		add_filter( 'body_class', array( $this, 'body_class' ) );

		/**
		 * Get the unserizlized Beaver Builder template data.
		 */
		//$bla = file_get_contents( dirname( __FILE__ ) . '/templates/beaver-builder-templates.dat' );print_r( maybe_unserialize( $bla ) );die;

	}

	/**
	 * Loading the custom Beaver Builder templates.
	 */
	public function load_templates() {

		/**
		 * Return if the builder isn't installed or if the current
		 * version doesn't support registering templates.
		 */
		if ( ! class_exists( 'FLBuilder' ) || ! method_exists( 'FLBuilder', 'register_templates' ) ) {
			return;
		}

		/**
		 * Register the path to your templates.dat file.
		 */
		FLBuilder::register_templates( dirname( dirname( __FILE__ ) ) . '/templates/beaver-builder-templates.dat' );
	}

	/**
	 * Add body class of 'fl-builder' when using page builder.
	 *
	 * @param   array  $classes The current body classes
	 * @return  array  The modified list of body classes
	 */
	public function body_class( $classes ) {

		if ( isset( $_GET[ 'fl_builder' ] ) ) {
			$classes[] = 'page-builder';
		}

		return $classes;
	}

}
