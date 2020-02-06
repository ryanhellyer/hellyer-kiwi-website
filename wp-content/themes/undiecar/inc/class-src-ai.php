<?php

/**
 * AI rosters.
 */
class SRC_AI extends SRC_Core {

	private $iracing_ids;

	// List of cars ( use % instead of \\ to simplify code escaping).
	private $cars = array(
		'mx5%mx52016_67'   => 'Global Mazda MX-5 Cup',
		'porsche911cup_88' => 'Porsche 911 GT3 Cup',
		'rt2000_1'         => 'Formula Skip Barber 2000',
	);

	/**
	 * Class constructor.
	 */
	public function __construct() {

		if ( isset( $_GET['ai_roster'] ) ) {
			add_action( 'init', array( $this, 'display_roster' ) );
		}

		add_shortcode( 'undiecar_ai_rosters', array( $this, 'shortcode' ) );
	}

	/**
	 * Fudging roster by adding in extras.
	 * Some of these competitors joined the first AI test race.
	 * Some are just important members of the Undiecar community who aren't aways in the season points table.
	 *
	 * @param array $driver_points The drivers points.
	 * @return array The modified drivers points.
	 */
	public function fudge_roster( $driver_points ) {

		$extra_drivers = array(
			'Gennadii Stepanov' => '80',
			'Ivo Andreini'      => '30',
			'James N Payne'     => '75',
			'Justin Hess'       => '90',
			'Rik Scott'         => '40',
			'Chema Hache'       => '40',
			'Markus Kramer'     => '1',
			'Luis Morga'        => '30',
			'Matt Fretwell'     => '90',
			'Alex Skinner'      => '30',
			'Nikolay Ladushkin' => '90',
		);
		foreach ( $extra_drivers as $driver_name => $advised_points ) {
			if ( ! isset( $driver_points[ $driver_name ] ) ) {
				$driver_points[ $driver_name ] = $advised_points;
			}
		}

		return $driver_points;
	}

