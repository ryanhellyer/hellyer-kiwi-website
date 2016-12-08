<?php

/**
 * Options page for setting membership number.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Free_Advice_Berlin_Number_of_Members {

	/**
	 * Set some constants for setting options.
	 */
	const MENU_SLUG = 'number-of-members';
	const GROUP     = 'number-of-members-group';
	const OPTION    = 'number-of-members';

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {

		// Add to hooks
		add_action( 'admin_init',           array( $this, 'register_settings' ) );
		add_action( 'admin_menu',           array( $this, 'create_admin_page' ) );
		add_shortcode( 'number_of_members', array( $this, 'number_shortcode' ) );
		add_shortcode( 'number_of_members_updated_date', array( $this, 'date_shortcode' ) );
	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {
		register_setting(
			self::GROUP,               // The settings group name
			self::OPTION,              // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);
	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {
		add_options_page(
			__ ( 'Membership number', 'free-advice-berlin' ), // Page title
			__ ( 'Membership number', 'free-advice-berlin' ), // Menu title
			'manage_options',                                 // Capability required
			self::MENU_SLUG,                                  // The URL slug
			array( $this, 'admin_page' )                      // Displays the admin page
		);
	}

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		?>
		<div class="wrap">
			<h1><?php _e( 'Number of members', 'free-advice-berlin' ); ?></h1>
			<p><?php _e( 'This allows you to set the number of group members we have.', 'free-advice-berlin' ); ?></p>

			<form method="post" action="options.php">

				<table class="form-table">
					<tr>
						<th>
							<label for="<?php echo esc_attr( self::OPTION ); ?>"><?php _e( 'Enter the number of group members.', 'free-advice-berlin' ); ?></label>
						</th>
						<td>
							<input type="text" id="<?php echo esc_attr( self::OPTION ); ?>" name="<?php echo esc_attr( self::OPTION ); ?>" value="<?php echo esc_attr( get_option( self::OPTION ) ); ?>" />
						</td>
					</tr>
					<tr>
						<th>
							<label for="date"><?php _e( 'Last updated', 'free-advice-berlin' ); ?></label>
						</th>
						<td>
							<input type="text" disabled="disabled" value="<?php echo date( 'Y-m-d', get_option( 'number-of-members-updated' ) ); ?>" />
						</td>
					</tr>
				</table>

				<?php settings_fields( self::GROUP ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'free-advice-berlin' ); ?>" />
				</p>
			</form>
		</div><?php
	}

	/**
	 * Sanitize the page or product ID
	 *
	 * @param   string   $input   The input string
	 * @return  array             The sanitized string
	 */
	public function sanitize( $input ) {
		$output = absint( $input );
		update_option( 'number-of-members-updated', time() );

		return $output;
	}

	/**
	 * The [number_of_members] shortcode which outputs the number of members to the page.
	 */
	public function number_shortcode() {
		return absint( get_option( self::OPTION ) );
	}

	/**
	 * The [number_of_members_updated_date] shortcode which outputs the last updated date.
	 */
	public function date_shortcode() {
		return date( 'Y-m-d', get_option( 'number-of-members-updated' ) );
	}

}
