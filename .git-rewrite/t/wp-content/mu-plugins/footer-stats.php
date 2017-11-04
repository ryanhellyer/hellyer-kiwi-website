<?php
 
/*
 * Add performance stats for current site to the footer.
 */
function add_performance_stats() {
	echo "\n";
	echo '<!-- Blog ' . get_current_blog_id() . ' was created in ' . timer_stop( 0 ) . ' seconds via ' . get_num_queries() . ' queries -->';
	echo "\n";
}
add_action( 'wp_footer', 'add_performance_stats', 99999 );