	/**
	 * Display the roster.
	 *
	 * @access private
	 */
	private function get_roster() {
		$current_season_points = self::get_driver_points_from_season( get_option( 'current-season' ) );
		$last_season_points    = self::get_driver_points_from_season( get_option( 'last-season' ) );

		// driver_points points from the two seasons.
		$driver_points = array();
		foreach ( array_keys( $current_season_points + $last_season_points ) as $key ) {
			$driver_points[ $key ] = ( isset( $current_season_points[ $key ] ) ? $current_season_points[ $key ] : 0 ) + ( isset( $last_season_points[ $key ] ) ? $last_season_points[ $key ] : 0 );
		}

		$driver_points = $this->fudge_roster( $driver_points );
		arsort( $driver_points );
		$max_points    = current( $driver_points );

		$roster = '{
	"intro": "' . sprintf( __( 'Generated by the Undiecar AI Roster %s %s.', 'undiecar' ), 'https://undiecar.com/ai-roster/', date( 'Y-m-d H:i:s' ) ) . '",
	"drivers": [';

		$count = 0;
		foreach ( $driver_points as $driver_name => $points ) {
			$count++;

			// Limit number of drivers.
			if ( isset( $_GET['number'] ) ) {
				$number = $_GET['number'];
			}
			if ( isset( $_GET['number'] ) && $count > $number ) {
				continue;
			}

			if ( isset( $member ) ) {
				$roster .= ',';
			}

			// Get member ID.
			$member = get_user_by( 'login', sanitize_title( $driver_name ) );
			if ( isset( $member->data->ID ) ) {
				$member_id = $member->data->ID;
			}

			// Get driver skill.
			$driver_skill = absint( 100 * ( ( count( $driver_points ) - $count ) / count( $driver_points ) ) );
			if ( 0 === $driver_skill ) {
				$driver_skill = 1; // Value of zero is not the worst.
			}

			// Get car number.
			if ( '' !== get_user_meta( $member_id, 'car_number', true ) ) {
				$car_number = get_user_meta( $member_id, 'car_number', true );
			} else {
				$car_number = rand( 100,300 );
			}
//https://undiecar.com/?ai_roster&display&cars=mx5%mx52016_67/porsche911cup_88
			// Get cars.
			if ( isset( $_GET['cars'] ) ) {
				$cars = explode( '/', $_GET['cars'] );
				if ( is_array( $cars ) ) {
					foreach ( $cars as $key => $car ) {
						$x = explode( '_', $car );
						if ( isset( $x[1] ) ) {
							$cars[ $key ] = array(
								'path' => $x[0],
								'id'   => $x[1],
							);
						}
					}
				}
				shuffle( $cars ); // Shuffle it to keep the car selection random.
			}
			if ( ! isset( $cars ) ) {
				// Add default cars if none specified.
				$cars = array(
					0 => array(
						'path' => 'porsche911cup',
						'id'   => 88,
					),
					1 => array(
						'path' => 'mx5%mx52016',
						'id'   => 67,
					),
				);
			}

			// Randomly select a car.
			$iracing_id  = get_user_meta( $member_id, 'custid', true );
			$uploads_dir = wp_upload_dir();
			$uploads_dir = $uploads_dir['path'] . '/paints/';
			$paint_file  = 'car_' . absint( $iracing_id ) . '.tga';
			$car_count   = count( $cars );
			$car_counter = 0;
			while ( $car_counter < $car_count ) {

				$car    = str_replace( '\\\\', '%', $cars[ $car_counter ] );
				$car_path = $car['path'];
				$path        = $uploads_dir . $car_path . '/' . $paint_file;
				if ( file_exists( $path ) ) {
					break;
					echo $path . "\n";
				}

				$car_counter++;
			}
			$car_id = $car['id'];

			// Storing iRacing ID (used later for getting paint files).
			$this->iracing_ids[ $car['path'] ][] = $iracing_id;

			// Get colour schemes.
			$helmet_design = get_user_meta( $member_id, 'helmet_design', true );
			if ( '' === $helmet_design ) {
				$helmet_design = '0,ee3442,447ac0,ffffff';
			}
			$suit_design = get_user_meta( $member_id, 'suit_design', true );
			if ( '' === $suit_design ) {
				$suit_design = '5,ee3442,447ac0,ffffff';
			}
			$car_design = get_user_meta( $member_id, 'car_design', true );
			if ( '' === $car_design ) {
				$car_design = '2,222958,447ac0,ffffff';
			}

			$roster .= '
		{
			"driverName": "' . esc_html( $driver_name ) . '",
			"carDesign": "' . esc_html( $car_design ) . '",
			"carNumber": "' . absint( $car_number ) . '",
			"suitDesign": "' . esc_html( $suit_design ) . '",
			"helmetDesign": "' . esc_html( $helmet_design ) . '",
			"carPath": "' . esc_html( $car_path ) . '",
			"carId": ' . absint( $car_id ) . ',
			"carClassId": 0,
			"sponsor1": 97,
			"sponsor2": 124,
			"numberDesign": "null,null,null,null",
			"driverSkill": ' . absint( $driver_skill ) . ',
			"driverAggression": 65,
			"driverOptimism": 50,
			"driverSmoothness": 0,
			"driverAge": 13,
			"pitCrewSkill": 53,
			"strategyRiskiness": 72,
			"iracing_id": ' . absint( $iracing_id );

			// Add paint file reference if it exists.
			$uploads_dir = wp_upload_dir();
			$uploads_dir = $uploads_dir['path'] . '/paints/';
			$paint_file  = 'car_' . absint( $iracing_id ) . '.tga';
			$car_path    = str_replace( '\\\\', '%', $car_path );
			$path        = $uploads_dir . $car_path . '/' . $paint_file;
			if ( file_exists( $path ) ) {
				$roster .= ',
			"carTgaName": "car_' . absint( $iracing_id ) . '.tga"';
			}


			$roster .= '
		}';
		}

		$roster .= '
	]
}';

