<?php

abstract class PrisnaSocialCounterItem {

	public $collection_item_index;
	protected $_properties;

	public function __construct($_properties=null) {

		if (is_object($_properties)) {
			$this->_properties = $_properties;
			$this->_set_properties();
		}

	}

	protected function _set_properties() {

		$this->setProperties($this->_properties);

	}

	public function getProperty($_property, $_html_entities=false) {

		return !$_html_entities ? $this->{$_property} : htmlentities($this->{$_property});

	}

	public function setProperties($_properties) {

		if (!is_null($_properties))
			foreach($_properties as $property => $value) 
				$this->setProperty($property, $value);

	}

	public function setProperty($_property, $_value) {

		return $this->{$_property} = $_value;

	}

	public function render($_options, $_html_encode=false) {

		if (array_key_exists('extra', $_options))
			if (array_key_exists('json', $_options['extra']))
				if ($_options['extra']['json'])
					$this->_json();

		if (array_key_exists('extra', $_options))
			if (array_key_exists('property', $_options['extra']))
				foreach ($_options['extra']['property'] as $property => $value)
					$this->{$property} = $value;

		$result = PrisnaSocialCounterCommon::renderObject($this, $_options, $_html_encode);

		return $result;

	}

	protected function _json() {

		// seems like there is some kind of bug in apache, so the field names have to be grabbed like this
		$fields = array();

		foreach ($this as $property => $value)
			if (!PrisnaSocialCounterCommon::endsWith($property, '_json'))
				$fields[] = $property;

		foreach ($fields as $value)
			if (substr($value, 0, 1) != '_')
				$this->setProperty($value . '_json', PrisnaSocialCounterCommon::jsonCompatible($this->getProperty($value)));

	}

}

abstract class PrisnaSocialCounterField {

	public $id;
	public $option_id;
	public $value;
	public $dependence;
	public $dependence_show_value;

	public $title_message;
	public $description_message;

	public $dependence_count;

	public $formatted_dependence;
	public $formatted_dependence_show_value;

	protected $_dependence;

	public function __construct($_properties) {

		$this->_set_properties($_properties);

	}

	protected function _set_properties($_properties) {

		foreach ($_properties as $property => $value)
			$this->{$property} = $value;

	}

	public function satisfyDependence($_fields) {

		if (PrisnaSocialCounterValidator::isEmpty($this->dependence))
			return;

		$this->_dependence = PrisnaSocialCounterCommon::getArrayItems($this->dependence, $_fields);
		if (is_null($this->dependence_count))
			$this->dependence_count = count($this->_dependence);

	}

	protected function _has_dependence() {

		return !is_null($this->dependence) && !PrisnaSocialCounterValidator::isEmpty($this->dependence);

	}

	protected function _dependence_show() {

		if (!is_array($this->_dependence))
			return true;

		$result = array();

		if (is_array($this->dependence_show_value)) {
			if (count($this->dependence_show_value) == count($this->_dependence)) {
				$keys = array_keys($this->_dependence);
				for ($i=0; $i<count($keys); $i++) {
					$field = $this->_dependence[$keys[$i]];
					if ($field->value == $this->dependence_show_value[$i])
						$result[] = $field->id;
				}
				return count($result) == count($this->_dependence);
			}
		}

		foreach ($this->_dependence as $field)
			if (PrisnaSocialCounterCommon::inArray($field->value, $this->dependence_show_value))
				$result[] = $field->id;

		return count($result) == count($this->_dependence);


	}

	protected function _get_formatted_dependence() {

		$result = array();

		if (!$this->_has_dependence())
			return '';

		foreach ($this->_dependence as $field)
			$result[] = $field->id;

		return implode(',', $result);

	}

	protected function _render($_options, $_html_encode) {
		
		$this->formatted_dependence = is_array($this->dependence) ? implode(',', $this->dependence) : $this->dependence;
		$this->formatted_dependence_show_value = is_array($this->dependence_show_value) ? implode(',', $this->dependence_show_value) : $this->dependence_show_value;

		$options = $_options;

		if (!array_key_exists('meta_tag_rules', $options))
			$options['meta_tag_rules'] = array();

		$options['meta_tag_rules'][] = array(
			'expression' => !empty($this->title_message),
			'tag' => 'title'
		);

		$options['meta_tag_rules'][] = array(
			'expression' => !empty($this->description_message),
			'tag' => 'description'
		);

		$options['meta_tag_rules'][] = array(
			'expression' => $this->_has_dependence(),
			'tag' => 'has_dependence'
		);

		$options['meta_tag_rules'][] = array(		
			'expression' => $this->_dependence_show(),
			'tag' => 'dependence.show'
		);

		$result = PrisnaSocialCounterCommon::renderObject($this, $options, $_html_encode);

		return $result;
		
	}

