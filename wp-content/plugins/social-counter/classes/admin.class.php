<?php

class PrisnaSocialCounterAdmin {

	public static function initialize() {

		if (!is_admin())
			return;

		add_action('admin_init', array('PrisnaSocialCounterAdmin', '_initialize'));
		add_action('admin_head', array('PrisnaSocialCounterAdmin', '_remove_messages'));
		add_action('plugins_loaded', array('PrisnaSocialCounterAdmin', 'initializeMenus'));
		add_filter('plugin_action_links', array('PrisnaSocialCounterAdmin', 'addSettingsLink'), 10, 2);

	}
	
	public static function _initialize() {

		self::_watch_events();

		self::_select_language();
		
		self::_load_styles();
		self::_load_scripts();
		
	}

	public static function addSettingsLink($_links, $_file) {

		if (PrisnaSocialCounterCommon::endsWith($_file, 'social-counter.php')) {
			$link = '<a href="plugins.php?page=' . PrisnaSocialCounterConfig::getAdminHandle() . '">' . __('Settings', 'prisna-social-counter') . '</a>';
			$_links[] = $link;
		}
		
		return $_links;
		
	}
	
	protected static function _watch_events() {

		@header('X-XSS-Protection: 0');

		if (PrisnaSocialCounterAdminEvents::isSavingSettings() || PrisnaSocialCounterAdminEvents::isResetingSettings())
			if (!check_admin_referer(PrisnaSocialCounterConfig::getAdminHandle(), '_prisna_social_counter_nonce'))
				PrisnaSocialCounterCommon::redirect(PrisnaSocialCounterCommon::getAdminPluginUrl());

		if (PrisnaSocialCounterAdminEvents::isSavingSettings())
			self::_save_settings();

		if (PrisnaSocialCounterAdminEvents::isResetingSettings())
			self::_reset_settings();

	}
	
	protected static function _save_settings() {

		PrisnaSocialCounterAdminForm::save();
		
	}
	
	protected static function _reset_settings() {
		
		PrisnaSocialCounterAdminForm::reset();
		
	}

	protected static function _load_scripts() {

		if (PrisnaSocialCounterAdminEvents::isLoadingAdminPage()) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('jquery-ui-widget');
			wp_enqueue_script('jquery-ui-mouse');
			wp_enqueue_script('jquery-ui-sortable');
			//wp_register_script( 'arqam-admin-scripts', plugins_url('assets/js/admin.js', __FILE__) , array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget', 'jquery-ui-mouse', 'jquery-ui-sortable', 'postbox', 'post' ), false, true ); 
			wp_enqueue_script('prisna-social-counter-admin-common', PRISNA_SOCIAL_COUNTER__JS .'/common.class.js', 'jquery-ui-core', PrisnaSocialCounterConfig::getVersion(), true);
			wp_enqueue_script('prisna-social-counter-admin', PRISNA_SOCIAL_COUNTER__JS .'/admin.class.js', array(), PrisnaSocialCounterConfig::getVersion());
		}

	}
	
	protected static function _load_styles() {

		if (PrisnaSocialCounterAdminEvents::isLoadingAdminPage() || strpos(PrisnaSocialCounterCommon::getAdminWidgetsUrl(), $_SERVER['REQUEST_URI']) !== false)
			wp_enqueue_style('prisna-social-counter-admin', PRISNA_SOCIAL_COUNTER__CSS .'/admin.css', false, PrisnaSocialCounterConfig::getVersion(), 'screen');

	}
	
	public static function _remove_messages() {
	
		if (PrisnaSocialCounterAdminEvents::isLoadingAdminPage() || strpos(PrisnaSocialCounterCommon::getAdminWidgetsUrl(), $_SERVER['REQUEST_URI']) !== false)	
			PrisnaSocialCounterCommon::renderCSS('.update-nag,div.updated,div.error,.notice{display:none !important}');
		
	}
	
	public static function initializeMenus() {

		add_action('admin_menu', array('PrisnaSocialCounterAdmin', '_add_options_page'));

	}

	public static function _add_options_page() {
		
		add_submenu_page('plugins.php', PrisnaSocialCounterConfig::getName(false, true), PrisnaSocialCounterConfig::getName(false, true), 'manage_options', PrisnaSocialCounterConfig::getAdminHandle(), array('PrisnaSocialCounterAdmin', '_render_main_form'));
		
	}

	protected static function _gen_meta_tag_rules_for_tabs() {
		
		$tabs = array(
			array('general', 'advanced', 'usage', 'premium'),
			array('advanced_general', 'advanced_import_export')
		);

		$current_tabs = array(
			PrisnaSocialCounterCommon::getVariable('prisna_tab', 'POST'),
			PrisnaSocialCounterCommon::getVariable('prisna_tab_2', 'POST')
		);

		$result = self::_gen_meta_tag_rules_for_tabs_aux($tabs, $current_tabs);

		return $result;
		
	}
	
	protected static function _gen_meta_tag_rules_for_tabs_aux($_tabs, $_currents, $_level=0) {
		
		$result = array();

		if (!is_array($_tabs[0])) {

			$current = $_currents[$_level];

			if (PrisnaSocialCounterValidator::isEmpty($current))
				$current = $_tabs[0];

			for ($i=0; $i<count($_tabs); $i++)
				$result[] = array(
					'expression' => $_tabs[$i] == $current,
					'tag' => $_tabs[$i] . '.show'
				);

		}
		else 
			for ($j=0; $j<count($_tabs); $j++) 
				$result = array_merge($result, self::_gen_meta_tag_rules_for_tabs_aux($_tabs[$j], $_currents, $j));

		return $result;		
		
	}
	
	public static function _render_main_form() {

		$form = new PrisnaSocialCounterAdminForm();

		echo $form->render(array(
			'type' => 'file',
			'content' => '/admin/main_form.tpl',
			'meta_tag_rules' => self::_gen_meta_tag_rules_for_tabs()
		));
	
	}

	protected static function _select_language() {

		load_plugin_textdomain('prisna-social-counter', false, dirname(plugin_basename(__FILE__)) . '/../languages');

	}
	
}

