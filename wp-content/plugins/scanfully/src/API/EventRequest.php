<?php
/**
 * The event request class file.
 *
 * @package Scanfully
 */

namespace Scanfully\API;

use Scanfully\Main;
use Scanfully\Options\Controller as OptionsController;

/**
 * This class is used to send events to the Scanfully API.
 */
class EventRequest extends Request {

	/**
	 * Send the request to the API.
	 *
	 * @param  array $data The data to send with the request.
	 *
	 * @return void
	 */
	public function send( array $data ): void {
		parent::do_request( '', $data );
	}

	/**
	 * Get the url for the request.
	 *
	 * @param  string $endpoint The endpoint to send the request to.
	 *
	 * @return string
	 */
	public function get_url( string $endpoint ): string {
		return sprintf( Main::get_api_url() . '/sites/%s/timeline', OptionsController::get_option( 'site_id' ) );
	}

	/**
	 * Get the body for the request.
	 *
	 * @param  array $data The data to send with the request.
	 *
	 * @return array
	 */
	public function get_body( array $data ): array {
		return array_merge( $data, [] );
	}
}