	public function output($_html_encode=false) {
		
	}

	public function render($_options, $_html_encode=false) {

		return $this->_render($_options, $_html_encode);

	}

}

class PrisnaSocialCounterSocialField extends PrisnaSocialCounterField {

	protected $_enabled_field;
	protected $_name_field;
	protected $_icon_color_field;
	protected $_text_color_field;
	protected $_background_color_field;
	protected $_current_field;
	protected $_unit_field;
	protected $_format_field;

	public $kind;
	
	public $enabled_formatted;
	public $name_formatted;
	public $icon_color_formatted;
	public $text_color_formatted;
	public $background_color_formatted;
	public $current_formatted;
	public $unit_formatted;
	public $format_formatted;
	public $shortcode_formatted;

	public $enabled_title_message;
	public $name_title_message;
	public $name_description_message;
	public $icon_color_title_message;
	public $text_color_title_message;
	public $background_color_title_message;
	public $current_title_message;
	public $current_description_message;
	public $unit_title_message;
	public $unit_description_message;
	public $format_title_message;
	public $format_description_message;

	public function __construct($_properties) {
		
		parent::__construct($_properties);

		$this->kind = self::getKind($this->id);

		$this->_enabled_field = new PrisnaSocialCounterToggleField(array(
			'title_message' => $this->enabled_title_message,
			'description_message' => '',
			'id' => $this->id . '_enabled',
			'values' => array(
				'true' => __('Yes', 'prisna-social-counter'),
				'false' => __('No', 'prisna-social-counter')
			),
			'value' => $this->value['enabled']
		));

		$this->_name_field = new PrisnaSocialCounterTextField(array(
			'title_message' => $this->name_title_message,
			'description_message' => $this->name_description_message,
			'id' => $this->id . '_name',
			'value' => $this->value['name']
		));

		$this->_icon_color_field = new PrisnaSocialCounterColorField(array(
			'title_message' => $this->icon_color_title_message,
			'description_message' => '',
			'id' => $this->id . '_icon_color',
			'value' => $this->value['icon_color']
		));

		$this->_text_color_field = new PrisnaSocialCounterColorField(array(
			'title_message' => $this->text_color_title_message,
			'description_message' => '',
			'id' => $this->id . '_text_color',
			'value' => $this->value['text_color']
		));

		$this->_background_color_field = new PrisnaSocialCounterColorField(array(
			'title_message' => $this->background_color_title_message,
			'description_message' => '',
			'id' => $this->id . '_background_color',
			'value' => $this->value['background_color']
		));

		$this->_current_field = new PrisnaSocialCounterTextField(array(
			'title_message' => $this->current_title_message,
			'description_message' => $this->current_description_message,
			'id' => $this->id . '_current',
			'value' => $this->value['current']
		));

		$this->_unit_field = new PrisnaSocialCounterSelectField(array(
			'title_message' => $this->unit_title_message,
			'description_message' => $this->unit_description_message,
			'id' => $this->id . '_unit',
			'values' => array(
				'none' => __('None', 'prisna-social-counter'),
				'likes' => __('Likes', 'prisna-social-counter'),
				'fans' => __('Fans', 'prisna-social-counter'),
				'followers' => __('Followers', 'prisna-social-counter')
			),
			'value' => $this->value['unit']
		));

		$this->_format_field = new PrisnaSocialCounterSelectField(array(
			'title_message' => $this->format_title_message,
			'description_message' => $this->format_description_message,
			'id' => $this->id . '_format',
			'values' => array(
				'none' => __('None', 'prisna-social-counter'),
				'comma' => __('Comma by thousands (eg: 2,246)', 'prisna-social-counter'),
				'rounded' => __('Rounded (eg: 2K)', 'prisna-social-counter'),
				'rounded_one' => __('Rounded w/1 decimal (eg: 2.2K)', 'prisna-social-counter'),
				'rounded_two' => __('Rounded w/2 decimals (eg: 2.25K)', 'prisna-social-counter')
			),
			'value' => $this->value['format']
		));

	}

	public static function extractName($_name, $_id) {
		
		$result = self::_extract_name($_name, $_id);
		
		if (empty($result))
			$result = $_name;
			
		return $result;
		
	}
	
	public static function _extract_name($_name, $_id) {
		
		$parts = explode('/', $_name);
		
		$new = end($parts);
		$c = 0;
		
		while (true) {
			
			$current = $new;
			
			if (empty($current))
				$new = prev($parts);
			else
				if (PrisnaSocialCounterCommon::startsWith($current, '?'))
					$new = prev($parts);
			
			if (!empty($new) && $new == $current)
				return $_id == 'google' ? ltrim($new, '+') : $new;
			
			$c++;
			
			if ($c == 10)
				return '';
			
		}
		
		$result = empty($last) ? prev($parts) : $last;
		
		return $_id == 'google' ? ltrim($result, '+') : $result;
		
	}
	
