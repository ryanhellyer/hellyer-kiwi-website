<?php

/**
 * Extra links found.
 *
 * @copyright Copyright (c) 2018, Strattic
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Strattic_Extra_Links extends Strattic_Core {

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {

		// Add hooks
		add_action( 'admin_init',        array( $this, 'register_settings' ) );
		add_action( 'strattic_settings', array( $this, 'admin_page' ), 20 );
		add_action( 'admin_init',        array( $this, 'include_sortable_script' ) );
	}

	/**
	 * Including sortable script.
	 */
	public function include_sortable_script() {
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {

		$a = 'strattic-extra-links';
		$b = 'strattic';
		add_settings_section(
			$a,
			esc_html__( 'Extra Links', 'strattic' ),
			null,
			$b
		);
		add_settings_field(
			$a,
			esc_html__( 'Extra Links field', 'strattic' ),
			array( $this, 'sanitize' ),
			$b,
			$a
		);
		register_setting(
			'strattic-settings',   // The settings group name
			$a,   // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);

	}

	/**
	 * Output the admin page.
	 *
	 * @global  string  $title  The page title set by add_submenu_page()
	 */
	public function admin_page() {

		add_action( 'admin_footer',  array( $this, 'scripts' ) );

		?>

		<h2><?php esc_html_e( 'Extra links', 'strattic' ); ?></h2>

		<table class="form-table">
			<tbody id="add-rows"><?php

			// Grab options array and output a new row for each setting
			$options = get_option( 'strattic-extra-links' );

			if ( is_array( $options ) ) {

				foreach( get_option( 'strattic-extra-links' ) as $key => $value ) {
					$url = esc_url( home_url() . $value );
					echo $this->get_row( $url );
				}

			}

			echo $this->get_row();

			?>
			</tbody>

		</table>

		<input type="button" id="add-new-row" class="button" value="<?php esc_html_e( 'New URL', 'strattic' ); ?>" /><?php
	}

	/**
	 * Get a single table row.
	 *
	 * @param  string  $value  Option value
	 * @return string  The table row HTML
	 */
	public function get_row( $value = '' ) {

		// Create the required HTML
		$row_html = '

					<tr class="sortable">
						<td>
							<input type="text" name="strattic-extra-links[]" value="' . esc_url( $value ) . '" />
						</td>
					</tr>';

		// Strip out white space
		$row_html = str_replace( '	', '', $row_html );
		$row_html = str_replace( "\n", '', $row_html );

		// Return the final HTML
		return $row_html;
	}

	/**
	 * Sanitize the input.
	 *
	 * @todo    improve santization method
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {

		$output = array();
		if ( is_array( $input ) ) {
			foreach ( $input as $key => $value ) {
				$value = str_replace( home_url(), '', $value ); // Strip home URL from the input (it gets added to save confusing the user, but we have no reason to store it)

				// We can ignore blank values, since they're just a blank input field
				if ( '' === $value ) {
					continue;
				}

				$output[] = wp_kses_post( $value );
			}
		}

		$output = array_unique( $output );

		return $output;
	}

	/**
	 * Output scripts into the footer.
	 * This is not best practice, but is implemented like this here to ensure that it can fit into a single file.
	 */
	public function scripts() {

		?>
		<style>
		.strattic-paths input[type=text],
		.strattic-manual-links input[type=text],
		.strattic-discovered-links input[type=text] {
			width: 100%;
		}
		.strattic-discovered-links #add-new-row {
			display: none;
		}
		.sortable textarea {
			width: 48%;
			margin-right: 1%;
		}
		.sortable .toggle {
			display: inline !important;
		}
		</style>
		<script>

			jQuery(function($){

				/**
				 * Adding some buttons
				 */
				function add_buttons() {

					// Loop through each row
					$( ".sortable" ).each(function() {

						// If no input field found with class .remove-setting, then add buttons to the row
						if(!$(this).find('input').hasClass('remove-setting')) {

							// Add a remove button
							$(this).append('<td><input type="button" class="remove-setting" value="X" /></td>');

							// Remove button functionality
							$('.remove-setting').click(function () {
								$(this).parent().parent().remove();
							});

						}

					});

				}

				// Create the required HTML (this should be added inline via wp_localize_script() once JS is abstracted into external file)
				var html = '<?php echo $this->get_row( '' ); ?>';

				// Add the buttons
				add_buttons();

				// Add a fresh row on clicking the add row button
				$( "#add-new-row" ).click(function() {
					$( "#add-rows" ).append( html ); // Add the new row
					add_buttons(); // Add buttons tot he new row
				});

				// Allow for resorting rows
				$('#add-rows').sortable({
					axis: "y", // Limit to only moving on the Y-axis
				});

 			});

		</script><?php
	}

}
