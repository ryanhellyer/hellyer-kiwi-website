<?php

class PrisnaSocialCounterCommon {

	public static function getHomeUrl($_path='') {

		return home_url($_path);
		
	}
	
	public static function getAdminWidgetsUrl() {
		
		return admin_url('widgets.php');
		
	}
	
	public static function getAjaxUrl() {
	
		return admin_url('admin-ajax.php');
		
	}

	public static function getSeparationValues($_count) {

		$result = array();
		
		for ($i=0; $i<=$_count; $i++)
			$result[$i] = $i != 0 ? $i . '%' : '';
			
		return $result;

	}
	
	public static function getRoundedValues($_count) {
		
		$result = array();
		
		for ($i=0; $i<=$_count; $i++)
			$result[$i] = $i != 0 ? sprintf(($i == 1 ? __('%s pixel', 'prisna-social-counter') : __('%s pixels', 'prisna-social-counter')), $i) : '';
			
		return $result;
		
	}

	public static function getAdminPluginUrl() {
		
		return admin_url('plugins.php?page=' . PrisnaSocialCounterConfig::getAdminHandle());
		
	}

	public static function stripHost($_url) {
		
		return preg_replace('/^(:\/\/|[^\/])+/', '', $_url);
		
	}

	public static function renderCSS($_code) {
	
		if (!empty($_code))
			echo '<style type="text/css">' . $_code . '</style>';
		
	}
	
	public static function getHost($_url) {
	
		preg_match('/^(:\/\/|[^\/])+/', $_url, $matches);
		
		return $matches[0];
	
	}
	
	public static function getSiteHost() {
	
		return self::getHost(get_option('home'));
	
	}
	
	public static function printHeaders() {

		header('Content-Type:application/json;charset=UTF-8');
		header('Content-Disposition:attachment');

	}
	
	public static function isOpenSSLInstalled() {

		return function_exists('openssl_encrypt');
		
	}
	
	public static function isFolderWritable($_folder) {
		
		return @is_writable($_folder) && is_array(@scandir($_folder));
		
	}

	public static function getArrayItems($_items, $_array) {
		
		$result = array();
		
		if (!is_array($_items))
			$_items = array($_items);
		
		for ($i=0; $i<count($_items); $i++)
			if (array_key_exists($_items[$i], $_array))
				$result[$_items[$i]] = $_array[$_items[$i]];
		
		return $result;
		
	}
	
	public static function inArray($_value, $_array) { 
		
		if (!is_array($_array))
			$_array = array($_array);		
	
		if (!is_array($_value))
			return in_array($_value, $_array);
		else {
			foreach ($_value as $single)
				if (in_array($single, $_array))
					return true;
			return false;
		}
	
	}

	public static function arrayKeysExists($_array_1, $_array_2) { 

		if (!is_array($_array_1))
			$_array_1 = array($_array_1);

		foreach ($_array_1 as $key)
			if (array_key_exists($key, $_array_2)) 
				return true; 
		
		return false;

	} 
	
	public static function startsWith($_string, $_start) {
		
		$length = strlen($_start);
		return substr($_string, 0, $length) === $_start;
		
	}

	public static function endsWith($_string, $_end) {
	
		return strcmp(substr($_string, strlen($_string) - strlen($_end)), $_end) === 0;
	
	}

	public static function cleanId($_string, $_separator='-', $_remove_last=true) {
		
		$result = preg_replace('/[^a-zA-Z0-9]+|\s+/', $_separator, strtolower($_string));
		
		if ($_remove_last === true && self::endsWith($result, '-'))
			$result = rtrim($result, '-');
		
		return $result;
		
	}
	
	public static function removeBreakLines($_string) {

		return preg_replace("/\n\r|\r\n|\n|\r/", '{{ break_line }}', $_string);

	}

	public static function htmlBreakLines($_string) {

		return preg_replace("/\n\r|\r\n|\n|\r/", '<br />', $_string);

	}

	public static function stripBreakLinesAndTabs($_string) {
		
		$result = self::stripBreakLines($_string);
		
		return str_replace("\t", '', $result);
		
	}
	
	public static function stripBreakLines($_string, $_flag=true) {

		if ($_flag)
			return preg_replace("/\n\r|\r\n|\n|\r/", '', $_string);
		else
			return preg_replace("/(\n\r|\r\n|\n|\r){2,}/", '', $_string);
			
	}

