<?php
/**
 * Plugin Name: Hello Samuel L Jackson
 * Description: Inserts random movie quotes by Samuel L. Jackson into your Admin header.
 * Version: 1.0
 * Author: John Regan
 * Author URI: http://johnregan3.me
 * License: GPLv2+
 */

/**
 * Copyright (c) 2014 John Regan (http://johnregan3.me/)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 *
 *
 * Mad props to Matt Mullenweg for the original code
 * and Chris Olbekson (c3mdigital) for the encouragement to
 * puruse such a trivial pursuit.
 *
 * If you enjoy this plugin, check out my Samuel L. Jackson Dummy Content Generator:
 * http://johnregan3.github.io/slj-dummy-content/
 */

function hello_slj_get_quote() {

	$quotes = array(
	"I'm serious as a heart attack",
	"Is she dead, yes or no?",
	"Hold on to your butts!",
	"No, motherfucker!",
	"Are you ready for the truth?",
	"I gotta piss.",
	"No man, I don't eat pork.",
	"Mmmmm, this is a tasty burger!",
	"What kills me is that everybody thinks I like jazz.",
	"Want to know what the 'L.' in Samuel L. Jackson means? None of your fucking business.",
	"Enough is enough! I have had it with these motherfucking snakes on this motherfucking plane!",
	"Well, that's good news. Snakes on crack.",
	"Turn this big motherfucker left, Troy!",
	"Eddie Kim somehow managed to fill the plane with poisonous snakes.",
	"I'm about to open some fucking windows!",
	"I guess I should speak louder so you can hear me?",
	"The reason we're gathered here on our God-given, much-needed day of rest is that we have a Polish hostage.",
	"Let's try to get in the killing mode.",
	"Yeah. What the hell? Mount up.",
	"Flip a bitch!",
	"I need your A-game boys... and girl.",
	"Drop Fruit of the Loomski in the A-car.",
	"How can I trust a man who won't eat a good old-fashioned American hotdog?",
	"Hey! Get the hell off my damn property.",
	"Street. Don't beat him so badly I can't get a rematch, all right?",
	"To her dumb country ass, Compton is Hollywood.",
	"How old is that machine gun shit?",
	"I didn't hear you wash your hands.",
	"My ass may be dumb, but I ain't no dumbass.",
	"Oh, ya'll a couple Cheech and Chongs, huh?",
	"Girl, don't make me put my foot in your ass.",
	"Try not to tear his clothes off, OK? They're new.",
	"Last chance, motherfucker. You sure?",
	"You keep fuckin' with me, you're gonna be asleep forever.",
	"Come on man! If it wasn't for me, you wouldn't HAVE that motherfuckin' boat!",
	"Oh shit, that shit rhymes! 'Blew Beau-mont's, brains out!'",
	"Let me have a screwdriver homes.",
	"Access main program. Access main security. Access main program grid.",
	"God damn it! I hate this hacker crap!",
	"You got to cool that shit off. And that's the double-truth, Ruth.",
	"We're keepers of the peace, not soldiers.",
	"It's Giuliani time!",
	"It's my duty to please that booty!",
	"You wouldn't know Egyptian cotton if the Pharaoh himself sent it to you, you knockoff-wearing motherfucker!",
	"Do you think that makes me less dangerous, or more dangerous?",
	"I'm gonna fuck you up for making me run!",
	"What's up with the 'cornbread' talk, man?",
	"I know cats who'd take out whole zipcodes for that kind of cheese.",
	"Yes, they deserved to die and I hope they burn in hell!",
	"Where - is - my - super - suit?",
	"You tell me where my suit is, woman!",
	"The guy has me on a platter and he won't shut up!",
	"To tell you the truth, I'd rather go bowling.",
	"Waste the motherfuckers.",
	"I was not going to stand by and see another Marine die just to live by those fucking rules.",
	"Normally, both your asses would be dead as fucking fried chicken, but you happen to pull this shit while I'm in a transitional period so I don't wanna kill you, I wanna help you.",
	"Your bones don't break, mine do.",
	"Look, just because I don't be givin' no man a foot massage don't make it right for Marsellus to throw Antwone into a glass motherfuckin' house, fuckin' up the way the nigger talks.",
	"Today's temperature's gonna rise up over 100 degrees, so there's a Jheri curl alert!",
);

	// And then randomly choose a line
	return wptexturize( $quotes[ mt_rand( 0, count( $quotes ) - 1 ) ] );
}

// This just echoes the chosen line, we'll position it later
function hello_slj() {
	$chosen = hello_slj_get_quote();
	echo "<p id='slj'>$chosen</p>";
}

add_action( 'admin_notices', 'hello_slj' );

function slj_css() {
	$x = is_rtl() ? 'left' : 'right';

	echo "
	<style type='text/css'>
	#slj {
		float: $x;
		padding-$x: 15px;
		padding-top: 5px;
		margin: 0;
		font-size: 11px;
	}
	</style>
	";
}

add_action( 'admin_head', 'slj_css' );

?>
