<?php

/**
 * Properly enqueue styles and scripts for our theme options page.
 *
 * This function is attached to the admin_enqueue_scripts action hook.
 */
function fruit_shake_admin_enqueue_scripts( $hook_suffix ) {
	wp_enqueue_style( 'twentyeleven-theme-options', get_template_directory_uri() . '/inc/theme-options/theme-options.css', false, '2011-04-28' );
}
add_action( 'admin_print_styles-appearance_page_theme_options', 'fruit_shake_admin_enqueue_scripts' );

/**
 * Init plugin options to white list our options
 */
function theme_options_init(){
	register_setting( 'fruit_shake_options', 'fruit_shake_theme_options', 'fruit_shake_theme_options_validate' );
}
add_action( 'admin_init', 'theme_options_init' );

/**
 * Load up the menu page
 */
function theme_options_add_page() {
	add_theme_page( __( 'Theme Options', 'fruit-shake' ), __( 'Theme Options', 'fruit-shake' ), 'edit_theme_options', 'theme_options', 'theme_options_do_page' );
}
add_action( 'admin_menu', 'theme_options_add_page' );

/**
 * Create array for our radio option
 */
$fruit_scheme_options = array(
	'banana' => array(
		'value' => 'banana',
		'label' => __( 'Banana', 'fruit-shake' )
	),
	'blueberry' => array(
		'value' => 'blueberry',
		'label' => __( 'Blueberry', 'fruit-shake' )
	),
	'dragon-fruit' => array(
		'value' => 'dragon-fruit',
		'label' => __( 'Dragon Fruit', 'fruit-shake' )
	),
);

/**
 * Check the number of daily posts in the last week
 * If it's greater than or equal to 7 add the Brown Banana color scheme
 */
$daily_posts_last_week = fruit_shake_daily_posts_in_last_week();

if ( 7 <= $daily_posts_last_week ) {
	$fruit_scheme_options['brown-banana']['value'] = 'brown-banana';
	$fruit_scheme_options['brown-banana']['label'] = __( 'Brown Banana', 'fruit-shake' );
}

/**
 * Create the options page
 */
function theme_options_do_page() {
	global $fruit_scheme_options;
	$options = get_option( 'fruit_shake_theme_options' );
?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php printf( __( '%s Theme Options', 'fruit-shake' ), wp_get_theme() ); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php settings_fields( 'fruit_shake_options' ); ?>

			<table class="form-table">

				<?php
				/**
				 * Fruit Scheme Options
				 */
				?>
				<tr valign="top" class="image-radio-option fruit-scheme"><th scope="row"><?php _e( 'Fruit Flavors', 'fruit-shake' ); ?></th>
					<td>
						<fieldset><legend class="screen-reader-text"><span><?php _e( 'Fruit Flavors', 'fruit-shake' ); ?></span></legend>

						<?php foreach ( $fruit_scheme_options as $option ) : ?>
							<div class="layout">
								<label class="description">
									<input type="radio" name="fruit_shake_theme_options[fruit_scheme]" value="<?php echo esc_attr( $option['value'] ); ?>" <?php checked( $options['fruit_scheme'], $option['value'] ); ?> /> <?php echo $option['label']; ?>
									<span>
										<img src="<?php echo get_template_directory_uri() . '/inc/theme-options/images/flavor-' . esc_attr( $option['value'] ) . '.png' ; ?>" width="200" height="147" alt="" />
									</span>
								</label>
							</div>
						<?php endforeach; ?>

						</fieldset>
					</td>
				</tr>
			</table>

			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

/**
 * Sanitize and validate input. Accepts an array, return a sanitized array.
 */
function fruit_shake_theme_options_validate( $input ) {
	global $select_options, $fruit_scheme_options;

	// Our radio option must actually be in our array of radio options
	if ( ! isset( $input['fruit_scheme'] ) || ! array_key_exists( $input['fruit_scheme'], $fruit_scheme_options ) )
		$input['fruit_scheme'] = null;

	return $input;
}

// adapted from http://planetozh.com/blog/2009/05/handling-plugins-options-in-wordpress-28-with-register_setting/