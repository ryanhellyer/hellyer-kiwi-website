This plugin replaces the built in WordPress search with FuseJS search.

It is designed to work with the twentynineteen theme. More themes coming soon along with generic support.


It works by creating a dump of data used for accessing search results at `/search.json`.

FuseJS is then configured in the `/js/strattic-fusejs-search.js` file to access that data. When a URL with the format `?s=searchstring` is accessed, it looks in the `/search.json` file for results and when found, it removes the content in the `#main` tag and replaces it with new HTML (`#main` will often need changed to something else for different themes and the templates adjusted to suit). The templating is handled via the Mustache JS script, which allows for templating in JS.

You can see a demonstration of it in action here:
https://fusejs.preview.strattic.io/?s=test


* Filters in the plugin:
`strattic_search_cache_time`
`strattic_search_index`
`strattic_search_post_types`
`strattic_search_fields`
`strattic_search_page_slug`
`strattic_search_name_attr`
