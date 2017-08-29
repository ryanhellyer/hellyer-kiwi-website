<?php

if ( ! isset( $_GET['format_email_content'] ) ) {
	return;
}



/*----- to Indycar drivers -------
SUBJECT: Undycar Series (undycar.com) invitation
*/

$message = "Hi  \" + shortened_name + \",
I hope you don't mind the impromptu message, but I saw you sometimes do races in the Indycar series here on iRacing and thought you may be interested in the new Undycar Series which is starting in just over a weeks time. The races are on Tuesdays at 21:00 GMT (see below for other time zones), starting in a week and a half on September 5th. 

[b][size=24][url]https://undycar.com/[/url][/size][/b]

The tracks and car are all free, so no need to buy anything. The car is the free Dallara Indycar (as used in the Dallara Dash series) with a fixed setup. Half the tracks are ovals, and half are road courses.

It would be awesome to see you on the track in the Undycar Series :)


Times in other time zones:
07:00 AEST (Sydney)
14:00 PDT (California)
17:00 EDT (New York)
21:00 GMT
23:00 CEST (Berlin)";





// ---------- To Dallara Dash drivers -------------
$message = "Hi  \" + shortened_name + \",
I hope you don't mind the impromptu message, but I saw you sometimes do races in the Dallara Dash series here on iRacing and thought you may be interested in the new Undycar Series which is starting in just over a weeks time. The races are on Tuesdays at 21:00 GMT (see below for other time zones), starting in a week and a half on September 5th. 

[b][size=24][url]https://undycar.com/[/url][/size][/b]

The tracks and car are all free, so no need to buy anything. The car is the free Dallara Indycar (as used in the Dallara Dash series) with a fixed setup. Half the tracks are ovals, and half are road courses.

It would be awesome to see you on the track in the Undycar Series :)


Times in other time zones:
07:00 AEST (Sydney)
14:00 PDT (California)
17:00 EDT (New York)
21:00 GMT
23:00 CEST (Berlin)";



// ---------- To Formula Renault drivers -------------
$message = "Hi  \" + shortened_name + \",
I hope you don't mind the impromptu message, but I saw you sometimes do races in the Formula Renault series.
I also drive those sometimes and really enjoy them, but I also want to drive faster more powerful cars so I\'ve created a new league called the Undycar Series and thought you may be interested in it too. It\'s starting in just over a weeks time. The races are on Tuesdays at 21:00 GMT (see below for other time zones), starting in a week and a half on September 5th. 

[b][size=24][url]https://undycar.com/[/url][/size][/b]

The tracks and car are all free, so no need to buy anything. The car is the free Dallara Indycar with a fixed setup. Half the tracks are ovals, and half are road courses.

It would be awesome to see you on the track in the Undycar Series :)


Times in other time zones:
07:00 AEST (Sydney)
14:00 PDT (California)
17:00 EDT (New York)
21:00 GMT
23:00 CEST (Berlin)";




$message = "Hi \" + shortened_name + \",
You should have received an invite to the Undycar Series league on iRacing now. The number of appicants for the league unexpectedly exploded on me today, so I've stopped looking for drivers now.

I'm running a test race at the Daytona road circuit in 80 minutes time to make sure I actually know how the hosted race sessions on iRacing work :P Feel free to join. Also feel free to use it as a practice session. If you crash out, just hop back in with the lead pack if you like (ignore the blue flags).
Test race schedule - session starts Saturday 20:00 GMT
Practise: 20:00 GMT
Qualifying: 20:30 GMT (open 10 min session)
[b]Race: 20:40 GMT (20 min race)[/b]
I'll launch a second race as soon as the first one has finished.

If you would like to chit-chat more easily during races, please join the Undycar Series Discord:
https://discord.gg/csjKs6z


The login/registration system is somewhat broken on the website. I didn't realise this until today, but I'll get it fixed some time later in the week. There's no urgency to login anyway though, as it's only useful for editing the text and images on your profile page.";



$message = str_replace( "\n", '\n', $message );
echo $message;
die;


/*
----- to Sim Racers TM on Facebook #1 ----

What are the best rules to have for a sim racing league?

I'm trying to write up the rules for the new Undycar Series starting soon and am keen on some input:
https://undycar.com/rules/

I'd prefer to keep things super simple, as this is intended just as a fun league, so delving into tons of detail and nuances that competitors are never going to read or care about is something I'd like to avoid. But if you think that's the wrong approach please let me know :)






----- to friends -------
SUBJECT: Undycar Series (undycar.com) invitation

MESSAGE:
Hi [NAME],
Are you interested in competing in the Undycar Series? The races are on Tuesdays at 21:00 GMT (see below for other time zones), starting in a week and a half on September 5th. 

[b][size=24][url]https://undycar.com/[/url][/size][/b]

The tracks and car are all free, so no need to buy anything. The car is the free Dallara Indycar (as used in the Dallara Dash series) with a fixed setup. Half the tracks are ovals, and half are road courses.

It would be awesome to see on the track in the Undycar Series :)


Times in other time zones:
07:00 AEST (Sydney)
14:00 PDT (California)
17:00 EDT (New York)
21:00 GMT
23:00 CEST (Berlin)




------- to SRC ----------
SUBJECT: Undycar Series (undycar.com) invitation

MESSAGE:
Hi,
If you want to try out iRacing without costing you a fortune, I'm making a new series called the "Undycar Series" for it which only uses their free cars and tracks (still need the US$5/month membership fee, but no need to buy cars or tracks).

The races are on Tuesdays at 23:00 German time, starting in a week and a half on September 5th. 

There's a website for it here:
https://undycar.com/

The car is a Dallara Indycar and will use a fixed setup. They're super fun to drive and no more difficult than the cars in SRC. Half the races on road courses and half on ovals (just like real Indycars).

Assuming the league is successful, we can use it to try to recruit new members for SRC too as I can pimp it via the Undycar Series website.

*/