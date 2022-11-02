/**
 * Load the search engine (in this case, FuseJS).
 *
 * @param object strattic_search (the main strattic_search object with all the configuration data).
 * @return object The FuseJS class objects.
 */
const strattic_search_engine = function( strattic_search ) {
	fuse = new Fuse( strattic_search.posts, strattic_search.fuse_js_options );

	return fuse;
}

/**
 * Get some search results.
 *
 * @param string search_param The string to search for.
 * @return array The search results.
 */
const strattic_search_get_results = function( search_param ) {
	const fuse_results = fuse.search( search_param );

	let results = [];
	for ( let i = 0; i < fuse_results.length; i++ ) {
		results[ i ] = fuse_results[ i ].item.id;
	}

	return results;
}
