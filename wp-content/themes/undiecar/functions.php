<?php

if ( is_super_admin() ) {

//add_action( 'template_redirect', function() {
//	print_r( get_post_meta( 3967 ) );die;
//} );

	require( 'tools/modify-results.php' );
	require( 'tools/get-names-from-json.php' );
	require( 'tools/emails.php' );
	require( 'tools/pull-names-from-csv.php' );
	require( 'tools/convert-json.php' );
	require( 'tools/create-user.php' );
	require( 'tools/TEST-auto-news.php' );
}

add_option( 'src_featured_page', '' );
add_option( 'src-season', '' );

require( 'inc/class-src-core.php' );
require( 'inc/class-src-admin.php' );
require( 'inc/class-src-setup.php' );
require( 'inc/class-src-gallery.php' );
require( 'inc/class-src-cron.php' );
require( 'inc/class-src-tracks.php' );
require( 'inc/class-src-seasons.php' );
require( 'inc/class-src-events.php' );
require( 'inc/class-src-register.php' );
require( 'inc/class-src-members.php' );
require( 'inc/class-src-settings.php' );
require( 'inc/class-src-cars.php' );
require( 'inc/class-src-schedule.php' );
require( 'inc/class-src-teams.php' );
require( 'inc/class-src-messages.php' );
require( 'inc/class-src-videos.php' );

require( 'inc/functions.php' );

require( 'tools/user-processing.php' );

new SRC_Admin;
new SRC_Gallery;
new SRC_Cron;
new SRC_Tracks;
new SRC_Seasons;
new SRC_Events;
new SRC_Register;
new SRC_Members;
new SRC_Settings;
new SRC_Cars;
new SRC_Schedule;
new SRC_Teams;
new SRC_Messages;
new SRC_Videos;
