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
$location = get_user_meta( $member_id, 'location', true );
$oval_avg_inc = get_user_meta( $member_id, 'oval_avg_inc', true );
$oval_license = get_user_meta( $member_id, 'oval_license', true );
$oval_irating = get_user_meta( $member_id, 'oval_irating', true );
$road_avg_inc = get_user_meta( $member_id, 'road_avg_inc', true );
$road_license = get_user_meta( $member_id, 'road_license', true );
$road_irating = get_user_meta( $member_id, 'road_irating', true );

get_header();

echo '<article id="main-content">';

if ( $member_id === get_current_user_id() ) {
	echo '
	<form action="" method="POST">

		<label>Email address</label>
		<input type="email" value="' . esc_attr( $email ) . '" />

		<label>Display name</label>
		<input type="text" value="' . esc_attr( $display_name ) . '" />

		<label>Location</label>
		<input type="text" value="' . esc_attr( $location ) . '" />

		';

	wp_nonce_field( 'src_nonce', 'src_nonce' );

	echo '
		<input type="submit" value="Save" />
	</form>';
}

//update_user_meta( $member_id, 'custid', 279455 );


echo 'Email address: ' . $email . '<br />';
echo 'Display name: ' . $display_name . '<br />';
echo 'location: ' . $location . '<br />';
echo 'oval_avg_inc: ' . $oval_avg_inc . '<br />';
echo 'oval_license: ' . $oval_license . '<br />';
echo 'oval_irating: ' . $oval_irating . '<br />';
echo 'road_avg_inc: ' . $road_avg_inc . '<br />';
echo 'road_license: ' . $road_license . '<br />';
echo 'road_irating: ' . $road_irating . '<br />';


echo 'This does not show when logged out of iRacing ... <img src="http://members.iracing.com/membersite/member/GetProfileImage?custid=279455" />';

echo '</article>';

get_footer();
