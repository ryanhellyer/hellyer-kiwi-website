<?php

/**
 * Main plugin class.
 */
class Auto_XML_Backup_Cron extends Auto_XML_Backup_Abstract {

	var $schedule = WEEK_IN_SECONDS;

	/**
	 * Class constructor
	 */
	public function __construct() {

		// Set the user defined schedule time
		$option = (array) get_option( self::SLUG );
		if ( isset( $option['schedule'] ) ) {
			$this->schedule = absint( $option['schedule'] );
		}
//echo $this->schedule;die;
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		$file = dirname( dirname( __FILE__ ) ) . '/auto-xml-backup.php';
		register_activation_hook( $file, array( $this, 'schedule_event' ) );
		register_deactivation_hook( $file, array( $this, 'deschedule_event' ) );

		add_action( 'auto_xml_backup', array( $this, 'task' ) );
	}

	/**
	 * Adds new cron schedule option(s).
	 *
	 * @param array   $schedules Cron schedule array
	 * @return array $schedules Amended cron schedule array
	 */
	public function cron_schedules( $schedules ) {

		$schedules['auto-xml-schedule'] = array(
			'interval' => $this->schedule,
			'display'  => sprintf( __( 'Every %s seconds' ), $this->schedule )
		);

		return $schedules;
	}

	/**
	 * The WP Cron task to run.
	 */
	public function task() {
		// Get the XML dump. Output buffering is required, because WordPress only supports echo'ing the XML.
		require_once( ABSPATH . 'wp-admin/includes/export.php' );

		// We need to override the headers set by wp-admin/includes/export.php
		header( 'Content-Disposition: inline' );
		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ), true );

		ob_start();
		export_wp( array() );
		$xml = ob_get_contents();
		ob_end_clean();

		// We need to override the headers set by wp-admin/includes/export.php
		header( 'Content-Disposition: inline' );
		header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ), true );

		// We need to store the XML file for wp_mail() to use it
		$dir = wp_upload_dir();
		$basedir = $dir['basedir'];
		$file = $basedir . '/auto-xml-backup.xml';
		file_put_contents( $file, $xml );

		// Get list of email addresses
		$option = (array) get_option( self::SLUG );
		if ( isset( $option['addresses'] ) ) {
			$emails = $option['addresses'];
		}
		$emails = explode( ',', $emails );
		foreach ( $emails as $key => $email ) {
			$email = sanitize_email( $email );
			$email_addresses = $email_addresses . ',' . $email;
		}

		// Send the email
		wp_mail(
			$email_addresses,
			sprintf(
				__( 'XML Backup of %s', 'auto-xml-backup' ),
				get_bloginfo( 'title' )
			),
			sprintf(
				__( 'An XML backup file for %s is attached.', 'auto-xml-backup' ),
				get_bloginfo( 'title' )
			),
			array(), // Headers
			array( $file ) // Attachments
		);

		// Delete the XML file off the server now that we're finished with it
		unlink( $basedir . '/auto-xml-backup.xml' );

		return;
	}

}
