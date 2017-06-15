<?php




if (       'dev-hellyer.kiwi' !== $_SERVER['HTTP_HOST']      && ! is_user_logged_in() && ! is_admin() && $GLOBALS['pagenow'] != 'wp-login.php' ) {

	echo '
	<style>
	body {background:#000;}
	img {display:block;margin:50px auto 0 auto;width:160px;height:auto;}
	p {font-family:sans-serif;color:#fff;font-size:32px;text-align:center;}
	#the-form {
		width:350px;margin:0 auto;text-align:left;
		margin-top:100px;
	}
	input {font-size:24px;display: block;width:100%;}
	#the-form p {
		text-align:left;
		font-size:24px;
	}
	</style>
	<img src="https://seacrestracing.club/wp-content/themes/src/images/logo.png" />
	<p>Website coming soon :)</p>

	<div id="the-form">
	';
	wp_login_form();
	echo '</div>';
	die;
}

function sample_admin_notice__success() {

	if ( ! is_super_admin() ) {
		return;
	}

	$message = '
<textarea style="width:49%;">

NEW FEATURE:
User meta:
	Steam link
	Years of experience
	Achievements in sim racings
	Leagues they compete in

Text:
	* Drivers wanted *
	We are accepting new driver registrations!
	[link]Sign up here[/link]

Tag posts with driver, so that those articles show up at bottom of their members page. Use same taxonomy for galleries post type.
OR JUST DO SEARCH QUERY ON DRIVERS NAME AND NICKNAME, if insufficient posts with thumbnails, then could display avatar or their members page car picture instead.

Maybe add galleries as forum post-type, but just style differently. Get bad URLs then though :/

Gertrudes Smith should be the site administrator
Once an event is completed (determined by the last race being won), she should automatically send emails out to drivers asking them to answer the questions on a page.
The page would include a form, asking random questions (perhaps select three from list of 20). Only drivers listed in the results should be contacted. Do an AM driver and PRO.
At start of season, Gertude would send a link to the "driver of the day" award which needs answered shortly after the race ends.
	Only drivers who drove in the event can choose driver of the event.
The day after each event, gallery images for the event, answers to the questions and driver of the event would be turned into a forum post.
Other things she could mention are:
	fastest lap of the race
	most laps led
	best crash
	who was on pole, who finished 1st, second, third in each race, and the round finishing order too.
	admins could have the ability to add random quotes
	biggest position gain in race


</textarea>

<textarea style="width:49%;">
INFORMATION ABOUT THE WEBSITE FOR DRIVERS

We do not have enough people writing content to have a dedicated news section, and none of us wanting to be spending all our time writing content, so I have made the system autopost some content, and we can easily turn the few interesting forum posts we have into news items which appear on the home page. This gives the appearance of a busy site, even though it is not ;)

Making news items:
If you think something should be a proper news item, just let an admin know (see about page for list of admins)

Driver profiles:
Your forum profile is the page linked to from the results section etc. To update it, simply update your forum profile. We reserve the right to edit your forum profile at any time for the benefit of the league (ie: to make the website look more interesting). You can change it to something dumb, but that will just piss us off, so please do not do that ;)

Private data:
Your profile includes some "real" information. If you do not want to reveal some private information (your name, birthdate, whatever), just put fake data in there. Please keep it believable though and do not put anything stupid in there please :P

Avatars:
We require everyone to add an avatar. If you do not pick one yourself, we will just give you one, and it may not be pretty ;) So for your own sake, please upload a picture of yourself (or something representing you at least).
</textarea>
	';

	?>
	<div class="notice notice-success is-dismissible">
		<p><?php echo $message; ?></p>
	</div>
	<?php
}
add_action( 'admin_notices', 'sample_admin_notice__success' );

require( 'inc/class-src-core.php' );
require( 'inc/class-src-bbpress.php' );
require( 'inc/class-src-bbcode.php' );
require( 'inc/class-src-seasons.php' );
require( 'inc/class-src-results.php' );
require( 'inc/class-src-members.php' );
require( 'inc/class-src-admin.php' );
require( 'inc/class-src-setup.php' );
require( 'inc/class-src-registration.php' );
require( 'inc/class-src-gallery.php' );
require( 'inc/class-src-available-cars.php' );

require( 'inc/functions.php' );

new SRC_Admin;
new SRC_BBCode;
new SRC_Results;
new SRC_Members;
new SRC_Seasons;
new SRC_Registration;
new SRC_Gallery;
new SRC_Available_Cars;
