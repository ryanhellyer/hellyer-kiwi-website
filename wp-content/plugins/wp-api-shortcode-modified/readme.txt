=== WordPress API Shortcode ===

Contributors: namaless
Tags: wordpress, api, shortcode
Donate link: http://namaless.com/donate
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add a short code for using into post or page and get informations about plugins or themes from wordpress.org repository.

== Description ==

Adds the Shortcode `[wpapi]` in posts, pages and even in text widgets.

### Fantastic !

With this plugin you will have access to all the information on WordPress.org for both themes to the plugins.

### Simple and easy to use

You can use a set of shortcodes to make your special publication of a plugin or theme present on WordPress.org, here's how it works:

`[wpapi type="plugin" slug="freelancer"][/wpapi]`

Use `type` with `plugin` or `theme`.
Use `slug` with the wordpress.org slug name.

Inside the main shortcode you can use this sub shortcodes:

`[wpapi_slug]` Show the slug, using this to customize urls example `<a href="http://wordpress.org/plugins/[wpapi_slug]">Wordpress Plugin Site</a>`.

`[wpapi_name]` Show the name of plugin/theme.

`[wpapi_author]` Show the author link used into plugin/theme file.

`[wpapi_contributors]` Show the contributors list used into readme.

`[wpapi_version]` Show the version of plugin/theme.

`[wpapi_added]` Show the date when the plugin/theme added to WordPress.org repository.

`[wpapi_last_updated]` Show the date when the plugin/theme is updated.

`[wpapi_author_profile]` Show the profile uri on WordPress.org.

`[wpapi_homepage]` Show the homepage link used into plugin/theme.

`[wpapi_requires]` Show the version of WordPress need to working to plugin/theme.

`[wpapi_tested]` Show the version of WordPress tested of the plugin/theme.

`[wpapi_downloaded]` Show the number of downloads.

`[wpapi_rating]` Show the rating.

`[wpapi_num_ratings]` Show the number of votes.

`[wpapi_download_link]` Show the download uri.

`[wpapi_donate_link]` Show the donate uri used into plugin/theme.

`[wpapi_description]` Show the description.

`[wpapi_installation]` Show the installation.

`[wpapi_screenshots]` Show the list of screenshots used into WordPress.org repository.

`[wpapi_changelog]` Show the changelog used into readme.txt.

`[wpapi_faq]` Show the faq used into readme.txt.

`[wpapi_other_notes]` Show other notes used into readme.txt.

### If condition enabled for all sub shortcodes

All sub shortcodes can used shortcode to check if the field is present or not. Use istead:
`[wpapi_if_{field}][/wpapi_if_{field}]`

Example for version:
`[wpapi_if_version]Version: [wpapi_version][/wpapi_if_version]`

== Installation ==

1. Upload "wordpress-api-shortcode" folder to the "/wp-content/plugins/" directory.
2. Activate the plugin through the "Plugins" menu in WordPress.

== Changelog ==

### 1.0.0
* Release stable version.

### 0.0.1
* Initial release.