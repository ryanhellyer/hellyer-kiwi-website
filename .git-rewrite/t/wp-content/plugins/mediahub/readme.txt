=== ForSite Media MediaHub ===
Contributors: forsitemedia, daankortenbach, DeFries, tibor, ryanhellyer
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4UVLDL3LEG2QG
Tags: mediahub
Requires at least: 4.2
Stable tag: 1.2

Automatically distribute posts from the MediaHub to your WordPress sites.

== Description ==

Automatically distribute posts from the MediaHub to your WordPress sites.

The MediaHub is a platform to collect, enrich and distribute digital content. More than 120 local communities in the Netherlands are using the MediaHub to share content and to automatically fill their communication channels (such as websites, narrow casting, television, newsletters and social media).

For more information, see www.doelgroep.tv.

== Installation ==

Installation of this plugin works like any other plugin out there. Either:

1. Upload the zip file to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

Or search for the plugin via your plugins menu.

= The following shortcodes are available for use =
<strong>Newsletters:</strong> `[mediahub_newsletter]`. Arguments available include `email`, `preposition`, `firstname`, `prefix_lastname` and `lastname`.
Example: `[mediahub_newsletter email=test@test.com preposition=Mr firstname=Justin prefix_lastname=van lastname=Bieber]`

<strong>Agenda</strong> `[mediahub_agenda]`. Arguments include `category`, `tags`, `days`, `before_date=20:4:2015`, `after_date=10:4:2015`, `posts_per_page`.
Example: `[mediahub_agenda category=nieuws tags=sometag days=2 before_date=20:4:2015 after_date=10:4:2015 posts_per_page=3]`



== Frequently Asked Questions ==

= Can I make a suggestion =

You most certainly can. Contact me via [Twitter](http://twitter.com/ForSite "Our Twitter Account")


== Changelog ==

= 1.2 =

Upgrade to support version 4 of the MediaHub API
Added multilingual support

= 1.1.3 =

Fixed debug error notices

= 1.1.2 =

Removed admin notification

= 1.1.1 =

Bug fix for attached media files that were not imported when using cron

= 1.1.0 =

Removed update blocker

= 1.0.3 =

* Updated readme file

= 1.0.2 =

* Bugfix plus updated readme file

= 1.0.1 =

* Added Readme

= 1.0.0 =

* First release. Upgrade from nothingness.

== Upgrade Notice ==

= 1.0.0 =

Upgrade from nothingness just to be one of the cool kids.


== Other Notes ==

You can find us here:

* [ForSite Media](http://www.forsitemedia.nl/ "ForSite Media")