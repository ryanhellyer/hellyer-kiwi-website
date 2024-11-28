<?php
/**
 * The main class file.
 *
 * @package Scanfully
 */

namespace Scanfully;

/**
 * The main class, this is where it all starts.
 */
class Main {

	/**
	 * The singleton instance.
	 *
	 * @var ?Main
	 */
	private static ?Main $instance = null;

	/**
	 * Singleton getter
	 *
	 * @return Main|null
	 */
	public static function get(): ?Main {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	const API_URL = 'https://api.scanfully.com/v1';
	const DASHBOARD_URL = 'https://app.scanfully.com';
	const CONNECT_URL = 'https://app.scanfully.com/connect';

	public static function get_api_url(): string {
		return apply_filters( 'scanfully_api_url', self::API_URL );
	}

	public static function get_dashboard_url(): string {
		return apply_filters( 'scanfully_dashboard_url', self::DASHBOARD_URL );
	}

	public static function get_connect_url(): string {
		return apply_filters( 'scanfully_connect_url', self::CONNECT_URL );
	}

	/**
	 * Set up the plugin.
	 *
	 * @return void
	 */
	public function setup(): void {
		/** Register all events */
		$this->register_events();

		/** Register cron */
		Cron\Controller::setup();

		/** Register connect */
		Connect\Controller::setup();
		Connect\Page::register();
		Connect\AdminNotice::setup();
	}

	/**
	 * Register all events.
	 *
	 * @return void
	 */
	private function register_events(): void {
		Events\Controller::register( new Events\ActivatedPlugin() ); // when a plugin is activated.
		Events\Controller::register( new Events\DeactivatedPlugin() ); // when a plugin is deactivated.
		Events\Controller::register( new Events\PluginUpdate() ); // when a plugin is updated.
		Events\Controller::register( new Events\ThemeUpdate() ); // when a plugin is updated.
		Events\Controller::register( new Events\RewriteRules() ); // when new rewrite rules are saved.
		Events\Controller::register( new Events\PostSaved() ); // when a post status is changed.
		Events\Controller::register( new Events\CoreUpdate() ); // when the core is updated.

		// custom events
		Events\Controller::setup_custom_events();
	}

}
