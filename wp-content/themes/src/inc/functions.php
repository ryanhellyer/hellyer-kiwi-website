<?php

function src_get_events( $season_slug ) {
	$season_id = src_get_id_from_slug( $season_slug, 'season' );
	$events = get_post_meta( $season_id, 'event', true );
	return $events;
}

if ( isset( $_GET['test'] ) ) {
	print_r( src_get_events( 3 ) );
	die;
}

function src_get_id_from_slug( $slug, $post_type ) {

	// Get season ID from slug
	$query = new WP_Query(
		array(
			'post_type'              => 'season',
			'posts_per_page'         => 1,
			'post_title'             => $slug,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		)
	);

	if ( $query->have_posts() ) {
		while ( $query->have_posts() ) {
			$query->the_post();
			$season_id = get_the_ID();
		}
	}

	return $season_id;
}

function src_get_drivers( $season_slug, $reorder_by_am = false ) {

	$season_id = src_get_id_from_slug( $season_slug, 'season' );
	$drivers = get_post_meta( get_the_ID(), '_seasons_drivers', true );	

	foreach ( $drivers as $key => $driver ) {

		if ( true === $reorder_by_am ) {
			$results = src_get_driver_resultsam( $season_slug, $driver[0], 'Points' );
		} else {
			$results = src_get_driver_results( $season_slug, $driver[0], 'Points' );
		}

		$points = 0;
		foreach ( $results as $x => $result ) {
			if ( is_numeric( $result ) ) {
				$points = $points + $result;
			}
		}

		$drivers[$key][6] = $points;
	}

	usort( $drivers, 'src_reorder_subarray' );

	return $drivers;
}

function src_get_teams( $season_slug ) {
	$teams = array();

	$drivers1 = $drivers2 = src_get_drivers( $season_slug );
	foreach ( $drivers1 as $key1 => $driver1 ) {
		$team1 = $driver1[4];
		$points1 = $driver1[6];

		foreach ( $drivers2 as $key2 => $driver2 ) {
			$team2 = $driver2[4];

			if ( $key1 !== $key2 && $team1 === $team2 ) {
				$points2 = $driver2[6];

				// Check if numeric as some values may be listed as DNF, C etc.
				if ( is_numeric( $points1 ) && is_numeric( $points2 ) ) {
					$teams[$driver1[4]] = $points1 + $points2;
				} else if ( is_numeric( $points1 ) ) {
					$teams[$driver1[4]] = $points1;
				} else {
					$teams[$driver1[4]] = $points2;
				}

			}
		}

	}

	arsort( $teams );

	return $teams;
}

function src_get_driver( $season_slug, $username ) {
	$season_id = src_get_id_from_slug( $season_slug, 'season' );
	$drivers = get_post_meta( get_the_ID(), '_seasons_drivers', true );	

	foreach ( $drivers as $key => $driver ) {
		if ( $username === $driver[0] ) {
			return $driver;
		}
	}

	return false;
}

function src_get_driver_info( $season_slug, $username, $info ) {
	$driver = src_get_driver( $season_slug, $username );

	if ( isset( $driver[1] ) && 'Number' === $info ) {
		return $driver[1];
	} else if ( isset( $driver[2] ) && 'Country' === $info ) {
		return $driver[2];
	} else if ( isset( $driver[3] ) && 'Car' === $info ) {
		return $driver[3];
	} else if ( isset( $driver[4] ) && 'Team' === $info ) {
		return $driver[4];
	} else if ( isset( $driver[5] ) && 'Class' === $info ) {
		return $driver[5];
	}

	return false;
}

function src_get_results( $season_slug ) {
	$season_id = src_get_id_from_slug( $season_slug, 'season' );
	$results = get_post_meta( $season_id, '_seasons_results', true );

	return $results;
}

function src_get_amresults( $season_slug ) {
	$season_id = src_get_id_from_slug( $season_slug, 'season' );
	$results = get_post_meta( $season_id, '_seasons_resultsam', true );

	return $results;
}

function src_get_driver_results( $season_slug, $username ) {
	$results = src_get_results( $season_slug );
	return $results[$username];
}

