<?php

class PrisnaSocialCounter {

	public static function initialize() {

		add_shortcode(PrisnaSocialCounterConfig::getWidgetName(true), array('PrisnaSocialCounter', '_create_shortcode'));
		add_action('wp_enqueue_scripts',array('PrisnaSocialCounter', '_enqueue_stylesheets'));
		
	}

	public static function _enqueue_stylesheets() {
	
		wp_enqueue_style('prisna-social-counter', PRISNA_SOCIAL_COUNTER__CSS . '/prisna-social-counter.css'); 
	
	}

	public static function _create_shortcode($_options) {

		if (!self::isAvailable())
			return;

		$settings = PrisnaSocialCounterConfig::getSettings(false);

		$counter = new PrisnaSocialCounterOutput((object) $settings, $_options);

		return $counter->render(array(
			'type' => 'file',
			'content' => '/main.tpl'
		));
		
	}
	
	public static function isAvailable() {

		if (is_admin())
			return false;

		if (PrisnaSocialCounterConfig::getSettingValue('test_mode') == 'true' && !current_user_can('administrator'))
			return false;

		return true;
		
	}
	
}

class PrisnaSocialCounterInterface {
	
	protected $_networks;
	
	public function __construct() {
		
		$this->_networks = array();
		
	}
	
	public function add($_network, $_value, $_current) {
	
		$this->_networks[$_network] = array(
			'name' => $_value,
			'status' => null,
			'result' => null
		);
	
	}
	
	protected function _get_cached() {
		
		$expire = (int) PrisnaSocialCounterConfig::getSettingValue('expire');
		$data = PrisnaSocialCounterConfig::getSettingValue('current');
		
		foreach ($this->_networks as $network => $properties)
			if (array_key_exists($network, $data) && is_array($data[$network]))
				if (array_key_exists($properties['name'], $data[$network])) {
					$value = (int) $data[$network][$properties['name']]['value'];
					$date = (int) $data[$network][$properties['name']]['date'];

					$expire_date = $date + $expire * 60 * 60;
					$status = $expire_date > time() ? 'db' : 'expired';

					$this->_networks[$network]['result'] = $value;
					$this->_networks[$network]['status'] = $status;
				}
	}

	protected function _get_remote() {
		
		$data = array();
		
		foreach ($this->_networks as $network => $properties)
			if ($properties['status'] != 'db')
				$data[$network] = $properties['name'];

		if (empty($data))
			return;

		$remote = $this->_connect($data);
		
		if (empty($remote) || (is_object($remote) && !$remote->success)) {
			foreach ($data as $network => $name)
				$this->_networks[$network]['status'] = 'fail';
			return;
		}

		if (is_object($remote) && property_exists($remote, 'result') && is_object($remote->result))
			foreach ($remote->result as $network => $count)
				if ($count != false) {
					$this->_networks[$network]['result'] = (int) $count;
					$this->_networks[$network]['status'] = 'success';
				}
				else
					$this->_networks[$network]['status'] = 'fail';

	}

	protected function _connect($_data) {
		
		$url = 'https://social.prisna.net/';
		
		$data = array();
		foreach ($_data as $network => $name)
			$data[] = 'nt[]=' . urlencode($network) . '&nm[]=' . urlencode($name);
		
		$response = wp_remote_post($url, array(
			'method' => 'POST',
			'timeout' => 15,
			'body' => implode('&', $data)
			)
		);

		if (is_wp_error($response))
			return false;
		else {
			$result = @json_decode($response['body']);
			return empty($result) ? false : $result;
		}
		
	}
	
	protected function _save() {
		
		$settings = PrisnaSocialCounterConfig::getSettingsValues();
		
		if (!array_key_exists('current', $settings))
			$settings['current'] = array();
		
		$flag = false;
		
		foreach ($this->_networks as $network => $properties)
			if ($properties['status'] == 'success' || $properties['status'] == 'fail') {
				
				if (!array_key_exists($network, $settings['current']))
					$settings['current'][$network] = array();

				$count = $properties['result'];

				$settings['current']['value'][$network][$properties['name']] = array(
					'value' => empty($count) ? 0 : $count,
					'date' => time()
				);
				
				if ($network == 'facebook' && $properties['name'] == 'PrisnaLtd') {
					
				}
				else {
				
					if (!empty($count))
						$settings[$network]['value']['current'] = $count;

				}
				
				$flag = true;
				
			}

		if ($flag == true)
			self::_commit(PrisnaSocialCounterConfig::getDbSettingsName(), $settings);
		
	}

