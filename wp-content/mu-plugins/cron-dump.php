<?php

if ( isset( $_GET['cron_dump'] ) ) {
	echo '<pre>';
	print_r( _get_cron_array() );
	echo '</pre>';
	die;
}
