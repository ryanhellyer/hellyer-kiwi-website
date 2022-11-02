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
$note               = get_user_meta( $member_id, 'note', true );
$invited            = get_user_meta( $member_id, 'invited', true );
$former_champion    = get_user_meta( $member_id, 'former_champion', true );
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


if ( $member_id === get_current_user_id() ) {
//	echo '<p>Please fill out your profile</p>';
} else if ( 2 < $missing_data_count ) {
//	echo '<p>If you want to see more information about this driver, please ask them to update their Undycar profile.</p>';
}


// Show image gallery
$images = get_user_meta( $member_id, 'images', true );
if ( is_array( $images ) ) {
	$image_ids = '';
	krsort( $images );
	foreach ( $images as $key => $image_id ) {
		$image_ids .= $image_id . ',';
	}

	if ( 1 === count( $images ) ) {
		$size = 'large';
		$columns = 1;
	} else if ( 2 === count( $images ) ) {
		$size = 'large';
		$columns = 2;
	} else if ( 15 < count( $images ) ) {
		$size = 'thumbnail';
		$columns = 8;
	} else if ( 11 < count( $images ) ) {
		$size = 'thumbnail';
		$columns = 6;
	} else {
		$size = 'medium';
		$columns = 3;
	}

	echo do_shortcode( '[gallery link="attachment" columns="' . esc_attr( $columns ) . '" size="' . esc_attr( $size ) . '" ids="' . esc_attr( $image_ids ) . '"]' );
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
			'club',
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

		echo '
		<label>Note</label>
		<input name="note" type="text" value="' . esc_attr( $note ) . '" />';

		echo '
		<label>Invited? (yes|null)</label>
		<input name="invited" type="text" value="' . esc_attr( $invited ) . '" />';

		echo '
		<label>Former champion? (yes|null)</label>
		<input name="former_champion" type="text" value="' . esc_attr( $former_champion ) . '" />';
	}


	// Notice
	if (
		'' != get_option( 'non-championship-user-message' )
//		&&
//		$member_id === get_current_user_id()// || is_super_admin()
	) {
		echo '
		<style>
		.important-information {
			border: 1px solid rgba(255,0,0,0.8);
			border-radius: 40px;
			background: rgba(255,0,0,0.1);
			padding: 20px 40px;
			margin: 60px 0;
		}
		</style>
		<div class="important-information">';

		/*
		echo '
			<h3>' . esc_html__( 'Which championship would you like to compete in?', 'undiecar' ) . '</h3>';

			$season = (array) get_user_meta( $member_id, 'season', true );

			if ( ! isset( $season['undiecar'] ) ) {
				$season['undiecar'] = '';
			}
			if ( ! isset( $season['lights'] ) ) {
				$season['lights'] = '';
			}
			if ( ! isset( $season['summer'] ) ) {
				$season['summer'] = '';
			}
			if ( ! isset( $season['special'] ) ) {
				$season['special'] = '';
			}

			echo '<p>';
			echo '<label>' . esc_html__( 'Undiecar Championship', 'undiecar' ) . '</label>';
			echo '<br />';
			echo '<input name="season[undiecar]" type="checkbox" ' . checked( $season['undiecar'], 1, false ) . ' value="1">';
			echo sprintf(
				esc_html__( 'Features the %s', 'undiecar' ),
				'<a href="' . esc_url( home_url() . '/car/dallara-ir-05/' ) . '">' . esc_html__( 'Dallara Indycar IR-05 circa 2011', 'undiecar' ) . '</a>'
			);
			echo '<br />';
			echo esc_html__( 'This car comes free with iRacing. All tracks used in the championship are also free.' );
			echo '</p>';

			echo '<p>';
			echo '<label>' . esc_html__( 'Undie Lights Championship', 'undiecar' ) . '</label>';
			echo '<br />';
			echo '<input name="season[lights]" type="checkbox" ' . checked( $season['lights'], 1, false ) . ' value="1">';
			echo sprintf(
				esc_html__( 'Features the %s', 'undiecar' ),
				'<a href="' . esc_url( home_url() . '/car/skip-barber-formula-2000/' ) . '">' . esc_html__( 'Skip Barber Formula 2000', 'undiecar' ) . '</a>'
			);
			echo '<br />';
			echo esc_html__( 'All tracks used in the championship are free.' );
			echo '</p>';

			echo '<p>';
			echo '<label>' . esc_html__( 'Undiecar Summer Series', 'undiecar' ) . '</label>';
			echo '<br />';
			echo '<input name="season[summer]" type="checkbox" ' . checked( 'x', 1, false ) . ' value="1">';
			echo sprintf(		esc_html__( 'Features the %s and %s', 'undiecar' ),
				'<a href="' . esc_url( home_url() . '/car/porsche-911-gt3-cup/' ) . '">' . esc_html__( 'Porsche 911 GT3 Cup', 'undiecar' ) . '</a>',
				'<a href="' . esc_url( home_url() . '/car/global-mazda-mx-5-cup/' ) . '">' . esc_html__( 'Global Mazda MX-5 Cup', 'undiecar' ) . '</a>'
			);
			echo '<br />';
			echo esc_html__( 'All tracks used in the championship are free.' );
			echo '</p>';

			echo '<p>';
			echo '<label>' . esc_html__( 'Special Events' ) . '</label>';
			echo '<br />';
			echo '<input name="season[special]" type="checkbox" ' . checked( 'x', 1, false ) . ' value="1">';
			echo esc_html__( 'Features a variety of events with both paid and free tracks and cars' );
			echo '</p>';


		*/
		echo get_option( 'non-championship-user-message' ) . '

		</div>';

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
		echo 'type="password" value="" placeholder="Enter a password here" />

		<br />
		<label>Receive notifications?</label>
		<input name="receive-notifications" type="checkbox" style="font-size:40px;" ';

		$receive_notifications = get_user_meta( $member_id, 'receive_notifications', true );
		$checked = '';
		if ( 'no' !== $receive_notifications ) {
			$checked = 1;
		}
		echo checked( $checked, 1, false );

		echo ' value="1" />
		<br />
		<span>Includes upcoming race information and various updates via iRacing PM or email</span>
		<br />
		<label>Receive less notifications?</label>
		<input name="receive-less-notifications" type="checkbox" style="font-size:40px;" ';

		$receive_less_notifications = get_user_meta( $member_id, 'receive_less_notifications', true );
		$checked = '';
		if ( 'yes' === $receive_less_notifications ) {
			$checked = 1;
		}
		echo checked( $checked, 1, false );

		echo ' value="1" />
		<br />
		<span>Receive only very important notifications</span>
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
