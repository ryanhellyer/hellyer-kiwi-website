<?php

/**
 * Core functionalities.
 * Methods used across multiple classes.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package SRC Theme
 * @since SRC Theme 1.0
 */
class SRC_Core {

	/**
	 * Getting the SRC users.
	 */
	public  function get_src_users() {

		$users = get_users(
			array(
				'orderby' => 'nicename',
			)
		);

		$options = array( 0 => '' );
		foreach ( $users as $user ) {
			$options[ $user->ID ] = $user->display_name;
		}

		return $options;
	}

	/**
	 * Get the various race classes.
	 *
	 * @return array
	 */
	public function get_race_classes() {

		return array(
			'' => '',
			'am' => 'AM',
			'pro' => 'PRO',
		);

	}

	/**
	 * Event types.
	 *
	 * @return array
	 */
	protected function event_types() {

		$types = array(
			'FP1' => 'Free Practice 1',
			'FP2' => 'Free Practice 1',
			'Qualifying' => 'Qualifying',
			'Race 1' => 'Race 1',
			'Race 2' => 'Race 2',
			'Race 3' => 'Race 3',
		);

		return $types;
	}

	/**
	 * The order driver data is stored in.
	 */
	public function driver_data_order() {
		return array( 
			0 => 'Username',
			1 => 'Nationality',
			2 => 'Car',
			3 => 'Team',
			4 => 'Class',
			5 => 'Pts',
		);
	}

}
