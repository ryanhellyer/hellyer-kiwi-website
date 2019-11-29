var PrisnaSocialCounterAdmin = {

	_tabs: {
		general: null,
		advanced: null
	},
	
	_form: null,
	_action: null,
	_buttons: {},
	
	_networks: {},
	
	_visual: {},
	
	_headings: {},
	
	_fields: {
		general: {},
		advanced: {}
	},
	
	initialize: function() {
		
		if (typeof PrisnaSocialCounterCommon == "undefined") {
			setTimeout(function() {
				PrisnaSocialCounterAdmin.initialize();
			}, 200);
			return;
		}

		PrisnaSocialCounterAdmin._initialize_elements();
		PrisnaSocialCounterAdmin._initialize_events();
		PrisnaSocialCounterAdmin._initialize_tooltips();
		PrisnaSocialCounterAdmin._initialize_headings();
		PrisnaSocialCounterAdmin._initialize_visual_fields();
		PrisnaSocialCounterAdmin._initialize_color_fields();
		PrisnaSocialCounterAdmin._initialize_sortables();
		PrisnaSocialCounterAdmin._initialize_preview();
		PrisnaSocialCounterAdmin._initialize_tabs();

		PrisnaSocialCounterAdmin._initialize_dependences();
		
	}, 
	
	_initialize_tabs: function() {
		
		jQuery(".prisna_social_counter_ui_tab_unselected").removeClass("prisna_social_counter_hidden_important");
		
		this._tabs.general = new PrisnaSocialCounterCommon.Tabs();

		this._tabs.general.registerTab("general", PrisnaSocialCounterAdmin._on_tab_change);
		this._tabs.general.registerTab("advanced", PrisnaSocialCounterAdmin._on_tab_change);
		this._tabs.general.registerTab("usage", PrisnaSocialCounterAdmin._on_tab_change);
		//this._tabs.general.registerTab("premium", PrisnaSocialCounterAdmin._on_tab_change);

		this._on_tab_change(this._tabs.general.getSelected());
		
		this._tabs.advanced = new PrisnaSocialCounterCommon.Tabs(2);

		this._tabs.advanced.registerTab("advanced_general");
		this._tabs.advanced.registerTab("advanced_import_export");

	},

	_on_tab_change: function(_param) {
		
		PrisnaSocialCounterAdmin._show_buttons(_param != "usage" && _param != "premium");
		
	},

	_show_buttons: function(_state) {
		
		if (_state) {
			this._buttons.save.show();
			this._buttons.reset.show();
		}
		else {
			this._buttons.save.hide();
			this._buttons.reset.hide();
		}
		
	},

	previewLink: function(_network) {
		
		var value = jQuery("#prisna_" + _network + "_name").val();
		
		if (value == "")
			return;
		
		var link = this._get_network_url(_network, value);
		
		window.open(link, "_blank");
		
	},
	
	_get_network_url: function(_network, _value) {
		
		var values = {
			'facebook': 'https://www.facebook.com/{{ name }}/',
			'twitter': 'https://twitter.com/{{ name }}',
			'google': 'https://plus.google.com/{{ name }}'
		};
		
		if (_network == 'google' && jQuery.isNumeric(_value))
			_value = '+' + _value;
		
		if (!(_network in values))
			return "";
		
		return values[_network].replace("{{ name }}", _value);
		
	},
	
	reGenPreview: function() {

		for (id in this._networks)
			this.genPreview(id);

	},
	
	genPreview: function(_network, _undefined) {
		
		var link = jQuery("#prisna_social_counter_network_" + _network);
		var logo = link.find(".prisna_social_counter_network_icon");
		var text = link.find(".prisna_social_counter_value");
		var unit = link.find(".prisna_social_counter_unit");
		var corners = jQuery("#prisna_rounded_corners").val();
		
		link.css({
			"background-color": this._networks[_network]["background_color"].val(),
			"-webkit-border-radius": corners + "px",
			"-moz-border-radius": corners + "px",
			"border-radius": corners + "px"
		});
		logo.css("color", this._networks[_network]["icon_color"].val());

		text.css("color", this._networks[_network]["text_color"].val());
		text.text(this._format_count(this._networks[_network]["current"].val(), this._networks[_network]["format"].val()));
		
		unit.css("color", this._networks[_network]["text_color"].val());
		
		var unit_aux = this._networks[_network]["unit"].get(0);
		var unit_text = unit_aux.selectedIndex > 0 ? unit_aux.options[unit_aux.selectedIndex].text : "";
		unit.text(unit_text);

		if (_undefined != undefined) {
			var container = jQuery("#prisna_social_counter_preview_" + _network + " > table");
			container.removeClass("prisna_social_counter_no_display");
		}
		
		this.genShortcode(_network, this._networks[_network]["name"].val(), this._networks[_network]["background_color"].val(), this._networks[_network]["icon_color"].val(), this._networks[_network]["text_color"].val(), corners, this._networks[_network]["current"].val(), this._networks[_network]["format"].val(), unit_aux.value);
		
	},
	
	genShortcode: function(_network, _name, _background_color, _icon_color, _text_color, _rounded_corners, _current, _format, _unit) {
		
		var values = {
			"network": _network,
			"name": _name,
			"background_color": _background_color,
			"icon_color": _icon_color,
			"background_color": _background_color,
			"text_color": _text_color,
			"rounded_corners": _rounded_corners,
			"current": _current,
			"format": _format,
			"unit": _unit,
			"rounded_corners": _rounded_corners
		};
		
		var result = "[prisna-social-counter";
		for (var i in values)
			if (values[i])
				result += " " + i + "=\"" + values[i] + "\"";
		result += "]";
		
		jQuery("#prisna_" + _network + "_shortcode").text(result);
		
	},
	
	_attach_event_preview: function(_network, _field) {
		
		var element = this._networks[_network][_field];
		var event = element.is('input') ? "blur" : "change";
		
		element.on(event, function() {
			PrisnaSocialCounterAdmin.genPreview(_network);
		});
		
	},
	
	_initialize_preview: function() {
		
		var sections = jQuery("div[class^='prisna_social_counter_social_']");
		var id;
		
		for (var i=0; i<sections.length; i++) {
			id = sections.eq(i).attr("id").replace("section_prisna_", "");
			this._networks[id] = {
				"name": jQuery("#prisna_" + id + "_name"),
				"background_color": jQuery("#prisna_" + id + "_background_color"),
				"icon_color": jQuery("#prisna_" + id + "_icon_color"),
				"text_color": jQuery("#prisna_" + id + "_text_color"),
				"current": jQuery("#prisna_" + id + "_current"),
				"format": jQuery("#prisna_" + id + "_format"),
				"unit": jQuery("#prisna_" + id + "_unit")
			};
		}

		for (id in this._networks) {
			for (var field in this._networks[id])
				this._attach_event_preview(id, field);
			this.genPreview(id, true);
		}

	},
	
	_initialize_color_fields: function() {
		
		var fields = jQuery(".prisna_social_counter_color_picker");
		var view;
		var foo;

		for (var i=0; i<fields.length; i++) {
			view = PrisnaSocialCounterCommon.$(fields[i].id + "_view");
			if (view)
				foo = new PrisnaSocialCounterCommon.jscolor.color(fields[i], { styleElement: view, transparent: true });
		}
		
	},

	_initialize_elements: function() {
	
		this._form = PrisnaSocialCounterCommon.$("prisna_admin");
		this._action = PrisnaSocialCounterCommon.$("prisna_social_counter_admin_action");

		this._fields.general.display_mode = jQuery("#prisna_display_mode");
		this._fields.general.style_inline = jQuery("#section_prisna_style_inline input");
		this._fields.general.show_flags = jQuery("#section_prisna_show_flags input");
		this._fields.general.languages = jQuery("#section_prisna_languages input");

		this._buttons.save = jQuery(".button-primary");
		this._buttons.reset = jQuery(".reset-settings");

	},
	
	_initialize_events: function() {
	
		jQuery("#prisna_rounded_corners").on("change", function() {
			PrisnaSocialCounterAdmin.reGenPreview();
		});
		
	},
	
	_initialize_dependences: function() {	

		PrisnaSocialCounterCommon.Dependencies.add(this._fields.general.show_flags, "click", function() {

			PrisnaSocialCounterAdmin.showSection("section_prisna_languages", this.value == "true");
			PrisnaSocialCounterAdmin.showSection("section_prisna_languages_order", this.value == "true");
			
		});
	
	},
	
	_initialize_headings: function() {
		
		var headings = jQuery(".prisna_social_counter_heading");
		for (var i=0; i<headings.length; i++)
			PrisnaSocialCounterAdmin._headings[headings[i].id] = new PrisnaSocialCounterCommon.Heading(headings[i]);

	},
	
	_initialize_visual_fields: function() {
		
		var fields = jQuery(".prisna_social_counter_visual input");
		for (var i=0; i<fields.length; i++)
			if (fields[i].checked)
				this._visual[fields.eq(i).attr("name")] = fields[i].value;
		
		jQuery(".prisna_social_counter_visual input").click(function() {

			var checkbox = jQuery(this);
			
			checkbox.parents(".prisna_social_counter_visual").find(".prisna_social_counter_field").removeClass("prisna_social_counter_visual_checked");
			checkbox.parents(".prisna_social_counter_field").addClass("prisna_social_counter_visual_checked");

		});
		
	},
		
	_initialize_sortables: function() {

		var sorter = jQuery(".prisna_social_counter_sortable").sortable({
			connectWith: ".prisna_social_counter_sortable",
			handle: ".prisna_social_counter_title",
			update: this._networks_order_update
		});

	},
	
	_format_count: function(_value, _format) {
		
		var result;
		var precision;
		
		var value = Math.round(_value);
		
		if (isNaN(value) || value == 0)
			value = 2246;

		if (value > 999999999)
			value = 1000000000;

		switch (_format) {
			case "comma":
				result = value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				break;
			case 'rounded':
			case 'rounded_one':
			case 'rounded_two':
			
				if (_format == 'rounded_one')
					precision = 1;
				else if (_format == 'rounded_two')
					precision = 2;
				else
					precision = 0;

				if (value < 1000)
					result = +value.toFixed(precision);
				else if (value < 1000000) {
					result = value/1000;
					result = +result.toFixed(precision);
					result += "K";
				}
				else if (value < 1000000000) {
					result = value/1000000;
					result = +result.toFixed(precision);
					result += "M";
				}
				else {
					result = value/1000000000;
					result = +result.toFixed(precision);
					result += "G";
				}
				break;
			default:
				result = value;
				break;
		}

		return result;
		
	},
	
	_networks_order_update: function() {
		
		var result = [];
		var target = jQuery("#prisna_order");
		var items = jQuery(".prisna_social_counter_social_order");
		for (var i=0; i<items.length; i++)
			result.push(items.eq(i).val());
		target.val(result.join(","));
		
	},
	
	_initialize_tooltips: function() {
		
		PrisnaSocialCounterCommon.initializeTooltip(".prisna_social_counter_tooltip");
		
	},
	
	showSection: function(_section_id, _state, _now) {
		
		if (_now === true) {
			if (_state)
				jQuery("#" + _section_id).show();
			else
				jQuery("#" + _section_id).hide();
		}
		else {
			if (_state)
				jQuery("#" + _section_id).slideDown("fast");
			else
				jQuery("#" + _section_id).slideUp("fast");
		}
		
	},
	
	submitSettings: function() {
		
		this._form.submit();
		
	},
	
	resetSettings: function(_message) {
	
		if (confirm(_message)) {
			this._action.value = "prisna_social_counter_reset_settings";
			this._form.submit();
			return true;
		} 

		return false;
		
	},
	
	hideMessage: function(_selector, _delay) {
		
		setTimeout(function() {
			jQuery(_selector).animate({
				opacity: "toggle",
				height: "toggle"
			}, "fast")
		}, _delay);
		
	}

};