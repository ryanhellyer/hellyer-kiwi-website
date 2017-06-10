<?php

function src_get_events( $season_slug ) {
	$season_id = src_get_id_from_slug( $season_slug, 'season' );
	$events = get_post_meta( $season_id, 'event', true );
	return $events;
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
