<?php

/**
 * Initialisation of the Facebook login process.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Simple_Facebook_Login_Init extends Simple_Facebook_Login_SDK {

	protected $messages = array();
	protected $facebook_login_url;
	protected $display_name;

	public function init() {

		if ( is_user_logged_in() ) {

			$this->messages[] = 'already-logged-in';

		} else if ( isset( $_GET[ 'simple-facebook-login' ] ) && 'callback' === $_GET[ 'simple-facebook-login' ] ) {

			$user_node = $this->process_facebook_callback();
			if ( is_object( $user_node ) ) {

				// Log in or register the Facebook user to WordPress
				$user_node->getField( 'email' );
				$email_address = $user_node[ 'email' ];
//$email_address = 'test' . rand() . '@test.com';
				if ( email_exists( $email_address ) ) {

					$user_node->getField( 'name' );
					$this->display_name = $user_node[ 'name' ];

					$this->log_user_in( $email_address, $this->display_name );

				} else {

					$user_node->getField( 'name' );
					$this->display_name = $user_node[ 'name' ];

//echo $email_address . '-' . $this->display_name;die;
//$this->display_name = 'test' . rand();
					$this->register_user( $email_address, $this->display_name );
					$this->log_user_in( $email_address, $this->display_name );

				}

			}

		} else {

			$this->facebook_login_url = $this->get_facebook_login_url();
			$this->messages[] = 'login-button';

		}

	}

	/**
	 * Get the HTML for display.
	 *
	 * @access  protected
	 * @return  string  HTML output to the page
	 */
	public function get_html() {

		$string = '';

		// Dump out messages
		if ( ! empty( $this->messages ) ) {

			// Get $messages variable
			require( dirname( __FILE__ ) . '/simple-facebook-login-messages.php' );
			$messages = apply_filters( 'simple_facebook_login_messages', $messages );
			foreach ( $this->messages as $key ) {

				if ( isset( $messages[ $key ][ 'text' ] ) ) {

					$class = '';
					if ( isset( $messages[ $key ][ 'class' ] ) ) {
						$class = $messages[ $key ][ 'class' ];
					}

					$string .= '<p class="' . esc_attr( $class ) . '">' . $messages[ $key ][ 'text' ] . '</p>';
				}
			}

		}

		$string = apply_filters( 'simple_facebook_login_html', $string );

		return $string;
	}

	/**
	 * Log the user in.
	 *
	 * @param  string  $email_address  The email address sent from Facebook
	 * @param  string  $display_name   The name sent from Facebook
	 */
	public function log_user_in( $email_address, $display_name ) {
		$user = get_user_by( 'email', $email_address );
//echo $email_address;echo "\n\n\n\n\n\n";print_r( $user );die;
		if ( isset( $user->data->user_login ) ) {
			$username = $user->data->user_login;

			if ( true === $this->programmatic_login( $username ) ) {
				$this->messages[] = 'you-have-been-logged-in';
			} else {
				$this->messages[] = 'login-process-failed';
			}


		} else {
			$this->messages[] = 'user-not-found';
		}

	}

	/**
	 * Register the user.
	 *
	 * @param  string   $email_address  The email address to register
	 * @param  string   $display_name   The display name to register
	 */
	public function register_user( $email_address, $display_name ) {

		// Create the user
		$user_data = array(
			'user_login'   => sanitize_title( $display_name ), // We create a username from the display name
			'display_name' => $display_name,
			'user_pass'    => wp_generate_password(),
			'user_email'   => $email_address,
		);
		$user_id = wp_insert_user( $user_data ) ;

		if ( ! is_wp_error( $user_id ) ) {

			$this->messages[] = 'you-are-registered';

		} else {

			$this->messages[] = 'user-generation-failed';

		}

	}

	/**
	 * Programmatically logs a user in.
	 * Based on code from https://wordpress.stackexchange.com/questions/53503/can-i-programmatically-login-a-user-without-a-password
	 * 
	 * @access protected
	 * @param  string   $username  The username to log in
	 * @return bool     true if the login was successful; false if it wasn't
	 */
	protected function programmatic_login( $username ) {

		add_filter( 'authenticate', array( $this, 'allow_programmatic_login' ), 10, 3 ); // hook in earlier than other callbacks to short-circuit them
		$user = wp_signon( array( 'user_login' => $username ) );
		remove_filter( 'authenticate', array( $this, 'allow_programmatic_login' ), 10, 3 );

		if ( is_wp_error( $user ) ) {
			return false;
		}

		if ( is_a( $user, 'WP_User' ) ) {
			wp_set_current_user( $user->ID, $user->user_login );

			if ( is_user_logged_in() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * An 'authenticate' filter callback that authenticates the user using only     the username.
	 *
	 * To avoid potential security vulnerabilities, this should only be used in     the context of a programmatic login,
	 * and unhooked immediately after it fires.
	 *
	 * Based on code from https://wordpress.stackexchange.com/questions/53503/can-i-programmatically-login-a-user-without-a-password
	 * 
	 * @param  object   $user  The WordPress user
	 * @param string    $username   The WordPress username
	 * @param string    $password   The users password
	 * @return bool|WP_User  A WP_User object if the username matched an existing user, or false if it didn't
	 */
	public function allow_programmatic_login( $user, $username, $password ) {
		return get_user_by( 'login', $username );
	}

}
