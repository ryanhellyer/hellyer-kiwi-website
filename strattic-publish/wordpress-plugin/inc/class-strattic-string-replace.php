<?php

/**
 * String Replacement.
 * Adds an admin page which allows for dynamically modifying strings of text in any page.
 * 
 * @copyright Copyright (c) 2018, Strattic
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class Strattic_String_Replace extends Strattic_Core {

	/**
	 * Fire the constructor up :D
	 */
	public function __construct() {

		// Add filter
		add_filter( 'strattic_buffer', array( $this, 'string_replace' ) );

		// Add hooks
		add_action( 'admin_init',    array( $this, 'register_settings' ) );
		add_action( 'admin_menu',    array( $this, 'create_admin_page' ) );

	}

	public function string_replace( $html ) {
		$replacements = get_option( 'strattic-string-replacement' );

		foreach ( $replacements as $key => $replacement ) {
			$search = $replacement[ 'search' ];
			$replace = $replacement[ 'replace' ];

			$html = str_replace( $search, $replace, $html );
		}

		return $html;
	}

	/**
	 * Init plugin options to white list our options.
	 */
	public function register_settings() {

		register_setting(
			'strattic-string-replacement', // The settings group name
			'strattic-string-replacement', // The option name
			array( $this, 'sanitize' )     // The sanitization callback
		);

	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {

		add_submenu_page(
			'strattic-submenu',
			esc_html__( 'String Replacement', 'strattic' ),
			esc_html__( 'String Replacement', 'strattic' ),
			'manage_options',
			'strattic-string-replacement',
			array( $this, 'admin_page' )
		);

	}

	/**
	 * Output the admin page.
	 *
	 * @global  string  $title  The page title set by add_submenu_page()
	 */
	public function admin_page() {
		global $title;

		add_action( 'admin_footer',  array( $this, 'scripts' ) );

		?>

		<div class="wrap">
			<h2><?php echo esc_html( $title ); ?></h2>

			<?php settings_errors(); ?>

			<p>
				<?php esc_html_e( 'You may perform live string replacements of the site here.', 'strattic' ); ?>
			</p>

			<form method="post" action="options.php" class="strattic-string-replacement">

				<table class="form-table">
					<tbody id="add-rows"><?php

					// Grab options array and output a new row for each setting
					$options = get_option( 'strattic-string-replacement' );
					if ( is_array( $options ) ) {

						foreach( $options as $key => $values ) {
							echo $this->get_row( $values );
						}

					}

					// Add a new row by default
					echo $this->get_row( null );

					?>
					</tbody>

				</table>

				<input type="button" id="add-new-row" value="<?php esc_html_e( 'New replacement', 'strattic' ); ?>" />

				<?php settings_fields( 'strattic-string-replacement' ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php esc_html_e( 'Save', 'strattic' ); ?>" />
				</p>

				<input type="hidden" name="strattic-page-label" value="<?php echo esc_attr( $title ); ?>" />
			</form>

			<?php $this->the_horizontal_menu(); ?>

		</div><?php
	}

	/**
	 * Get a single table row.
	 * 
	 * @param  string  $value  Option value
	 * @return string  The table row HTML
	 */
	public function get_row( $values = null ) {

		$search = '';
		if ( isset( $values[ 'search' ] ) ) {
			$search = $values[ 'search' ];
		}
		$replace = '';
		if ( isset( $values[ 'search' ] ) ) {
			$replace = $values[ 'replace' ];
		}

		// Create the required HTML
		$row_html = '

					<tr class="sortable">
						<td>
							<textarea type="text" name="strattic-string-replacement[search][]">' . esc_textarea( $search ) . '</textarea>
							<textarea type="text" name="strattic-string-replacement[replace][]">' . esc_textarea( $replace ) . '</textarea>
						</td>
					</tr>';

		// Strip out white space
		$row_html = str_replace( '	', '', $row_html );
		$row_html = str_replace( "\n", '', $row_html );

		// Return the final HTML
		return $row_html;
	}

	/**
	 * Sanitize the page or product ID.
	 * Can't actually sanitize here because we don't know what sort of data people will want to string replace :/
	 * The code has been laid out to allow data sanitization at a later date if required.
	 *
	 * @param   array   $input   The input string
	 * @return  array            The sanitized string
	 */
	public function sanitize( $input ) {
		$output = array();

		// Loop through each bit of data
		foreach( $input[ 'search' ] as $key => $values ) {
			$sanitized_key   = absint( $key );

			if ( isset( $input[ 'search' ][ $key ][ 0 ] ) && $input[ 'replace' ][ $key ][ 0 ] && '' !== $input[ 'search' ][ $key ][ 0 ] && '' !== $input[ 'replace' ][ $key ][ 0 ] ) {

				// Data could be sanitized at this point ...
				$sanitized_value[ 'search' ] = $input[ 'search' ][ $key ];
				$sanitized_value[ 'replace' ] = $input[ 'replace' ][ $key ];

				$output[ $sanitized_key ] = $sanitized_value;
			}

		}

		add_settings_error(
			'strattic-error',
			esc_attr( 'settings_updated' ),
			sprintf(
				esc_html__( 'The %s have been updated!', 'strattic' ),
				esc_html( strtolower( $_POST[ 'strattic-page-label' ] ) )
			),
			'updated'
		);

		return $output;
	}

	/**
	 * Output scripts into the footer.
	 * This is not best practice, but is implemented like this here to ensure that it can fit into a single file.
	 */
	public function scripts() {

		?>
		<style>
		.strattic-string-replacement textarea {
			width: 46%;
			margin-right: 4%;
			font-family: monospace;
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
				var html = '<?php echo $this->get_row( null ); ?>';

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