		return $roster;
	}

	/**
	 * Display the roster.
	 */
	public function display_roster() {

		if ( isset( $_GET['display'] ) ) {

			echo $this->get_roster();
			die;
		} else {
			$zip = new ZipArchive;
			$file_name = 'undiecar-roster.zip';
			if ( $zip->open( $file_name, ZipArchive::CREATE ) === TRUE ) {
				$dir_name = 'Undiecar';

				// Add a file new.txt file to zip using the text specified
				$zip->addFromString( $dir_name . '/roster.json', $this->get_roster() );

				// Add paint files.
				$uploads_dir = wp_upload_dir();
				$uploads_dir = $uploads_dir['path'] . '/paints/';
				foreach ( $this->iracing_ids as $car_slug => $iracing_ids ) {
					foreach ( $iracing_ids as $key => $iracing_id ) {
						$paint_file = 'car_' . absint( $iracing_id ) . '.tga';
						$path       = $uploads_dir . $car_slug . '/' . $paint_file;

						if ( file_exists( $path ) ) {
							$zip->addFile ( $path, $dir_name . '/' . $paint_file );	
						}
					}
				}

				// All files are added, so close the zip file.
				$zip->close();

				header( "Content-type: application/zip" );
				header( "Content-Disposition: attachment; filename=$file_name" );
				header( "Pragma: no-cache" );
				header( "Expires: 0" );
				readfile( $file_name );
				unlink( $file_name );
				exit;
			}
		}

	}

	/**
	 * The rosters shortcode.
	 */
	public function shortcode() {

		$content = '<form id="undiecar-rosters" method="get" action="">';

		$content .= '<label>Car #1</label>';
		$content .= '<select id="undiecar-car1" name="undiecar-car1">';
		$content .= '<option value="">' . esc_html__( 'None', 'undiecar' ) . '</option>';
		foreach ( $this->cars as $car_slug => $car_name ) {
			$content .= '<option value="' . esc_attr( $car_slug ) . '">' . esc_html( $car_name ) . '</option>';
		}
		$content .= '</select>';

		$content .= '<label>Car #2</label>';
		$content .= '<select id="undiecar-car2" name="undiecar-car2">';
		$content .= '<option value="">' . esc_html__( 'None', 'undiecar' ) . '</option>';
		foreach ( $this->cars as $car_slug => $car_name ) {
			$content .= '<option value="' . esc_attr( $car_slug ) . '">' . esc_html( $car_name ) . '</option>';
		}
		$content .= '</select>';

		$content .= '<label>Car #3</label>';
		$content .= '<select id="undiecar-car3" name="undiecar-car3">';
		$content .= '<option value="">' . esc_html__( 'None', 'undiecar' ) . '</option>';
		foreach ( $this->cars as $car_slug => $car_name ) {
			$content .= '<option value="' . esc_attr( $car_slug ) . '">' . esc_html( $car_name ) . '</option>';
		}
		$content .= '</select>';

		$content .= '<label>' . esc_html__( 'Number of cars', 'undiecar' ) . '</label>';
		$content .= '<input type="number" id="undiecar-number" value="30" name="undiecar-number" />';

		$content .= '<input id="undiecar-get-roster" type="submit" class="button" value="' . esc_attr__( 'Get roster', 'undiecar' ) . '" />';

		$content .= '</form>';


		$content .= "\n<script>
var undiecar_roster_button = document.getElementById( 'undiecar-get-roster' );
var undiecar_roster_form_url = '" . esc_url( get_permalink() ) . "';
undiecar_roster_button.addEventListener( 'click', function( e ) {
	let car1 = document.getElementById( 'undiecar-car1' ).value;
	let car2 = document.getElementById( 'undiecar-car2' ).value;
	let car3 = document.getElementById( 'undiecar-car3' ).value;

	// Nasty hard coded hack to create AI roster URL.
	let url_parts = '';
	if ( '' !== car1 ) {
		if ( '' === url_parts ) {
			url_parts = '?ai_roster&cars=' + car1;
		} else {
			url_parts = url_parts + '/' + car1;
		}
	}
	if ( '' !== car2 ) {
		if ( '' === url_parts ) {
			url_parts = '?ai_roster&cars=' + car2;
		} else {
			url_parts = url_parts + '/' + car2;
		}
	}
	if ( '' !== car3 ) {
		if ( '' === url_parts ) {
			url_parts = '?ai_roster&cars=' + car3;
		} else {
			url_parts = url_parts + '/' + car3;
		}
	}
	if (
		'' === car1
		&&
		'' === car2
		&&
		'' === car3
	) {
		url_parts = '?ai_roster';
	}
	let number = document.getElementById( 'undiecar-number' ).value;
	url_parts = url_parts + '&number=' + number;
	let url = undiecar_roster_form_url + url_parts;

	// Redirect instead of processing form submission.
	window.location = url;
	e.preventDefault();
} );
</script>\n";

		return $content;
	}

}
