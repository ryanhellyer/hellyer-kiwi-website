<?php

/**
 * AI rosters.
 */
class SRC_AI extends SRC_Core {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		if ( isset( $_GET['ai_roster'] ) ) {
			add_action( 'init', array( $this, 'display_roster' ) );
		}
	}

	/**
	 * Display the roster.
	 */
	public function display_roster() {
		$season_id = get_option( 'last-season' );
		$driver_points = self::get_driver_points_from_season( $season_id );

		$roster = '{
	"drivers": [';

		foreach ( $driver_points as $driver_name => $points ) {

			// Get member ID.
			$member = get_user_by( 'login', sanitize_title( $driver_name ) );
			if ( isset( $member->data->ID ) ) {
				$member_id = $member->data->ID;
			} else {
				continue;
			}

			// Get car number.
			if ( '' !== get_user_meta( $member_id, 'car_number', true ) ) {
				$car_number = get_user_meta( $member_id, 'car_number', true );
			} else {
				$car_number = rand( 100,300 );
			}

			$roster .= '
		{
			"driverName": "' . esc_html( $driver_name ) . '",
			"carDesign": "2,222958,447ac0,ffffff",
			"carNumber": "' . absint( $car_number ) . '",
			"suitDesign": "5,ee3442,447ac0,ffffff",
			"helmetDesign": "0,ee3442,447ac0,ffffff",
			"carPath": "porsche911cup",
			"carId": 88,
			"carClassId": 0,
			"sponsor1": 97,
			"sponsor2": 124,
			"numberDesign": "null,null,null,null",
			"driverSkill": 50,
			"driverOptimism": 45,
			"driverAge": 50,
			"pitCrewSkill": 53,
			"strategyRiskiness": 72,
			"driverAggression": 100,
			"driverSmoothness": 0
		},';
		}

		$roster .= '
	]
}';

		echo $roster;
		die;

	}

}
