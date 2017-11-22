<?php
/**
 * The main template file.
 *
 * @package Undycar Theme
 * @since Undycar Theme 1.0
 */

add_filter( 'body_class', function( $classes ) {
	return array_merge( $classes, array( 'member', 'page' ) );
} );

define( 'SRC_MEMBERS_TEMPLATE', true );

$member_id = $src_member->ID;
$email = $src_member->user_email;
$display_name = $src_member->display_name;

$location           = get_user_meta( $member_id, 'location', true );
$nationality        = get_user_meta( $member_id, 'nationality', true );
$description        = get_user_meta( $member_id, 'description', true );
$car_number         = get_user_meta( $member_id, 'car_number', true );
$racing_experience  = get_user_meta( $member_id, 'racing_experience', true );
$first_racing_games = get_user_meta( $member_id, 'first_racing_games', true );
$twitter            = get_user_meta( $member_id, 'twitter', true );
$facebook           = get_user_meta( $member_id, 'facebook', true );
$youtube            = get_user_meta( $member_id, 'youtube', true );
$avatar             = get_user_meta( $member_id, 'avatar', true );
$header_image       = get_user_meta( $member_id, 'header_image', true );
$season             = get_user_meta( $member_id, 'season', true );
$team               = get_user_meta( $member_id, 'team', true );
$note               = get_user_meta( $member_id, 'note', true );
$custid             = get_user_meta( $member_id, 'custid', true );

get_header();

echo '<article id="main-content">';

echo get_avatar( $member_id, 512, 'monsterid' );

echo '<h2>' . esc_html( $display_name )  . '</h2>';



echo '<p>' . $description . '</p>';

echo '<p>';

$missing_data_count = 0;

if ( '' !== $car_number ) {
	echo '<strong>Car number:</strong> ' . esc_html( $car_number ) . '<br />';
} else {
	$missing_data_count++;
}

$countries = src_get_countries();
if ( isset( $countries[$location] ) ) {
	$location = $countries[$location];
}

if ( '' !== $location ) {
	echo '<strong>Location:</strong> ' . esc_html( $location ) . '<br />';
} else {
	$missing_data_count++;
}

if ( '' !== $nationality ) {
	echo '<strong>Country:</strong> ' . esc_html( $nationality ) . '<br />';
} else {
	$missing_data_count++;
}

if ( '' !== $racing_experience ) {
	echo '<strong>Racing experience:</strong> ' . $racing_experience . '<br />';
} else {
	$missing_data_count++;
}

if ( '' !== $first_racing_games ) {
	echo '<strong>First racing games:</strong> ' . $first_racing_games . '<br />';
} else {
	$missing_data_count++;
}

if ( '' !== $custid ) {
	echo '<strong>iRacing account:</strong> <a href="' . esc_url( 'http://members.iracing.com/membersite/member/CareerStats.do?custid=' . $custid ) . '">' . esc_html( $display_name ) . ' on iRacing</a><br />';
}

$social_network_counter = 0;
$social_networks = '';
if ( '' !== $twitter ) {

	$social_networks .= '<a href="' . esc_url( $twitter ) . '">Twitter</a>';

	$social_network_counter++;
}
if ( '' !== $facebook ) {

	if ( 0 < $social_network_counter ) {
		$social_networks .= ' | ';
	}

	$social_networks .= '<a href="' . esc_url( $facebook ) . '">Facebook</a>';

	$social_network_counter++;
}
if ( '' !== $youtube ) {

	if ( 0 < $social_network_counter ) {
		$social_networks .= ' | ';
	}

	$social_networks .= '<a href="' . esc_url( $youtube ) . '">YouTube</a>';

	$social_network_counter++;
}

$plaural = '';
if ( 1 !== $social_network_counter ) {
	$plaural = 's';
}

if ( 0 < $social_network_counter ) {
	echo '<strong>Social network' . esc_html( $plaural ) . ':</strong> ' . $social_networks;
} else {
	$missing_data_count++;
}

echo '</p>';

if (
	$member_id === get_current_user_id()
	&&
	'3' !== $season
) {
	echo '<p><strong><u>Unfortunately all positions are now filled for season 3</u></strong>, but you have been placed on the reserve list and will be notified if any spots become available or of any special events we may hold. If you only want to compete in the special events, please contact <a href="https://undiecar.com/member/ryan-hellyer/">Ryan Hellyer</a> via either <a href="http://members.iracing.com/membersite/member/CareerStats.do?custid=279455">iRacing private message</a> or via our <a href="https://discord.gg/csjKs6z">Discord channel</a>.</p>';
}


if ( $member_id === get_current_user_id() ) {
//	echo '<p>Please fill out your profile</p>';
} else if ( 2 < $missing_data_count ) {
//	echo '<p>If you want to see more information about this driver, please ask them to update their Undycar profile.</p>';
}


/**
 * Form for editing the members details.
 */
