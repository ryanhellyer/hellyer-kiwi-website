<?php

/**
 * Set the Safe Redirect Manager plugin to support a huge number of redirects.
 */
add_filter( 'srm_max_redirects', function() {
	return 10000;
});
