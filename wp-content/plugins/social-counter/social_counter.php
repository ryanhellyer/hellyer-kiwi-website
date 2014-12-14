<?php
/*
Plugin Name: Social Counter
Plugin URI: https://github.com/jzvikas/Social-Counter 
Description: Show social networks statistics
Author: djjmz
Version: 1.0.1
Author URI: https://github.com/jzvikas/Social-Counter
*/
require_once('social.php');
require_once('widget.php');
function social_counter() {
$current_url = home_url(add_query_arg(array()));
$social = new Social;
echo '<div class="sc_div">
<img src="'.plugins_url('img/twitter.png', __FILE__ ).'"/>'.$social->get_tweets($current_url).'
<img src="'.plugins_url('img/linkedin.png', __FILE__ ).'"/>'.$social->get_linkedin($current_url).'
<img src="'.plugins_url('img/facebook.png', __FILE__ ).'"/>'.$social->get_fb($current_url).'
<img src="'.plugins_url('img/google.png', __FILE__ ).'"/>'.$social->get_plusones($current_url).'
<img src="'.plugins_url('img/stumbleupon.png', __FILE__ ).'"/>'.$social->get_stumble($current_url).'
<img src="'.plugins_url('img/delicious.png', __FILE__ ).'"/>'.$social->get_delicious($current_url).'
<img src="'.plugins_url('img/reddit.png', __FILE__ ).'"/>'.$social->get_reddit($current_url).'
<img src="'.plugins_url('img/pinterest.png', __FILE__ ).'"/>'.$social->get_pinterest($current_url).'
</div>';
}
add_shortcode('social_counter', 'social_counter');
//===============================================
function social_counter_load_widget() {
register_widget( 'Social_Counter' );
}
add_action( 'widgets_init', 'social_counter_load_widget' );
//===============================================
function sc_styles()
{
wp_enqueue_style( 'font-awesome', plugins_url('css/style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'sc_styles' );
/*
function add_post_content($content) {
	if(is_singular()) {
		$content .= '<p>This article is copyright &copy; '.date('Y').'&nbsp;'.bloginfo('name').'</p>';
	}
	return $content;
}
add_filter('the_content', 'add_post_content');*/