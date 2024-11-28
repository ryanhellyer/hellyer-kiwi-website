<?php
/**
 * The rewrite rules event class file.
 *
 * @package Scanfully
 */

namespace Scanfully\Events;

/**
 * Class RewriteRules
 *
 * @package Scanfully\Events
 */
class RewriteRules extends Event {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct( 'RewriteRules', 'update_option_rewrite_rules', 10, 3 );
	}

	/**
	 * Get the post body
	 *
	 * @param  array $data The data to send.
	 *
	 * @return array
	 */
	public function get_post_body( array $data ): array {
		return [
			'rewrite_rules' => $data[1],
		];
	}

	/**
	 * A check if a event should fire
	 *
	 * @param  array $data
	 *
	 * @return bool
	 */
	public function should_fire( array $data ): bool {
//		error_log(print_r($data, true));
		return true;
	}
}
