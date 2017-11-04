<?php

/**
 * Functions for the plugin.
 *
 * @package 	WPAS
 * @subpackage 	Includes
 * @since      	0.0.1
 * @author     	Pulido Pereira Nuno Ricardo <pereira@nunoapps.com>
 * @copyright  	Copyright (c) 2007 - 2013, Pulido Pereira Nuno Ricardo
 * @link       	http://nunoapps.com/plugins/wp-api-shortcode
 * @license    	http://www.gnu.org/licenses/gpl-2.0.html
 */

// Add shortcode filter into widgets 'text'.
add_filter('widget_text', 'do_shortcode');