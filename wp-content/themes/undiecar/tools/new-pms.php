<?php

/********************************
 * 
 * to change ...
 ****** use $drivers_to_rarely_notify
 * 
 ********************************/

/*
<textarea id="Form_To" name="To" class="MultiComplete" data-users="[{&quot;id&quot;:971,&quot;name&quot;:&quot;Ryan Hellyer&quot;}]" rows="6" cols="100">971</textarea>
*/

if ( ! isset( $_GET['new_pms'] ) ) {
	return;
}

if ( '' !== $_GET['new_pms'] ) {
	$until_driver_id = absint( $_GET['new_pms'] );
}


$transient_key = 'QctHnMRwk5qHJWhc';

// No season set, so lets just dump them all out.
$all_drivers       = get_users( array( 'number' => 2000, 'orderby' => 'ID' ) );
$drivers_to_notify = array();
foreach ( $all_drivers as $driver ) {
	$driver_name = $driver->data->display_name;
	$driver_id   = $driver->ID;

	// Keep skipping until we reach the next driver ID in line to be processed.
	if ( isset( $until_driver_id ) ) {
		if ( $driver_id !== $until_driver_id ) {
			//echo $driver_id . "\n";
			continue;
		} else {
			unset( $until_driver_id );
			continue;
		}
	}

	if ( 'no' !== get_user_meta( $driver_id, 'receive_notifications', true ) ) {

		$drivers_to_rarely_notify[] = $driver_name;
		if ( 'yes' !== get_user_meta( $driver_id, 'receive_less_notifications', true ) ) {
			$drivers_to_notify[] = $driver_name;
		}
	}

}

$go = false;
//$drivers_to_notify = $drivers_to_rarely_notify;
//$drivers_to_notify = array( 'Ryan Hellyer' );
//print_r( $drivers_to_notify );
//die;

