=== Sorbet ===
Contributors: automattic
Donate link:
Tags: clean, design, teal, blue, gray, green, orange, pink, red, light, one-column, two-columns, right-sidebar, fixed-layout, responsive-layout, custom-background, custom-colors, custom-header, custom-menu, editor-style, featured-images, flexible-header, full-width-template, infinite-scroll, post-formats, rtl-language-support, sticky-post, translation-ready, blog, gaming, journal, lifestream, photoblogging, school, tumblelog, artistic, bright, colorful, modern, playful, vibrant
Tested up to: 3.8
Stable tag: 3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

Sorbet is a delicious treat for your blog or website. Colorful post formats help your content pop, while secondary information (navigation, search, social links, and widgets) is tucked neatly away in the header for easy access that doesn't crowd your content.

== Installation ==

1. In your admin panel, go to Appearance -> Themes and click the Add New button.
2. Click Upload and Choose File, then select the theme's .zip file. Click Install Now.
3. Click Activate to use your new theme right away.

== Frequently Asked Questions ==

= Where can I add widgets? =

Sorbet includes four optional widget areas: a Sidebar alongside your content, and three Header Columns located behind a gear icon in the header.

If you haven’t added widgets to the Sidebar, Sorbet becomes a sleek one-column theme, making its tumblelog-style design perfect for cataloguing your web finds.

Configure these areas by going to Appearance → Widgets in your Dashboard.

= Where is my custom menu? =

Sorbet's primary menu is tucked behind the menu icon (three horizontal lines) in the header. Click the open menu icon to see it.

= Does Sorbet use featured images? =

If a Featured Image at least 700px wide is set for a post, it will display above the post on the blog index and archives. Featured images will also appear on single post view for Image formatted posts.

= Does Sorbet have social links? =

You can add links to a multitude of social services to a toggle menu (the heart icon) by following these steps:

1. Create a new Custom Menu, and assign it to the Social Links Menu location
2. Add links to each of your social services using the Links panel
3. Icons for your social links will automatically appear under the heart icon in the header

= Quick Specs (all measurements in pixels) =

1. The main column width is 646.
2. The Primary Sidebar is 250.
3. The Header Column widths are 300 each.

== Changelog ==

= 8 March 2016 =
* CSS header cleanup for preparing .org submission.

= 8 February 2016 =
* Changing theme author to Automattic - to ensure that all our in-house themes have the same author.

= 6 November 2015 =
* Add support for missing Genericons and update to 3.4.1.

= 20 August 2015 =
* Add text domain and/or remove domain path. (O-S)

= 31 July 2015 =
* Remove .`screen-reader-text:hover` and `.screen-reader-text:active` style rules.

= 15 July 2015 =
* Always use https when loading Google Fonts.

= 6 May 2015 =
* Fully remove example.html from Genericons folders.
* Remove index.html file from Genericions.

= 17 December 2014 =
* Ensure submenus are accessible to touch devices.

= 27 November 2014 =
* Add support for upcoming Eventbrite services.

= 11 November 2014 =
* fix broken translation string in pt-br

= 5 August 2014 =
* Update readme in preparation for WP.org resubmission
* Update version number to match WP.org version
* Adjust font sizes on icons to better align to the grid values; update theme author to WordPress.com
* Trigger .resize on header sidebar area so widgets like the gallery widget render at the proper size

= 24 July 2014 =
* change theme/author URIs and footer links to `wordpress.com/themes`.

= 3 July 2014 =
* remove unused `outdoorsy` tag, add `outdoors` tag

= 30 June 2014 =
* Decrease .post-format z-index. Fix issue with submenus vs post format icon.

= 1 June 2014 =
* add/update pot files.

= 30 May 2014 =
* update footer credit.

= 16 May 2014 =
* Remove unnecessary hack.

= 15 May 2014 =
* Update font size for infinite footer credits; update readme.txt.

= 8 May 2014 =
* Add left position to screen-reader-text on the toggles to remove horizontal scroll on mobile/tablet.

= 14 April 2014 =
* Update Responsive video support

= 28 February 2014 =
* Use `px` unit for pseudo elements because IEs have issue with relative unit. Fix #2257

= 27 February 2014 =
* Remove remaining references to the genericons as a follow-up to r16710. Fixes #2249 (again).

= 23 February 2014 =
* Add support for RSS feed social link in the Social Links menu. Props @macmanx

= 21 February 2014 =
* Add margin to the responsive video container to keep it from sticking to the content below.

= 20 February 2014 =
* remove false references to Genericons font files from style-wpcom.css.

= 17 February 2014 =
* Update Genericons and enqueue its own stylesheet.

= 5 February 2014 =
* added default icon to social links.

= 3 February 2014 =
* Fix paging bug for RTL styles
* Fix navigation bug when Jetpack is not active; minor adjustment to comment author spacing

= 30 January 2014 =
* Update readme.txt, add POT file
* Move class wrapper to social links nav menu arguments
* Remove support for Jetpack social links
* Use a custom menu for the Social Links rather than Jetpack; more flexible, easier to configure.
* Add support for the audio post format

= 29 January 2014 =
* more updates to tags
* Update tags in style.css
* Add description to style.css

= 23 January 2014 =
* Add RTL styles
* Adjust margins on site navigation
* Split header widget area into three columns to avoid widget float weirdness
* Ensure search button text does not get cut off on small screens/devices
* Add transition effect to page links; adjust page links bottom margins; fix comment navigation so it doesn't stack
* Fix author archives header content on small screens, adding padding to the wrapper
* Fix bug in social links where a blank icon would appear in Firefox
* Style page links
* Add title elements to toggle links in the header so it's clearer what they are for; remove Pages prefix from page links, will style those
* Darken navigation links a bit
* Add infinite_scroll_has_footer_widgets function; remove line numbers from CSS files; adjust suggested height of custom header

= 22 January 2014 =
* Update custom header admin styling to match the front end
* Fix references to outdated font name, changing Alegreya Sans to Source Sans Pro
* Add a footer custom menu; remove unnecessary styles; add screenshot; tweak editor style support

= 21 January 2014 =
* Fix comment navigation; apply correct $content_width
* Convert media queries from px to em for better zooming
* Ensure post/page title is only displayed if it exists; update colors; add full-width page template; minor style tweaks
* Update editor style, add WordPress.com specific styles, update $themecolors
* Remove dummy social links from header; add support for Jetpack social links
* Remove unnecessary JS files
* Initial commit
