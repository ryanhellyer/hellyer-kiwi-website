<?php

namespace Scanfully\Cron;

use Scanfully\Connect;
use Scanfully\Health;
use Scanfully\Options;

class Controller {

	public const ACTION_TWICE_DAILY = 'scanfully_twice_daily';
	public const ACTION_DAILY = 'scanfully_daily';

	/**
	 *
	 *
	 * @return void
	 */
	public static function setup(): void {
		// cron 'callbacks'
		add_action( self::ACTION_TWICE_DAILY, [ self::class, 'twice_daily' ] );
		add_action( self::ACTION_DAILY, [ self::class, 'daily' ] );

		// schedule events
		self::schedule_events();
	}

	/**
	 * Daily cron function
	 *
	 * @return void
	 */
	public static function daily(): void {
		// check if we need to refresh the access token
		self::refresh_access_token_if_needed();

		// get options
		$options = Options\Controller::get_options();

		// connected only actions
		if ( $options->is_connected ) {
			// send directory data daily
			Health\Controller::send_directories_data();
		}
	}

	/**
	 * Daily cron function
	 *
	 * @return void
	 */
	public static function twice_daily(): void {
		// check if we need to refresh the access token
		self::refresh_access_token_if_needed();

		// get options
		$options = Options\Controller::get_options();

		// connected only actions
		if ( $options->is_connected ) {
			// send site data twice per day
			Health\Controller::send_site_data();
		}

	}

	/**
	 * Schedule events
	 *
	 * @return void
	 */
	private static function schedule_events(): void {
		if ( ! wp_next_scheduled( self::ACTION_TWICE_DAILY ) ) {
			wp_schedule_event( time(), 'twicedaily', self::ACTION_TWICE_DAILY );
		}

		if ( ! wp_next_scheduled( self::ACTION_DAILY ) ) {
			wp_schedule_event( time(), 'daily', self::ACTION_DAILY );
		}
	}

	/**
	 * Clear all scheduled events
	 *
	 * @return void
	 */
	public static function clear_scheduled_events(): void {
		wp_clear_scheduled_hook( self::ACTION_DAILY );
	}

	/**
	 * Refresh the access token if needed
	 *
	 * @return void
	 */
	private static function refresh_access_token_if_needed(): void {

		// get options
		$options = Options\Controller::get_options();

		// check if we're connected, if not return
		if ( ! $options->is_connected ) {
			return;
		}

		try {
			// create a time object for now
			$now = new \DateTime();
			$now->setTimezone( new \DateTimeZone( 'UTC' ) );

			// create time object for expires
			$expires = new \DateTime( $options->expires );
			$expires->setTimezone( new \DateTimeZone( 'UTC' ) );
			$expires->modify( '-2 days' );


			// check if the access token is expired
			if ( $now > $expires ) {
				// refresh the access token
				$tokens = Connect\Controller::refresh_access_token( $options->refresh_token, $options->site_id );

				// check if we got tokens
				if ( empty( $tokens ) ) {
					error_log( 'Failed to refresh access token' );

					return;
				}
				// create a new expires time object
				$new_expires = new \DateTime( $tokens['expires'] );
				$new_expires->setTimezone( new \DateTimeZone( 'UTC' ) );

				// update the options
				$options = new Options\Options(
					true,
					$tokens['site_id'],
					$tokens['access_token'],
					$tokens['refresh_token'],
					$new_expires->format( Connect\Controller::DATE_FORMAT ),
					'',
					$now->format( Connect\Controller::DATE_FORMAT )
				);

				// save options
				Options\Controller::set_options( $options );
			}
		} catch ( \Exception $e ) {
			// handle the exception
		}

	}

}