//echo 'count: ' . count( $drivers_to_notify );
//die;
foreach ( $drivers_to_notify as $key => $driver_name ) {
	$driver_id = undiecar_get_user_id_by_display_name( $driver_name );
	$actual_driver_name = $driver_name;
	$driver_name = str_replace( ' ', '+', $driver_name );
/*
	if ( 'Christopher+Kehrer' === $driver_name ) {
		$go = true;
	}
	if ( false === $go ) {
		echo $driver_name . "\n";
		continue;
	}
*/
	// Get forum user ID.
	$command = "
  curl 'https://forums.iracing.com/user/tagsearch?q=" . $driver_name . "' \
	  -H 'authority: forums.iracing.com' \
	  -H 'pragma: no-cache' \
	  -H 'cache-control: no-cache' \
	  -H 'sec-ch-ua: \" Not A;Brand\";v=\"99\", \"Chromium\";v=\"96\", \"Google Chrome\";v=\"96\"' \
	  -H 'accept: application/json, text/javascript, */*; q=0.01' \
	  -H 'x-requested-with: XMLHttpRequest' \
	  -H 'sec-ch-ua-mobile: ?0' \
	  -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36' \
	  -H 'sec-ch-ua-platform: \"Linux\"' \
	  -H 'sec-fetch-site: same-origin' \
	  -H 'sec-fetch-mode: cors' \
	  -H 'sec-fetch-dest: empty' \
	  -H 'referer: https://forums.iracing.com/messages/add' \
	  -H 'accept-language: en-GB,en;q=0.9,en-US;q=0.8,en-NZ;q=0.7,pt;q=0.6' \
	  -H 'cookie: vf_iracing_P3982-sid=NE140BHA7RL6; vf_iracing_P3982-tk=QctHnMRwk5qHJWhc%3A971%3A1623092713%3A959f8842e27c686bf0d88ad061ce3305; theme=light; vf_iracing_P3982=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDQwMDA3MDIsImlhdCI6MTY0MTQwODcwMiwic3ViIjo5NzF9.rYK8ZzUVtmGNVaGm1_yDubhuVx7eNLdMqNpTTANtOD0; vfo_s=144221726; __cfruid=020b2d0ea0badc62070f5389047b1ae09666b143-1642279886; irsso_membersv2=DD6CF9CB0A93ADE7593D44F6C7201C8C88AEB14EA5D01714D91C575E86B2B5A565E820356A47CDA42BF39D2FBE835B1AB2D79B947DCECF544D4ACB1F909665F56E97D92C0B9762173B464EEA1CE5A938E7DA6E12B12D4C60FD6E058A12C30BB27C0B6BD9B1F3742674410DE5650E078297FE4E788FEC7752EF5073251E15E6E5' \
	  --compressed";

	$json = shell_exec( $command );
	$data = json_decode( $json, true );

	if ( ! isset( $data[0] ) ) {
		echo 'Fail: ' . $driver_name . '<br />';
//		ryans_log( 'Fail: ' . $driver_name );
		$timer = 0;
	} else {
		echo 'Success: ' . $driver_name . ' - ' . $driver_id . '<br />';
//		ryans_log( 'Success: ' . $driver_name . ' - ' . $driver_id );
		$timer = 35;

		$forum_user_id = $data[0]['id'];

		// Form message.
		$message = <<<EOT
	Hi [NAME],
	Our annual 1.2 hours of Butthurt is on this Tuesday! This year we will be racing the GT3s (McLaren, Ford GT, Mercedes, BMW M4, Lambo, Porsche and Ferrari Evo) and the Global MX-5 cup cars at Mount Panorama.

	Event notice: [url]https://undiecar.com/event/1-2-hours-of-butthurt/[/url]
	Practice: 19:00 GMT
	Qualifying: 20:30 GMT (10 mins open)
	Race: 20:40 GMT (~1.2 hours / 70 mins)

	You will find the race listed on the league sessions page [url]https://members.iracing.com/membersite/member/LeagueSessions.do[/url].

	There will be some polite AI cruising around the back of the grid. They're registered in Trading Paints, but AI rosters there tend to be a bit buggy, so if you want ensure they definitely work, then copy the following AI roster to your iRacing install manually ... [url]https://undiecar.com/files/undiecar-12-hours-of-butthurt.zip[/url]

	[b]Live streams and member websites[/b]
	Thomas Lademann (German): [url]https://www.twitch.tv/asphaltschneider/[/url] (his post-race crash analysis is particularly interesting)
	Steven Brumfield (German): [url]https://www.twitch.tv/stevenbrumfield[/url]
	If you would like your stream included here, please let us know :)

	Thomas also has a merch store in case you want to promote your schnitzelness ... [url]https://streamlabs.com/asphaltschneider/merch[/url]

	[b]Discord[/b]
	If you have any trouble finding the event in iRacing or have any questions, just ask in the Undiecar Discord channel. There’s usually someone around on Discord to help out. If you like to jibber jabber often mid-race, then you should join our Discord voice chat during races. There's usually a small group of us chit chatting all race long.
	[url]https://spamannihilator.com/check/undiecar/[/url]

	[b]Donations[/b]
	If you would like to help out with the league, please visit our donations page :)
	[url]https://undiecar.com/donate/[/url]


	[img]https://undiecar.com/message/reminder-1-2-hours-of-butthurt/?driver=[FULL_NAME][/img]
	--------
	[size=10]Visit here to unsubscribe from these messages ... [url][UNSUBSCRIBE][/url]
	Visit here to receive less of these messages ...  [url][UNSUBSCRIBE_PARTIALLY][/url]
	[/size]



	Note: iRacing has done the bizarre thing of temporarily having two forums and therefore two private messaging systems. I have no way to know which to use to contact you, but if you would prefer me to stick with one, just let us know which you would prefer we will make sure to only message you on one of them.
	EOT;

		add_shortcode(
			'img',
			function ( $atts ) {
				return '';
			}
		);
		// Swap out name.
/*
*/
		$first_name = explode( '+', $driver_name )[0];
		$message    = str_replace( '[NAME]', $first_name, $message );

		$url     = 'https://undiecar.com/member/' . sanitize_title( $actual_driver_name ) . '/?' . base64_encode( sanitize_title( $actual_driver_name ) . '|receive_notifications' );
		$message = str_replace( '[UNSUBSCRIBE]', $url, $message );

		$url     = 'https://undiecar.com/member/' . sanitize_title( $actual_driver_name ) . '/?' . base64_encode( sanitize_title( $actual_driver_name ) . '|receive_less_notifications' );
		$message = str_replace( '[UNSUBSCRIBE_PARTIALLY]', $url, $message );
//echo $message;die;

		$message = str_replace( '[url]', '', $message );
		$message = str_replace( '[/url]', '', $message );
		$message = str_replace( '[b]', '-- ', $message );
		$message = str_replace( '[/b]', ' --', $message );
		$message = str_replace( '[size=10]', '', $message );
		$message = str_replace( '[/size]', '', $message );

		$message = do_shortcode( $message );

		$message = str_replace( "\n", '\n', $message );
		$message = urlencode( $message );