if (
	( $member_id === get_current_user_id() &&	is_user_logged_in() )
	||
	is_super_admin()
) {

	if ( is_super_admin() ) {

		$meta_keys = array(
			'oval_irating',
			'oval_license',
			'oval_avg_inc',
			'road_irating',
			'road_license',
			'road_avg_inc',
			'custid',
		);

		echo '
		<hr />
		<p>
			<strong>Super admin only data</strong><br /><small style="line-height:18px;display:block;">';
		foreach ( $meta_keys as $meta_key ) {
			echo $meta_key . ': ' . esc_html( get_user_meta( $member_id, $meta_key, true ) ) . '<br />';
		}
		echo '</small></p>';

	}


	echo '
	<hr />

	<form action="" method="POST" enctype="multipart/form-data">';

	if ( is_super_admin() ) {

		// Shove seasons into an array
		$query = new WP_Query( array(
			'posts_per_page'         => 100,
			'post_type'              => 'season',
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
		) );
		$seasons[] = 'reserve';
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$season_slug = get_post_field( 'post_name', get_post( get_the_ID() ) );
				$seasons[ get_the_ID() ] = $season_slug;

				if ( $season_slug === $season ) {
					$current_season_id = get_the_ID();
				}

			}
		}

		echo '
		<label>Season?</label>
		<select name="season">';
		foreach ( $seasons as $key => $season_slug ) {
			echo '<option ' . selected( $season, $season_slug ) . ' value="' . esc_attr( $season_slug ) . '">' . esc_html( $season_slug ) .  '</option>';
		}
		echo '</select>';

		echo '
		<label>Note</label>
		<input name="note" type="text" value="' . esc_attr( $note ) . '" />';

		$teams = get_post_meta( $current_season_id, 'team_names', true );

		echo '
		<label>Team</label>
		<select name="team">
			<option value="">None</option>';
		foreach ( $teams as $key => $team_name ) {
			echo '<option ' . selected( $team, $team_name ) . ' value="' . esc_attr( $team_name ) . '">' . esc_html( $team_name ) .  '</option>';
		}
		echo '
		</select>';

	}

	echo '
		<label>Email address</label>
		<input name="email" type="email" value="' . esc_attr( $email ) . '" />

		<label>Password</label>
		<input name="password" ';

		// If password never set, then highlight this field as it's critical for them to log back in
		if ( '1' !== get_user_meta( $member_id, 'password_set', true ) ) {
			echo 'class="highlighted-field" ';
		}

		echo 'type="text" value="" placeholder="Enter a password here" />

		<br />
		<label>Receive extra Undiecar Championship communication?</label>
		<input name="receive-extra-communication" type="checkbox" style="font-size:40px;" ';

		$checked = get_user_meta( $member_id, 'receive_extra_communication', true );
		echo checked( $checked, 1, false );

		echo ' value="1" />
		<br />
		<span>Includes upcoming race information and various updates via iRacing PM or email</span>
		<br /><br />

		<label>Current location</label>
		<input name="location" type="text" value="' . esc_attr( $location ) . '" />

		<label>Country</label>
		<select name="nationality">';
		foreach ( SRC_Core::get_countries() as $country_code => $country ) {
			echo '<option ' . selected( $nationality, $country_code ) . ' value="' . esc_attr( $country_code ) . '">' . esc_html( $country ) .  '</option>';
		}
		echo '
		</select>

		<label>Car number</label>
		<input name="car-number" type="text" value="' . esc_attr( $car_number ) . '" />

		<label>Description of yourself</label>
		<textarea name="description">' . esc_textarea( $description ) . '</textarea>

		<label>Racing experience</label>
		<textarea name="racing-experience">' . esc_textarea( $racing_experience ) . '</textarea>

		<label>First racing games</label>
		<textarea name="first-racing-games">' . esc_textarea( $first_racing_games ) . '</textarea>

		<label>Social Networks</label>
		<input name="twitter" type="text" value="' . esc_attr( $twitter ) . '" placeholder="Twitter" />
		<input name="facebook" type="text" value="' . esc_attr( $facebook ) . '" placeholder="Facebook" />
		<input name="youtube" type="text" value="' . esc_attr( $youtube ) . '" placeholder="YouTube" />

		<label>Header image</label>
		<input name="header_image" type="file" />';

		$header_image = wp_get_attachment_image_src( $header_image, 'thumbnail' );
		if ( isset( $header_image[0] ) && '' !== $header_image[0] ) {
			echo '<img src="' . esc_url( $header_image[0] ) . '" style="max-width:200px;max-height:100px;" />';
		}
		echo '<br />


		<label>Profile picture</label>
		<input name="avatar" type="file" />';

		$avatar = wp_get_attachment_image_src( $avatar, 'thumbnail' );
		if ( isset( $avatar[0] ) && '' !== $avatar[0] ) {
			echo '<img src="' . esc_url( $avatar[0] ) . '" style="max-width:200px;max-height:100px;" />';
		}
		echo '<br /><br /><br />';


	wp_nonce_field( 'src_nonce', 'src_nonce' );

	echo '

		<input name="member-id" type="hidden" value="' . esc_attr( $member_id ) . '" />

		<input type="submit" value="Save" />
	</form>';
}


echo '</article>';

get_footer();
