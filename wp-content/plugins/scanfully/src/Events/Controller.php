<?php
/**
 * The events controller class file.
 *
 * @package Scanfully
 */

namespace Scanfully\Events;

/**
 * Class Controller
 *
 * @package Scanfully\Events
 */
class Controller {

	/**
	 * The events
	 *
	 * @var array
	 */
	private static array $events = [];

	/**
	 * Register the events
	 *
	 * @param  Event $event The event to register.
	 *
	 * @return void
	 */
	public static function register( Event $event ): void {
		self::$events[] = $event;
	}

	/**
	 * Setup custom events.
	 * Some events aren't straight forward WP actions to Scanfully events. This is where we can add them.
	 *
	 * @return void
	 */
	public static function setup_custom_events(): void {
		self::plugin_update_event();
		self::theme_update_event();
	}

	/**
	 * Custom plugin update event.
	 *
	 * @return void
	 */
	private static function plugin_update_event(): void {

		// this is an odd one, but we need to hook into the upgrader_package_options to get the old version number and pass it to the upgrader_install_package_result via the hook_extra
		add_filter( 'upgrader_package_options', function ( $options ) {

			// check if a plugin is being updated
			if ( isset( $options['hook_extra']['plugin'] ) ) {
				$data = get_file_data( WP_PLUGIN_DIR . '/' . $options['hook_extra']['plugin'], array( 'Version' => 'Version' ) );

				if ( ! empty( $data['Version'] ) ) {
					$options['hook_extra']['old_version'] = $data['Version'];
				}
			}

			return $options;
		}, 99, 1 );


		// this is a filter run after the plugin has been updated.
		// Preferably we would use the upgrader_process_complete but it's a weird action that can be called from 2 places.
		// On a single update, it does contain the hook_extra but on a bulk update it doesn't.
		// And for some reason when you update via AJAX (which is what happens when you update a plugin from the plugin page) it is treated as a bulk update with 1 plugin.
		add_filter( 'upgrader_install_package_result', function ( $result, $hook_extra ) {

			// check if a plugin is being updated
			if ( isset( $hook_extra['plugin'] ) ) {

				$plugin_slug = $hook_extra['plugin'];

				// don't fire for our own plugin
				if ( $plugin_slug === 'scanfully/scanfully.php' ) {
					return $result;
				}

				// get new plugin data
				$plugin_data = get_file_data( WP_PLUGIN_DIR . '/' . $plugin_slug, [
						'Name'        => 'Plugin Name',
						'Version'     => 'Version',
						'Author'      => 'Author',
						'RequiresWP'  => 'Requires at least',
						'RequiresPHP' => 'Requires PHP',
					]
				);

				// fire our custom action so our event system can pick it up
				do_action( 'scanfully_plugin_updated', [
					'name'         => $plugin_data['Name'] ?? '',
					'version'      => $plugin_data['Version'] ?? '',
					'old_version'  => $hook_extra['old_version'] ?? '', // this is the old version number
					'author'       => $plugin_data['Author'] ?? '',
					'slug'         => $plugin_slug,
					'requires_wp'  => $plugin_data['RequiresWP'] ?? '',
					'requires_php' => $plugin_data['RequiresPHP'] ?? '',
				] );

			}

			return $result;
		}, 99, 2 );
	}

	/**
	 * Theme update event.
	 *
	 * @return void
	 */
	private static function theme_update_event(): void {

		// this is an odd one, but we need to hook into the upgrader_package_options to get the old version number and pass it to the upgrader_install_package_result via the hook_extra
		add_filter( 'upgrader_package_options', function ( $options ) {

			// check if a plugin is being updated
			if ( isset( $options['hook_extra']['theme'] ) ) {

				$data = get_file_data( $options['destination'] . '/' . $options['hook_extra']['theme'] . '/' . 'style.css', [ 'Version' => 'Version' ] );

				if ( ! empty( $data['Version'] ) ) {
					$options['hook_extra']['old_version'] = $data['Version'];
				}
			}

			return $options;
		}, 99, 1 );


		add_filter( 'upgrader_install_package_result', function ( $result, $hook_extra ) {

			// check if a plugin is being updated
			if ( isset( $hook_extra['theme'] ) ) {

				$theme_slug = $hook_extra['theme'];

				$theme_data = get_file_data( get_theme_root() . '/' . $theme_slug . '/' . 'style.css', [
					'Name'        => 'Theme Name',
					'Version'     => 'Version',
					'Author'      => 'Author',
					'Template'    => 'Template',
					'RequiresWP'  => 'Requires at least',
					'RequiresPHP' => 'Requires PHP',
				] );

				// fire our custom action so our event system can pick it up
				do_action( 'scanfully_theme_updated', [
					'name'         => $theme_data['Name'] ?? '',
					'version'      => $theme_data['Version'] ?? '',
					'old_version'  => $hook_extra['old_version'] ?? '', // this is the old version number
					'author'       => $theme_data['Author'] ?? '',
					'slug'         => $theme_slug,
					'requires_wp'  => $theme_data['RequiresWP'] ?? '',
					'requires_php' => $theme_data['RequiresPHP'] ?? '',
				] );

			}

			return $result;
		}, 99, 2 );

	}
}
