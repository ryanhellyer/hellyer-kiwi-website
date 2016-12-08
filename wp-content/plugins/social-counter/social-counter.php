<?php

/*
 * Plugin Name: Social Counter
 * Plugin URI: http://wordpress.org/extend/plugins/social-counter/
 * Description: Get the total count of fans and followers from your social network profiles. Without the need of complicated API keys. A neat solution to encourage visitors to grow your social network.
 * Author: Prisna
 * Version: 2.0
 * Author URI: https://www.prisna.net/
 * License: GPL2+
 * Text Domain: prisna-social-counter
 * Domain Path: /languages/
 */

define('PRISNA_SOCIAL_COUNTER__MINIMUM_WP_VERSION', '3.6');
define('PRISNA_SOCIAL_COUNTER__VERSION', '2.0');

define('PRISNA_SOCIAL_COUNTER__PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PRISNA_SOCIAL_COUNTER__PLUGIN_URL', plugin_dir_url(__FILE__));

define('PRISNA_SOCIAL_COUNTER__PLUGIN_CLASSES_DIR', PRISNA_SOCIAL_COUNTER__PLUGIN_DIR . '/classes/');
define('PRISNA_SOCIAL_COUNTER__TEMPLATES', PRISNA_SOCIAL_COUNTER__PLUGIN_DIR . '/templates');

define('PRISNA_SOCIAL_COUNTER__JS', PRISNA_SOCIAL_COUNTER__PLUGIN_URL . 'javascript');
define('PRISNA_SOCIAL_COUNTER__CSS', PRISNA_SOCIAL_COUNTER__PLUGIN_URL . 'styles');
define('PRISNA_SOCIAL_COUNTER__IMAGES', PRISNA_SOCIAL_COUNTER__PLUGIN_URL . 'images');

require_once PRISNA_SOCIAL_COUNTER__PLUGIN_CLASSES_DIR . 'common.class.php';
require_once PRISNA_SOCIAL_COUNTER__PLUGIN_CLASSES_DIR . 'base.class.php';
require_once PRISNA_SOCIAL_COUNTER__PLUGIN_CLASSES_DIR . 'config.class.php';

if (is_admin())
	require_once PRISNA_SOCIAL_COUNTER__PLUGIN_CLASSES_DIR . 'admin.class.php';
else
	require_once PRISNA_SOCIAL_COUNTER__PLUGIN_CLASSES_DIR . 'main.class.php';

?>