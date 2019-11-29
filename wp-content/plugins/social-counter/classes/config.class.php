<?php
 
class PrisnaSocialCounterConfig {
	
	const NAME = 'PrisnaSocialCounter';
	const UI_NAME = 'Social Counter';
	const WIDGET_NAME = 'Prisna Social Counter';
	const WIDGET_INTERNAL_NAME = 'prisna-social-counter';
	const ADMIN_SETTINGS_NAME = 'prisna-social-counter-settings';
	const ADMIN_SETTINGS_IMPORT_EXPORT_NAME = 'prisna-social-counter-plugin-import-export-settings';
	const DB_SETTINGS_NAME = 'prisna-social-counter-settings';
	
	protected static $_settings = null;

	public static function getName($_to_lower=false, $_ui=false) {
		
		if ($_ui)
			return $_to_lower ? strtolower(self::UI_NAME) : self::UI_NAME;
		else
			return $_to_lower ? strtolower(self::NAME) : self::NAME;
		
	}

	public static function getWidgetName($_internal=false) {
	
		return $_internal ? self::WIDGET_INTERNAL_NAME : self::WIDGET_NAME;
		
	}

	public static function getVersion() {
	
		return PRISNA_SOCIAL_COUNTER__VERSION;
		
	}	

	public static function getAdminHandle() {
		
		return self::ADMIN_SETTINGS_NAME;
		
	}

	public static function getAdminImportExportHandle() {
		
		return self::ADMIN_SETTINGS_IMPORT_EXPORT_NAME;
		
	}

	public static function getDbSettingsName() {
		
		return self::DB_SETTINGS_NAME;
		
	}

	protected static function _get_settings() {
		
		$option = get_option(self::getDbSettingsName());
		return !$option ? array() : $option;
		
	}
	
	public static function getSettings($_force=false, $_direct=false) {
		
		if (is_array(self::$_settings) && $_force == false)
			return self::$_settings;
		
		$current = self::_get_settings();

		if ($_direct)
			return $current;

		$defaults = self::getDefaults();

		$result = PrisnaSocialCounterCommon::mergeArrays($defaults, $current);

		return self::$_settings = $result;
		
	}

	public static function getSetting($_name, $_force=false) {
		
		$settings = self::getSettings($_force);
		
		return array_key_exists($_name, $settings) ? $settings[$_name] : null;
		
	}

	protected static function _compare_settings($_id, $_setting_1, $_setting_2) {
		
		if (PrisnaSocialCounterCommon::endsWith($_id, '_template') || PrisnaSocialCounterCommon::endsWith($_id, '_template_dd'))
			return PrisnaSocialCounterCommon::stripBreakLinesAndTabs($_setting_1['value']) == PrisnaSocialCounterCommon::stripBreakLinesAndTabs($_setting_2['value']);
		
		if ($_id == 'override')
			if ($_setting_1['value'] != $_setting_2['value'] && PrisnaSocialCounterValidator::isEmpty($_setting_1['value']))
				return true;
		
		if ($_id == 'languages')
			return $_setting_1['value'] === $_setting_2['value'];
			
		return $_setting_1['value'] == $_setting_2['value'];
		
	}
	
	protected static function _get_settings_values_for_export() {
		
		$settings = self::_get_settings();
		
		return count($settings) > 0 ? base64_encode(serialize($settings)) : __('No settings to export. The current settings are the default ones.', 'prisna-social-counter');
		
	}
	
	public static function getSettingsValues($_force=false, $_new=true) {
		
		$result = array();
		$settings = self::getSettings($_force);
				
		$defaults = self::getDefaults();
				
		foreach ($settings as $key => $setting) {
		
			if (!array_key_exists($key, $defaults))
				continue;
		
			if ($_new == false || !self::_compare_settings($key, $setting, $defaults[$key])) {
				$result[$key] = array(
					'value' => $setting['value']
				);
			}
			
		}
			
		return $result;

	}
	