class PrisnaSocialCounterAdminBaseForm extends PrisnaSocialCounterItem {
	
	public $title_message;
	public $saved_message;
	public $save_button_message;
	public $reset_message;
	public $reset_button_message;
	public $reseted_message;
	
	protected $_fields;
	
	public function __construct() {
		
		$this->title_message = __('Social Counter', 'prisna-social-counter');
		$this->saved_message = __('Settings saved.', 'prisna-social-counter');
		$this->reseted_message = __('Settings reseted.', 'prisna-social-counter');
		$this->reset_message = __('All the settings will be reseted and restored to their default values. Do you want to continue?', 'prisna-social-counter');
		$this->save_button_message = __('Save changes', 'prisna-social-counter');
		$this->reset_button_message = __('Reset settings', 'prisna-social-counter');

	}
	
	public static function commit($_name, $_result) {
		
		self::_commit($_name, $_result);
		
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
	
	public function render($_options, $_html_encode=false) {
		
		return parent::render($_options, $_html_encode);
		
	}
	
	protected function _prepare_settings() {}
	protected function _set_fields() {}
	
}

class PrisnaSocialCounterAdminForm extends PrisnaSocialCounterAdminBaseForm {
	
	public $group_1;
	public $group_2;
	public $group_3;

	public $group_100;
	public $group_101;
	public $group_102;

	public $nonce;
	
	public $tab;
	public $tab_2;
	
	public $general_message;
	public $advanced_message;
	public $advanced_general_message;
	public $advanced_import_export_message;
	public $usage_message;
	public $premium_message;
	
	public $advanced_import_success_message;
	public $advanced_import_fail_message;
	public $wp_version_check_fail_message;
	
	protected static $_imported_status;
	
	public function __construct() {
		
		parent::__construct();
		
		$this->general_message = __('Networks', 'prisna-social-counter');

		$this->advanced_message = __('Advanced', 'prisna-social-counter');
		$this->advanced_general_message = __('General', 'prisna-social-counter');
		$this->usage_message = __('Usage', 'prisna-social-counter');
		$this->premium_message = __('Premium', 'prisna-social-counter');
		$this->advanced_import_export_message = __('Import / Export', 'prisna-social-counter');
		$this->advanced_import_success_message = __('Settings succesfully imported.', 'prisna-social-counter');
		$this->advanced_import_fail_message = __('There was a problem while importing the settings. Please make sure the exported string is complete. Changes weren\'t saved.', 'prisna-social-counter');
		$this->wp_version_check_fail_message = sprintf(__('Social Counter requires WordPress version %s or later.', 'prisna-social-counter'), PRISNA_SOCIAL_COUNTER__MINIMUM_WP_VERSION);

		$this->nonce = wp_nonce_field(PrisnaSocialCounterConfig::getAdminHandle(), '_prisna_social_counter_nonce');

		$this->_set_fields();

	}
	
