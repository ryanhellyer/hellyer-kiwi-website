<?php
/*

Plugin Name: Spam Destroyer Pro
Plugin URI: http://geek.ryanhellyer.net/products/spam-destroyer-pro/
Description: Kills spam dead in it's tracks
Author: Ryan Hellyer
Version: 1.6
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2013 Ryan Hellyer




Based on the following plugins ...

Cookies for Comments by Donncha O Caoimh
http://ocaoimh.ie/cookies-for-comments/

WP Hashcash by Elliot Back
http://wordpress-plugins.feifei.us/hashcash/



This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/

define( 'SPAM_DESTROYER_PRO_VERSION', '1.6' ); // version number
define( 'SPAM_DESTROYER_PRO_URL', plugins_url( '/',  __FILE__ ) ); // URL for the plugins folder

/**
 * Spam Destroyer Pro class
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Spam_Destroyer_Pro {

	private $spam_key; // Key used for confirmation of bot-like behaviour
	private $captcha_question; // Question for CAPTCHA
	private $captcha_value; // Value of CAPTCHA
	private $speed = 5; // Will be killed as spam if posted faster than this
	private $protection = 'low'; // Current protection level (intended for future iterations of the plugin)
	private $auto_delete = false; // Automatically delete spam messages?
	private $cookie_lifetime = DAY_IN_SECONDS; // If users comment this period of time AFTER opening the post, they will be detected as spam

	/**
	 * Preparing to launch the almighty spam attack!
	 * Spam, prepare for your imminent death!
	 */
	public function __construct() {

		// Add filters
		add_filter( 'preprocess_comment',                   array( $this, 'check_for_comment_evilness' ) ); // Support for regular post/page comments
		add_filter( 'wpmu_validate_blog_signup',            array( $this, 'check_for_registration_evilness' ) ); // Support for multisite site signups
		add_filter( 'wpmu_validate_user_signup',            array( $this, 'check_for_registration_evilness' ) ); // Support for multisite user signups
		add_filter( 'bbp_new_topic_pre_content',            array( $this, 'check_for_registration_evilness' ), 1 ); // Support for bbPress topics
		add_filter( 'bbp_new_reply_pre_content',            array( $this, 'check_for_registration_evilness' ), 1 ); // Support for bbPress replies

		// Add to hooks
		add_action( 'init',                                 array( $this, 'set_key' ) );
		add_action( 'init',                                 array( $this, 'set_captcha' ) );

		add_action( 'bp_signup_validate',                   array( $this, 'check_for_registration_evilness' ) ); // Support for BuddyPress signups
		add_action( 'register_post',                        array( $this, 'check_for_registration_evilness' ) ); // Support for bbPress signups

		add_action( 'comment_form',                         array( $this, 'extra_input_field' ) ); // WordPress comments page (do not use "comment_form_after_fields" as not all themes include that)
		add_filter( 'gform_form_tag',                       array( $this, 'gravityforms_input_field' ), 10, 2 ); // Gravity Forms

		add_action( 'signup_hidden_fields',                 array( $this, 'extra_input_field' ) ); // WordPress multi-site signup page
		add_action( 'bp_custom_profile_edit_fields',        array( $this, 'extra_input_field' ) ); // BuddyPress signup page
		add_action( 'bbp_theme_before_topic_form_content',  array( $this, 'extra_input_field' ) ); // bbPress signup page
		add_action( 'bbp_theme_before_reply_form_content',  array( $this, 'extra_input_field' ) ); // bbPress signup page
		add_action( 'register_form',                        array( $this, 'extra_input_field' ) ); // bbPress user registration page
	}

	/**
	 * Set CAPTCHA question
	 * 
	 * Uses day of the week and numerical representation
	 * of a nonce to create a simple math question
	 */
	public function set_captcha() {

		// First number from date
		$number1 = date( 'N' ); // Grab day of the week
		$number1 = $number1 / 2;
		$number1 = (int) $number1;

		// Second number from nonce
		$string = wp_create_nonce( 'spam-killer' ); // Using a nonce since it's semi-random but also consistent over a set time period
		$number2 = ord( $string ); // Convert string to numerical number
		$number2 = $number2 / 20;
		$number2 = (int) $number2;

		$this->captcha_value = $number1 + $number2;
		$this->captcha_question = $number1 . ' + ' . $number2;
	}

	/**
	 * Set spam key
	 * Needs set at init due to using nonces
	 */
	public function set_key() {

		// set spam key using home_url() and new nonce as salt
		$string = home_url() . wp_create_nonce( 'spam-killer' );
		$this->spam_key = md5( $string );

	}

	/**
	 * Loading the javascript payload
	 */
	public function load_payload() {

		// Ignore if user is logged in
		if ( is_user_logged_in() )
			return;

		// Load the payload
		wp_enqueue_script(
			'kill_it_dead',
			plugins_url( 'kill.js',  __FILE__ ),
			'',
			'1.2',
			true
		);

		// Set the key as JS variable for use in the payload
		wp_localize_script(
			'kill_it_dead',
			'spam_destroyer',
			array(
				'key'      => $this->spam_key, 
				'lifetime' => $this->cookie_lifetime,
			)
		);

	}

	/**
	 * echo the extra input field
	 */
	public function extra_input_field() {
		echo $this->get_extra_input_field();
	}

	/**
	 * Display extra input field for Gravity Forms
	 */
	public function gravityforms_input_field( $form ) {
		return $form . $this->get_extra_input_field();
	}

	/**
	 * An extra input field, which is intentionally filled with garble, but will be replaced dynamically with JS later
	 *
	 * return string
	 */
	public function get_extra_input_field() {

		// Ignore if user is logged in
		if ( is_user_logged_in() )
			return;

		// Hidden input field for those with JavaScript turned on
		$string = '<input type="hidden" id="killer_value" name="killer_value" value="' . md5( rand( 0, 999 ) ) . '"/>';

		// Those without JavaScript on get the CAPTCHA
		$string .= '<noscript>';
		$string .= '<p class="spam-killer">';
		$string .= '<label>' . __( 'What is', 'spam-killer' ) . ' ' . $this->captcha_question . '?</label>';
		$string .= '<input type="text" id="killer_captcha" name="killer_captcha" value="" />';
		$string .= '</p>';
		$string .= '</noscript>';

		// Enqueue the payload - placed here so that it is ONLY used when on a page utilizing the plugin
		$this->load_payload();

		return $string;
	}

	/**
	 * Kachomp! Be gone evil demon spam!
	 * Checks if the user is doing something evil
	 * If they're detected as being evil, then the little bastards are killed dead in their tracks!
	 * 
	 * @param array $comment The comment
	 * @return array The comment
	 */
	public function check_for_comment_evilness( $comment ) {

		// If the user is logged in, then they're clearly trusted, so continue without checking
		if ( is_user_logged_in() )
			return $comment;

		$type = $comment['comment_type'];

		// Process trackbacks and pingbacks
		if ( $type == "trackback" || $type == "pingback" ) {

			// Check the website's IP against the url it's sending as a trackback, mark as spam if they don't match
			$server_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			$web_ip = gethostbyname( parse_url( $comment['comment_author_url'], PHP_URL_HOST ) );
			if ( $server_ip != $web_ip ) {
				$this->kill_spam_dead( $comment ); // Patchooo! Website IP doesn't match server IP, therefore kill it dead as a pancake :)
			}

			// look for our link in the page itself
			// Work out the link we're looking for
			$permalink = get_permalink( $comment['comment_post_ID'] );
			$permalink = preg_replace( '/\/$/', '', $permalink );

			// Download the trackback/pingback
			$response = wp_remote_get( $comment['comment_author_url'] );
			if ( 200 == $response['response']['code'] ) {
				$page_body = $response['body'];
			} else {
				// BAM! Suck on that sploggers! Page doesn't exist, therefore kill the little bugger dead in it's tracks
				$this->kill_spam_dead( $comment );
			}

			// Look for permalink in trackback/pingback page body
			$pos = strpos( $page_body, $permalink );
			if ( $pos === false ) {
			} else {
				// Whammo! They didn't even mention us, so killing the blighter since it's of no interest to us anyway
				$this->kill_spam_dead( $comment );
			}

		} else {

			// Check for cookies presence
			if ( isset( $_COOKIE[ $this->spam_key ] ) ) {
				// If time not set correctly, then assume it's spam
				if ( $_COOKIE[$this->spam_key] > 1 && ( ( time() - $_COOKIE[$this->spam_key] ) < $this->speed ) ) {
					$this->kill_spam_dead( $comment ); // Something's up, since the commenters cookie time frame doesn't match ours
				}
			} else {
				$this->kill_spam_dead( $comment ); // Ohhhh! Cookie not set, so killing the little dick before it gets through!
			}

			// Set the captcha variable (if JS is turned on, then it won't have been set)
			$killer_captcha = '';
			if ( isset( $_POST['killer_captcha'] ) ) {
				$killer_captcha = $_POST['killer_captcha'];
			}

			// Kill spam dead if CAPTCHA and hidden field are not set correctly
			if ( $killer_captcha != $this->captcha_value && $_POST['killer_value'] != $this->spam_key ) {
				$this->kill_spam_dead( $comment );
			}

		}

		// YAY! It's a miracle! Something actually got listed as a legit comment :) W00P W00P!!!
		return $comment;
	}

	/**
	 * Kills splogger signups, BuddyPress and bbPress spammers dead in their tracks
	 * This method is an alternative to pouring kerosine on sploggers and lighting a match.
	 *
	 * @param array $result The result of the registration submission
	 * @return array $result The result of the registration submission
	 */
	public function check_for_registration_evilness( $result ) {

		// Ignore if user is logged in
		if ( is_user_logged_in() )
			return $result;

		// Check for cookies presence
		if ( isset( $_COOKIE[ $this->spam_key ] ) ) {
			// If time not set correctly, then assume it's spam
			if ( $_COOKIE[$this->spam_key] > 1 && ( ( time() - $_COOKIE[$this->spam_key] ) < $this->speed ) ) {
				$this->kill_spam_registration_dead(); // Something's up, since the cookie time frame doesn't match ours
			}
		} else {
			$this->kill_spam_registration_dead(); // Ohhhh! Cookie not set, so killing the little dick before it gets through!
		}

		// Set the captcha variable
		if ( isset( $_POST['killer_captcha'] ) ) {
			$killer_captcha = $_POST['killer_captcha'];
		} else {
			$killer_captcha = '';
		}

		if ( $killer_captcha != $this->captcha_value ) {
			$this->failed_captcha(); // Woops! They failed the captcha, but best not kill it in case they're a real person!
		}
		if ( $_POST['killer_value'] != $this->spam_key ) {
			$this->kill_spam_registration_dead(); // Ding dong the spam is dead!
		}

		return $result;
	}

	/*
	 * Error message for spammers
	 */
	public function spam_commenter_error() {

		$error = sprintf(
			'<p>' . __( 'Sorry, but our system automatically detected your message as spam.<br />If you feel this is an error then please contact an administrator.', 'spam-destroyer' ) . '</p>' . 
			'<p>' . __( 'We require you to post the comment less than %s hours after loading the post/page.<br />If you have cookies turned off, please turn them back on and resubmit your comment.', 'spam-destroyer' ) . '</p>',
			( $this->cookie_lifetime / 60 / 60 ) // Cookie length time in hours
		);

		wp_die( $error, '403 Forbidden', array( 'response' => 403 ) );
	}

	/**
	 * Error message for those who fail the CAPTCHA
	 */
	public function failed_captcha() {
		$error = __( '<strong>Sorry, but you did not answer the CAPTCHA correctly.', 'spam-destroyer' );

		wp_die( $error, '403 Forbidden', array( 'response' => 403 ) );
	}

	/**
	 * If they attempt to register illegally, they will suffer the consequences ... 
	 */
	public function kill_spam_registration_dead() {
		$error = '
		<p>' . __( 'Sorry, but our system automatically detected your registration as spam.<br />If you feel this is an error then please contact an administrator.', 'spam-destroyer' ) . '</p>
		<p>' . __( 'If you have cookies turned off, please turn them back on and resubmit your comment.', 'spam-destroyer' ) . '</p>';

		wp_die( $error, '403 Forbidden', array( 'response' => 403 ) );
	}

	/**
	 * Be gone evil demon spam!
	 * Kill spam dead in it's tracks :)
	 * 
	 * @param array $comment The comment
	 */
	public function kill_spam_dead( $comment ) {

		// Only store spam comments if auto-delete is turned off
		if ( $this->auto_delete == false ) {
			$data = array(
				'comment_post_ID'      => (int) $_POST['comment_post_ID'],
				'comment_author'       => esc_html( $_POST['author'] ),
				'comment_author_email' => esc_html( $_POST['email'] ),
				'comment_author_url'   => esc_url( $_POST['url'] ),
				'comment_content'      => esc_html( $_POST['comment'] ),
				'comment_author_IP'    => esc_html( $_SERVER['REMOTE_ADDR'] ),
				'comment_agent'        => esc_html( $_SERVER['HTTP_USER_AGENT'] ),
				'comment_date'         => date( 'Y-m-d H:i:s' ),
				'comment_date_gmt'     => date( 'Y-m-d H:i:s' ),
				'comment_approved'     => 'spam',
			);
			$comment_id = wp_insert_comment( $data );
		}

		// Serve error message
		$this->spam_commenter_error();

	}

}
$spam_destroyer = new Spam_Destroyer_Pro();