	private static $_disabled_magic_quotes_flag = false;

	private final static function _disable_magic_quotes() {

		if (self::$_disabled_magic_quotes_flag)
			return;

		if (get_magic_quotes_gpc()) {
			$process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
			while (list($key, $val) = each($process)) {
				foreach ($val as $k => $v) {
					unset($process[$key][$k]);
					if (is_array($v)) {
						$process[$key][stripslashes($k)] = $v;
						$process[] = &$process[$key][stripslashes($k)];
					} else {
						$process[$key][stripslashes($k)] = stripslashes($v);
					}
				}
			}
			unset($process);
		}

		self::$_disabled_magic_quotes_flag = true;

	}

	public static function getVariable($_var_name, $_method='POST', $_escape_html=false, $_strip_quotes=false) {

		self::_disable_magic_quotes();

		if (strtolower($_method) == 'get')
			$result = isset($_GET[$_var_name]) ? $_GET[$_var_name] : false;
		else
			$result = isset($_POST[$_var_name]) ? $_POST[$_var_name] : false;

		if ($result !== false && $_strip_quotes)
			$result = preg_replace('/\"|\'|\\\\/', '', $result);
			
		if ($result !== false) {
			if (is_array($result)) {
				array_walk_recursive($result, create_function('&$val', '$val = stripslashes($val);'));
				return $result;
			}
			else
				$result = stripslashes($result);
		}

		return $_escape_html ? self::escapeHtmlBrackets($result) : $result;

	}

	public static function mergeImages($_message, $_filenames, $_base_url) {

		$images = array();
		for ($i = 0; $i < count($_filenames); $i++)
			$images[] = '<img src="' . $_base_url . $_filenames[$i] . '" alt="" />';
		return self::mergeText($_message, $images);

	}

	public static function mergeText($_message, $_new_values_array) {

		$match_array = array();
		for ($i = 0; $i < count($_new_values_array); $i++)
			$match_array[] = "[$i]";
		return str_replace($match_array, $_new_values_array, $_message);

	}

	public static function mergeArrays($_array_1, $_array_2) {
		
	  foreach ($_array_2 as $key => $value) {
		
		if (!is_array($_array_1)) {
			continue;
			var_dump('Array 1 is not an array!');
			var_dump($_array_1);
			die();
		}		

		if (!is_array($_array_2)) {
			continue;
			var_dump('Array 2 is not an array!');
			var_dump($_array_2);
			die();
		}

		if (array_key_exists($key, $_array_1) && is_array($value))
		  $_array_1[$key] = self::mergeArrays($_array_1[$key], $_array_2[$key]);
		else
		  $_array_1[$key] = $value;

	  }

	  return $_array_1;

	}

	/**
	*
	* render object methods
	*
	*/

	protected static $_render_object_cache;
	
	protected static function _initialize_template_cache() {
		
		if (!is_array(self::$_render_object_cache))
			self::$_render_object_cache = array();
		
	}
	
	protected static function _set_template($_file, $_content) {
		
		self::$_render_object_cache[$_file] = $_content;
		
	}
	
	protected static function _get_template($_file) {
		
		return array_key_exists($_file, self::$_render_object_cache) ? self::$_render_object_cache[$_file] : false;
		
	}
	
	public static function renderObject($_object, $_options=null, $_htmlencode=false) {

		self::_initialize_template_cache();

		if ($_options['type'] == 'file')
			$template = PRISNA_SOCIAL_COUNTER__TEMPLATES . $_options['content'];
		else if ($_options['type'] == 'html')
			$html = $_options['content'];
		else {
			var_dump('--------');
			print_r($_options);
			var_dump('--------');
			return 'template type error';
		}

		if (array_key_exists('meta_tag_rules', $_options))
			$meta_tag_rules = $_options['meta_tag_rules'];
		else
			$meta_tag_rules = null;

		if (!is_array($_object)) {

			if ($_options['type'] == 'file') {
				
				$result = self::_get_template($template);
				
				if ($result !== false)
					self::_set_template($template, $result);
				else {
					ob_start();
					if (is_file($template))
						include $template;
					else {
						echo "$template does not exist!<br />";
						#var_dump('Error: ');
						#print_r($_options);
					}
					$result = ob_get_clean();
				}
				
			}
			else 
				$result = $html;

			if ($_object != null)
				foreach ($_object as $property => $value) 
					$result = self::stampCustomValue("{{ $property }}", $value, $result, $_htmlencode);

			if (is_array($meta_tag_rules))
				$result = self::displayHideMetaTags($_object, $meta_tag_rules, $result);

		} 
		else {

			$result = '';

			foreach ($_object as $single_object) {

				$temp_object = is_array($single_object) ? (object) $single_object : $single_object;

				$result .= self::renderObject($temp_object, $_options, $_htmlencode);
				
			}

		}

		return $result;

	}

