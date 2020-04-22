<?php
/*
Plugin Name: Strattic Anti-Spam
Plugin URI: https://strattic.com/
Description: Strattic anti-spam tool
Author: Ryan Hellyer
Version: 1;0
Author URI: https://strattic.com/

Copyright (c) 2012 - 2018 Strattic


Based on the following open source projects:

Spam Destroyer by Ryan Hellyer
https://geek.hellyer.kiwi/plugins/spam-destroyer/

Cookies for Comments by Donncha O Caoimh
http://ocaoimh.ie/cookies-for-comments/

WP Hashcash by Elliot Back
http://wordpress-plugins.feifei.us/hashcash/

Spam Catharsis by Brian Layman
http://TheCodeCave.com/plugins/spam-catharsis/

Script para la generación de CAPTCHAS by Jose Rodrigueze
http://code.google.com/p/cool-php-captcha


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/

/**
 * Main anti-spam class
 * 
 * @copyright Copyright (c), Strattic
 */
class Strattic_Anti_Spam {

	const ENCRYPTION_METHOD = 'AES-256-CBC';
	const INITIALIZATION_VECTOR = 'ik3m3mfmenektn37'; // 16 characters long

	/**
	 * Class constructor
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'payload' ) );
		add_filter( 'strattic_buffer', array( $this, 'add_input_fields' ) );

	}

	/**
	 * The payload.
	 * Decrypts the encrypted blob and adds it to the other input field.
	 */
	public function payload() {

		// Load the payload
		wp_enqueue_script(
			'strattic-anti-spam',
			STRATTIC_ASSETS . 'anti-spam.js',
			'',
			STRATTIC_VERSION,
			true
		);

		wp_localize_script(
			'strattic-anti-spam',
			'strattic_anti_spam',
			$this->encrypt( 'DECRYPTED BLOB' )
		);
	}


	/**
	 * Adds input fields to forms.
	 * Only applies to forms which submit to the current domain name.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @access private
	 * @param  string  $html  The HTML to be modified
	 * @return string  The modified HTML code
	 */
	public function add_input_fields( $html ) {

		// Implement DOMDocument
		$dom = new \DOMDocument();
		libxml_use_internal_errors( true ); // Disable error display since most sites have HTML errors
		$dom->loadHTML( $html );
		libxml_use_internal_errors( false );

		// Iterate through each form
		$nodes = $dom->getElementsByTagName( 'form' );
		foreach( $nodes as $node ) {

			// If action attribute isn't point to current site, then bail out because we aren't processing it anyway
			if (
				'' === $node->getAttribute( 'action' )
				||
				strpos( $node->getAttribute( 'action' ), $this->get_domain_name() ) === false
			) {
				continue;
			}

			// Add blob input field (the is decrypted and added to the other input field)
			$element = $dom->createElement( 'input' );
			$element->setAttribute( 'type', 'text' );
			$element->setAttribute( 'name', 'strattic-anti-spam' );
			$element->setAttribute( 'value', md5( rand() ) );
			$node->appendChild( $element );

		}

		return $dom->saveHTML();
	}

	/**
	 * Encrypt.
	 * This currently only uses base64 encoding as encryption.
	 * This is extremely crude, but should be sufficient for blocking spam-bots.
	 * This can be upgraded to a proper encryption algorithm such as AES in future.
	 *
	 * @access   private
	 * @param    string   $text   Text to encrypt
	 * @return   string   Encrypted text
	 */
	private function encrypt( $plain_text ) {

		return base64_encode( $plain_text );
	}

	/**
	 * The encryption key needs to be something which can be calculated on an external server.
	 * To allow for easy calculation of the spam key, we use a simple MD5 of the domain name.
	 *
	 * @access   private
	 * @return   string   The spam key
	 */
	private function get_spam_key() {

		// We use MD5 here as it gives a 32 character long string - required as AES key
		$scrambled_string = md5( $this->get_domain_name() );

		return $scrambled_string;
	}

	/**
	 * Get the current sites domain name.
	 *
	 * @access   private
	 * @return   string   The domain name
	 */
	private function get_domain_name() {

		$url_bits = parse_url( home_url() );
		if ( isset( $url_bits[ 'host' ] ) ) {
			$domain_name = $url_bits[ 'host' ];
		} else {
			wp_die( 'Error: Domain name not found' );
		}

		return $domain_name;
	}


}
