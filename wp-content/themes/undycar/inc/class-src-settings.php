<?php

/**
 * SRC settings page.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyergmail.com>
 * @since 1.0
 */
class SRC_Settings {

	/**
	 * Set some constants for setting options.
	 */
	const MENU_SLUG = 'src-page';
	const GROUP     = 'src-group';
	const OPTION    = 'src-current-season';

	/**
	 * Fire the constructor up :D
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
			self::GROUP,               // The settings group name
			self::OPTION,              // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);
	}

	/**
	 * Create the page and add it to the menu.
	 */
	public function create_admin_page() {

		add_submenu_page(
			'edit.php?post_type=event',
			__ ( 'Settings', 'src' ),    // Page title
			__ ( 'Settings', 'src' ),    // Menu title
			'manage_options',            // Capability required
			self::MENU_SLUG,             // The URL slug
			array( $this, 'admin_page' ) // Displays the admin page
		);
	}

	/**
	 * Output the admin page.
	 */
	public function admin_page() {

		?>
		<div class="wrap">
			<h1><?php _e( 'Settings', 'src' ); ?></h1>

			<form method="post" action="options.php">

				<table class="form-table">
					<tr>
						<th>
							<label for="<?php echo esc_attr( self::OPTION ); ?>"><?php _e( 'Select the current season', 'src' ); ?></label>
						</th>
						<td><?php

							$query = new WP_Query( array(
								'posts_per_page'         => 100,
								'post_type'              => 'season',
								'no_found_rows'          => true,
								'update_post_meta_cache' => false,
								'update_post_term_cache' => false,
							) );
							if ( $query->have_posts() ) {
								echo '<select type="text" id="' . esc_attr( self::OPTION ) . '" name="' . esc_attr( self::OPTION ) . '">';
								while ( $query->have_posts() ) {
									$query->the_post();

									$selected = '';
									if ( get_option( self::OPTION ) === (string) get_the_ID() ) {
										$selected = ' selected="selected"';
									}

									echo '<option' . $selected . ' value="' . esc_attr( get_the_ID() ) . '">' . esc_html( get_the_title() ) . '</option>';

								}
								echo '</select>';
							}

							?>

							<p class="description">This is typically used for deciding which season to promote on the website.</p>
						</td>
					</tr>
				</table>

				<?php settings_fields( self::GROUP ); ?>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'src' ); ?>" />
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
		$output = wp_kses_post( $input );
		return $output;
	}

}