	public static function getKind($_id) {
		
		$id = explode('_', $_id, 2);
		return $id[1];
		
	}
	
	public function output($_html_encode=false) {

		$this->enabled_formatted = $this->_enabled_field->output('toggle_row.tpl');
		$this->name_formatted = $this->_name_field->output('text_name_row.tpl');
		$this->current_formatted = $this->_current_field->output('text_row.tpl');
		$this->icon_color_formatted = $this->_icon_color_field->output('color_row.tpl');
		$this->text_color_formatted = $this->_text_color_field->output('color_row.tpl');
		$this->background_color_formatted = $this->_background_color_field->output('color_row.tpl');
		$this->unit_formatted = $this->_unit_field->output('select_row.tpl');
		$this->format_formatted = $this->_format_field->output('select_row.tpl');
		$this->shortcode_formatted = __('Shortcode', 'prisna-social-counter');
		
		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/social.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterColorField extends PrisnaSocialCounterField {

	public function output($_template='color.tpl', $_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterTextField extends PrisnaSocialCounterField {

	public function output($_template='text.tpl', $_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterPremiumField extends PrisnaSocialCounterField {

	public $images_path;

	public function output($_html_encode=false) {

		$this->images_path = PRISNA_SOCIAL_COUNTER__IMAGES;

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/premium.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => time() < strtotime('2016-01-02 00:00:00'),
					'tag' => 'banner'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterUsageField extends PrisnaSocialCounterField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/usage.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterHeadingField extends PrisnaSocialCounterField {

	public $group;

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/heading.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->value == "true",
					'tag' => 'value'
				),
				array(
					'expression' => !PrisnaSocialCounterValidator::isEmpty($this->description_message),
					'tag' => 'description'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterHeading2Field extends PrisnaSocialCounterField {

	public $group;

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/heading_2.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->value == "true",
					'tag' => 'value'
				),
				array(
					'expression' => !PrisnaSocialCounterValidator::isEmpty($this->description_message),
					'tag' => 'description'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterToggleField extends PrisnaSocialCounterField {

	public $name;
	public $value_true;
	public $option_true;
	public $value_false;
	public $option_false;

	protected function _set_properties($_properties) {

		foreach ($_properties as $property => $value)
			$this->{$property} = $value;

		$this->name = $this->id;
		$keys = array_keys($_properties['values']);
		$this->value_true = $keys[0];
		$this->option_true = $_properties['values'][$keys[0]];
		$this->value_false = $keys[1];
		$this->option_false = $_properties['values'][$keys[1]];

	}

	public function output($_template='toggle.tpl', $_html_encode=false) {

		if (!in_array($this->value, array($this->value_true, $this->value_false)))
			$this->value = $this->value_true;

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template,
			'meta_tag_rules' => array(
				array(
					'expression' => $this->value == $this->value_true,
					'tag' => 'value_true.checked'
				),
				array(
					'expression' => $this->value == $this->value_false,
					'tag' => 'value_false.checked'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterTextareaField extends PrisnaSocialCounterField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/textarea.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterExportField extends PrisnaSocialCounterField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/export.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterCheckboxOptionField extends PrisnaSocialCounterField {

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/Checkbox_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterCheckboxField extends PrisnaSocialCounterField {

	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaSocialCounterItemCollection();

		foreach ($this->values as $key => $value) {

			$this->collection->add(new PrisnaSocialCounterCheckboxOptionField((object) array(
				'id' => PrisnaSocialCounterCommon::cleanId($this->id . '_' . $key, '_'),
				'name' => $this->id,
				'checked' => is_array($this->value) ? in_array((string) $key, $this->value, true) : false,
				'option' => $key,
				'value' => $value
			)), $key);

		}

	}

	public function output($_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/checkbox.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterRadioOptionField extends PrisnaSocialCounterField {

	public $indent;

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/radio_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterRadioField extends PrisnaSocialCounterField {

	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaSocialCounterItemCollection();

		foreach ($this->values as $key => $value) {

			$this->collection->add(new PrisnaSocialCounterRadioOptionField((object) array(
				'id' => $this->id . '_' . $key,
				'name' => $this->id,
				'checked' => $this->value == $key,
				'option' => $key,
				'value' => $value
			)), $key);

		}

	}

	public function output($_template='radio.tpl', $_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterVisualOptionField extends PrisnaSocialCounterField {

	protected $_parent;

	public function __construct($_properties, $_parent) {

		$this->_set_properties($_properties);
		$this->_parent = $_parent;

	}

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/visual_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->checked,
					'tag' => 'checked'
				),
				array(
					'expression' => $this->collection_item_index % $this->_parent->col_count == 0,
					'tag' => 'new_row'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterVisualField extends PrisnaSocialCounterField {

	public $col_count;
	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaSocialCounterItemCollection();

		foreach ($this->values as $key => $value) {

			$this->collection->add(new PrisnaSocialCounterVisualOptionField((object) array(
				'id' => $this->id . '_' . $key,
				'name' => $this->id,
				'checked' => $this->value == $key,
				'option' => $key,
				'value' => $value
			), $this), $key);

		}

	}

	public function output($_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/visual.tpl'
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterSelectOptionField extends PrisnaSocialCounterField {

	protected $_parent;

	public function __construct($_properties, $_parent) {

		$this->_set_properties($_properties);
		$this->_parent = $_parent;

	}

	public function output($_html_encode=false) {

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/select_option.tpl',
			'meta_tag_rules' => array(
				array(
					'expression' => $this->selected,
					'tag' => 'selected'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterSelectField extends PrisnaSocialCounterField {

	public $force_selected;
	
	public $collection_formatted;
	protected $collection;

	public function __construct($_properties) {

		$this->_set_properties($_properties);
		$this->_set_options();

	}

	protected function _set_options() {

		$this->collection = new PrisnaSocialCounterItemCollection();
		$selected_flag = false;

		foreach ($this->values as $key => $value) {

			if ($this->value == $key)
				$selected_flag = true;

			$this->collection->add(new PrisnaSocialCounterSelectOptionField((object) array(
				'selected' => $this->value == $key,
				'option' => $key,
				'value' => $value
			), $this), $key);

		}

		if ($this->force_selected === true && $selected_flag !== true)
			$this->collection->add(new PrisnaSocialCounterSelectOptionField((object) array(
				'selected' => true,
				'option' => $this->value,
				'value' => $this->value
			), $this), $this->value);

	}

	public function output($_template='select.tpl', $_html_encode=false) {

		$this->collection_formatted = $this->collection->render(array(
			'type' => 'html',
			'content' => '{{ collection }}'
		), $_html_encode);

		$result = parent::render(array(
			'type' => 'file',
			'content' => '/admin/' . $_template,
			'meta_tag_rules' => array(
				array(
					'expression' => property_exists($this, 'post_id') && !PrisnaSocialCounterValidator::isEmpty($this->post_id),
					'tag' => 'has_post_id'
				)
			)
		), $_html_encode);

		return $result;

	}	

}

class PrisnaSocialCounterItemCollection {

    protected $_position = 0;

    public $collection;

    public function __construct() {

		$this->_position = 0;

	}

    public function add($_object, $_index=null) {

		if (is_null($_index))
			$this->collection[] = $_object;
		else
			$this->collection[$_index] = $_object;

	}

    public function rewind() {

        $this->_position = 0;

    }

    public function current() {

		$keys = array_keys($this->collection);
        return $this->collection[$keys[$this->_position]];

	}

    public function getFirst() {

		$keys = array_keys($this->collection);
        return $this->collection[$keys[0]];

	}

    public function getLast() {

		$keys = array_keys($this->collection);
        return $this->collection[$keys[count($keys)-1]];

	}

    public function key() {

		return $this->_position;

    }

    public function next() {

        ++$this->_position;

	}

    public function count() {

		return count($this->collection);

	}

    public function valid() {

		$keys = array_keys($this->collection);

		if (!isset($keys[$this->_position]))
			return false;

		return isset($this->collection[$keys[$this->_position]]);

	}

	protected function _add_count_for_render() {

		if (count($this->collection) > 0) {
			$i = 0;
			foreach ($this->collection as $item) {
				$item->collection_item_index = $i;
				$i++;
			}
		}

	}

	public function render($_options, $_html_encode=false) {

		$result = '';
		$partial = array();

		$this->_add_count_for_render();

		if (count($this->collection) > 0) 
			foreach ($this->collection as $item)
				$partial[] = $item->output($_html_encode);

		$object = (object) array(
			'collection' => join("\n", $partial),
			'collection_count' => count($partial)
		);

		foreach ($this as $property => $value)
			if (!is_array($value))
				$object->{$property} = $value;				

		if (!array_key_exists('meta_tag_rules', $_options))
			$_options['meta_tag_rules'] = array();

		$_options['meta_tag_rules'][] = array(
			'expression' => count($partial) == 0,
			'tag' => 'collection.is_empty'
		);

		$result = PrisnaSocialCounterCommon::renderObject($object, $_options, $_html_encode);

		return $result;

	}

}

?>