function src_get_driver_resultsam( $season_slug, $username ) {
	$results = src_get_amresults( $season_slug );
	return $results[$username];
}

function src_get_driver_points( $season_slug, $username ) {
	$points_array = src_get_driver_results( $season_slug, $username );

	$points = 0;
	foreach ( $points_array as $key => $race_points ) {
		$points = $points + $race_points;
	}

	return $points;
}

function src_get_driver_ampoints( $season_slug, $username ) {
	$points_array = src_get_driver_resultsam( $season_slug, $username );

	$points = 0;
	foreach ( $points_array as $key => $race_points ) {
		$points = $points + $race_points;
	}

	return $points;
}

function src_get_nationality( $username ) {
	$user = get_user_by( 'login', $username);

	$nationality = '';
	if ( isset( $user->ID ) ) {
		$nationality = get_user_meta( $user->ID, 'nationality', true );
	}

	return $nationality;
}

function src_get_display_name_from_username( $username ) {
	$user = get_user_by( 'login', $username);

	if ( isset( $user->data->display_name ) ) {
		$name = $user->data->display_name;
	} else {
		$name = $username; // If no display name found, then just return the username
	}

	return $name;
}

function src_get_memberurl_from_username( $username ) {

	$user = get_user_by( 'login', $username);
	if ( isset( $user->data->ID ) ) {

		$user_id = $user->data->ID;
		$url = bbp_get_user_profile_url( $user_id );

		return $url;
	}

	return false;
}

function src_reorder_subarray( $a, $b ) {
	return $b[6] - $a[6];
}

add_shortcode( 'src-available-cars', 'src_available_cars_shortcode' );
function src_available_cars_shortcode() {

$season_slug = 3;

	// Form array of available car information
	$available_cars = array();
	foreach ( src_get_available_cars( $season_slug ) as $key => $car ) {
		$available_cars[$key]['manufacturer'] = $car['manufacturer'];
		$available_cars[$key]['model']        = $car['model'];
		$available_cars[$key]['available'] = 2;

		foreach ( src_get_drivers( $season_slug ) as $x => $driver ) {

			if ( $car['manufacturer'] . ' ' . $car['model'] === $driver[3] ) {
				echo $car['manufacturer'] . ' ' . $car['model'].': ' . $driver[3] . "\n";
				$available_cars[$key]['taken_by'] = $driver[4];
				$available_cars[$key]['available'] = src_how_many_spots_left_in_team( $season_slug, $driver[4] );
			}

		}

	}

	$content = '<table>
	<tr>
		<th></th>
		<th>Manufacturer</th>
		<th>Model</th>
		<th>Remaining</th>
	</tr>';
	$count = 0;
	foreach ( $available_cars as $key => $car ) {
		$count++;

		$strike = '';

		if ( 0 === $car['available'] ) {
			$strike = '<s>';
		}

		$content .= '<tr>';

		$content .= '<td>' . $strike . absint( $count ) . $strike . '</td>';
		$content .= '<td>' . $strike . esc_html( $car['manufacturer'] ) . $strike . '</td>';
		$content .= '<td>' . $strike . esc_html( $car['model'] ) . $strike . '</td>';

		$content .= '<td>' . $strike . esc_html( $car['available'] ) . $strike . '</td>';

		$content .= '</tr>';
	}

	$content .= '</table>';

	return $content;
}

function src_how_many_spots_left_in_team( $season_slug, $team_name ) {

	$number = 0;
	foreach ( src_get_drivers( $season_slug ) as $key => $driver ) {

		if ( $team_name === $driver[4] ) {
			$number++;
		}

	}

	$number_left = max( 2 - $number, 0 );

	return $number_left;
}

function src_get_available_cars( $season_slug ) {
	return src_get_all_cars( $season_slug );	
}

function src_get_all_cars( $season_slug ) {

	$season_id = src_get_id_from_slug( $season_slug, 'season' );

	$cars = get_post_meta( $season_id, 'car', true );

	return $cars;
}


/**
 * Get every country.
 *
 * @return array
 */
function src_get_countries() {

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

	return $countries;
}
