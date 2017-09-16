<?php

require( 'tools/user-processing.php' );
require( 'tools/emails.php' );
require( 'tools/pull-names-from-csv.php' );
require( 'tools/convert-json.php' );
require( 'tools/girls.php' );


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

require( 'inc/functions.php' );

new SRC_Admin;
new SRC_Gallery;
new SRC_Cron();
new SRC_Tracks();
new SRC_Seasons();
new SRC_Events();
new SRC_Register();
new SRC_Members();
new SRC_Settings();
new SRC_Cars();
new SRC_Schedule();
