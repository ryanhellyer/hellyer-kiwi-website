<?php

class Social_Counter extends WP_Widget {

    function __construct() {
        parent::__construct('Social_Counter', 'Social Counter', array('description' => '&nbsp;'));
    }

    public function widget($args, $instance) {
        global $post;
        extract($args);
        $title = apply_filters('widget_title', $instance['title']);
        $cache = ($instance['cache'] == 'on') ? 'on' : 'off';
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];
        $current_url = home_url(add_query_arg(array()));
        $social = new Social;
        if (!is_dir(WP_CONTENT_DIR . '/cache/')) {
            mkdir(WP_CONTENT_DIR . '/cache/', 0777);
        }
        $cache_file = WP_CONTENT_DIR . '/cache/' . md5($_SERVER['REQUEST_URI']) . '.html';
        if ($cache == 'on') {
            if (!file_exists($cache_file) or ( time() - filemtime($cache_file)) > 3600) {
                $widget_text = '<div class="sc_div">
<img src="' . plugins_url('img/twitter.png', __FILE__) . '" alt="Twitter"/>' . $social->get_tweets($current_url) . '<br>
<img src="' . plugins_url('img/linkedin.png', __FILE__) . '" alt="LinkedIn"/>' . $social->get_linkedin($current_url) . '<br>
<img src="' . plugins_url('img/facebook.png', __FILE__) . '" alt="Facebook"/>' . $social->get_fb($current_url) . '<br>
<img src="' . plugins_url('img/google.png', __FILE__) . '" alt="Google+"/>' . $social->get_plusones($current_url) . '<br>
<img src="' . plugins_url('img/stumbleupon.png', __FILE__) . '" alt="StumbleUpon"/>' . $social->get_stumble($current_url) . '<br>
<img src="' . plugins_url('img/delicious.png', __FILE__) . '" alt="Delicious"/>' . $social->get_delicious($current_url) . '<br>
<img src="' . plugins_url('img/reddit.png', __FILE__) . '" alt="Reddit"/>' . $social->get_reddit($current_url) . '<br>
<img src="' . plugins_url('img/pinterest.png', __FILE__) . '" alt="Pinterest"/>' . $social->get_pinterest($current_url) . '<br>
</div>';
                file_put_contents($cache_file, $widget_text);
                echo $widget_text;
            } else {
                echo file_get_contents($cache_file);
            }
        } 
        else {
            echo '<div class="sc_div">
<img src="' . plugins_url('img/linkedin.png', __FILE__) . '" alt="LinkedIn"/>' . $social->get_linkedin($current_url) . '<br>
<img src="' . plugins_url('img/google.png', __FILE__) . '" alt="Google+"/>' . $social->get_plusones($current_url) . '<br>
<img src="' . plugins_url('img/twitter.png', __FILE__) . '" alt="Twitter"/>' . $social->get_tweets($current_url) . '<br>
<img src="' . plugins_url('img/facebook.png', __FILE__) . '" alt="Facebook"/>' . $social->get_fb($current_url) . '<br>
<img src="' . plugins_url('img/stumbleupon.png', __FILE__) . '" alt="StumbleUpon"/>' . $social->get_stumble($current_url) . '<br>
<img src="' . plugins_url('img/delicious.png', __FILE__) . '" alt="Delicious"/>' . $social->get_delicious($current_url) . '<br>
<img src="' . plugins_url('img/reddit.png', __FILE__) . '" alt="Reddit"/>' . $social->get_reddit($current_url) . '<br>
<img src="' . plugins_url('img/pinterest.png', __FILE__) . '" alt="Pinterest"/>' . $social->get_pinterest($current_url) . '<br>
</div>';
        }
        echo $args['after_widget'];
    }

    public function form($instance) {
        if (isset($instance['title'])) {
            $title = $instance['title'];
        } else {
            $title = 'Title';
        }
        if (isset($instance['cache']) and $instance['cache'] == 'on') {
            $cache = $instance['cache'];
            $checked = 'checked="checked"';
        } else {
            $cache = 'false';            
        }
        echo '<p>
<label for="' . $this->get_field_id('title') . '">Title:</label>
<input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" /><br>Caching&nbsp;<input class="widefat"  id="' . $this->get_field_id('cache') . '" name="' . $this->get_field_name('cache') . '" type="checkbox" '.$checked.'/></p>';
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['cache'] = (!empty($new_instance['cache'])) ? $new_instance['cache'] : 'false';
        return $instance;
    }

}
