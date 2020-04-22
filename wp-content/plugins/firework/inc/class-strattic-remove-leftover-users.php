<?php

/**
 * Removes left over users from previous hosting companies.
 * Detects presence of old accounts by email address.
 * 
 * @copyright Copyright (c), Strattic
 * @since 1.4
 */
class Strattic_Remove_Leftover_Users extends Strattic_Core {

	/**
	 * Class constructor
	 */
	public function __construct() {

		if ( $this->is_emails_bad() ) {
			add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
		}

	}

	/**
	 * If there are bad email addresses.
	 *
	 * @return  bool  true if bad emails present, false if not
	 */
	private function is_emails_bad() {

		if  ( 0 < count( $this->get_users() ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Public getter for is_emails_bad().
	 * Used for unit testing.
	 *
	 * @return  bool  true if bad emails present, false if not
	 */
	public function get_is_emails_bad() {
		return $this->is_emails_bad();
	}

	/**
	 * Get users with bad email addresses.
	 */
	public function get_users() {
		global $wpdb; 

		$domains = array( 'wpengine.com' );

		$str  =''; //Initialize string;
		for ( $i = 0; $i < count( $domains ); $i++ ) {

			if ($i == count( $domains ) - 1 ) {
				$str.= " user_email like '%{$domains[$i]}' ";
			} else {
				$str.= " user_email like '%{$domains[$i]}' or ";
			}

		}

		// Use direct query to avoid loading all users at once
		$users = $wpdb->get_results( "SELECT * FROM $wpdb->users WHERE $str" );

		return $users;
	}

	/**
	 * Display Admin Notice, pointing out the presence of users from previous webhosts.
	 */
	public function display_admin_notice() {
//delete_site_option( 'strattic-ignore-bad-users' );
		$screen = get_current_screen(); 
		if (
			isset( $screen->base ) && 'toplevel_page_strattic' == $screen->base
			&&
			'yes' !== get_site_option( 'strattic-ignore-bad-users' )
		) {

			$url = wp_nonce_url( admin_url( 'admin.php?page=strattic&ignore-bad-users-warning=true' ), 'ignore-bad-users-notice-nonce' );

			$users = $this->get_users();
			$list_of_users = '';
			foreach ( $users as $count => $user ) {
				$list_of_users .= '<a href="' . esc_url( get_edit_user_link( $user->ID ) ) . '">' . esc_html( $user->display_name ) . '</a>';

				if ( $count !== ( count( $users ) - 1 ) ) {
					$list_of_users .= ', ';
				}

			}

			echo '
			<div class="error">
				<p>
					' . esc_html__( 'Your site appears to have some leftover user accounts from previous webhosts. You may like to remove these from your website.', 'strattic' ) . '
					<br /><br />
					The users are: ' . wp_kses_post( $list_of_users ) . '
					<br />
					&nbsp;
					<a class="button" href="' . esc_url( $url ) . '">' . esc_html__( 'Hide this message forever', 'strattic'  ) . '</a>
				</p>
			</div>';

		}

	}

	/**
	 * Set the plugin to no longer bug users if user asks not to be.
	 */
	public function set_no_bug() {

		// Bail out if not on correct page
		if (
			isset( $_GET['_wpnonce'] )
			&&
			(
				wp_verify_nonce( $_GET['_wpnonce'], 'ignore-bad-users-notice-nonce' )
				&&
				is_admin()
				&&
				isset( $_GET[ 'ignore-bad-users-warning' ] )
				&&
				current_user_can( $this->permissions )
			)
		) {
			add_site_option( 'strattic-ignore-bad-users', 'yes' );
		}

	}

}
