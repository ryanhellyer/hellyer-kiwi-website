<?php
/**
 * Handles the frontend functionality of the SpamDestroyer plugin.
 *
 * @package   SpamDestroyer\Frontend
 * @copyright Copyright (c) Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;

/**
 * Encryption Class
 *
 * This class is responsible for encrypting and decrypting text.
 */
class Encryption {

	/**
	 * Encrypt.
	 *
	 * @param string $text Text to encrypt.
	 * @return string Encrypted text.
	 */
	public function encrypt( string $text ): string {
		if ( function_exists( 'openssl_encrypt' ) ) {
			$text = openssl_encrypt( $text, $this->encryption_method, $this->spam_key, 0, 'ik3m3mfmenektn37' );
		}

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		$text = base64_encode( $text );
		return $text;
	}

	/**
	 * Decrypt.
	 *
	 * @param string $text Text to decrypt.
	 * @return string Decrypted text.
	 */
	public function decrypt( string $text ): string {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$text = base64_decode( $text );

		if ( function_exists( 'openssl_decrypt' ) ) {
			$text = openssl_decrypt(
				$text,                            // The text to be decrypted.
				$this->config::ENCRYPTION_METHOD, // The cipher method.
				$this->config->get_spam_key(),    // The password.
				0,                                // Options - leave at 0.
				'ik3m3mfmenektn37'                // Initialization vector.
			);
		}

		return $text;
	}
}
