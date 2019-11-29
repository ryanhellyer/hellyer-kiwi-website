<?php

/**
 * Facebook login setup.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Free Advice Berlin
 * @since Free Advice Berlin 1.0
 */
class Free_Advice_Berlin_Facebook {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'fab_before_comments', array( $this, 'login' ) );
	}

	/**
	 * The login code.
	 */
	public function login() {
		echo '
<fb:login-button id="facebook-login" size="xlarge" scope="public_profile,email" onlogin="checkLoginState();">
	Log in to give input
</fb:login-button>
';
	}

}
