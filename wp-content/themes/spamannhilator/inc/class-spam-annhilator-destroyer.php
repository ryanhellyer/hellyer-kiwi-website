<?php

/**
 * XXX.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Spam Annhilator theme
 * @since Spam Annhilator theme 1.0
 */
class Spam_Annhilator_Destroyer {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {

//		$spam_destroyer = new Spam_Destroyer_Generate_CAPTCHA;
//		add_filter( 'spam-annhilator-input-field', array( $spam_destroyer, 'get_extra_input_field' ) );

	}

}
