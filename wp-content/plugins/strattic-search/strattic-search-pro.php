<?php

add_filter( 'strattic_search_pro', 'strattic_search_pro' );
function strattic_search_pro( $fields ) { 

	foreach ( $fields as $key => $field ) {
		$fields[ $key ][ 'active' ] = true;
	}

	return $fields;
}
