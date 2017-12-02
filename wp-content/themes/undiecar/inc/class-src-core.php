<?php

/**
 * Core functionalities.
 * Methods used across multiple classes.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @license http://www.gnu.org/licenses/gpl.html GPL
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */
class SRC_Core {

	/**
	 * Fraction used for assigning negative points for incidents.
	 * A tiny fraction of a point is removed for each incident to split drivers who are on equal points.
	 */
	const FRACTION = 0.0001;

	/**
	* Event types.
	*
	* @return array
	*/
	protected function event_types() {

		$types = array(
			'FP1'        => 'Free Practice 1',
			'FP2'        => 'Free Practice 2',
			'Qualifying' => 'Qualifying',
			'Race 1'     => 'Race 1',
			'Race 2'     => 'Race 2',
			'Race 3'     => 'Race 3',
		);

		return $types;
	}


	/**
	 * The teams championship table.
	 *
	 * @param  string  $content   The post content
	 * @param  bool    $bypass    true if bypassing post-type check
	 * @param  int     $limit     the max number of drivers to show
	 * @param  string  $title     title to use
	 * @param  string  $save_results  true if saving results - this is used for storing results at end of season
	 * @param  int     $season_id the ID of the season of the championsing permanship table
	 */
	static function teams_championship( $content, $title = false, $season_id = null ) {

		if ( null === $season_id ) {
			$season_id = get_the_ID();
		}

		/*
		 * Use stored results if available and set to use them.
		 *  Otherwise recalculate the results (normal mid-season)
		 */
		$stored_results = get_post_meta( $season_id, '_stored_results', true );
		$use_stored_results = get_post_meta( $season_id, '_permanently_store_results', true );

		if ( '' === $stored_results || '1' !== $use_stored_results ) {
			$stored_results = self::get_driver_points_from_season( $season_id );
		} // End of championship positions calculation
		else {
			$content .= "<!-- Using permanently stored results -->";
		}

		if ( false === $title ) {
			$title = __( 'Teams championship', 'src' );
		}

		// Loop through each team
		$teams_query = new WP_Query( array(
			'post_type'      => 'team',
			'post_status'    => 'publish',
			'posts_per_page' => 100,
			'no_found_rows'  => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$teams_list = array();
		if ( $teams_query->have_posts() ) {
			while ( $teams_query->have_posts() ) {
				$teams_query->the_post();

				// Check this team is competing in this season
				if ( 'on' === get_post_meta( $season_id, 'team-' . get_the_ID(), true ) ) {

					// Add team to list
					if ( ! isset( $teams_list[get_the_ID()] ) ) {
						$teams_list[get_the_ID()]['points'] = 0;
					}

					// Add up driver results
					$count = 1;
					while ( $count < 4 ) {

						// Get driver name
						$driver_name = '';
						$user_id = get_post_meta( get_the_ID(), '_driver_' . $count, true );
						if ( is_numeric( $user_id ) ) {
							$user = get_userdata( $user_id );
							$driver_name = $user->data->display_name;
						} else if ( 'error' === $user_id ) {
							$driver_name = $user_id;
						}

						// Add driver names
						if ( 'error' !== $driver_name ) {
							$teams_list[get_the_ID()]['drivers'][] = $driver_name;
						}

						// Add drivers points to the tally
						if ( isset( $stored_results[$driver_name] ) ) {
							$teams_list[get_the_ID()]['points'] = $teams_list[get_the_ID()]['points'] + $stored_results[$driver_name];
						}

						$count++;
					}

				}

			}
			wp_reset_postdata();
		}

		if ( array() !== $teams_list ) {
			$content .= '<h3>' . esc_html( $title ) . '</h3>';
			$content .= '<table id="src-teams-championship">';

			$content .= '<thead><tr>';

			$content .= '
				<th class="col-pos">Pos</th>
				<th class="col-name">Team name</th>
				<th class="col-name">Drivers</th>
				<th class="col-inc">Inc</th>
				<th class="col-pts">Pts</th>';
			$content .= '</tr></thead>';

			$content .= '<tbody>';

			$position = 0;
			$car_number = '';
			$nationality = '';
			foreach ( $teams_list as $team_id => $data ) {
				$position++;

				$team_name = get_the_title( $team_id );
				$linked_name = '<a href="' . esc_url( get_permalink( $team_id ) ) . '">' . esc_html( $team_name ) . '</a>';

				// Get incidents - these are found within the points, as drivers lose a fraction of a point for every incident
				$whole = floor( $data['points'] );
				$inc = ( 1 - ( $data['points'] - $whole ) ) / self::FRACTION;
				$inc = ( 0 + ( $data['points'] - $whole ) );
				if ( 0 == $inc ) {
					$inc = 1;
				}
				$inc = 1 - $inc;
				$inc = $inc / self::FRACTION;

				// Creat driver names list
				if ( is_array( $data['drivers'] ) ) {
					$driver_list = '';
					foreach ( $data['drivers'] as $driver_name ) {
						$driver_list .= '
						<a href="' . esc_url( home_url() . '/member/' . sanitize_title( $driver_name ) . '/' ) . '">' . esc_html( $driver_name ) . '</a>
						<br />';
					}
				}

				// Don't bother showing drivers who haven't scored any points yet
				$points = absint( round( $data['points'] ) );

				$content .= '<tr>';

				$content .= '<td class="col-pos">' . esc_html( $position ) . '</td>';
				$content .= '<td class="col-name">' . $linked_name . '</td>';
				$content .= '<td class="col-inc">' . $driver_list /* escaped earlier */ . '</td>';
				$content .= '<td class="col-inc">' . absint( $inc ) . '</td>';
				$content .= '<td class="col-pts">' . absint( $points ) . '</td>'; // Need to use absint() here due to fractions being used to put low incident drivers in front

				$content .= '</tr>';

			}
			$content .= '</tbody>';

			$content .= '</table>';
		}

		return $content;
	}


	/**
	 * The championship table.
	 *
	 * @param  string  $content   The post content
	 * @param  bool    $bypass    true if bypassing post-type check
	 * @param  int     $limit     the max number of drivers to show
	 * @param  string  $title     title to use
	 * @param  string  $save_results  true if saving results - this is used for storing results at end of season
	 * @param  int     $season_id the ID of the season of the championsing permanship table
	 */
	static function championship( $content, $bypass = false, $limit = 100, $title = false, $save_results = false, $season_id = null ) {

		if ( 'season' !== get_post_type() && true !== $bypass ) {
			return $content;
		}

		if ( is_front_page() ) {

			if ( '' === get_option( 'current-season' ) ) {
				$season_id = get_option( 'last-season' );
			} else {
				$season_id = get_option( 'current-season' );
			}

		} else if ( null === $season_id ) {
			$season_id = get_the_ID();
		}

		// Don't show championship listings for special season
		$season = get_post( $season_id ); 
		if ( 'special-events' === $season->post_name ) {
			return $content;
		}

		/*
		 * Use stored results if available and set to use them.
		 *  Otherwise recalculate the results (normal mid-season)
		 */
		$stored_results = get_post_meta( $season_id, '_stored_results', true );
		$use_stored_results = get_post_meta( $season_id, '_permanently_store_results', true );

		if ( '' === $stored_results || '1' !== $use_stored_results ) {

			$stored_results = self::get_driver_points_from_season( $season_id );

			// Someone has asked for the results to be stored permanently (used for end of season)
			if ( true === $save_results ) {
				update_post_meta( $season_id, '_stored_results', $stored_results );
			}

		} // End of championship positions calculation
		else {
			$content .= "<!-- Using permanently stored results -->";
		}

		if ( false === $title ) {
			$title = __( 'Drivers championship', 'src' );
		}

		if ( array() !== $stored_results ) {
			$content .= '<h3>' . esc_html( $title ) . '</h3>';
			$content .= '<table id="src-championship">';

			$content .= '<thead><tr>';

			$content .= '
				<th class="col-pos">Pos</th>
				<th class="col-name">Name</th>
				<th class="col-number">Num</th>
				<th class="col-nationality">Country</th>
				<th class="col-inc">Inc</th>
				<th class="col-pts">Pts</th>';
			$content .= '</tr></thead>';

			$content .= '<tbody>';

			$position = 0;
			$car_number = '';
			$nationality = '';
			foreach ( $stored_results as $name => $points ) {
				$position++;

				// Limit the number of drivers shown
				if ( $position > $limit ) {
					continue;
				}

				$linked_name = $name;
				$member = get_user_by( 'login', sanitize_title( $name ) );
				if ( isset( $member->data->ID ) ) {
					$member_id = $member->data->ID;

					$car_number = '';
					if ( '' !== get_user_meta( $member_id, 'car_number', true ) ) {
						$car_number = get_user_meta( $member_id, 'car_number', true );
					}

					$nationality = '';
					if ( '' !== get_user_meta( $member_id, 'nationality', true ) ) {
						$country_code = get_user_meta( $member_id, 'nationality', true );
						$country = self::get_countries( $country_code );
						if ( ! is_array( $country ) ) {
							$nationality = $country;
						} else {
							$nationality = $country_code; // Supporting legacy values for nationality
						}

					}

					$linked_name = '<a href="' . esc_url( home_url() . '/member/' . sanitize_title( $name ) . '/' ) . '">' . esc_html( $name ) . '</a>';

				}

				// Get incidents - these are found within the points, as drivers lose a fraction of a point for every incident
				$whole = floor( $points );
				$inc = ( 1 - ( $points - $whole ) ) / self::FRACTION;
				$inc = ( 0 + ( $points - $whole ) );
				if ( 0 == $inc ) {
					$inc = 1;
				}
				$inc = 1 - $inc;
				$inc = $inc / self::FRACTION;

				// Don't bother showing drivers who haven't scored any points yet
				$points = absint( round( $points ) );
				if ( 0 !== $points && '' !== $name) {

					$content .= '<tr>';

					$content .= '<td class="col-pos">' . esc_html( $position ) . '</td>';
					$content .= '<td class="col-name">' . $linked_name . '</td>';
					$content .= '<td class="col-number">' . esc_html( $car_number ) . '</td>';
					$content .= '<td class="col-nationality">' . esc_attr( $nationality ) . '</td>';
					$content .= '<td class="col-inc">' . absint( $inc ) . '</td>';
					$content .= '<td class="col-pts">' . absint( $points ) . '</td>'; // Need to use absint() here due to fractions being used to put low incident drivers in front

					$content .= '</tr>';
				}

			}
			$content .= '</tbody>';

			$content .= '</table>';
		}

		return $content;
	}

	/**
	 * Register the user.
	 *
	 * @todo Complete PHPDoc
	 */
	public function register_user( $username, $display_name, $password, $email, $member_info ) {
		// Create the user
		$user_data = array(
			'user_login'   => $username,
			'display_name' => $display_name,
			'user_pass'    => $password,
			'user_email'   => $email,
		);
		$user_id = wp_insert_user( $user_data ) ;

		// If no error, then add meta keys and log the person in
		if ( ! is_wp_error( $user_id ) ) {

			// Add some meta keys
			$meta_keys = array(
				'location',
				'oval_avg_inc',
				'oval_license',
				'oval_irating',
				'road_avg_inc',
				'road_license',
				'road_irating',
				'custid',
			);
			foreach ( $meta_keys as $meta_key ) {
				if ( isset( $member_info[$meta_key] ) ) {
					update_user_meta(
						$user_id,
						esc_html( $meta_key ),
						esc_html( $member_info[$meta_key] )
					);
				}
			}

			update_user_meta( $user_id, 'receive_extra_communication', 1 );
			update_user_meta( $user_id, 'season', 'reserve' );
			update_user_meta( $user_id, 'note', 'new signup ' + date( 'Y-m-d' ) );
			wp_mail(
				'ryanhellyer@gmail.com',
				'New Undiecar driver: ' . $display_name,
				'<a href="' . esc_url( 'https://undiecar.com/member/' . sanitize_title( $display_name ) ) . '/">' . esc_html( $display_name ) . '</a> has signed up to the Undiecar Championship.'
			);

			return true;

		} else {
			define( 'SRC_LOGIN_ERROR', true );

			return false;
		}
	}

	/**
	 * Does the name exist within iRacing?
	 * If they do return their info.
	 *
	 * @param  string  $display_name  Name of member
	 * @return array|bool   array if member exists in iRacing, otherwise false
	 */
	public function iracing_member_info( $display_name ) {
		$dir = wp_upload_dir();

		$stats = file_get_contents( $dir['basedir'] . '/iracing-members.json' );
		$stats = json_decode( $stats, true );

		// If user exists in iRacing, then return their stats, otherwise return false
		if ( isset( $stats[$display_name] ) ) {
			return $stats[$display_name];
		} else {
			return false;
		}
		/*



		$stats = file_get_contents( $dir['basedir'] . '/iracing-members-simple.json' );
		$stats = json_decode( $stats, true );

		if ( in_array ( $display_name , $stats ) ) {
			return true;
		}
		*/


	}

	/**
	 * Get all drivers from a specific season.
	 * Defaults to all seasons.
	 *
	 * @param  string  $season  The season to get drivers from
	 * @return array  all the drivers for the chosen season
	 */
	public function get_seasons_drivers( $season = 'all' ) {
		$drivers = array();

		$all_drivers = get_users( array( 'number' => 1000 ) );
		foreach ( $all_drivers as $driver ) {
			$driver_id = $driver->ID;

			// Ignore season 1 drivers who haven't set their password (means they never intended to register for the site)
			if (
				'reserve' === $season
				&&
				'1' !== get_user_meta( $driver_id, 'season', true )
				&&
				get_post_field( 'post_name', get_post( get_option( 'next-season' ) ) ) !== get_user_meta( $driver_id, 'season', true )
			) {

				// check if super admin, to avoid Ryan's personal account appearing in list
				if ( ! is_super_admin( $driver_id ) ) {
					$drivers[] = $driver->ID;
				}

			} else if  (
				'all' === $season || $season === get_user_meta( $driver_id, 'season', true )
				&&
				'reserve' !== $season
			) {
				$drivers[] = $driver->ID;
			}

		}

		return $drivers;
	}

	/**
	 * Get every country.
	 *
	 * @return array
	 */
	static function get_countries( $country_code = null ) {

		$countries = array(
			"AF" => "Afghanistan",
			"AX" => "Åland Islands",
			"AL" => "Albania",
			"DZ" => "Algeria",
			"AS" => "American Samoa",
			"AD" => "Andorra",
			"AO" => "Angola",
			"AI" => "Anguilla",
			"AQ" => "Antarctica",
			"AG" => "Antigua and Barbuda",
			"AR" => "Argentina",
			"AM" => "Armenia",
			"AW" => "Aruba",
			"AU" => "Australia",
			"AT" => "Austria",
			"AZ" => "Azerbaijan",
			"BS" => "Bahamas",
			"BH" => "Bahrain",
			"BD" => "Bangladesh",
			"BB" => "Barbados",
			"BY" => "Belarus",
			"BE" => "Belgium",
			"BZ" => "Belize",
			"BJ" => "Benin",
			"BM" => "Bermuda",
			"BT" => "Bhutan",
			"BO" => "Bolivia",
			"BA" => "Bosnia and Herzegovina",
			"BW" => "Botswana",
			"BV" => "Bouvet Island",
			"BR" => "Brazil",
			"IO" => "British Indian Ocean Territory",
			"BN" => "Brunei Darussalam",
			"BG" => "Bulgaria",
			"BF" => "Burkina Faso",
			"BI" => "Burundi",
			"KH" => "Cambodia",
			"CM" => "Cameroon",
			"CA" => "Canada",
			"CV" => "Cape Verde",
			"KY" => "Cayman Islands",
			"CF" => "Central African Republic",
			"TD" => "Chad",
			"CL" => "Chile",
			"CN" => "China",
			"CX" => "Christmas Island",
			"CC" => "Cocos (Keeling) Islands",
			"CO" => "Colombia",
			"KM" => "Comoros",
			"CG" => "Congo",
			"CD" => "Congo, The Democratic Republic of The",
			"CK" => "Cook Islands",
			"CR" => "Costa Rica",
			"CI" => "Cote D'ivoire",
			"HR" => "Croatia",
			"CU" => "Cuba",
			"CY" => "Cyprus",
			"CZ" => "Czech Republic",
			"DK" => "Denmark",
			"DJ" => "Djibouti",
			"DM" => "Dominica",
			"DO" => "Dominican Republic",
			"EC" => "Ecuador",
			"EG" => "Egypt",
			"SV" => "El Salvador",
			"GQ" => "Equatorial Guinea",
			"ER" => "Eritrea",
			"EE" => "Estonia",
			"ET" => "Ethiopia",
			"FK" => "Falkland Islands (Malvinas)",
			"FO" => "Faroe Islands",
			"FJ" => "Fiji",
			"FI" => "Finland",
			"FR" => "France",
			"GF" => "French Guiana",
			"PF" => "French Polynesia",
			"TF" => "French Southern Territories",
			"GA" => "Gabon",
			"GM" => "Gambia",
			"GE" => "Georgia",
			"DE" => "Germany",
			"GH" => "Ghana",
			"GI" => "Gibraltar",
			"GR" => "Greece",
			"GL" => "Greenland",
			"GD" => "Grenada",
			"GP" => "Guadeloupe",
			"GU" => "Guam",
			"GT" => "Guatemala",
			"GG" => "Guernsey",
			"GN" => "Guinea",
			"GW" => "Guinea-bissau",
			"GY" => "Guyana",
			"HT" => "Haiti",
			"HM" => "Heard Island and Mcdonald Islands",
			"VA" => "Holy See (Vatican City State)",
			"HN" => "Honduras",
			"HK" => "Hong Kong",
			"HU" => "Hungary",
			"IS" => "Iceland",
			"IN" => "India",
			"ID" => "Indonesia",
			"IR" => "Iran, Islamic Republic of",
			"IQ" => "Iraq",
			"IE" => "Ireland",
			"IM" => "Isle of Man",
			"IL" => "Israel",
			"IT" => "Italy",
			"JM" => "Jamaica",
			"JP" => "Japan",
			"JE" => "Jersey",
			"JO" => "Jordan",
			"KZ" => "Kazakhstan",
			"KE" => "Kenya",
			"KI" => "Kiribati",
			"KP" => "Korea, Democratic People's Republic of",
			"KR" => "Korea, Republic of",
			"KW" => "Kuwait",
			"KG" => "Kyrgyzstan",
			"LA" => "Lao People's Democratic Republic",
			"LV" => "Latvia",
			"LB" => "Lebanon",
			"LS" => "Lesotho",
			"LR" => "Liberia",
			"LY" => "Libyan Arab Jamahiriya",
			"LI" => "Liechtenstein",
			"LT" => "Lithuania",
			"LU" => "Luxembourg",
			"MO" => "Macao",
			"MK" => "Macedonia, The Former Yugoslav Republic of",
			"MG" => "Madagascar",
			"MW" => "Malawi",
			"MY" => "Malaysia",
			"MV" => "Maldives",
			"ML" => "Mali",
			"MT" => "Malta",
			"MH" => "Marshall Islands",
			"MQ" => "Martinique",
			"MR" => "Mauritania",
			"MU" => "Mauritius",
			"YT" => "Mayotte",
			"MX" => "Mexico",
			"FM" => "Micronesia, Federated States of",
			"MD" => "Moldova, Republic of",
			"MC" => "Monaco",
			"MN" => "Mongolia",
			"ME" => "Montenegro",
			"MS" => "Montserrat",
			"MA" => "Morocco",
			"MZ" => "Mozambique",
			"MM" => "Myanmar",
			"NA" => "Namibia",
			"NR" => "Nauru",
			"NP" => "Nepal",
			"NL" => "Netherlands",
			"AN" => "Netherlands Antilles",
			"NC" => "New Caledonia",
			"NZ" => "New Zealand",
			"NI" => "Nicaragua",
			"NE" => "Niger",
			"NG" => "Nigeria",
			"NU" => "Niue",
			"NF" => "Norfolk Island",
			"MP" => "Northern Mariana Islands",
			"NO" => "Norway",
			"OM" => "Oman",
			"PK" => "Pakistan",
			"PW" => "Palau",
			"PS" => "Palestinian Territory, Occupied",
			"PA" => "Panama",
			"PG" => "Papua New Guinea",
			"PY" => "Paraguay",
			"PE" => "Peru",
			"PH" => "Philippines",
			"PN" => "Pitcairn",
			"PL" => "Poland",
			"PT" => "Portugal",
			"PR" => "Puerto Rico",
			"QA" => "Qatar",
			"RE" => "Reunion",
			"RO" => "Romania",
			"RU" => "Russian Federation",
			"RW" => "Rwanda",
			"SH" => "Saint Helena",
			"KN" => "Saint Kitts and Nevis",
			"LC" => "Saint Lucia",
			"PM" => "Saint Pierre and Miquelon",
			"VC" => "Saint Vincent and The Grenadines",
			"WS" => "Samoa",
			"SM" => "San Marino",
			"ST" => "Sao Tome and Principe",
			"SA" => "Saudi Arabia",
			"SN" => "Senegal",
			"RS" => "Serbia",
			"SC" => "Seychelles",
			"SL" => "Sierra Leone",
			"SG" => "Singapore",
			"SK" => "Slovakia",
			"SI" => "Slovenia",
			"SB" => "Solomon Islands",
			"SO" => "Somalia",
			"ZA" => "South Africa",
			"GS" => "South Georgia and The South Sandwich Islands",
			"ES" => "Spain",
			"LK" => "Sri Lanka",
			"SD" => "Sudan",
			"SR" => "Suriname",
			"SJ" => "Svalbard and Jan Mayen",
			"SZ" => "Swaziland",
			"SE" => "Sweden",
			"CH" => "Switzerland",
			"SY" => "Syrian Arab Republic",
			"TW" => "Taiwan, Province of China",
			"TJ" => "Tajikistan",
			"TZ" => "Tanzania, United Republic of",
			"TH" => "Thailand",
			"TL" => "Timor-leste",
			"TG" => "Togo",
			"TK" => "Tokelau",
			"TO" => "Tonga",
			"TT" => "Trinidad and Tobago",
			"TN" => "Tunisia",
			"TR" => "Turkey",
			"TM" => "Turkmenistan",
			"TC" => "Turks and Caicos Islands",
			"TV" => "Tuvalu",
			"UG" => "Uganda",
			"UA" => "Ukraine",
			"AE" => "United Arab Emirates",
			"GB" => "United Kingdom",
			"US" => "United States",
			"UM" => "United States Minor Outlying Islands",
			"UY" => "Uruguay",
			"UZ" => "Uzbekistan",
			"VU" => "Vanuatu",
			"VE" => "Venezuela",
			"VN" => "Viet Nam",
			"VG" => "Virgin Islands, British",
			"VI" => "Virgin Islands, U.S.",
			"WF" => "Wallis and Futuna",
			"EH" => "Western Sahara",
			"YE" => "Yemen",
			"ZM" => "Zambia",
			"ZW" => "Zimbabwe"
		);

		if ( null !== $country_code && isset( $countries[$country_code] ) ) {
			return $countries[$country_code];
		} else if ( null !== $country_code && ! isset( $countries[$country_code] ) ) {
			return $country_code; // Legacy - catering for when country codes were loaded as a user submitted text string of their nationality
		} else {
			return $countries;
		}
	}

	/**
	 * Get informational block about a driver.
	 *
	 * @param  int  $driver_id   The drivers ID
	 * @return string  the block of HTML
	 */
	public function get_driver_block( $driver_id ) {

		if ( ! is_numeric( $driver_id ) ) {
			return;
		}

		$driver = get_userdata( $driver_id );
		$driver_name = $driver->data->display_name;
		$driver_slug = sanitize_title( $driver_name );

		$nationality        = get_user_meta( $driver_id, 'nationality', true );
		$car_number         = get_user_meta( $driver_id, 'car_number', true );
		$twitter            = get_user_meta( $driver_id, 'twitter', true );
		$facebook           = get_user_meta( $driver_id, 'facebook', true );
		$youtube            = get_user_meta( $driver_id, 'youtube', true );

		$drivers_list = '
		<p class="driver-block">
			<img class="alignright" src="' . get_avatar_url( $driver_id, array( 'size' => 150, 'default' => 'monsterid' ) ) . '" alt="" width="150" height="150" />
			<strong>
				<a href="' . esc_url( home_url() . '/member/' . $driver_slug ) . '">' . esc_html( $driver_name ) . '</a>';


		if ( '' !== $car_number ) {
			$drivers_list .= '
				#' . esc_html( $car_number );
		}

		$drivers_list .= '
			</strong>
			<br />';

		if ( '' !== $nationality ) {
			$drivers_list .= '
			Country: ' . esc_html( $this->get_countries( $nationality ) ) . '
			<br />';
		}

		if ( '' !== $twitter ) {

			$twitter_text = $twitter;
			if ( strpos( $twitter, 'twitter.com' ) === false ) {
				$twitter = 'https://twitter.com/' . $twitter . '/';
			}

			$drivers_list .= '		
			Twitter: <a href="' . esc_url( $twitter ) . '">@' . esc_html( $twitter_text ) . '</a>
			<br />';
		}

		if ( '' !== $facebook ) {

			$facebook_text = $facebook;
			if ( strpos( $facebook, 'facebook.com' ) === false ) {
				$facebook = 'https://facebook.com/' . $facebook . '/';
			}

			$drivers_list .= '
			Facebook: <a href="' . esc_url( $facebook ) . '">' . esc_html( $facebook_text ) . '</a>
			<br />';
		}

		if ( '' !== $youtube ) {

			$youtube_text = $youtube;
			if ( strpos( $youtube, 'youtube.com' ) === false ) {
				$youtube = 'https://youtube.com/' . $youtube . '/';
			} else {
				$youtube_text = 'youtube.com/' . $youtube . '/';
			}

			$drivers_list .= '
			YouTube: <a href="' . esc_url( $youtube )  . '">' . esc_html( $youtube ) . '</a>';
		}

		$drivers_list .= '
		</p>';

		return $drivers_list;
	}

	/**
	 * Get all driver points from a season.
	 */
	static function get_driver_points_from_season( $season_id ) {

		// Get all events from that season
		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'event',

			'meta_key'               => 'season',
			'meta_value'             => $season_id,

			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );

		$stored_results = $fastest_laps = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$fastest_laps = array();
				$incident_results = array();
				foreach ( array( 1, 2, 3 ) as $key => $race_number ) {
					$results = get_post_meta( get_the_ID(), '_results_' . $race_number, true );
					$results = json_decode( $results, true );
					$points_positions = get_post_meta( $season_id, 'points_positions', true );

					if ( is_array( $results ) ) {

						// Add points for finishing position and calc incidents
						foreach ( $results as $pos => $result ) {

							$name = $result['name'];
							if ( isset( $points_positions[$pos - 1] ) ) {

								if ( isset( $stored_results[$name] ) ) {
									$stored_results[$name] = $stored_results[$name] + $points_positions[$pos - 1];
								} else {
									$stored_results[$name] = $points_positions[$pos - 1];
								}

							}

							// Store fastest laps
							if ( isset( $result['fastest_lap_time'] ) && '' !== $result['fastest_lap_time'] ) {

								$time_exploded = explode( ':', $result['fastest_lap_time'] );

								if ( isset( $time_exploded[1] ) ) {
									$time = $time_exploded[0] * 60 + $time_exploded[1];
								} else {
									$time = $time_exploded[0];
								}

								if (
									! isset( $fastest_laps[$name] )
									||
									(
										isset( $fastest_laps[$name] )
										&&
										$fastest_laps[$name] > $time
									)
								) {
									$fastest_laps[$name] = $time;
								}

							}

							// Get least incident info (we ignore anyone who isn't within one lap of the lead)
							if (
								$results[1]['laps-completed'] === $result['laps-completed']
								||
								( $results[1]['laps-completed'] - 1 ) === $result['laps-completed']
							) {
								$name = $result['name'];
								$incident_results[$name][$key] = $result['incidents'];
							} else {
								$incident_results[$name][$key] = 100000;
							}

							// Adding tiny fraction of a point to allow us to work out who is in front when there is a draw on points
							if ( isset( $stored_results[$name] ) ) {
								$stored_results[$name] = $stored_results[$name] - ( $result['incidents'] * self::FRACTION );
							} else {
								$stored_results[$name] = 0 - ( $result['incidents'] * self::FRACTION );
							}

						}

						// Give bonus points for most spectacular crash in each race
						$most_spectacular_crash_name = get_post_meta( get_the_ID(), 'event_race_' . $race_number . '_most_spectacular_crash', true );
						if ( isset( $stored_results[$most_spectacular_crash_name] ) ) {
							$stored_results[$most_spectacular_crash_name] = $stored_results[$most_spectacular_crash_name] + 1;
						} else if ( '' !== $most_spectacular_crash_name ) {
							$stored_results[$most_spectacular_crash_name] = 1;
						}

					}

				}

				// Work out who gets points for the least incidents
				// Work out how many races there were - only want to count drivers who completed both races
				foreach ( $incident_results as $x => $driver_incidents ) {

					if ( isset( $max ) && $max < count( $driver_incidents ) ) {
						$max = count( $driver_incidents );
					} else if ( ! isset( $max ) ) {
						$max = count( $driver_incidents );
					}

				}
				// Remove drivers who weren't in both races
				foreach ( $incident_results as $x => $driver_incidents ) {

					if ( $max !== count( $driver_incidents ) ) {
						unset( $incident_results[$x] );
					} else {
						$incident_results[$x] = array_sum( $driver_incidents );
					}

				}
				asort( $incident_results );

				$least_incident_drivers = array();
				unset( $least_incidents );
				foreach ( $incident_results as $driver_name => $incidents ) {
					// Grab definite least incidents value
					if ( ! ( isset( $least_incidents ) ) ) {
						$least_incidents = $incidents;
					}

					if ( $least_incidents === $incidents ) {
						$least_incident_drivers[] = $driver_name;
					}

				}

				// Add least incident points
				foreach ( $least_incident_drivers as $lkey => $name ) {

					if ( isset( $stored_results[$name] ) ) {
						$stored_results[$name] = $stored_results[$name] + 1;
					} else {
						$stored_results[$name] = 1;
					}

				}

				if ( 'update' === get_option( 'undiecar-cache' ) || empty( get_post_meta( get_the_ID(), '_least_incidents', true ) ) ) {

					// Store least incident info in events
					if ( is_array( $least_incident_drivers ) ) {
						update_post_meta( get_the_ID(), '_least_incidents', $least_incident_drivers );
					} else {
						delete_post_meta( get_the_ID(), '_least_incidents' );
					}

				}

				// Add bonus point for pole
				$qual_results = get_post_meta( get_the_ID(), '_results_qual', true );
				$qual_results = json_decode( $qual_results, true );
				if ( isset( $qual_results[1] ) ) {
					$pole_position = $qual_results[1];
					$name = $pole_position['name'];
					if ( isset( $stored_results[$name] ) ) {
						$stored_results[$name] = $stored_results[$name] + 1;

						// Record who won pole
						if ( 'update' === get_option( 'undiecar-cache' ) || '' === get_post_meta( get_the_ID(), '_pole_position', true ) ) {
							update_post_meta( get_the_ID(), '_pole_position', $name );
						}

					}
				}

				// Add bonus points for fastest lap in each event
				asort( $fastest_laps );

				// Fixing buggy fastest laps
				if ( 'update' === get_option( 'undiecar-cache' ) ) {
					delete_post_meta( get_the_ID(), '_fastest_lap' );
				}

				foreach ( $fastest_laps as $name => $fastest_lap_time ) {

					if ( isset( $stored_results[$name] ) ) {

						// Record who won bonus point
						if ( 'update' === get_option( 'undiecar-cache' ) || '' === get_post_meta( get_the_ID(), '_fastest_lap', true ) ) {
							update_post_meta( get_the_ID(), '_fastest_lap', $name );
						}

						$stored_results[$name] = $stored_results[$name] + 1;
						break;
					}

				}

			}

		}
		wp_reset_query();

		arsort( $stored_results );

		return $stored_results;
	}

}