	protected static function _commit($_name, $_result) {

		if (!get_option($_name))
			add_option($_name, $_result);
		else
			update_option($_name, $_result);
		
		if (!get_option($_name)) {
			delete_option($_name);
			add_option($_name, $_result);
		}

	}

	public function process() {
		
		$this->_get_cached();
		$this->_get_remote();
		$this->_save();
		
		return $this->_networks;
		
	}
	
}

class PrisnaSocialCounterOutput extends PrisnaSocialCounterItem {
	
	protected static $_id;
	
	protected $_interface;
	
	protected $_networks;
	protected $_options;
	
	public $align_mode;
	public $custom_css;
	public $dynamic_css;
	
	public $content_formatted;

	protected static $_separation_flag;

	public function __construct($_properties, $_options) {

		$this->_properties = $_properties;
		$this->_options = is_array($_options) ? $_options : array();
		$this->_set_properties();

	}
	
	public function setProperty($_property, $_value) {

		return $this->{$_property} = $_value['value'];

	}
	
	public function getNetworks() {

		if (!is_null($this->_networks))
			return $this->_networks;

		$networks_order = PrisnaSocialCounterConfig::getSettingValue('order');
		$networks_order = empty($networks_order) ? array() : explode(',', $networks_order);
		
		$this->_networks = array();

		foreach ($this->_properties as $name => $setting)
			if (array_key_exists('type', $setting) && $setting['type'] == 'social') {
				if (empty($networks_order))
					$this->_networks[] = $name;
				else
					$this->_networks[$networks_order[$setting['group']-1]-1] = $name;
			}

		return $this->_networks;
		
	}
	
	public function isValidNetwork($_name) {
		
		return in_array($_name, $this->getNetworks());
		
	}
	
	protected function _get_selected_networks() {

		if (array_key_exists('network', $this->_options))
			if (self::isValidNetwork($this->_options['network']))
				return array($this->_options['network']);

		$result = array();
		$networks = self::getNetworks();

		ksort($networks);

		foreach ($networks as $network)
			$result[] = $network;

		return $result;

	}
	
	protected function _gen_widget_class($_columns) {

		$columns = $_columns > 1 ? 'columns-' . $_columns : 'single';
		$class = ' prisna-social-counter-' . $columns;
		
		if (array_key_exists('widget', $this->_options))
			if ($this->_options['widget'] == 'true')
				$class .= ' prisna-social-counter-in-widget';

		if (in_array($this->_properties->align_mode['value'], array('left', 'right')))
			$class .= ' prisna-social-counter-align-' . $this->_properties->align_mode['value'];
		
		return $class;
		
	}
	
	protected function _get_sub_content_object($_html_encode) {
		
		$info = $this->_get_sub_content($_html_encode);
		$content = $info['content'];
		
		$columns = count($info['networks']);
		
		$result = array(
			'content' => empty($content) ? false : $content,
			'align_mode' => $this->_properties->align_mode['value'],
			'widget' => $this->_gen_widget_class($columns),
			'networks' => $info['networks']
		);
		
		return (object) $result;
		
	}
	
	protected function _format_count($_value, $_format) {
		
		$value = (int) $_value;
		
		switch ($_format) {
			case 'comma':
				$result = number_format($value);
				break;
			case 'rounded':
			case 'rounded_one':
			case 'rounded_two':
			
				if ($_format == 'rounded_one')
					$precision = 1;
				else if ($_format == 'rounded_two')
					$precision = 2;
				else
					$precision = 0;

				if ($value < 1000)
					$result = $value;
				else if ($value < 1000000)
					$result = number_format($value/1000, $precision) . 'K';
				else if ($value < 1000000000)
					$result = number_format($value/1000000, $precision) . 'M';
				else 
					$result = number_format($value/1000000000, $precision) . 'G';
				break;
			default:
				$result = $value;
				break;
		}

		return $result;
		
	}
	
	protected function _get_interface() {
		
		if (empty($this->_interface))
			$this->_interface = new PrisnaSocialCounterInterface();
		
		return $this->_interface;
		
	}
	
	protected function _process_network_count() {
		
		return $this->_get_interface()->process();
		
	}
	
