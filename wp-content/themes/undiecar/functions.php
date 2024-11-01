<?php
/*
if ( is_super_admin() ) {
	require( 'tools/get-names-from-json.php' );
	require( 'tools/modify-results.php' );
	require( 'tools/emails.php' );
	require( 'tools/pull-names-from-csv.php' );
	require( 'tools/convert-json.php' );
	require( 'tools/create-user.php' );
	require( 'tools/TEST-auto-news.php' );
	require( 'tools/get-standings.php' );
	require( 'tools/check-if-contacted.php' );
	require( 'tools/auto-unsubscribe-from-frequent-messages.php' );
	require( 'tools/new-pms.php' );
}
*/

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
require( 'inc/class-src-messages.php' );
require( 'inc/class-src-videos.php' );
require( 'inc/class-src-ai.php' );
require( 'inc/class-src-protest.php' );
require( 'inc/class-src-post-cards.php' );

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
new SRC_Messages;
new SRC_Videos;
new SRC_AI();
new SRC_Protest();
new SRC_Post_Cards();
