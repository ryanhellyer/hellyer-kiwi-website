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
			'next-season',             // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);

		register_setting(
			self::GROUP,               // The settings group name
			'current-season',          // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);

		register_setting(
			self::GROUP,               // The settings group name
			'last-season',             // The option name
			array( $this, 'sanitize' ) // The sanitization callback
		);
		register_setting(
			self::GROUP,               // The settings group name
			'non-championship-user-message', // The option name
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
							<label for="next-season"><?php _e( 'Select the next season', 'src' ); ?></label>
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
								echo '<select type="text" id="next-season" name="next-season">';
								while ( $query->have_posts() ) {
									$query->the_post();

									$selected = '';
									if ( get_option( 'next-season' ) === (string) get_the_ID() ) {
										$selected = ' selected="selected"';
									}

									echo '<option' . $selected . ' value="' . esc_attr( get_the_ID() ) . '">' . esc_html( get_the_title() ) . '</option>';

								}
								echo '</select>';
							}

							?>

						</td>
					</tr>
					<tr>
						<th>
							<label for="current-season"><?php _e( 'Select the current season', 'src' ); ?></label>
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
								echo '<select type="text" id="current-season" name="current-season">';

								$selected = '';
								if ( get_option( 'current-season' ) === '' ) {
									$selected = ' selected="selected"';
								}
								echo '<option' . $selected . ' value="">' . esc_html__( 'Not applicable', 'src' ) . '</option>';

								while ( $query->have_posts() ) {
									$query->the_post();

									$selected = '';
									if ( get_option( 'current-season' ) === (string) get_the_ID() ) {
										$selected = ' selected="selected"';
									}

									echo '<option' . $selected . ' value="' . esc_attr( get_the_ID() ) . '">' . esc_html( get_the_title() ) . '</option>';

								}
								echo '</select>';
							}

							?>

						</td>
					</tr>
					<tr>
						<th>
							<label for="last-season"><?php _e( 'Select the last season', 'src' ); ?></label>
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
								echo '<select type="text" id="last-season" name="last-season">';
								while ( $query->have_posts() ) {
									$query->the_post();

									$selected = '';
									if ( get_option( 'last-season' ) === (string) get_the_ID() ) {
										$selected = ' selected="selected"';
									}

									echo '<option' . $selected . ' value="' . esc_attr( get_the_ID() ) . '">' . esc_html( get_the_title() ) . '</option>';

								}
								echo '</select>';
							}

							?>

						</td>
					</tr>
					<tr>
						<th>
							<label for="non-championship-user-message"><?php _e( 'Message to show non-championship registered users', 'undiecar' ); ?></label>
						</th>
						<td>
							<textarea style="width:100%;" id="non-championship-user-message" name="non-championship-user-message"><?php
							echo esc_textarea( get_option( 'non-championship-user-message' ) );
							?></textarea>
						</td>
					</tr>
				</table>

				<p class="description">These are typically used for deciding which season to promote on the website.</p>

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