	protected static function displayHideMetaTags($_object, $_meta_tag_rules, $_html) {

		$result = $_html;

		foreach ($_meta_tag_rules as $meta_tag_rule) {

			if (array_key_exists('property', $meta_tag_rule))
				$_expression = ($_object->{$meta_tag_rule['property']} == $meta_tag_rule['value']);
			else if (array_key_exists('expression', $meta_tag_rule)) 
				$_expression = $meta_tag_rule['expression'];

			$result = self::displayHideMetaTag($_expression, $meta_tag_rule['tag'], $result);

		}

		return $result;

	}

	public static function displayHideMetaTag($_expression, $_tag, $_html)	{

		if ($_expression) {
			$_html = self::displayHideBlock("$_tag.true", $_html, true);
			$_html = self::displayHideBlock("$_tag.false", $_html, false);
		} 
		else {
			$_html = self::displayHideBlock("$_tag.true", $_html, false);
			$_html = self::displayHideBlock("$_tag.false", $_html, true);
		}

		return $_html;

	}

	protected static function displayHideBlock($_name, $_html, $_state) {

		if ($_state) {

			$_names = array (
				"{{ $_name:begin }}",
				"{{ $_name:end }}"
			);
			$results = str_replace($_names, '', $_html);

		} 
		else {

			$occurrence_ini = strpos($_html, "{{ $_name:begin }}");
			$occurrence_end = strpos($_html, "{{ $_name:end }}", $occurrence_ini);
			$last_occurrence_ini = 0;
			$positions = array ();
			$results = $_html;

			while ((!PrisnaSocialCounterValidator::isEmpty($occurrence_ini)) && (PrisnaSocialCounterValidator::isInteger($occurrence_ini)) && (!PrisnaSocialCounterValidator::isEmpty($occurrence_end)) && (PrisnaSocialCounterValidator::isInteger($occurrence_end))) {
				$positions[] = array (
					$occurrence_ini,
					$occurrence_end
				);
				$occurrence_ini = strpos($_html, "{{ $_name:begin }}", $occurrence_end);
				$occurrence_end = strpos($_html, "{{ $_name:end }}", $occurrence_ini);
			}

			$_name_length = strlen("{{ $_name:end }}");
			$results = $_html;

			rsort($positions);

			foreach ($positions as $position) {
				$results = substr_replace($results, '', $position[0], $position[1] - $position[0] + $_name_length);
			}

		}

		return $results;

	}

	public static function stampCustomValue($_tag, $_value, $_html, $_htmlencode=false) {

		if (is_string($_value) || is_int($_value) || is_float($_value) || is_null($_value))
			$result = str_replace($_tag, $_htmlencode ? utf8_decode($_value) : $_value, $_html);
		else
			$result = $_html;

		return $result;

	}

}

class PrisnaSocialCounterUI extends WP_Widget {
	
	public function __construct() {
		
		parent::__construct(PrisnaSocialCounterConfig::getWidgetName(true), PrisnaSocialCounterConfig::getWidgetName(), array(
			'description' => sprintf(__('Add the %s.', 'prisna-social-counter'), PrisnaSocialCounterConfig::getName(false, true))
		));

	}
 
