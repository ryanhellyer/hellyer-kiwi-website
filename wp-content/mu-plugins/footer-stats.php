<?php

function  ryan_show_footer_stats() {
	echo '
<!-- ' . get_num_queries() . ' queries in ';
	timer_stop(1);
	echo 'seconds.
-->';
}
add_action( 'wp_footer', 'ryan_show_footer_stats' );
