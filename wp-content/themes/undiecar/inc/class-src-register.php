<?php

/**
 * Register a user.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Register extends SRC_Core {

	/**
	 * Constructor.
	 * Add methods to appropriate hooks and filters.
	 */
	public function __construct() {

		// Add action hooks
		add_action( 'init',                array( $this, 'init' ) );
		add_action( 'init',                array( $this, 'process_login' ) );
		add_shortcode( 'src-register',     array( $this, 'register_shortcode' ) );
		add_shortcode( 'src-login',        array( $this, 'login_shortcode' ) );
		add_filter( 'src_register_start',  array( $this, 'register_start_fields' ) );
		add_filter( 'src_register_end',    array( $this, 'register_end_fields' ) );

	}

	/**
	 * Display registration/login/profile page shortcode content.
	 *
	 * @param   array   $args  The shortcodes arguments
	 */
	public function login_shortcode() {

		// Don't show shortcode when logged in and on front page (form serves no purpose there then)
		if ( is_user_logged_in() ) {
			return;
		}

		if ( defined( 'UNDYCAR_LOGIN_ERROR' ) ) {
			echo '<p>Woops! Something went wrong!</p>';
		}

		$content = '
<form action="" method="POST">
	<input name="src-login-name" type="text" value="" placeholder="iRacing name" required />
	<input name="src-login-password" type="password" value="" placeholder="' . esc_attr__( 'Enter your password here', 'undiecar' ) . '" required />
	<input type="submit" value="' . esc_attr__( 'Log in', 'undiecar' ) . '" />
</form>';



		return $content;
	}

	public function process_login() {

		if ( isset( $_POST['src-login-name'] ) && isset( $_POST['src-login-password'] ) ) {
			$username = sanitize_title( $_POST['src-login-name'] );


			if ( false === $this->attempt_user_login( 'XXX', $username, 'XXX', $_POST['src-login-password'] ) ) {
				define( 'UNDYCAR_LOGIN_ERROR', true );
			}
		}

	}

	/**
	 * Display registration/login/profile page shortcode content.
	 *
	 * @param   array   $args  The shortcodes arguments
	 */
	public function register_shortcode( $args ) {
		$content = '';

		// Don't show shortcode when logged in and on front page (form serves no purpose there then)
		if ( is_user_logged_in() && is_front_page() ) {
			return;
		}

		$url = '';
		if ( isset( $args['url'] ) ) {
			$url = $args['url'];
		}

		// Sanitize inputs
		$username = '';
		if ( isset( $_POST['src-name'] ) ) {
			$username = sanitize_user(  $_POST['src-name'] );
		}
		$email = '';
		if ( isset( $_POST['src-email'] ) ) {
			$email = sanitize_email( $_POST['src-email'] );
		}
		$display_name = '';
		if ( isset( $_POST['src-name'] ) ) {
			$display_name = esc_html( $_POST['src-name'] );
		}
		$password = '';
		if ( isset( $_POST['src-password'] ) ) {
			$password = $_POST['src-password'];
		}

		if ( defined( 'SRC_LOGIN_ERROR' ) ) {
			$message_text = __( 'We detected that you were already a member, so tried logging you in and something went wrong.', 'src' );
		} else if ( defined( 'SRC_IRACING_MEMBER_DOES_NOT_EXIST' ) ) {
			$message_text = __( 'That name was not found within iRacing. This often happens when an account is new (less than two months old) and iRacing has not updated their whole system yet. Simply send a private message to "<a href="http://members.iracing.com/membersite/member/CareerStats.do?custid=279455">Ryan Hellyer</a>" on iRacing.com and he will manually register your account for you.', 'src' );
		} else if ( defined( 'SRC_USERNAME_EXISTS' ) || defined( 'SRC_EMAIL_EXISTS' ) ) {

			if ( defined( 'SRC_USERNAME_EXISTS' ) ) {
				$message_text = __( 'You already have an account ;) You can login with it if you like.', 'src' );
$message_text .= ' The form below is broken, please use the login page until then ... https://undiecar.com/login/';
			}

			// If username exists, but email does, then stick display name into form and hide the email field
			if ( ! defined( 'SRC_USERNAME_EXISTS' ) || defined( 'SRC_EMAIL_EXISTS' ) ) {
				$user = get_user_by( 'email', $email );
				$message_text = __( 'You already have an account ;) You can login with it if you like.', 'src' );

$message_text .= ' The form below is broken, please use the login page until then ... https://undiecar.com/login/';

				$display_name = $user->display_name;
				define( 'SRC_USERNAME_EXISTS', true );
			}

		}

		if ( isset( $message_text ) ) {
			$content .= '<p><strong>' . wp_kses_post( $message_text ) . '</strong></p>';
		}

		$content .= '
<form action="' . esc_attr( $url ) . '" method="POST">
';

		$content = apply_filters( 'src_register_start', $content );

		$content .= '
	<input name="src-name" type="text" value="' . esc_attr( $display_name ) . '" placeholder="iRacing name" required />';

		if ( ! defined( 'SRC_USERNAME_EXISTS' ) ) {
			$content .= '
	<input name="src-email" type="email" value="' . esc_attr( $email ) . '" placeholder="Email address" required />';
		}

		if (
			( defined( 'SRC_USERNAME_EXISTS' ) || defined( 'SRC_EMAIL_EXISTS' ) )
			||
			is_user_logged_in()
		) {

			if ( defined( 'SRC_USERNAME_EXISTS' ) || defined( 'SRC_EMAIL_EXISTS' ) ) {
				$password_placeholder = __( 'Enter your password', 'src' );
			} else {
				$password_placeholder = __( 'Enter a unique password', 'src' );
			}

			$content .= '
	<input name="src-password" type="password" value="' . esc_attr( $password ) . '" placeholder="' . esc_attr( $password_placeholder ) . '" required />';
		}

		$content = apply_filters( 'src_register_end', $content );

		if ( defined( 'SRC_USERNAME_EXISTS' ) || defined( 'SRC_EMAIL_EXISTS' ) ) {
			$joinus_text = __( 'Sign in', 'src' );
		} else {
			$joinus_text = __( 'Join us', 'src' );
		}

		$content .= '
	<input type="submit" value="' . $joinus_text . '" />
</form>';


		// If argument of "echo" is set, then echo it
		if ( ! isset( $args['echo'] ) ) {
			echo $content;
		} else {
			return $content;
		}

	}

	/**
	 * Init.
	 */
	public function init() {

		if ( isset( $_POST['src-name'] ) || isset( $_POST['src-email'] ) ) {

			// Create cookie, as some signup processes are triggering 404's when there's an error
			setcookie( 'undycar_signup', true, time() + ( 86400 * 30 ), '/' );

			// Sanitize inputs
			$username     = sanitize_title( sanitize_user(  $_POST['src-name'] ) );
			$email        = sanitize_email( $_POST['src-email'] );
			$display_name = esc_html( $_POST['src-name'] );
			if ( isset( $_POST['src-password'] ) ) {
				$password = $_POST['src-password'];
			} else {
				$password = md5( rand() . $username . $email ); // They haven't entered a password, so we just make one for them
			}

			// Check if iRacing member exists
			if ( ! $member_info = $this->iracing_member_info( $display_name ) ) {
				define( 'SRC_IRACING_MEMBER_DOES_NOT_EXIST', true );
				return;
			}

			// Check if username exists
			if ( username_exists( $username ) ) {
				define( 'SRC_USERNAME_EXISTS', true );
				return;
			}

			// Check if email address exists
			if ( email_exists( $email ) ) {
				define( 'SRC_EMAIL_EXISTS', true );
				return;
			}

			// Register user and log them in
			if ( true === $this->register_user( $username, $display_name, $password, $email ) ) {
				// Log the user in
				$this->attempt_user_login( $user_id, $username, $email, $password );
			}
		}

	}

	/*
	 * The initial input fields for profile editing.
	 *
	 * @global  object  $wpdb  Accessed to allow for faster querying
	 */
	public function register_start_fields( $content ) {
		global $wpdb;

/*
		if ( is_user_logged_in() ) {
			// Work out ID of special events
			$post_title = 'Special Events';
			$special_event_col = $wpdb->get_col( "select ID from $wpdb->posts where post_title LIKE '" . $post_title . "%' ");
			$query_args = array(
				'post__in'               => $special_event_col,
				'post_type'              => 'season',
				'posts_per_page'         => 100,
				'post_status'            => array( 'any' ),
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
				'fields'                 => 'ids',
			);
			$seasons = new WP_Query( $query_args );
			if ( $seasons->have_posts() ) {
				while ( $seasons->have_posts() ) {
					$seasons->the_post();
					$special_events_id = get_the_ID();
				}
			}

			// Get all the Special Events
			$query_args = array(
				'post_type'              => 'event',
				'posts_per_page'         => 10,
				'post_status'            => array( 'future' ),
				'meta_query' => array(
					array(
						'key'     => 'season',
						'value'   => array( $special_events_id ),
						'compare' => 'IN',
					),
				),
				'no_found_rows'          => true,  // useful when pagination is not needed.
				'update_post_meta_cache' => false, // useful when post meta will not be utilized.
				'update_post_term_cache' => false, // useful when taxonomy terms will not be utilized.
				'fields'                 => 'ids',
			);

			$content .= '<p>' . __( 'Which events would you like to sign up for?', 'src' );
			$content .= '<input type="checkbox" name="src-events[]" value="' . esc_attr( 'regular-season' ) . '"><label>' . esc_html__( 'Regular Season', 'src' ) . '</label>';
			$posts = new WP_Query( $query_args );
			if ( $posts->have_posts() ) {
				while ( $posts->have_posts() ) {
					$posts->the_post();

					$content .= '<input type="checkbox" name="src-events[]" value="' . esc_attr( get_the_title( get_the_ID() ) ) . '"><label>' . esc_html( get_the_title( get_the_ID() ) ) . '</label>';
				}
			}
			$content .= '</p>';

		}
*/
		return $content;
	}

	/**
	 * Extra input fields for profile editing.
	 */
	public function register_end_fields( $content ) {

/*
		if ( is_user_logged_in() ) {
			$content .= '
	<input name="src-website" type="website" placeholder="Website address" />
	<textarea name="src-description" placeholder="Add a description about yourself here. Perhaps include previous sim or real world racing experience, favourite games etc."></textarea>';
		}
*/
		
		return $content;
	}

	/**
	 * Attempt to log the user in.
	 */
	public function attempt_user_login( $user_id, $username, $email, $password ) {

		$credentials = array();
		$credentials['user_login'] = $username;
		$credentials['user_password'] = $password;
		$credentials['remember'] = true;

		$user = wp_signon( $credentials, false );
		if ( is_wp_error( $user ) ) {
			return false;
		}

		// Redirect after login
		$redirect_to = home_url( '/member/' . sanitize_title( $username ) . '/' );
		wp_safe_redirect( $redirect_to, 302 );
		exit();

	}


}
