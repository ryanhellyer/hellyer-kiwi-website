# Strattic WordPress plugin

This plugin is used for providing a list of URLs to be converted to static.

## How to implement

NEEDS UPDATING FOR "SERVERLESS" IMPLEMENTATION.

## Structure

### Client plugin

NEEDS UPDATING FOR "SERVERLESS" IMPLEMENTATION.

There is an extra client plugin (located at `/usr/local/bin/strattic-publish/wordpress-plugin/mu-plugins/`), which provides the code which is used in the clients file system. This is added to their mu-plugins directory. The remaining code is within this repository (the one you are in right now) and is kept in the `/usr/local/bin/strattic-publish/` directory, which the client can not access.

Plugins added in the mu-plugins folder do not show up in the normal WordPress plugin admin page and are automatically loaded (no activation necessary). This is the standard place to put plugins which should always be active and not modified by clients.

### strattic.php

This is the main loading point for the whole plugin. This file is included by the "client plugin" in the mu-plugins folder.

This file uses a PHP autoloader to load various classes in the `/inc/` folder, which add new features to the site.

## Features

### Strattic_Core()

Provides core methods and constants used in other parts of the plugin. This is not intended to be instantiated on it's own, but extended by other classes.

### Strattic_Admin()

Adds the Strattic menu links to the WordPress admin panel.
Adds the main Strattic admin page for publishing the site.

### Strattic_Admin_Links()

Adds extra admin pages. Some of these appear as tabs at the bottom of the primary admin page.

These pages include:
- Publish
- Manual links
- Discovered links
- String Replacement

### Strattic_Anti_Spam()

This adds a generic anti-spam feature for forms.

[THIS FEATURE CURRENTLY DISABLED UNTIL FURTHER TESTING COMPLETED]

### Strattic_API()

This is the primary class used for generating the list of URLs used to power the core of the Strattic service. All roads lead to this class ;)

#### End point

- `/strattic-api/'`

A JSON feed of data required for a deployment. Includes a key of 'settings' which includes the deployment settings, including the deployment type and (if applicable) the CloudFront URL, CloudFront ID, S3 bucket etc.

The 'paths' key contains an array of paths to be deployed.
Each new line is a new URL for processing.
Exceptions are `end-first-stage` and `end-second-stage`. All URLs before `end-first-stage` represent extremely important URLs which may need to be published immediately, such as recently updated posts, home page and main feed. Everything between `end-first-stage` and `end-second-stage` are URLs which are considered important. Anything after `end-second-stage` is considered very low priority and is simply handled as a background task for completion, such as RDS and atom feeds.


#### Strattic_API::get_everything()

This method grabs all the thingz! It currently processed the following data:

- `get_all_posts()` - grabs all public posts
- `get_extra_pagination()` - attempts to get paginated blog and home page URLs
- `get_date_archives()` - gets date archive pages
- `get_all_terms()` - gets all term pages
- `get_feeds()` - gets all feed pages
- `get_user_pages()` - gets all user profile pages
- `comeet_redirects()` - gets all comeet_redirects()

### Strattic_Buffer()

Provides a filter for output buffering. This can be used for dynamically modifying the output of pages.

This is currently used by the Strattic_String_Replace() and Strattic_Strip_Double_Slashes() classes.

### Strattic_Discover_Links()

When a user visits a page, we check if it's been found before and if not, we store it so that we can see which links are being missed from the API. These discovered links are then appended to the API calls to ensure that nothing is missed.

[THIS FEATURE CURRENTLY DISABLED UNTIL FURTHER TESTING COMPLETED]

### Strattic_Search()

Provides an admin page for modifying the FuseJS setup and adds the required JavaScript to the frontend for searching. A WP_Cron task regenerates the search index every hour. Whenever a post/page is updated, the search index is regenerated (and the WP_Cron deferred for another hour).

The admin interface for this is available to the client, but is not linked in the admin panel as the settings are very complicated and require careful modification. The defaults should suit most sites (and if they don't, then we should change the defaults).

The page can be accessed by directly loading the page at `/wp-admin/admin.php?page=strattic-search-settings`.

[THIS FEATURE IS CURRENTLY ACTIVE, BUT HAS HAD VERY LITTLE TESTING AND MAY NOT BEHAVE AS ADVERTISED ;)]

### Strattic_String_Replace()

Provides an admin page for doing basic string replacements. These changeswill appear in both the staging and static sites.

Due to the nature of the strings being replaced, no data sanitization is currently implemented for these strings. The admin page is only available to administrators however, so shouldn't pose any significant security problems; all requests are checked for both user permission (user level check) and user intention (nonce check) to ensure that only valid requests are processed.

### Strattic_Strip_Double_Slashes()

This feature removes double slashes on pages. It does this by filtering the page content via a filter provided by the Strattic_Buffer() class.

Double slashes should never occur within a good site setup, but some plugins and themes generate poor URL formatting including double slashes sometimes. Invalid double slashes within URLs are not supported on Amazon S3 and so need to be removed before output.

The double slash removal occurs both in the staging and static environments.

A complete DOM parser is used for removing the double slashes. Regex is too prone to errors for this task. The DOM parser is slower, but far more reliable at stripping the double slashes.

#### Strattic_Fix_Hard_Coded_URLs()

This attempts to automatically correct hard coded URLs outputted to the page. It attempts to string replace the CloudFront domain with the staging domain, and converts from http to https.

## Future features

### Non permalink URLs

Non permalink URL is currently contained within the plugin but is commented out due to our current implementation of Amazon S3 and Cloudfront not being able to handle query vars. In theory, we should be able to support these in future by uploading a static file with a different name, and pointing specific URLs at that new location via Cloudfront.

These non-permalink URLs are required for the functioning of some (mostly poorly coded) plugins, but also for providing URL shortening. Some URLs are even outputted on many of our public sites, but do not work, eg; `<link rel='shortlink' href='https://www.wpgarage.com/?p=2962' />`.

### WPML support

Support for this was supposedly added by an earlier developer, but I (Ryan) couldn't immediately figure out how it worked, so this has not been implemented in this new iteration of the plugin just yet.