	public static function getSettingValue($_name, $_force=false) {
		
		$setting = self::getSetting($_name, $_force);
		
		if (is_null($setting))
			return null;
		
		$result = $setting['value'];
		
		if (PrisnaSocialCounterValidator::isBool($result))
			$result = $result == 'true' || $result === true;
		
		return $result;
		
	}

	public static function getDefaults($_force=false) {
		
		$settings = self::_get_settings();
		$display_mode = array_key_exists('display_mode', $settings) ? $settings['display_mode']['value'] : 'inline';
		
		$result = array(

			'facebook_heading' => array(
				'title_message' => __('Facebook', 'prisna-social-counter'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_facebook_heading',
				'type' => 'heading',
				'group' => 1
			),

			'facebook' => array(
				'id' => 'prisna_facebook',
				'type' => 'social',
				'value' => array(
					'enabled' => 'true',
					'name' => 'PrisnaLtd',
					'current' => '',
					'icon_color' => '#ffffff',
					'text_color' => '#ffffff',
					'background_color' => '#3b5998',
					'unit' => 'likes',
					'format' => 'rounded'
				),
				'enabled_title_message' => __('Enabled', 'prisna-social-counter'),
				'name_title_message' => __('URL/ID/Name', 'prisna-social-counter'),
				'name_description_message' => __('Sets the ID or Name of the Facebook page (not Facebook application; and the page MUST be publicly accessible). It accepts the page\'s URL too. For instance:<br/><br/><ul><li>https://www.facebook.com/PrisnaLtd/?fref=ts</li><li>PrisnaLtd</li></ul>', 'prisna-social-counter'),
				'current_title_message' => __('Current value', 'prisna-social-counter'),
				'current_description_message' => __('Sets the new current count value. It\'ll override the current value until the next live update from the network, defined in:<br/><br/>Advanced &gt; General &gt; Cache refresh frequency<br /><br />Leave this field empty to force a live update.', 'prisna-social-counter'),
				'icon_color_title_message' => __('Icon color', 'prisna-social-counter'),
				'text_color_title_message' => __('Text color', 'prisna-social-counter'),
				'background_color_title_message' => __('Background', 'prisna-social-counter'),
				'unit_title_message' => __('Unit', 'prisna-social-counter'),
				'unit_description_message' => __('Defines the unit of the counter.', 'prisna-social-counter'),
				'format_title_message' => __('Format', 'prisna-social-counter'),
				'format_description_message' => __('Defines the format of the counter. For instance:<br/><br/>If the total count is: 2246<br/><br/>None &gt; 2246<br/>Rounded &gt; 2K<br/>Rounded w/1 decimal &gt; 2.2K<br/>Rounded w/2 decimals &gt; 2.25K<br/><br/>Note: the <i>Rounded</i> format rounds to the nearest thousand, million.', 'prisna-social-counter'),
				'dependence' => 'facebook_heading',
				'dependence_show_value' => 'true',
				'group' => 1
			),

			'twitter_heading' => array(
				'title_message' => __('Twitter', 'prisna-social-counter'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_twitter_heading',
				'type' => 'heading',
				'group' => 2
			),

			'twitter' => array(
				'id' => 'prisna_twitter',
				'type' => 'social',
				'value' => array(
					'enabled' => 'true',
					'name' => '',
					'current' => '',
					'icon_color' => '#ffffff',
					'text_color' => '#ffffff',
					'background_color' => '#45b0e3',
					'unit' => 'followers',
					'format' => 'rounded'
				),
				'enabled_title_message' => __('Enabled', 'prisna-social-counter'),
				'name_title_message' => __('URL/ID/Name', 'prisna-social-counter'),
				'name_description_message' => __('Sets the ID or Name of the Twitter page. It accepts the page\'s URL too. For instance:<br/><br/><ul><li>https://twitter.com/WordPress</li><li>WordPress</li></ul>', 'prisna-social-counter'),
				'current_title_message' => __('Current value', 'prisna-social-counter'),
				'current_description_message' => __('Sets the new current count value. It\'ll override the current value until the next live update from the network, defined in:<br/><br/>Advanced &gt; General &gt; Cache refresh frequency', 'prisna-social-counter'),
				'icon_color_title_message' => __('Icon color', 'prisna-social-counter'),
				'text_color_title_message' => __('Text color', 'prisna-social-counter'),
				'background_color_title_message' => __('Background', 'prisna-social-counter'),
				'unit_title_message' => __('Unit', 'prisna-social-counter'),
				'unit_description_message' => __('Defines the unit of the counter.', 'prisna-social-counter'),
				'format_title_message' => __('Format', 'prisna-social-counter'),
				'format_description_message' => __('Defines the format of the counter. For instance:<br/><br/>If the total count is: 1246<br/><br/>None &gt; 1246<br/>Rounded &gt; 1<br/>Rounded w/1 decimal &gt; 1.2<br/>Rounded w/2 decimals &gt; 1.25', 'prisna-social-counter'),
				'dependence' => 'twitter_heading',
				'dependence_show_value' => 'true',
				'group' => 2
			),

			'google_heading' => array(
				'title_message' => __('Google+', 'prisna-social-counter'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_google_heading',
				'type' => 'heading',
				'group' => 3
			),

			'google' => array(
				'id' => 'prisna_google',
				'type' => 'social',
				'value' => array(
					'enabled' => 'true',
					'name' => '',
					'current' => '',
					'icon_color' => '#ffffff',
					'text_color' => '#ffffff',
					'background_color' => '#fa0101',
					'unit' => 'followers',
					'format' => 'rounded'
				),
				'enabled_title_message' => __('Enabled', 'prisna-social-counter'),
				'name_title_message' => __('URL/ID/Name', 'prisna-social-counter'),
				'name_description_message' => __('Sets the ID or Name of the Google+ page or profile. It accepts the page\'s URL too. For instance:<br/><br/><ul><li>https://plus.google.com/u/0/+WordPress</li><li>WordPress</li></ul>', 'prisna-social-counter'),
				'current_title_message' => __('Current value', 'prisna-social-counter'),
				'current_description_message' => __('Sets the new current count value. It\'ll override the current value until the next live update from the network, defined in:<br/><br/>Advanced &gt; General &gt; Cache refresh frequency', 'prisna-social-counter'),
				'icon_color_title_message' => __('Icon color', 'prisna-social-counter'),
				'text_color_title_message' => __('Text color', 'prisna-social-counter'),
				'background_color_title_message' => __('Background', 'prisna-social-counter'),
				'unit_title_message' => __('Unit', 'prisna-social-counter'),
				'unit_description_message' => __('Defines the unit of the counter.', 'prisna-social-counter'),
				'format_title_message' => __('Format', 'prisna-social-counter'),
				'format_description_message' => __('Defines the format of the counter. For instance:<br/><br/>If the total count is: 1246<br/><br/>None &gt; 1246<br/>Rounded &gt; 1<br/>Rounded w/1 decimal &gt; 1.2<br/>Rounded w/2 decimals &gt; 1.25', 'prisna-social-counter'),
				'dependence' => 'google_heading',
				'dependence_show_value' => 'true',
				'group' => 3
			),

			'current' => array(
				'id' => 'prisna_current',
				'type' => '',
				'value' => array(
					'facebook' => array(),
					'twitter' => array(),
					'google' => array()
				),
				'group' => 500
			),

			'order' => array(
				'title_message' => '',
				'description_message' => '',
				'id' => 'prisna_order',
				'type' => 'text',
				'value' => '',
				'group' => 99
			),

			'test_mode' => array(
				'title_message' => __('Test mode', 'prisna-social-counter'),
				'description_message' => __('Sets whether the translator is in test mode or not. In "test mode", the translator will be displayed only if the current logged in user has admin privileges.<br />Is useful for setting up the translator without letting visitors to see the changes while the plugin is being implemented.', 'prisna-social-counter'),
				'id' => 'prisna_test_mode',
				'type' => 'toggle',
				'value' => 'false',
				'values' => array(
					'true' => __('Yes, enable test mode', 'prisna-social-counter'),
					'false' => __('No, disable test mode', 'prisna-social-counter')
				),
				'group' => 100
			),

			'align_mode' => array(
				'title_message' => __('Align mode (within widget boundaries)', 'prisna-social-counter'),
				'description_message' => __('Sets the alignment mode of the translator within its container.', 'prisna-social-counter'),
				'id' => 'prisna_align_mode',
				'type' => 'radio',
				'value' => 'center',
				'values' => array(
					'left' => __('Left', 'prisna-social-counter'),
					'center' => __('Center', 'prisna-social-counter'),
					'right' => __('Right', 'prisna-social-counter')
				),
				'group' => 500
			),

			'expire' => array(
				'title_message' => __('Cache refresh frequency', 'prisna-social-counter'),
				'description_message' => __('Sets the refresh period for the cache.', 'prisna-social-counter'),
				'id' => 'prisna_expire',
				'type' => 'select',
				'values' => array(
					'12' => __('12 hours', 'prisna-social-counter'),
					'24' => __('1 day', 'prisna-social-counter'),
					'48' => __('2 days', 'prisna-social-counter'),
					'72' => __('3 days', 'prisna-social-counter'),
					'168' => __('1 week', 'prisna-social-counter')
				),
				'value' => '24',
				'group' => 100
			),

			'rounded_corners' => array(
				'title_message' => __('Rounded corners', 'prisna-social-counter'),
				'description_message' => __('Sets the rounded corners of the counter.', 'prisna-social-counter'),
				'id' => 'prisna_rounded_corners',
				'type' => 'select',
				'values' => PrisnaSocialCounterCommon::getRoundedValues(20),
				'value' => '',
				'group' => 100
			),

			'separation' => array(
				'title_message' => __('Separation', 'prisna-social-counter'),
				'description_message' => __('Sets the separation between the networks within the counter.', 'prisna-social-counter'),
				'id' => 'prisna_separation',
				'type' => 'select',
				'values' => PrisnaSocialCounterCommon::getSeparationValues(20),
				'value' => '',
				'group' => 100
			),

			'custom_css' => array(
				'title_message' => __('Custom CSS', 'prisna-social-counter'),
				'description_message' => __('Defines custom CSS rules.', 'prisna-social-counter'),
				'id' => 'prisna_custom_css',
				'type' => 'textarea',
				'value' => '',
				'group' => 100
			),

			'templates_heading' => array(
				'title_message' => __('Templates', 'prisna-social-counter'),
				'description_message' => '',
				'value' => 'false',
				'id' => 'prisna_templates_heading',
				'type' => 'heading',
				'group' => 100
			),
			
			'container_template' => array(
				'title_message' => __('Container template', 'prisna-social-counter'),
				'description_message' => __('Sets the main container template. New templates can be created if the provided one doesn\'t fit the web page requirements.', 'prisna-social-counter'),
				'id' => 'prisna_container_template',
				'type' => 'textarea',
				'value' => '<div class="prisna-social-counter{{ widget }}">
	<ul class="prisna-social-counter-sub-container">
		{{ content }}
	</ul>
</div>',
				'dependence' => 'templates_heading',
				'dependence_show_value' => 'true',
				'group' => 100
			),
			
			'network_template' => array(
				'title_message' => __('Network template', 'prisna-social-counter'),
				'description_message' => __('Sets the network\'s template. New templates can be created if the provided one doesn\'t fit the web page requirements.', 'prisna-social-counter'),
				'id' => 'prisna_network_template',
				'type' => 'textarea',
				'value' => '<li id="prisna-social-counter-network-{{ id }}" class="prisna-social-counter-network prisna-social-counter-network-{{ network }}">
	<a href="{{ network_url }}" target="_blank"><i class="prisna-social-counter-icon"></i><span class="prisna-social-counter-value">{{ count }}</span><span class="prisna-social-counter-unit">{{ unit }}</span></a>
</li>',
				'dependence' => 'templates_heading',
				'dependence_show_value' => 'true',
				'group' => 100
			),

			'import' => array(
				'title_message' => __('Import settings', 'prisna-social-counter'),
				'description_message' => __('Imports previously exported settings. Paste the previously exported settings in the field. If the data\'s structure is correct, it will overwrite the current settings.', 'prisna-social-counter'),
				'id' => 'prisna_import',
				'value' => '',
				'type' => 'textarea',
				'group' => 101
			),

			'export' => array(
				'title_message' => __('Export settings', 'prisna-social-counter'),
				'description_message' => __('Exports the current settings to make a backup or to transfer the settings from the development server to the production server. Triple click on the field to select all the content.', 'prisna-social-counter'),
				'id' => 'prisna_export',
				'value' => self::_get_settings_values_for_export(),
				'type' => 'export',
				'group' => 101
			),
			
			'usage' => array(
				'title_message' => __('Usage', 'prisna-social-counter'),
				'description_message' => '',
				'id' => 'prisna_usage',
				'type' => 'usage',
				'value' => sprintf(__('
				
				- Go to the <em>Appereance &gt; Widgets</em> panel, search for the following widget<br /><br />
				
				<span class="prisna_social_counter_shortcode">%s</span><br /><br />
				
				- Or copy and paste the following code into pages, posts, etc...<br /><br />
				
				<span class="prisna_social_counter_shortcode">[prisna-social-counter]</span><br /><br />
				
				- Or copy and paste the following code into any page, post or front end PHP file<br /><br />
				
				<span class="prisna_social_counter_shortcode">&lt;?php echo do_shortcode(\'[prisna-social-counter]\'); ?&gt;</span><br />
				
				', 'prisna-gwt'), self::getWidgetName()),
				'group' => 102
			),
			
			'usage_extended' => array(
				'title_message' => __('Usage (extended parameters)', 'prisna-social-counter'),
				'description_message' => '',
				'id' => 'prisna_usage_extended',
				'type' => 'usage',
				'value' => sprintf(__('
				
				The shortcode accepts a number of different parameters:<br /><br />

				<span class="prisna_social_counter_shortcode">[prisna-social-counter]</span><br />

				<ul>
				<li><em>network</em>: Sets the network. Allowed values (all lowercase): facebook, twitter, google.</li>
				<li><em>name</em>: Sets the network name. Eg: PrisnaLtd.</li>
				<li><em>background_color</em>: Sets the background color, formatted in RGB hexadecimal. Eg: #3B5998.</li>
				<li><em>icon_color</em>: Sets the icon color, formatted in RGB hexadecimal. Eg: #FFFFFF.</li>
				<li><em>text_color</em>: Sets the text color, formatted in RGB hexadecimal. Eg: #FFFFFF.</li>
				<li><em>rounded_corners</em>: Sets how rounded corners are. Values are in pixels. Use 0 for squared corners. Eg: 5.</li>
				<li><em>format</em>: Sets the format of the counter. Allowed values (all lowercase): none, comma, rounded, rounded_one, rounded_two.</li>
				<li><em>unit</em>: Sets the unit of the counter. Allowed values (all lowercase): none, likes, fans, followers.</li>
				<li><em>current</em>: Sets the current/initial value of the counter, it\'ll be displayed until the next live update from the network. Eg: 1200.</li>
				<li><em>width</em>: Sets the width of the counter. Values in pixels or percentages. Eg: 120px.</li>
				</ul>
				
				Samples:<br /><br />
				
				<span class="prisna_social_counter_shortcode">[prisna-social-counter network="facebook" name="PrisnaLtd" width="120px"]</span><br />
				<span class="prisna_social_counter_shortcode">[prisna-social-counter network="facebook" name="PrisnaLtd" background_color="#985F46" icon_color="#212121" text_color="#212121" format="rounded_two" unit="followers" width="30&#37;" rounded_corners="5"]</span><br />
				
				
				<br />
				
				', 'prisna-gwt'), self::getWidgetName()),
				'group' => 102
			)
			
		);
			
		
		return $result;
		
	}

}

?>
