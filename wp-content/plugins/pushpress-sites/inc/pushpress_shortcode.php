<?php

class PushPress_Shortcode {

//	private $model;
//	private $subdomain;

	function __construct( $subdomain ) {
//		$this->subdomain = $subdomain;
//		$this->model = new Wp_Pushpress_Model();
	}

	public static function pushpress_shortcode( $atts ) { 

		if ( ! $atts['name']) { 
			return;
		}

		$option_name = "pp-shortcode-" . $atts['name'];
		$option = get_option($option_name);

		return $option;
	}

}
add_shortcode( 'pushpress-shortcode', array( 'PushPress_Shortcode', 'pushpress_shortcode' ) );
