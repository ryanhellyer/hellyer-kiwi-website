<?php

/**
 * Adds an admin notice, alerting the admin that the email address field is blank.
 */
class Auto_XML_Backup_Admin_Notice extends Auto_XML_Backup_Abstract {

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'message' ) );
	}

	/**
	 * Output the message.
	 */
	public function message() {

		// Bail out if email addresses already stored
		if ( '' != get_option( self::SLUG ) ) {
			return;
		}

		echo '
		<div class="notice notice-warning">
		<p>' . sprintf(
			__( 'Email addresses need to be added to the <a href="%s">Auto XML Backup settings page</a>.', 'auto-xml-backup' ),
			esc_url( $this->get_settings_url() )
		) . '</p>
		</div>';
	}

}
