=== Strattic Widgets ===
Contributors: strattic,ryanhellyer
Tags: widgets, sidebars, widget, sidebar, ajax, static
Requires at least: 6.0
Tested up to: 6.2
Stable tag: 1.0.6


Converts widget areas to be fully AJAX'd.


== Description ==

Converts widget areas to be fully AJAX'd. Instead of outputting HTML for each page, an AJAX request will be run to access the content in the widget area.


= Including or excluding widget areas =
By default, all widget areas are converted to be loaded via AJAX. But there are two filters included for including and excluding widget areas in the AJAXifying process, `strattic_included_widget_areas` and `strattic_excluded_widget_areas`. They may be implemented as follows (obviously do not try to include AND exclude at the same time).

```add_filter(
	'strattic_included_widget_areas',
	function() {
		$widget_areas = array(
			'sidebar-1', // widget area to be AJAX'd.
			'sidebar-2', // widget area to be AJAX'd.
		);

		return $widget_areas;
	}
);```

```add_filter(
	'strattic_excluded_widget_areas',
	function() {
		$widget_areas = array(
			'sidebar-1', // widget area to not be AJAX'd.
			'sidebar-2', // widget area to not be AJAX'd.
		);

		return $widget_areas;
	}
);```


= Static hosting =
The functionality for this plugin is very useful for when building a static site via WordPress. It allows for widget areas to be modified, without requiring every page to be republished. We have included support for <a href="https://www.strattic.com/">Strattic web hosting</a> so that the AJAX requests are published during every site publish (including selective publishes).


== Installation ==

After you've downloaded and extracted the files:

1. Upload the complete 'strattic-widgets' folder to the '/wp-content/plugins/' directory OR install via the plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress
4. And yer done!

There are no settings pages for this plugin.


== Frequently Asked Questions ==

= Does this only work on Strattic (https://www.strattic.com/)? =

No, this plugin should work equally well on other hosting platforms. It includes a small Strattic specific component to force the AJAX requests to always be published on our platform, but this does nothing when not running on Strattic and does not interfere with other web hosts.


= Where's the plugin settings page? =

There isn't one.


= Does it work in older versions of WordPress? =

It probably works on extremely old versions of WordPress (possibly as old as version 2.1), but we haven't checked and will only be supporting the latest version of WordPress.


== Changelog ==

= 1.0.7 =
* Changed plugin name
* Implemented generic AJAX system instead of relying on widgets
* Implemented expiry for old AJAX items

= 1.0.6 =
* Shifting JS to event handler to avoid clashes with other code

= 1.0.5 =
* Shifting JS to event handler to avoid clashes with other code

= 1.0.4 =
* Coding standards changes

= 1.0.3 =
* Implementing all widget area data into a single AJAX request

= 1.0.2 =
* Addition of include and exclude filters

= 1.0.1 =
* Addition of Strattic specific components

= 1.0.0 =
* Initial plugin build
