<?php

if ( isset( $_GET['test'] ) ) {
	add_action( 'wp_footer', 'temp_notice' );
	function temp_notice() {
		echo '<div style="position:fixed;width:100px;height:50px;background:red;border:2px solid #000;right: 10px;bottom:10px;">Pressabl 3</div>';
	}
}