	protected function _add_network_count($_network, $_name, $_current) {

		$this->_get_interface()->add($_network, $_name, $_current);
		
	}
	
	protected function _get_network_unit($_network, $_value) {
		
		$mx = array(
			'none' => '',
			'likes' => __('Likes', 'prisna-social-counter'),
			'fans' => __('Fans', 'prisna-social-counter'),
			'followers' => __('Followers', 'prisna-social-counter')
		);
		
		return array_key_exists($_value, $mx) ? $mx[$_value] : '';
		
	}
	
	protected function _get_network_url($_network, $_value) {
		
		$mx = array(
			'facebook' => 'https://www.facebook.com/{{ name }}/',
			'twitter' => 'https://twitter.com/{{ name }}',
			'google' => 'https://plus.google.com/{{ name }}'
		);
		
		if ($_network == 'google' && !is_numeric($_value))
			$_value = '+' . $_value;
		
		return str_replace('{{ name }}', $_value, array_key_exists($_network, $mx) ? $mx[$_network] : '');
		
	}
	
	protected function _validate_network($_network, $_properties) {
		
		$name = $_properties['name'];
		
		if (empty($name))
			return false;
			
		$enabled = $_properties['enabled'];

		if ($enabled != 'true')
			return false;		
			
		return true;
		
	}
	
	protected static function _gen_id() {
		
		if (empty(self::$_id))
			self::$_id = 0;
		
		self::$_id++;
		
		return self::$_id;
		
	}
	
	protected function _get_network_properties($_network) {
		
		$result = $this->_properties->{$_network}['value'];
		
		$result['id'] = self::_gen_id();
		
		foreach ($result as $name => $value)
			if (array_key_exists($name, $this->_options))
				$result[$name] = $this->_options[$name];
				
		return $result;

	}
	
	protected function _get_sub_content($_html_encode) {

		$result = array(
			'content' => array(),
			'networks' => array()
		);
		
		$networks = $this->_get_selected_networks();
				
		$data = array();
		
		foreach ($networks as $network) {

			$properties = $this->_get_network_properties($network);
			
			$name = $properties['name'];
			$unit = $properties['unit'];
			$current = $properties['current'];
			$format = $properties['format'];
			
			if (!$this->_validate_network($network, $properties))
				continue;
			
			$result['networks'][$network] = $properties;
			
			$data[$network] = (object) array(
				'id' => $properties['id'],
				'name' => $name,
				'format' => $format,
				'current' => $current,
				'network' => $network,
				'network_url' => $this->_get_network_url($network, $name),
				'count' => $current,
				'unit' => $this->_get_network_unit($network, $unit)
			);
		
			$this->_add_network_count($network, $name, $current);
			
		}			
			
		$counters = $this->_process_network_count();
		foreach ($counters as $network => $count) {
			$count_result = $count['status'] != 'fail' && $count['result'] != 0 ? $count['result'] : $data[$network]->current;
			$data[$network]->count = $this->_format_count($count_result, $data[$network]->format);
		}
			
		foreach ($data as $network => $single)
			$result['content'][] = PrisnaSocialCounterCommon::renderObject($single, array(
				'type' => 'html',
				'content' => $this->_properties->network_template['value']
			), $_html_encode);
		
		$result['content'] = implode('', $result['content']);
		
		return $result;
		
	}

	public static function genColor($_value) {
		
		if (PrisnaSocialCounterCommon::startsWith($_value, '#'))
			return $_value;
		
		if ($_value == 'transparent')
			return $_value;
		
		return '#' . $_value;
		
	}
	
	protected function _is_widget() {
		
		if (!array_key_exists('widget', $this->_options))
			return false;
		
		return $this->_options['widget'] == 'true';
		
	}
	
	protected function _gen_separation_css($_separation, $_data) {

		if (!empty(self::$_separation_flag))
			return '';

		self::$_separation_flag = true;

		$result = '';

		$partial = array();

		$columns = count($_data);
		
		switch ($columns) {
			case 2:
				$width = (100 - $_separation) / 2;
				break;
			case 3:
			$width = (100 - ($_separation * 2)) / 3;
				break;
			default:
				return $result;
		}
		
		$width = intval($width * 100) / 100;
		
		$partial['.prisna-social-counter-columns-' . $columns . ' .prisna-social-counter-network'] = 'width: ' . $width . '% !important; margin-bottom: ' . $_separation . '% !important;';
		$partial['.prisna-social-counter-columns-' . $columns . ' .prisna-social-counter-network:nth-child(' . $columns . 'n+2)'] = 'margin-left: ' . $_separation . '% !important;' . ($columns == 3 ? 'margin-right: ' . $_separation . '% !important;' : '');

		foreach ($partial as $selector => $rules)
			$result .= "$selector {\n\t$rules\n}\n";
		
		return $result;

	}
	
