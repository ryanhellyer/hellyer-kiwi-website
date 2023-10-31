<?php

/**
 * @todo
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;
 
/**
 * Create questions for CAPTCHA.
 *
 * Derived from "Script para la generación de CAPTCHAS" by Jose Rodrigueze - http://code.google.com/p/cool-php-captcha
 *
 * @author  Jose Rodriguez <jose.rodriguez@exec.cl>
 * @author  Ryan Hellyer <ryanhellyer@gmail.com>
 * @license GPLv3
 */
class Question {

	/**
	 * The Config class instance.
	 *
	 * @var \SpamDestroyer\Config
	 */
	private $config;

	/**
	 * The Encryption class instance.
	 *
	 * @var \SpamDestroyer\Frontend\Encryption
	 */
	private $encryption;

	/**
	 * Class constructor.
	 * @param \SpamDestroyer\Config $config The Config instance.
	 * @param \SpamDestroyer\Frontend\Encryption $encryption The Encryption instance.
	 */
	public function __construct( \SpamDestroyer\Config $config,  \SpamDestroyer\Frontend\Encryption $encryption ) {
		$this->config     = $config;
		$this->encryption = $encryption;
	}

	/**
	 * Get the encrypted text.
	 * The encrypted text is stored alongside the time stamp, so we need to separate them.
	 *
	 * @return string Encrypted text.
	 */
	public function get_encrypted_question() {

		// Get a question
		$text = $this->get_question();

		// Add the time to the text.
		$text .= '|||' . time();

		// Return encrypted text string.
		return $this->encryption->encrypt( $text );
	}

	/**
	 * Text generation.
	 *
	 * @return string Text
	 */
	protected function get_question() {
		$text = $this->get_dictionary_captcha_text();
		if ( ! $text ) {
			$text = $this->get_random_captcha_text();
		}
		return $text;
	}

	/**
	 * Random text generation.
	 *
	 * @return string Text
	 */
	protected function get_random_captcha_text( $length = null ) {
		if ( empty( $length ) ) {
			$length = rand( $this->min_word_length, $this->max_word_length );
		}

		$words  = "abcdefghijlmnopqrstvwyz";
		$vocals = "aeiou";

		$text  = '';
		$vocal = rand( 0, 1 );
		for ( $i = 0; $i < $length; $i++ ) {
			if ( $vocal ) {
				$text .= substr( $vocals, mt_rand( 0, 4 ), 1 );
			} else {
				$text .= substr( $words, mt_rand( 0, 22 ), 1 );
			}
			$vocal = !$vocal;
		}
		return $text;
	}

	/**
	 * Random dictionary word generation.
	 *
	 * @param boolean $extended Add extended "fake" words
	 * @return string Word
	 */
	function get_dictionary_captcha_text( $extended = false ) {
		$words_file = apply_filters( 'spam_destroyer_word_file', SPAM_DESTROYER_DIR . '/assets/words.txt' );

		$fp     = fopen( $words_file, 'r' );
		$length = strlen( fgets( $fp ) );
		if ( ! $length ) {
			return false;
		}

		$line   = rand( 1, ( filesize( $words_file ) / $length ) - 2 );
		if ( fseek( $fp, $length * $line ) == -1 ) {
			return false;
		}
		$text = trim( fgets( $fp ) );
		fclose( $fp );

		// Change ramdom vowels
		if ( $extended ) {
			$text   = preg_split( '//', $text, -1, PREG_SPLIT_NO_EMPTY );
			$vocals = array( 'a', 'e', 'i', 'o', 'u' );
			foreach ( $text as $i => $char ) {
				if ( mt_rand( 0, 1 ) && in_array( $char, $vocals ) ) {
					$text[$i] = $vocals[mt_rand( 0, 4 )];
				}
			}
			$text = implode( '', $text );
		}

		return $text;
	}

}
