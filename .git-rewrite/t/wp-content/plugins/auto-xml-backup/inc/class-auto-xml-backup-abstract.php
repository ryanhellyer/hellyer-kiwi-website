<?php

/**
 * Admin panel class.
 */
class Auto_XML_Backup_Abstract {

	const SLUG = 'auto-xml-backup';

	/**
	 * Get the settings page URL.
	 *
	 * @return  string  The settings page URL
	 */
	public function get_settings_url() {
		return admin_url() . 'options-general.php?page=' . self::SLUG;
	}

	/**
	 * On activation, set a time, frequency and name of an action hook to be scheduled.
	 */
	public function schedule_event() {

		// Schedule the Cron task
		$first_run_time = current_time ( 'timestamp' ) + $this->schedule;
//		wp_schedule_event( $first_run_time, 'auto-xml-schedule', 'auto_xml_backup' );
wp_schedule_event( $first_run_time, 'twicedaily', 'auto_xml_backup' );
	}

	/**
	 * On deactivation, remove all functions from the scheduled action hook.
	 */
	public function deschedule_event() {
		wp_clear_scheduled_hook( 'auto_xml_backup' );
	}

}
