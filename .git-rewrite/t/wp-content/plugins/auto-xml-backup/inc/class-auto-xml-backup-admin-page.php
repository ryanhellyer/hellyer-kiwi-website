<?php

/**
 * Admin panel class.
 */
class Auto_XML_Backup_Admin_Page extends Auto_XML_Backup_Abstract {

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {

		// Add to hooks
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_menu', array( $this, 'create_admin_page' ) );
	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {
		register_setting(
			self::SLUG,                // The settings group name
			self::SLUG,                // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);
	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {
		add_options_page(
			__ ( 'Auto XML Backup', 'auto-xml-backup' ), // Page title
			__ ( 'Auto XML Backup', 'auto-xml-backup' ), // Menu title
			'manage_options',                            // Capability required
			self::SLUG,                                  // The URL slug
			array( $this, 'admin_page' )                 // Displays the admin page
		);
	}

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		$option = (array) get_option( self::SLUG );
		if ( ! isset( $option['addresses'] ) ) {
			$option['addresses'] = '';
		}

		if ( ! isset( $option['schedule'] ) ) {
			$option['schedule'] = WEEK_IN_SECONDS;
		}

		$schedules = array(
			__( 'Every minute (testing only)', 'auto-xml-backup' )  => 60,
			__( 'Hourly', 'auto-xml-backup' )  => HOUR_IN_SECONDS,
			__( 'Daily', 'auto-xml-backup' )   => DAY_IN_SECONDS,
			__( 'Weekly', 'auto-xml-backup' )  => WEEK_IN_SECONDS,
			__( 'Monthly', 'auto-xml-backup' ) => 30 * DAY_IN_SECONDS,
			__( 'Yearly', 'auto-xml-backup' )  => YEAR_IN_SECONDS,
		);
		?>
		<div class="wrap">
			<h1><?php _e( 'Auto XML Backup', 'auto-xml-backup' ); ?></h1>
			<p><?php _e( 'Any email addresses listed here, will receive a copy of the sites XML export data.', 'auto-xml-backup' ); ?></p>

			<form method="post" action="options.php">

				<table class="form-table">

					<tr>
						<th>
							<label for="<?php echo esc_attr( self::SLUG . '[addresses]' ); ?>"><?php _e( 'Enter a comma delimited list of email addresses.', 'auto-xml-backup' ); ?></label>
						</th>
						<td>
							<textarea style="width:100%;height:100px;" id="<?php echo esc_attr( self::SLUG . '[addresses]' ); ?>" name="<?php echo esc_attr( self::SLUG . '[addresses]' ); ?>"><?php echo esc_textarea( $option['addresses'] ); ?></textarea>
						</td>
					</tr>

					<tr>
						<th>
							<label for="<?php echo esc_attr( self::SLUG . '[schedule]' ); ?>"><?php _e( 'How often should emails be sent?', 'auto-xml-backup' ); ?></label>

							THIS HAS BEEN DEACTIVATED UNTIL BUGS HAVE BEEN IRONED OUT. NOW DEFAULTS TO TWICE DAILY!
						</th>
						<td>
							<select id="<?php echo esc_attr( self::SLUG . '[schedule]' ); ?>" name="<?php echo esc_attr( self::SLUG . '[schedule]' ); ?>"><?php
							foreach ( $schedules as $name => $time ) {
								echo '<option ' . selected( $option['schedule'], $time, false ) . ' value="' . esc_attr( $time ) . '">' . esc_html( $name ) . '</option>';
							}
							?></select>
						</td>
					</tr>

				</table>

				<?php settings_fields( self::SLUG ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'auto-xml-backup' ); ?>" />
				</p>
			</form>
		</div><?php
	}

	/**
	 * Sanitize the field inputs.
	 *
	 * @param   array  $input The input string
	 * @return  array  The sanitized array
	 */
	public function sanitize( $input ) {
		$output['addresses'] = '';
		$output['schedule']  = WEEK_IN_SECONDS;

		// Sanitize the email addresses
		if ( isset( $input['addresses'] ) ) {
			$emails = explode( ',', $input['addresses'] );
			foreach ( $emails as $email ) {
				$email = trim( $email );
				$email = sanitize_email( $email );
				if ( '' != $email ) {
					$output['addresses'] .= $email . ',';
				}
			}
		}

		// Sanitize the schedule
		if ( isset( $input['schedule'] ) ) {
			$output['schedule'] = absint( $input['schedule'] );
		}

		// Reset the schedule
		$this->deschedule_event();
		$this->schedule_event();

		return $output;
	}

}
