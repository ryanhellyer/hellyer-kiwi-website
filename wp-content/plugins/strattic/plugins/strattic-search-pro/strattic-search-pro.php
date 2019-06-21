<?php
/*
Plugin Name: Strattic Search Pro
Plugin URI:  http://strattic.com
Description: 

Author: Strattic
Author URI: https://strattic.com/

Copyright 2018 Strattic

*/

add_filter( 'strattic_search_pro', 'strattic_search_pro' );
function strattic_search_pro( $fields ) { 

	foreach ( $fields as $key => $field ) {
		$fields[ $key ][ 'active' ] = true;
	}

	return $fields;
}