	public static function getImportedStatus() {
		
		return self::$_imported_status;
		
	}
	
	protected static function _set_imported_status($_status) {
	
		self::$_imported_status = $_status;
		
	}

	protected static function _import() {
		
		$settings = PrisnaSocialCounterConfig::getDefaults(true);
		$key = $settings['import']['id'];
		
		$value = PrisnaSocialCounterCommon::getVariable($key, 'POST');
		
		if ($value === false || PrisnaSocialCounterValidator::isEmpty($value))
			return null;
		
		$decode = base64_decode($value);
		
		if ($decode === false) {
			self::_set_imported_status(false);
			return false;
		}
		
		$unserialize = @unserialize($decode);

		if (!is_array($unserialize)) {
			self::_set_imported_status(false);
			return false;
		}
		
		$result = array();

		foreach ($settings as $key => $setting) {
			
			if (in_array($key, array('import', 'export')))
				continue;
			
			if (array_key_exists($key, $unserialize))
				$result[$key] = $unserialize[$key];

		}

		if (count($result) == 0) {
			self::_set_imported_status(false);
			return false;
		}

		self::_commit(PrisnaSocialCounterConfig::getDbSettingsName(), $result);		
		self::_set_imported_status(true);
		
		return true;
		
	}
	
	protected static function _get_social_value($_id) {
		
		$result = array(
			'enabled' => false,
			'name' => false,
			'current' => false,
			'icon_color' => false,
			'text_color' => false,
			'background_color' => false,
			'unit' => false,
			'format' => false
		);

		foreach ($result as $key => $foo)
			$result[$key] = PrisnaSocialCounterCommon::getVariable($_id . '_' . $key, 'POST');

		if (!empty($result['name']))
			$result['name'] = PrisnaSocialCounterSocialField::extractName($result['name'], $_id);

		if (!empty($result['current']))
			$result['current'] = is_numeric($result['current']) ? preg_replace('/[,.]/', '', $result['current']) : '';

		return $result;

	}
	
	public static function save() {
		
		if (!is_null(self::_import()))
			return;

		$settings = PrisnaSocialCounterConfig::getDefaults();
		$result = array();

		foreach ($settings as $key => $setting) {
			
			$value = PrisnaSocialCounterCommon::getVariable($setting['id'], 'POST');
			
			switch ($key) {
				case 'current': {
					break;
				}
				case 'import':
				case 'export': {
					continue;
					break;
				}
				default: {

					if ($setting['type'] == 'social') {
						$value = self::_get_social_value($setting['id']);
						$result[$key] = array('value' => $value);
					}

					if (PrisnaSocialCounterCommon::endsWith($key, '_class'))
						$value = trim(PrisnaSocialCounterCommon::cleanId($value));

					$unset_template = PrisnaSocialCounterCommon::endsWith($key, '_template') && PrisnaSocialCounterCommon::stripBreakLinesAndTabs($value) == PrisnaSocialCounterCommon::stripBreakLinesAndTabs($setting['value']);

					if (!$unset_template && $value !== false && $value != $setting['value'])
						$result[$key] = array('value' => $value);
					else
						unset($result[$key]);
					break;

				}
			}
		}

		$result['current'] = array(
			'value' => self::_set_current(PrisnaSocialCounterConfig::getSettingValue('current'), $result)
		);

		self::_commit(PrisnaSocialCounterConfig::getDbSettingsName(), $result);

	}
	
	protected static function _set_current($_current, $_values) {

		$_settings = PrisnaSocialCounterConfig::getSettingsValues();
		$_defaults = PrisnaSocialCounterConfig::getDefaults();

		foreach ($_defaults as $key => $setting) {

			if (!array_key_exists('type', $setting) || $setting['type'] != 'social')
				continue;

			$setting = array_key_exists($key, $_settings) ? $_settings[$key] : array('value' => array('name' => '', 'current' => ''));

			if ($setting['value']['current'] != 
			$_values[$key]['value']['current']) {
				
				if ($_values[$key]['value']['current'] != '')
					if (array_key_exists($_values[$key]['value']['name'], $_current[$key]))
						$_current[$key][$_values[$key]['value']['name']]['value'] = (int) $_values[$key]['value']['current'];
				
			}

			if ($setting['value']['name'] != $_values[$key]['value']['name'] || $_values[$key]['value']['current'] == '')
				if (array_key_exists($_values[$key]['value']['name'], $_current[$key]))
					unset($_current[$key][$_values[$key]['value']['name']]);

		}

		return $_current;
		
	}
	
