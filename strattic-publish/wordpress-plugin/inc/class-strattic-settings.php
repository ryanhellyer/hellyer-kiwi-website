<?php

/**
 * Settings page.
 * Only available for Strattic Admins.
 * 
 * @copyright Copyright (c) 2018, Strattic
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.1
 */
class Strattic_Settings extends Strattic_Core {

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {

		// Add hooks
		add_action( 'admin_init',    array( $this, 'register_settings' ) );
		add_action( 'admin_menu',    array( $this, 'create_admin_page' ) );

	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {

		register_setting(
			'strattic_settings',   // The settings group name
			'strattic_settings',   // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);

	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {

		add_submenu_page(
			'strattic-submenu',
			esc_html__( 'Strattic settings', 'strattic' ),
			esc_html__( 'Strattic settings', 'strattic' ),
			'manage_options',
			'strattic-settings',
			array( $this, 'admin_page' )
		);

	}

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		?>

		<div class="wrap">
			<h2>Strattic settings</h2>

			<?php settings_errors(); ?>

			<p><?php esc_html_e( 'These settings are used when running "Publish dev". These settings only affect our internal publication tests, they do not affect the clients settings.', 'strattic' ); ?></p>

			<form method="post" action="options.php">

				<table class="form-table"><?php

				foreach ( $this->get_admin_settings() as $setting => $value ) {

					echo '
					<tr>
						<th scope="row"><label for="' . esc_attr( 'strattic_settings[' . $setting . ']' ) . '">' . esc_html( $value[ 'text' ] ) . '</label></th>
						<td>
					';

					if ( 'checkbox' === $value[ 'type' ] ) {
						echo '<input type="checkbox" name="' . esc_attr( 'strattic_settings[' . $setting . ']' ) . '"  id="' . esc_attr( 'strattic_settings[' . $setting . ']' ) . '" value="1" ' . checked( $value[ 'saved' ], true, false ) . ' />';
					} else if ( 'deployment' === $value[ 'type' ] ) {

						echo '<select name="' . esc_attr( 'strattic_settings[' . $setting . ']' ) . '" id="' . esc_attr( 'strattic_settings[' . $setting . ']' ) . '">';

						$files = array();
						echo '<option value="" />Default</option>';
						foreach ( glob( STRATTIC_DIRECTORY . '*.py' ) as $file ) {

							$file = explode( '/', $file );
							$file = end( $file );

							echo '<option ' . selected( $value[ 'saved' ], $file, false ) . ' value="' . esc_attr( $file ) . '" />' . esc_html( $file ) . '</option>';
						}

						echo '</select>';

					} else {
						echo '<input type="' . esc_attr( $value[ 'type' ] ) . '" name="' . esc_attr( 'strattic_settings[' . $setting . ']' ) . '"  id="' . esc_attr( 'strattic_settings[' . $setting . ']' ) . '" value="' . esc_attr( $value[ 'saved' ] ) . '" placeholder="' . esc_attr( $value[ 'default' ] ) . '" />';
					}

					echo '
							<p class="description">Defaults to the production ' . esc_html( strtolower( $value[ 'text' ] ) ) . ' if none specified.</p>
						</td>
					</tr>';

				}

				?>

				</table>

				<?php settings_fields( 'strattic_settings' ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save', 'strattic' ); ?>" />
				</p>

			</form>

			<?php $this->the_horizontal_menu(); ?>

		</div><?php
	}

	/**
	 * Sanitize the page or product ID.
	 *
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {
		$output = array();

		// Loop through each bit of data
		foreach( $input as $key => $value ) {
			$output[ esc_html( $key ) ] = esc_html( $value );
		}

		add_settings_error(
			'strattic-error',
			esc_attr( 'settings_updated' ),
			'The settings have been updated!',
			'updated'
		);

		// Return the sanitized data
		return $output;
	}

}