/*
echo "\n\n";
$message = urldecode( $message );
$message = str_replace( '\n', "\n", $message );
echo $message;die;
*/

		// Send message.
		$command = <<<EOT
		curl 'https://forums.iracing.com/messages/add' \
		  -H 'authority: forums.iracing.com' \
		  -H 'pragma: no-cache' \
		  -H 'cache-control: no-cache' \
		  -H 'sec-ch-ua: " Not;A Brand";v="99", "Google Chrome";v="97", "Chromium";v="97"' \
		  -H 'sec-ch-ua-mobile: ?0' \
		  -H 'sec-ch-ua-platform: "Linux"' \
		  -H 'upgrade-insecure-requests: 1' \
		  -H 'origin: https://forums.iracing.com' \
		  -H 'content-type: application/x-www-form-urlencoded' \
		  -H 'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Safari/537.36' \
		  -H 'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9' \
		  -H 'sec-fetch-site: same-origin' \
		  -H 'sec-fetch-mode: navigate' \
		  -H 'sec-fetch-user: ?1' \
		  -H 'sec-fetch-dest: document' \
		  -H 'referer: https://forums.iracing.com/messages/add' \
		  -H 'accept-language: en-GB,en;q=0.9,en-US;q=0.8,en-NZ;q=0.7,pt;q=0.6' \
		  -H 'cookie: vf_iracing_P3982-sid=NE140BHA7RL6; vf_iracing_P3982-tk=QctHnMRwk5qHJWhc%3A971%3A1623092713%3A959f8842e27c686bf0d88ad061ce3305; theme=light; vf_iracing_P3982=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2NDQwMDA3MDIsImlhdCI6MTY0MTQwODcwMiwic3ViIjo5NzF9.rYK8ZzUVtmGNVaGm1_yDubhuVx7eNLdMqNpTTANtOD0; vfo_s=144221726; __cfruid=020b2d0ea0badc62070f5389047b1ae09666b143-1642279886; irsso_membersv2=DD6CF9CB0A93ADE7593D44F6C7201C8C88AEB14EA5D01714D91C575E86B2B5A565E820356A47CDA42BF39D2FBE835B1AB2D79B947DCECF544D4ACB1F909665F56E97D92C0B9762173B464EEA1CE5A938E7DA6E12B12D4C60FD6E058A12C30BB27C0B6BD9B1F3742674410DE5650E078297FE4E788FEC7752EF5073251E15E6E5' \
		  --data-raw 'TransientKey=[TRANSIENT_KEY]&hpt=&To=[FORUM_USER_ID]&Format=Rich&Body=%5B%7B%22insert%22%3A%22[MESSAGE]%5Cn%22%7D%5D&Start+Conversation=Post+Message' \
		  --compressed
		EOT;

		$command = str_replace( '[FORUM_USER_ID]', $forum_user_id, $command );
		$command = str_replace( '[TRANSIENT_KEY]', $transient_key, $command );
		$command = str_replace( '[MESSAGE]', $message, $command );
//if ( 971 !== $forum_user_id ) {
//	echo $command;
//	die;
//}
		// Get the ID for this driver.
		foreach ( $all_drivers as $driver ) {
			if ( $driver_name === str_replace( ' ', '+', $driver->data->display_name ) ) {
				$driver_id = $driver->ID;
			}
		}

		$json = shell_exec( $command );

		echo '<br />Ryan, add a check here to confirm that the command worked. It should not report that it was spam and it should not be blank';
		// text to look for when spam "A spam block is now in effect on your account"
		/* text to look for when success (with the carriage returns) "New Message

You"
		*/

//		$json ='JSON TEST';
		echo '<br />';
		echo '<textarea>' . print_r( strip_tags( $json ), true ) . '</textarea>';
		echo '<br />';

//		ryans_log( print_r( $json, true ), 2 );
//die("\n\n\n".'completed');
	}

	$url = home_url() . '/?new_pms=' . $driver_id;
	echo '<meta http-equiv="refresh" content="' . esc_attr( $timer ) . ';url=' . $url . '" />';
	echo '<br />';
	echo 'next: ' . $url;
	die();
}

function undiecar_get_user_id_by_display_name( $display_name ) {
	global $wpdb;

	if ( ! $user = $wpdb->get_row( $wpdb->prepare(
		"SELECT `ID` FROM $wpdb->users WHERE `display_name` = %s", $display_name
	) ) ) {
		return false;
	}

	return $user->ID;
}

die;