	protected function _gen_rounded_corners_css($properties) {

		$result = '';
		
		if (!array_key_exists('rounded_corners', $this->_options))
			return $result;

		$rounded_corners = (int) $this->_options['rounded_corners'];

		if (empty($rounded_corners))
			return $result;

		$partial = array();
		
		$id = $properties['id'];
		
		$partial['#prisna-social-counter-network-' . $id . ' a'] = '-webkit-border-radius: ' . $rounded_corners . 'px; -moz-border-radius: ' . $rounded_corners . 'px; border-radius: ' . $rounded_corners . 'px;';

		foreach ($partial as $selector => $rules)
			$result .= "$selector {\n\t$rules\n}\n";
		
		return $result;
		
	}
	
	protected function _gen_width_css($properties) {

		$result = '';
		
		if (!array_key_exists('width', $this->_options))
			return $result;

		$partial = array();
		
		$id = array_key_exists('widget_id', $this->_options) ? $this->_options['widget_id'] : 'prisna-social-counter-network-' . $properties['id'];
		
		if ($this->_is_widget())
			$partial['#' . $id . ' .prisna-social-counter'] = 'width: ' . $this->_options['width'] . ' !important';
		else
			$partial['#' . $id . '.prisna-social-counter-network'] = 'width: ' . $this->_options['width'] . ' !important';

		foreach ($partial as $selector => $rules)
			$result .= "$selector {\n\t$rules\n}\n";
		
		return $result;
		
	}
	
	protected function _gen_network_css($network, $properties) {
		
		$result = '';
		$partial = array();
		
		$id = $properties['id'];
		
		$parent_css = 'background-color: ' . self::genColor($properties['background_color']) . ' !important;';
		
		$rounded_corners = PrisnaSocialCounterConfig::getSettingValue('rounded_corners');
		if (!empty($rounded_corners))
			$parent_css .= '-webkit-border-radius: ' . $rounded_corners . 'px; -moz-border-radius: ' . $rounded_corners . 'px; border-radius: ' . $rounded_corners . 'px;';
		
		$partial['#prisna-social-counter-network-' . $id . ' a'] = $parent_css;
		
		$partial['#prisna-social-counter-network-' . $id . ' a .prisna-social-counter-icon'] = 'color: ' . self::genColor($properties['icon_color']) . ' !important;';
		$partial['#prisna-social-counter-network-' . $id . ' a .prisna-social-counter-value, #prisna-social-counter-network-' . $id . ' a .prisna-social-counter-unit'] = 'color: ' . self::genColor($properties['text_color']) . ' !important;';
		
		foreach ($partial as $selector => $rules)
			$result .= "$selector {\n\t$rules\n}\n";
		
		return $result;
		
	}
	
	protected function _gen_dynamic_css($_data) {
		
		$result = array();

		foreach ($_data as $network => $properties) {

			$result[] = $this->_gen_network_css($network, $properties);

			if (array_key_exists('width', $this->_options))
				$result[] = $this->_gen_width_css($properties);

			if (array_key_exists('rounded_corners', $this->_options))
				$result[] = $this->_gen_rounded_corners_css($properties);

		}

		$result[] = $this->_gen_separation_css((int) PrisnaSocialCounterConfig::getSettingValue('separation'), $_data);

		$this->dynamic_css = implode('', $result);
		
	}
	
	public function render($_options, $_html_encode=false) {

		$data = $this->_get_sub_content_object($_html_encode);

		if (empty($data->content))
			return '';

		$this->_gen_dynamic_css($data->networks);

		$this->content_formatted = PrisnaSocialCounterCommon::renderObject($data, array(
			'type' => 'html',
			'content' => $this->_properties->container_template['value']
		), $_html_encode);

		return parent::render($_options, $_html_encode);
		
	}

}

PrisnaSocialCounter::initialize();

?>