	public static function reset() {
		
		if (get_option(PrisnaSocialCounterConfig::getDbSettingsName()))
			delete_option(PrisnaSocialCounterConfig::getDbSettingsName());

	}

	public function render($_options, $_html_encode=false) {
		
		$this->_prepare_settings();

		$is_importing = PrisnaSocialCounterAdminEvents::isSavingSettings() && PrisnaSocialCounterValidator::isBool(self::getImportedStatus());

		if (!array_key_exists('meta_tag_rules', $_options))
			$_options['meta_tag_rules'] = array();

		$_options['meta_tag_rules'][] = array(
			'expression' => PrisnaSocialCounterAdminEvents::isSavingSettings() && !$is_importing,
			'tag' => 'just_saved'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => $is_importing && self::getImportedStatus(),
			'tag' => 'just_imported_success'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => $is_importing && !self::getImportedStatus(),
			'tag' => 'just_imported_fail'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => !version_compare($GLOBALS['wp_version'], PRISNA_SOCIAL_COUNTER__MINIMUM_WP_VERSION, '<'),
			'tag' => 'wp_version_check'
		);

		$_options['meta_tag_rules'][] = array(
			'expression' => PrisnaSocialCounterAdminEvents::isResetingSettings(),
			'tag' => 'just_reseted'
		);
		
		return parent::render($_options, $_html_encode);

	}

	protected function _set_fields() {
		
		if (is_array($this->_fields))
			return;
			
		$this->_fields = array();
			
		$settings = PrisnaSocialCounterConfig::getSettings(true);
		
		foreach ($settings as $key => $setting) { 
			
			if (!array_key_exists('type', $setting))
				continue;
			
			$field_class = 'PrisnaSocialCounter' . ucfirst($setting['type']) . 'Field';
			
			if ($field_class == 'PrisnaSocialCounterField')
				continue;
			
			$this->_fields[$key] = new $field_class($setting);
		}
		
	}
	
	protected function _prepare_settings() {
		
		$networks_order = PrisnaSocialCounterConfig::getSettingValue('order');
		
		$networks_order = empty($networks_order) ? array() : explode(',', $networks_order);
		
		$groups = array(
			array(1, 3),
			array(99, 102)
		);
		
		for ($i=0; $i<count($groups); $i++) {
			for ($j=$groups[$i][0]; $j<=$groups[$i][1]; $j++) {

				$k = $j > 98 ? $j : (empty($networks_order) ? $j : $networks_order[$j-1]);

				$partial = array();
				
				foreach ($this->_fields as $key => $field) {
					if ($field->group == $k) {
						$field->satisfyDependence($this->_fields);
						$partial[] = $field->output();
					}
				}

				$group = 'group_' . $j;
				
				$join = implode("\n", $partial);
				
				$this->{$group} = $k < 99 ? '<div class="prisna_social_counter_network_sortable">' . $join . '</div>' : $join;
				
			}
		}
		
		$tab = PrisnaSocialCounterCommon::getVariable('prisna_tab', 'POST');
		$this->tab = $tab !== false ? $tab : '';

		$tab_2 = PrisnaSocialCounterCommon::getVariable('prisna_tab_2', 'POST');
		$this->tab_2 = $tab_2 !== false ? $tab_2 : '';

	}

}

class PrisnaSocialCounterAdminEvents {

	public static function isLoadingAdminPage() {
		
		return in_array(PrisnaSocialCounterCommon::getVariable('page', 'GET'), array(PrisnaSocialCounterConfig::getAdminHandle()));
		
	}
	
	public static function isSavingSettings() {
		
		return PrisnaSocialCounterCommon::getVariable('prisna_social_counter_admin_action', 'POST') === 'prisna_social_counter_save_settings';
		
	}
	
	public static function isResetingSettings() {
		
		return PrisnaSocialCounterCommon::getVariable('prisna_social_counter_admin_action', 'POST') === 'prisna_social_counter_reset_settings';
		
	}

}

PrisnaSocialCounterAdmin::initialize();

?>