<?php
        
function pushpress_coaches_shortcode_folder() { 
    return plugin_dir_path( dirname( __FILE__ ) ) . 'shortcodes';
}

function pushpress_coaches_shortcode($atts=array()) {

	global $post;

	$meta = get_post_meta($post->ID);

	//echo '<pre>';
	//echo $post->ID;
	//echo $atts['field'];
	// var_dump($atts);
	//echo $meta['coach_first_name'][0];
	//var_dump($meta);
	//die();

	$return = "";
	switch ($atts['field']) {
		case 'first_name':
			$return = $meta['coach_first_name'][0];
			break;
		case 'last_name':
			$return = $meta['coach_last_name'][0];
			break;
		case 'name':
		case 'full_name':
			$return = $meta['coach_first_name'][0] . ' ' . $meta['coach_last_name'][0];
			break;
		case 'title':
			$return = $meta['coach_title'][0];
			break;
		case 'feats_of_strength':
			$return = $meta['coach_feats_of_strength'][0];
			break;
		case 'qualifications':
			$return = $meta['coach_qualifications'][0];
			break;
		case 'about':
			$return = $meta['coach_about'][0];
			break;
		case 'turning_point':
			$return = $meta['coach_turning_point'][0];
			break;
		case 'motivation':
			$return = $meta['coach_motivation'][0];
			break;
		case 'phone':
			$return = $meta['coach_phone'][0];
			break;
		case 'email':
			$return = $meta['coach_email'][0];
			break;
		case 'facebook':
			if (isset($atts['link'])) { 
				$return = '<a href="' . $meta['coach_facebook'][0] . '" target="_facebook">' . $meta['coach_facebook'][0]  . '</a>';
			}
			else { 
				$return = $meta['coach_facebook'][0];
			}
			break;
		case 'instagram':
			if (isset($atts['link'])) { 
				$return = '<a href="https://instagram.com/' . $meta['coach_instagram'][0] . '" target="_instagram">@' . $meta['coach_instagram'][0]  . '</a>';
			}
			else { 
				$return = "@". $meta['coach_instagram'][0];
			}
			break;
		case 'twitter':
			if (isset($atts['link'])) { 
				$return = '<a href="https://twitter.com/' . $return = $meta['coach_twitter'][0] . '" target="_twitter">@' . $return = $meta['coach_twitter'][0]  . '</a>';
			}
			else { 
				$return = "@" . $meta['coach_twitter'][0];
			}
			
			break;
		case 'website':
			if (isset($atts['link'])) { 
				$return = '<a href="' . $return = $meta['coach_website'][0] . '" target="_website">' . $return = $meta['coach_website'][0]  . '</a>';
			}
			else { 
				$return = $return = $meta['coach_website'][0];
			}
			
			break;
	}

	return $return;
}
add_shortcode( 'pushpress-coach', 'pushpress_coaches_shortcode' );