	public function form($_instance) {

		$result = '<p><table border="0" cellpadding="0" cellspacing="0" style="width: 100%">';

		$title = isset($_instance['title']) ? $_instance['title'] : '';
		$width = $this->_get_input_value('width', $_instance);
		$width_unit = $this->_get_input_value('width_unit', $_instance);
		
		$result .= '<tr><td style="padding-bottom: 2px;"><label for="' . $this->get_field_id('title') . '">' . __('Title:', 'prisna-social-counter') . '</label></td></tr>
					<tr><td><input class="widefat" id="' . $this->get_field_id('title') . '" name="'. $this->get_field_name('title') . '" type="text" value="' . esc_attr($title) . '" style="height: 28px;"/></td></tr>';

		$result .= '<tr><td style="padding: 10px 0 2px;"><label for="' . $this->get_field_id('width_unit') . '">' . __('Width:', 'prisna-social-counter') . '</label></td></tr>
			<tr><td>
				<table border="0" cellpadding="0" cellspacing="0">
					<tr>
						<td><input id="' . $this->get_field_id('width') . '" name="'. $this->get_field_name('width') . '" type="text" value="' . esc_attr($width) . '" style="width: 80px; height: 28px;" /></td>
						<td>&nbsp;</td>
						<td>
							<select id="' . $this->get_field_id('width_unit') . '" name="'. $this->get_field_name('width_unit') . '" style="height: 28px;">
								<option value="%"' . ($width_unit == '%' ? ' selected="selected"' : '') . '>%</option>
								<option value="px"' . ($width_unit == 'px' ? ' selected="selected"' : '') . '>' . __('pixels', 'prisna-social-counter') . '</option>
							</select>
						</td>
					</tr>
				</table>
			</td></tr>';

		$result .= '</table></p>';
		
		echo $result;

	}

	protected function _add_class($_html, $_class_name) {
		
		$result = $_html;
		
		$pattern = '/\bclass\=\".*?\"/';
		preg_match($pattern, $_html, $matches);
		
		if (empty($matches))
			$result = str_replace('>', ' class="' . $_class_name . '">', $result);
		else {
			$class_attribute = substr($matches[0], 0, -1) . ' ' . $_class_name . '"';
			$result = str_replace($matches[0], $class_attribute, $result);
		}

		return $result;
		
	}

	protected function _get_input_value($_name, $_instance) {
		
		$result = array_key_exists($_name, $_instance) ? $_instance[$_name] : null;
		
		switch ($_name) {
			case 'width':
				$result = (int) $result;
				if (empty($result))
					$result = 100;
				break;
			case 'width_unit':
				if (!in_array($result, array('%', 'px')))
					$result = '';
				break;			
		}
		
		return $result;
		
	}

	public function widget($_arguments, $_instance) {

		//$style = PrisnaSocialCounterConfig::getSettingValue('style');
		
		$title = array_key_exists('title', $_instance) ? apply_filters('widget_title', $_instance['title']) : null;
		$width = $this->_get_input_value('width', $_instance);
		$width_unit = $this->_get_input_value('width_unit', $_instance);

		$output_pre = '';
		$width_param = '';

		if ($width != 100 || $width_unit != '%')
			$width_param = ' width="' . $width . $width_unit . '"';

		extract($_arguments, EXTR_SKIP);

		if (!empty($title))
			$output_pre = $this->_add_class($_arguments['before_title'], 'prisna-social-counter-align-' . PrisnaSocialCounterConfig::getSettingValue('align_mode')) . $title . $_arguments['after_title'];
			
		$output_content = do_shortcode('[' . PrisnaSocialCounterConfig::getWidgetName(true) . ' widget_id="' . $_arguments['widget_id'] . '" widget="true"' . $width_param . ']');
		
		if (!empty($output_content))
			echo $before_widget . $output_pre . $output_content . $after_widget;
	
	}

	public static function isAvailable() {
	
		if (PrisnaSocialCounterConfig::getSettingValue('test_mode') == 'true' && !current_user_can('administrator'))
			return false;
		
		return true;
		
	}
	
	public static function _initialize_widget() {

		if (!self::isAvailable())
			return;

		register_widget('PrisnaSocialCounterUI');

	}

}

add_action('widgets_init', array('PrisnaSocialCounterUI', '_initialize_widget'));

class PrisnaSocialCounterValidator {

	public static function isInteger($_number) {

		if (!self::isEmpty($_number))
			return ((string) $_number) === ((string) (int) $_number);
		else
			return true;

	}

	public static function isEmpty($_string) {
	
		return (empty($_string) && strlen($_string) == 0);

	}

	public static function isBool($_string) {
	
		return ($_string === 'true' || $_string === 'false' || $_string === true || $_string === false);

	}

}

?>