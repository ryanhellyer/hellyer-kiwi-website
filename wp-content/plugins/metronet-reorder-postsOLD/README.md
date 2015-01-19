Reorder Posts for WordPress
=============

Reorder Posts is a WordPress plugin that allows a simple and easy way to reorder your post types using WordPress.

This plugin is very powerful and highly configurable if you need some serious reordering.

If you have a feature request, please add an issue.

The stable version of this plugin <a href="https://wordpress.org/plugins/metronet-reorder-posts/">can be found on WordPress.org</a>.

Features
----------------------
<ul>
<li>Adds "Reorder" sub-menu to all post types by default</li>
<li>Hierarchical post type support (i.e., supports nested posts)</li>
<li>Allows you to re-nest hierarchical posts</li>
<li>Auto-saves order without having to click an update button</li>
<li>Dedicated settings panel for determining which post types can be reordered</li>
<li>Advanced settings panel for overriding the menu order of custom post type queries</li>
</ul>

Advanced customization is allowed via hooks.  See the Plugin Filters section.

Installation
---------------------
Either install the plugin via the WordPress admin panel, or ... 

1. Upload `metronet-reorder-posts` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

For each post type, you will see a new "Reorder" submenu.  Simply navigate to "Reorder" to change the order of your post types. Changes are saved immediately, there is no need to click a save or update button.  

By default, ordering is enabled for all post types.  A settings panel is available for determining which post types to enable ordering for.  Advanced options allow you to change the menu order for post types.

Plugin Filters
---------------------

The plugin filters are demonstrated in the code below.

```php
//Example filter usage for Reorder Posts
//https://github.com/ronalfy/reorder-posts

/* Override which post types to use Reorder for 
	Takes and returns an array of post types
*/
add_filter( 'metronet_reorder_post_types', 'reorder_override_post_types' );
function reorder_override_post_types( $post_types = array() ) {
	return array( 'post', 'page', 'custom_post_type1', 'custom_post_type2' );	
}

/* The following filters take and return booleans (true, false)*/
/* Call WordPress functions __return_false or __return_true */
add_filter( 'metronet_reorder_post_allow_admin', '__return_true' ); //Enable or disable the admin panel settings for the plugin
add_filter( 'metronet_reorder_allow_menu_order', '__return_true' ); //Enable or disable the plugin's advanced menu_order modifications for all post types
add_filter( 'metronet_reorder_allow_menu_order_post', '__return_true' ); //Enable or disable the plugin's advanced menu_order modifications for a single post type (format metronet_reorder_allow_menu_order_{post_type}) - If Filter metronet_reorder_allow_menu_order is false, there is no need for this filter
```

The filters below demonstrate how to add re-ordering to <a href="https://wordpress.org/plugins/the-events-calendar/">The Events Calendar</a> plugin.

```php
//Add The Events Calendar post types to main Events menu item
//Format metronet_reorder_menu_location_{post_type}
add_filter( 'metronet_reorder_menu_location_tribe_venue', 'unique_menu_event_post_type_locations' );
add_filter( 'metronet_reorder_menu_location_tribe_organizer', 'unique_menu_event_post_type_locations' );
function unique_menu_event_post_type_locations( $menu_location ) {
	return 'edit.php?post_type=tribe_events';	
}

//Re-label The Events Calendar post types
//Format metronet_reorder_menu_label_{post_type}
add_filter( 'metronet_reorder_menu_label_tribe_events', 'unique_reorder_label_events', 10, 2 );
add_filter( 'metronet_reorder_menu_label_tribe_venue', 'unique_reorder_label_events', 10, 2 );
add_filter( 'metronet_reorder_menu_label_tribe_organizer', 'unique_reorder_label_events', 10, 2 );
function unique_reorder_label_events( $label = '', $post_type = '' ) {
	$post_type = get_post_type_object( $post_type );
	$label = isset( $post_type->label ) ? $post_type->label : '';
	return 'Reorder ' . $label;
}
```

Credits
----------------------
This plugin was originally developed for <a href="https://metronet.no/">Metronet AS in Norway</a>.

The plugin is now independently developed by <a href="https://hellyer.kiwi/">Ryan Hellyer</a> and <a href="http://www.ronalfy.com">Ronald Huereca</a>.