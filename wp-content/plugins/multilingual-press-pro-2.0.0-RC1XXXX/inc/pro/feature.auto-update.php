<?php # -*- coding: utf-8 -*-

add_action( 'mlp_and_wp_loaded', 'mlp_feature_autoupdate' );

function mlp_feature_autoupdate( Inpsyde_Property_List_Interface $data ) {
	new Mlp_Auto_Update( $data );
}