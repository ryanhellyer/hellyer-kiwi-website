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
$custid             = get_user_meta( $member_id, 'custid', true );

get_header();

echo '<article id="main-content">';

echo get_avatar( $member_id, 512, 'monsterid' );

echo '<h2>' . esc_html( $display_name )  . '</h2>';



// Notice
if (
	'' != get_option( 'non-championship-user-message' )
	&&
	$member_id === get_current_user_id()// || is_super_admin()
	&&
	(
		// This is a crude hack until we check the actual current and next seasons
		'5' !== $season
		&&
		'6' !== $season
		&&
		'7' !== $season
		&&
		'8' !== $season
		&&
		'9' !== $season
		&&
		'10' !== $season
	)
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
<div class="important-information">
' . get_option( 'non-championship-user-message' ) . '
</div>';

}





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
		$seasons[] = 'banned';
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
		<label>Season? (test multi-season)</label>';
		$season_membership[] = $season;

		if ( $query->have_posts() ) {
			$count = 0;
			while ( $query->have_posts() ) {
				$query->the_post();

				$selected_season = '';
				if ( in_array( $season_slug, $season_membership ) ) {
					$selected_season = $season_slug;
				}

				$drivers = get_post_meta( get_the_ID(), 'drivers', true );
				$selected_season = '';
				if ( is_array( $drivers ) && in_array( $member_id, $drivers ) ) {
					$selected_season = 1;
				}

				if ( 0 === $count ) {
					echo '<br />';
				} else {
					echo ' | &nbsp ';
				}

				echo '<label>' . esc_html( get_the_title( get_the_id() ) ) . '</label>';
				echo '<input name="' . esc_attr( 'multi-season[' . get_the_ID() . ']' ) . '"type="checkbox" ' . checked( $selected_season, 1, false ) . ' value="1">';

				$count++;
			}
			echo '<br />';

		}

		echo '
		<label>Note</label>
		<input name="note" type="text" value="' . esc_attr( $note ) . '" />';

		echo '
		<label>Invited? (yes|null)</label>
		<input name="invited" type="text" value="' . esc_attr( $invited ) . '" />';
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
