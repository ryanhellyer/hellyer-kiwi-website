# SO Hide SEO Bloat

[![plugin version](https://img.shields.io/wordpress/plugin/v/so-clean-up-wp-seo.svg)](https://wordpress.org/plugins/so-clean-up-wp-seo)

###### Last updated on 2017.2.28
###### requires at least WordPress 4.7.2
###### tested up to WordPress 4.7.3
###### Author: [Piet Bos](https://github.com/senlin)

Free addon for the Yoast SEO plugin to hide the bloat it adds to your WordPress backend; now with Settings Page!

## Description

Almost anyone who uses the Yoast SEO plugin will agree that it is a great SEO plugin, but the developers are adding more and more unwanted things to the WordPress backend.

The purpose of the SO Hide SEO Bloat plugin, a free addon for the Yoast SEO plugin, is to clean up all those unwanted things.

Since v2.0.0 we have a Settings page! With v2.1.0 we have less options on the Settings page, due to the 3.1 version release of the Yoast SEO plugin. It seems that team Yoast has finally seen the light as they have "_temporarily disabled all non-vital notifications_". Their changelog says that they are trying to come up with a "more user-friendly way" of dealing with these nags, basically admitting that they have been harassing their user-base. 
Either way we welcome this development! Who knows? Perhaps in the future this plugin becomes completely redundant? ;) 

With the settings page is that you can fine-tune what is hidden or removed to your liking. By default most of the bloat that the Yoast SEO plugin generates is hidden or removed, just like before when there were no settings yet.

It is a good idea to have a look at the Settings page if only to see what you can fine-tune. The link to the page has been added to the Yoast SEO menu and of course there is also a link to it from the Plugins page.

The default settings of the current release are as follows:

* hide the cartoon-style sidebar ads on almost all settings pages of the Yoast SEO plugin
* hide about nag that shows on every update of the plugin
* hide warning in the advanced tab of Yoast SEO UI in edit Post/Page screen when your site is blocking access to robots
* hide image warning nag that shows in edit Post/Page screen when featured image is smaller than 200x200 pixels
* hide add keyword button that shows in edit Post/Page and only serves to show an ad for the premium version
* hide content/seoscore in publish/update box on edit Post/Page
* hide readability tab and content analysis metabox item
* hide the issue counter (added to v3.3 of Yoast SEO plugin)
* hide the SEO Score, Title and Meta description admin columns on the Posts/Pages screens; Focus keyword column can be hidden too
* hide the SEO Score admin column on taxonomies (added to v3.1 of Yoast SEO plugin)
* hide the ad for the premium version in the help center or hide the whole help center (added to v3.2 of Yoast SEO plugin)
* hide the email support of the help center as it is a premium-only feature and therefore an "ad in disguise" (added to v4.4 of Yoast SEO plugin)
* hide the red star behind the "Go Premium" submenu text (added to v3.6 of Yoast plugin and changed again with v3.7)
* hide the Upsell Notice &amp; Notification box that show in the Yoast SEO Dashboard
* remove the Yoast SEO widget from the WordPress Dashboard

If you like the SO Hide SEO Bloat plugin, please consider leaving a [review](https://wordpress.org/support/view/plugin-reviews/so-clean-up-wp-seo?rate=5#postform). You can also help a great deal by [translating the plugin](https://translate.wordpress.org/projects/wp-plugins/so-clean-up-wp-seo) into your own language.
Alternatively you are welcome to make a [donation](https://so-wp.com/plugins/donations/). Thanks!

## Frequently Asked Questions

### Where is the settings page?

The link to the page has been added to the Yoast SEO menu and of course there is also a link to it from the Plugins page.

### I have updated the plugin and a new setting has been added, but I still can see that particular item.

That indeed can happen when we add a new setting. The plugin's settings then need to be re-saved. So all you need to do is go to the Settings page of the SO Hide SEO Bloat plugin and save them. Then all should be good.

### Can I use SO Hide SEO Bloat on Multisite?

Yes, you can.
For version 2.4.0 [Andy Fragen](https://github.com/afragen) has refactored that part of the plugin to make it fully Multisite compatible. The Settings screen only shows in Network Admin as we don't think it makes sense that individual sites override the Network Settings.

### The name of the plugin is confusing, it hides bloat of which SEO plugin?

Yes, you are right, the name is a bit vague (see Changelog v1.8.0). On the other hand there is only one SEO plugin that adds a lot of bloat to the WordPress Dashboard and that is the Yoast SEO plugin. 

### The plugin doesn't do anything!

Do you have the Yoast SEO plugin installed? It hides the bloat from that plugin only. 
If you have and the plugin still doesn't do anything, then please open a [support ticket](https://github.com/senlin/so-clean-up-wp-seo/issues).

### With a settings page comes additional entries in the database; what happens on uninstall?

Great question!
Indeed the SO Hide SEO Bloat plugin writes its settings to the database. The included `uninstall.php` file removes all the plugin-related entries from the database once you remove the plugin via the WordPress Plugins page (not on deactivation).

### I have an issue with this plugin, where can I get support?

Please open an issue here on [Github](https://github.com/senlin/so-clean-up-wp-seo/issues)

## Contributions

We welcome your contributions very much! PR's will be considered and of course bug reports and feature requests can also be seen as contributions!
**If you're interested in becoming involved, please [let us know](https://so-wp.com/info-contact/) or simply send a PR with your proposed improvement.** 

## License

* License: GNU Version 3 or Any Later Version
* License URI: http://www.gnu.org/licenses/gpl-3.0.html

## Donations

* Donate link: http://so-wp.com/plugins/donations

## Connect with us through

[Website](https://bohanintl.com)

[Website](https://so-wp.com)

[Github](https://github.com/senlin) 

[LinkedIn](https://www.linkedin.com/in/pietbos) 

[WordPress](https://profiles.wordpress.org/senlin/) 


## Changelog


### 2.5.5 

* release date 2017.2.28 (triggered by release of Yoast SEO 4.4)
* hide the email support of the help center as it is a premium-only feature and therefore an "ad in disguise"

### 2.5.4 

* release date 2016.12.22 (triggered by release of Yoast SEO 4.0)
* fix: change robots nag hiding via settings instead of globally
* improvement: content analysis - hide readability tab
* improvement: upsell notice: hide entire notifications box
* add FAQ

### 2.5.3 

* release date 2016.11.29 (triggered by release of Yoast SEO 3.9)
* hide "Go Premium" text from adminbar dropdown
* hide dismissed notices and warnings in Yoast SEO Dashboard
* new setting: globally hide upsell notice in Yoast SEO Dashboard

### 2.5.2

* release date 2016.10.11 (triggered by release of Yoast SEO 3.7)
* once again hide red premium star, this time from the opposite side of the metabox on the Edit Post/Page screen

### 2.5.1

* release date 2016.10.09
* add rule to hide additional star tab of Yoast SEO metabox

### 2.5.0 (2016.10.06)

* release date 2016.10.06
* remove tour setting (redundant since v3.6 of Yoast SEO)
* remove adminbar setting (redundant since v3.6 of Yoast SEO)
* add new setting that hides the red star behind the "Go Premium" submenu that was added in v3.6 of Yoast SEO (it is probably necessary to save the settings page for this change to take effect). Thanks to Jake Jackson for reporting [this issue](https://github.com/senlin/so-clean-up-wp-seo/issues/19).

### 2.4.0 (2016.08.13)

* release date 2016.08.13
* with a BIG THANK YOU to [Andy Fragen](https://github.com/afragen) who made the plugin fully Multisite compatible and therewith we have been able to finally close [this issue](https://github.com/senlin/so-clean-up-wp-seo/issues/4).
* Andy also cleaned up some misc spacing and refactored part of the code for it to work smoother. People interested can see the full [PR](https://github.com/senlin/so-clean-up-wp-seo/pull/16).
* tested with WP version 4.6

### 2.3.0 (2016.06.20)

* release date 2016.06.20 triggered by changes made with version 3.3 of the Yoast SEO plugin
* hide coloured ball of content analysis also from metabox tab (edit Post/Page screens)
* substitute hide wpseo-score traffic light (v1.7.4) with hide content and SEO score (Yoast SEO 3.3.0), thanks to [Andrea Balzarini](https://github.com/andrebalza) [issue 15](https://github.com/senlin/so-clean-up-wp-seo/pull/15), because "a SEO plugin telling you that your content is poor while saving a new post, is not just nagging, but offensive too"
* hide yoast-issue-counter "enhancement" introduced in Yoast SEO 3.3 from adminbar and sidebar
* move minimum WordPress version up to 4.3

### 2.2.0 (2016.04.21)

* release date 2016.04.21 triggered by changes made with version 3.2 of the Yoast SEO plugin
* hide the ad for the premium version in the help center or hide the whole help center (added to v3.2 of Yoast SEO plugin)
* tested up to WP 4.5

### 2.1.0 (2016.03.02)

* simplify the CSS rules and add the rule to hide the seo-score column on taxonomies (added to v3.1.0 of Yoast SEO plugin)
* remove option to hide tagline nag (temporarily disabled in v3.1 of Yoast SEO plugin)
* partly remove option to hide robots nag (partly temporarily disabled in v3.1 of Yoast SEO plugin)
* remove option to hide GSC nag (temporarily disabled in v3.1 of Yoast SEO plugin)
* remove option to hide recalculate nag (temporarily disabled in v3.1 of Yoast SEO plugin)
* adjust readme files

### 2.0.2 (2016.02.26)

* add translator details Dutch language file
* update readme files (text and tags)
* PR [#11](https://github.com/senlin/so-clean-up-wp-seo/pull/11) add empty array as default for `get_option cuws_hide_admin_columns` to avoid warnings form subsequent `in_array` checks - credits [Ronny Myhre Njaastad](https://github.com/ronnymn)
* remove whitespace

### 2.0.1 (2016.02.05)

* include text-domain in plugin header which I forgot to do in the 2.0.0 release; apologies

### 2.0.0 (2016.02.04)

* complete rewrite of the plugin
* new Settings page to fine tune what is hidden/removed to your liking
* new screenshots
* tested up to WP 4.4.2

### 1.8.0 (2016.01.28)

* name change to avoid "Yoast" trademark violation

### 1.7.5 (2015.12.26)

* remove SEO score algorithm recalculate nag

### 1.7.4 (2015.11.19)

* remove wpseo-score traffic light next to Move to trash on Edit Post/Page screen

### 1.7.3 (2015.11.19)

* version 3.0 of Yoast SEO has introduced a cool new UI for the Edit screens. This also shows a + icon and when clicking that, you'll have a big fat ad in your face. This is a premium feature and the only function of the + icon therefore is to irritate you with an ad. We have therefore made it invisible. 
* tested up to WP 4.4
* adjust readme files

### 1.7.2 (2015.09.30)

* [BUG FIX] fix bug that slipped in (forgot to remove) 1.7.1 release, thanks for the [report](https://wordpress.org/support/topic/171-update-problem) [@stansbury](https://wordpress.org/support/profile/stansbury)

### 1.7.1 (2015.09.30)

* remove function that checks whether Yoast SEO has been installed; reason is to simplify things a bit.
* adjust readme files

### 1.7 (2015.09.16)

* remove yst_opengraph_image_warning nag that was added to Yoast SEO 2.1, but we never noticed it before. In the changelog it has been described as "validation error", which of course is nonsense, because the world is larger than social media. The nag manifests itself by placing thick red borders around your Featured Image as well as a red-bordered warning message when your Featured Image is smaller than 200x200 pixels.
* change function name
* add screenshot of before/after yst_opengraph_image_warning nag
* adjust readme files

### 1.6 (2015.08.07)

* remove GSC (Google Search Console) nag that was introduced in Yoast SEO 2.3.3

### 1.5 (2015.07.22)

* remove overview dashboard widget that was introduced in Yoast SEO 2.3
* change plugin name to reflect the name-change of the plugin it cleans up for ([WordPress SEO became Yoast SEO](https://yoast.com/yoast-seo-2-3/)) 

### 1.4 (2015.06.17)

* remove updated nag (introduced with Yoast SEO version 2.2.1)
* remove previous so_cuws_remove_about_tour() function that has become redundant from Yoast SEO 2.2.1 onwards; replaced with with so_cuws_ignore_tour() function

### 1.3.2.1 (2015.05.15)

* Clean up white space

### 1.3.2 (2015.05.14)

* Fix issue that WP SEO columns were still showing on Edit Posts/Pages pages 

### 1.3.1 (2015.05.01)

* Added styling to remove Tour Intro and button to start tour
* Added screenshots
* Removed redundant dashboard widget function 

### 1.3 (2015.04.30)

* Added function to remove Yoast SEO Settings from Admin Bar, inspired by comment of [Lee Rickler](https://profiles.wordpress.org/lee-rickler/) in discussion on [Google+](https://plus.google.com/u/0/+PietBos/posts/AUfs8ZdwLP3)
* put code actions/filters in order

### 1.2 (2015.04.30)

* Release on wordpress.org Repo

### 1.1 (2015.04.27)

* Release version 
* banner image (in assets folder) by [Leigh Kendell](https://unsplash.com/leighkendell)

### 1.0 (2015.04.24)

* Initial plugin [code snippet](https://github.com/senlin/Code-Snippets/blob/0ae24e6fc069efe26e52007c05c7375012ee688a/Functions/Admin-Dashboard/remove-yoast-crap.php)

## Update Notice

### 2.0.0

* Version 2.0.0 is a complete rewrite of the SO Hide SEO Bloat plugin. Please visit the Settings page after you have updated to this version, so you can fine tune what is hidden/removed.

### 1.8.0

* name change to avoid "Yoast" trademark violation

### 1.5

* We have changed the name of our plugin to reflect the name change of the plugin it cleans up after

### 1.4

* Version 2.2.1 of the Yoast SEO plugin changes a lot of things around. The automatic redirect to the plugin's About page is no longer, so we have removed the function that disables it. The new version introduced an updated nag that doesn't let itself be dismissed easily, so we have simply hidden it altogether. The super irritating balloon to follow the intro tour was back again too, we have countered that with a functiobn that sets the user_meta of that intro tour to true, which means "seen".
