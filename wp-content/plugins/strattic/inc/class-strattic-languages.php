<?php

/**
 * Strattic multilingual support.
 * 
 * @copyright Strattic 2018
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 */
class Strattic_Languages {

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'localization' ), 5 );
	}

	/*
	 * Setup localization for translations.
	 */
	public function localization() {

		// Localization
		load_plugin_textdomain(
			'strattic', // Unique identifier
			false, // Deprecated abs path
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' // Languages folder
		);

	}
}
