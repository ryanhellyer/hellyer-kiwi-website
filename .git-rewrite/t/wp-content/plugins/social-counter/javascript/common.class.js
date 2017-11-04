var PrisnaSocialCounterCommon = {

	$: function() {
		var _elements = new Array();
		for (var i = 0; i < arguments.length; i++) {
			var _element = arguments[i];
			if (typeof _element == "string")
				_element = document.getElementById(_element);
			if (arguments.length == 1)
				return _element;
			_elements.push(_element);
		}
		return _elements;
	},
	
	addEvent: function(element, evType, fn, useCapture) {
		if(element.addEventListener) {
			element.addEventListener(evType, fn, useCapture);
			return true;
		}
		else if(element.attachEvent) {
			var r = element.attachEvent("on" + evType, fn);
			return r;
		}
		else {
			element["on" + evType] = fn;
		}
	},

	mergeText: function(message, newValuesArray) {
		result = message;
		var i = 0;
		for(var i=0; i<newValuesArray.length; i++) result = result.replace("["+i+"]", newValuesArray[i]);
		return result;
	},

	unserialize: function(_url) {

		var result = {};
		var full_option;
		var option;
		var value;
		var params = _url;
		var single = arguments[1] || false;

		params = params.slice(params.indexOf('?') + 1).split("&");

		for (var i=0; i<params.length; i++) {
			
			full_option = params[i].split("=");
			option = full_option[0];
			value = full_option[1];

			if (single == option)
				return value;
			
			result[option] = value;
				
		}

		if (single !== false)
			return false;

		return result;
	
	},

	cleanId: function(_string, _separator) {
		
		var separator = _separator || "-";
		
		return _string.replace(/[^a-zA-Z0-9]+|\s+/g, separator).toLowerCase();
		
	},

	getOwnerParent: function(_element) {
		
		if (_element._owner)
			return _element;

		var result = _element.parentNode;
		try {
			while (!result._owner) 
				result = result.parentNode;
			return result;
		}
		catch(e) {
			return null;
		}

	},

	clickSelected: function(_container) {
		
		var fields = jQuery(_container).find("input[type=radio]");
		
		if (fields.length > 0)
			for (var i=0; i<fields.length; i++)
				if (fields[i].checked)
					fields.eq(i).click();
		
	},
	
	getFieldValue: function(_container) {
		
		var fields = jQuery(_container).find("input[type=radio]");
		
		if (fields.length > 0) {
			for (var i=0; i<fields.length; i++)
				if (fields[i].checked)
					return fields[i].value;
		}
		else {
			
			fields = jQuery(_container).find("select");
		
			if (fields.length > 0)
				return fields[0].value;

		}

		return false;
		
	},
	
	getOwner: function(e) {

		return e.srcElement || e.target;

	},
	
	trim: function(_string) {
		
		return this != window ? _string.replace(/^\s*|\s*$/g,"") : null;

	},
	
	startsWith: function(_string, substr) {
		if (this == window) return null;
		return _string.substring(0, substr.length) == substr;
	},

	endsWith: function(_string, substr) {
		if (this == window) return null;
		return _string.length >= substr.length && _string.substring(_string.length - substr.length) == substr;
	},
	
	inArray: function(_value, _array, _property) {
	
		if (!(_array instanceof Array))
			_array = _array.split(",");

		for (var i=0; i<_array.length; i++) {
			if (_array[i] instanceof Object) {
				if (_array[i][_property] == _value)
					return i;
			}				
			else if (_array[i] == _value)
				return i;
		}
				
		return false;
		
	},
	
	getHeadingContainer: function(_item) {

		var section = jQuery(_item).parents(".prisna_social_counter_section");
		var heading = section.prevUntil(".prisna_social_counter_heading");
		var result = heading.length > 0 ? jQuery(heading[heading.length-1]) : section;
			
		return result.prev();
		
	},
	
	getHeadingObject: function(_item, _headings) {

		var heading_container = this.getHeadingContainer(_item);
		var heading_id = heading_container.attr("id");
		return _headings[heading_id];
		
	}
	
};

PrisnaSocialCounterCommon.Dependencies = {
	
	_targets: {},

	_record: function(_target) {
		
		var foo = _target[0];
		var section = jQuery(foo).parents(".prisna_social_counter_section");
		
		this._targets[section.attr("id")] = _target;
		
	},	
	
	simulate: function(_section) {
		
		var section_id = _section.id;
		var type;

		if (typeof this._targets[section_id] != "object")
			return false;
		
		if (PrisnaSocialCounterCommon.CSS.hasClass(_section, "prisna_social_counter_toggle"))
			type = "toggle";

		switch (type) {
			case "toggle": {
				for (var i=0; i<this._targets[section_id].length; i++)
					if (this._targets[section_id][i].checked)
						jQuery(this._targets[section_id][i]).click();
				break;
			}
		}
		
	},
	
	add: function(_target, _event, _function) {
		
		this._record(_target);
		
		_target.bind(_event, _function);
		
	}
	
};

PrisnaSocialCounterCommon.Heading = function(_target) {
	
	this._id = _target.id;
	this._target = _target;
	this._items = [];

	this._initialize_items();
	this._initialize_events();
	this._set_styles();	
	
};
	
PrisnaSocialCounterCommon.Heading.prototype._initialize_events = function() {
	
	PrisnaSocialCounterCommon.addEvent(this._target, "click", function(e) {

		var owner = PrisnaSocialCounterCommon.getOwnerParent(PrisnaSocialCounterCommon.getOwner(e));
		owner._owner._click();
		
	}, false);
	
};

PrisnaSocialCounterCommon.Heading.prototype.isShowing = function() {
	
	return !PrisnaSocialCounterCommon.CSS.hasClass(this._target, "prisna_social_counter_heading_hiding");
	
};

PrisnaSocialCounterCommon.Heading.prototype._click = function() {
	
	this._show(!this.isShowing());
	
};

PrisnaSocialCounterCommon.Heading.prototype._show = function(_state, _now) {

	PrisnaSocialCounterCommon.CSS.chooseClass(this._target, _state, "prisna_social_counter_heading_showing", "prisna_social_counter_heading_hiding");

	var items = jQuery(this._items);
	items = items.not(".prisna_social_counter_no_display.prisna_social_counter_section_tabbed_2");

	if (_now === true) {
		if (_state)
			items.show();
		else
			items.hide();
	}
	else {
		if (_state)
			items.slideDown("fast");
		else
			items.slideUp("fast");

		PrisnaSocialCounterCommon.Cookie.set("prisna_social_counter_heading_" + this._id, _state ? "true" : "false", 10, false, false, false);

	}
	
	this._click_items(items, _now === true); // to satisfy dependencies

};

PrisnaSocialCounterCommon.Heading.prototype._click_items = function(_items, _delayed) {

	if (_delayed)
		setTimeout(function() {
			for (var i=0; i<_items.length; i++)
				PrisnaSocialCounterCommon.Dependencies.simulate(_items[i]);
		}, 200);
	else
		for (var i=0; i<_items.length; i++)
			PrisnaSocialCounterCommon.Dependencies.simulate(_items[i]);
	
};

PrisnaSocialCounterCommon.Heading.prototype._click_item = function(_item, _name) {
	
	var type;
	
	if (PrisnaSocialCounterCommon.CSS.hasClass(_item, "prisna_social_counter_toggle"))
		type = "toggle";
	
	switch (type) {
		case "toggle": {
			
			break;
		}
	}
	
};

PrisnaSocialCounterCommon.Heading.prototype._initialize_items = function() {

	this._target._owner = this;
	
	var item = this._get_next_sibling(this._target);

	while (item != null && item.className.match(/\bprisna_social_counter_section_tabbed_\d+\b/)) {
		this._items.push(item);
		item = this._get_next_sibling(item);
	}
	
	var cookie = PrisnaSocialCounterCommon.Cookie.get("prisna_social_counter_heading_" + this._id);
	if (cookie == "true" || cookie == "false")
		this._show(cookie == "true", true);
	
};
	
PrisnaSocialCounterCommon.Heading.prototype._get_next_sibling = function(_reference) {

	var result = _reference.nextSibling;
	while (result != null && result.nodeType != 1)
		result = result.nextSibling;
	
	return result;
	
};
	
PrisnaSocialCounterCommon.Heading.prototype._set_styles = function() {
		
	PrisnaSocialCounterCommon.CSS.addClass(this._target, "prisna_social_counter_heading_enabled");
	
};

PrisnaSocialCounterCommon.Cookie = {

	get: function(name) {
		var start = document.cookie.indexOf(name+"=");
		var len = start + name.length + 1;
		if ((!start) && (name != document.cookie.substring(0, name.length))) {
			return null;
		}
		if (start == -1) 
			return null;
		var end = document.cookie.indexOf( ';', len );
		if (end == -1) 
			end = document.cookie.length;
		return decodeURIComponent(document.cookie.substring(len, end));
	},

	set: function(name, value, expires, path, domain, secure) {
		var today = new Date();
		today.setTime(today.getTime());

		if (expires)
			expires = expires * 1000 * 60 * 60 * 24;
		
		var expires_date = new Date(today.getTime() + (expires));
		document.cookie = name + '=' + escape(value) +
			((expires) ? ';expires=' + expires_date.toGMTString() : '') + //expires.toGMTString()
			((path) ? ';path=' + path : '') +
			((domain) ? ';domain=' + domain : '') +
			((secure) ? ';secure' : '');
	},

	remove: function(name, path, domain) {
		if (PrisnaSocialCounterCommon.Cookie.get(name)) document.cookie = name + '=' +
				((path) ? ';path=' + path : '') +
				((domain) ? ';domain=' + domain : '') +
				';expires=Thu, 01-Jan-1970 00:00:01 GMT';
	}
	
};

PrisnaSocialCounterCommon.initializeTooltip = function(_target) {

	jQuery(_target).tooltip({
		effect: "slide",
		relative: true,
		direction: "left",
		position: "center left"
	});

};

PrisnaSocialCounterCommon.CSS = {

	hasClass: function(_element, className) { 
		_element = PrisnaSocialCounterCommon.$(_element);
		if (!_element)
			return;
		if(_element&&className&&_element.className) { 
			return new RegExp('\\b'+PrisnaSocialCounterCommon.trim(className)+'\\b').test(_element.className);
		}
		return false;
	},
	
	addClass: function(_element, className) {
		_element = PrisnaSocialCounterCommon.$(_element);
		if (!_element)
			return;
		if(_element&&className) {
			if(!PrisnaSocialCounterCommon.CSS.hasClass(_element, className)) {
				className = PrisnaSocialCounterCommon.trim(className);
				if(_element.className) { 
					_element.className += " " + className;
				}
				else { 
					_element.className = className; 
				}
			}
		}
		return this;
	},
	
	removeClass: function(_element, className) {
		_element = PrisnaSocialCounterCommon.$(_element);
		if (!_element)
			return;
		if(_element&&className&&_element.className) {
			className = PrisnaSocialCounterCommon.trim(className);
			var regexp = new RegExp("\\b" + className + "\\b","g");
			_element.className = _element.className.replace(regexp,"");
		}
		return this;
	},
		
	conditionClass: function(_element, className, shouldShow) { 
		if(shouldShow) { 
			PrisnaSocialCounterCommon.CSS.addClass(_element, className);
		}
		else { 
			PrisnaSocialCounterCommon.CSS.removeClass(_element, className);
		}
	},
		
	chooseClass: function(_element, expression, trueClass, falseClass) { 
		PrisnaSocialCounterCommon.CSS.conditionClass(_element, trueClass, expression);
		PrisnaSocialCounterCommon.CSS.conditionClass(_element, falseClass, !expression);
	},
		
	setClass: function(_element, className) { 
		_element = PrisnaSocialCounterCommon.$(_element);
		if (!_element)
			return;
		_element.className = className;
		return this;
	},
		
	toggleClass: function(_element, className) {
		_element = PrisnaSocialCounterCommon.$(_element);
		if(PrisnaSocialCounterCommon.CSS.hasClass(_element, className)) {
			return PrisnaSocialCounterCommon.CSS.removeClass(_element, className);
		}
		else {
			return PrisnaSocialCounterCommon.CSS.addClass(_element, className);
		}
	},
		
	setStyle: function(_element, name, value) {
		if (!_element)
			return;
		_element.style[name] = value;
		return _element;
	}

};

PrisnaSocialCounterCommon.Tabs = function(level) {
	
	this.tabs = [];
	this.level = typeof level == "undefined" ? 0 : level;

};

PrisnaSocialCounterCommon.Tabs.prototype.registerTab = function(param, callback) {
	
	var target = PrisnaSocialCounterCommon.$(param + "_menu");
	
	if (!target)
		return;
	
	target._owner = this;
	
	PrisnaSocialCounterCommon.addEvent(PrisnaSocialCounterCommon.$(param + "_menu"), "click", function(e) {

		var owner = PrisnaSocialCounterCommon.getOwnerParent(PrisnaSocialCounterCommon.getOwner(e));
		owner._owner.selectTab(param);
		
		if (typeof callback != "undefined")
			callback(param);
		
	}, false);
	
	this.tabs.push(param);

};

PrisnaSocialCounterCommon.Tabs.prototype.selectTab = function(name) {

	for(var i=0; i<this.tabs.length; i++) {
		this.displayTab(this.tabs[i], name == this.tabs[i]);
		this.selectMenu(this.tabs[i], false);
	}

	this.selectMenu(name, true);

	this.setField(name);

};

PrisnaSocialCounterCommon.Tabs.prototype.setField = function(name) {
	
	var aux = this.level !== 0 ? "_" + this.level : "";
	PrisnaSocialCounterCommon.$("prisna_tab" + aux).value = name;
	
};

PrisnaSocialCounterCommon.Tabs.prototype.displayTab = function(name, state) {

	PrisnaSocialCounterCommon.CSS.chooseClass(PrisnaSocialCounterCommon.$(name + "_tab"), state, "prisna_social_counter_display", "prisna_social_counter_no_display");

};

PrisnaSocialCounterCommon.Tabs.prototype.getSelected = function() {
	
	for(var i=0; i<this.tabs.length; i++) 
		if(PrisnaSocialCounterCommon.CSS.hasClass(this.tabs[i] + "_menu", "prisna_social_counter_ui_tab_selected")) 
			return this.tabs[i];
	
	return "";
};

PrisnaSocialCounterCommon.Tabs.prototype.selectMenu = function(name, state) {

	PrisnaSocialCounterCommon.CSS.chooseClass(PrisnaSocialCounterCommon.$(name + "_menu"), state, "prisna_social_counter_ui_tab_selected", "prisna_social_counter_ui_tab_unselected");

};

(function(a) {
	a.tools = a.tools || {
		version: "dev"
	}, a.tools.tooltip = {
		conf: {
			effect: "toggle",
			fadeOutSpeed: "fast",
			predelay: 0,
			delay: 30,
			opacity: 1,
			tip: 0,
			fadeIE: !1,
			position: ["top", "center"],
			offset: [0, 0],
			relative: !1,
			cancelDefault: !0,
			events: {
				def: "mouseenter,mouseleave",
				input: "focus,blur",
				widget: "focus mouseenter,blur mouseleave",
				tooltip: "mouseenter,mouseleave"
			},
			layout: "<div/>",
			tipClass: "tooltip"
		},
		addEffect: function(a, c, d) {
			b[a] = [c, d]
		}
	};
	var b = {
		toggle: [function(a) {
			var b = this.getConf(),
				c = this.getTip(),
				d = b.opacity;
			d < 1 && c.css({
				opacity: d
			}), c.show(), a.call()
		}, function(a) {
			this.getTip().hide(), a.call()
		}],
		fade: [function(b) {
			var c = this.getConf();
			!a.browser.msie || c.fadeIE ? this.getTip().fadeTo(c.fadeInSpeed, c.opacity, b) : (this.getTip().show(), b())
		}, function(b) {
			var c = this.getConf();
			!a.browser.msie || c.fadeIE ? this.getTip().fadeOut(c.fadeOutSpeed, b) : (this.getTip().hide(), b())
		}]
	};

	function c(b, c, d) {
		var e = d.relative ? b.position().top : b.offset().top,
			f = d.relative ? b.position().left : b.offset().left,
			g = d.position[0];
		e -= c.outerHeight() - d.offset[0], f += b.outerWidth() + d.offset[1], /iPad/i.test(navigator.userAgent) && (e -= a(window).scrollTop());
		var h = c.outerHeight() + b.outerHeight();
		g == "center" && (e += h / 2), g == "bottom" && (e += h), g = d.position[1];
		var i = c.outerWidth() + b.outerWidth();
		g == "center" && (f -= i / 2), g == "left" && (f -= i);
		return {
			top: e,
			left: f
		}
	}

	function d(d, e) {
		var f = this,
			g = d.add(f),
			h, i = 0,
			j = 0,
			k = d.attr("title"),
			l = d.attr("data-tooltip"),
			m = b[e.effect],
			n, o = d.is(":input"),
			p = o && d.is(":checkbox, :radio, select, :button, :submit"),
			q = d.attr("type"),
			r = e.events[q] || e.events[o ? p ? "widget" : "input" : "def"];
		if (!m) throw "Nonexistent effect \"" + e.effect + "\"";
		r = r.split(/,\s*/);
		if (r.length != 2) throw "Tooltip: bad events configuration for " + q;
		d.bind(r[0], function(a) {
			clearTimeout(i), e.predelay ? j = setTimeout(function() {
				f.show(a)
			}, e.predelay) : f.show(a)
		}).bind(r[1], function(a) {
			clearTimeout(j), e.delay ? i = setTimeout(function() {
				f.hide(a)
			}, e.delay) : f.hide(a)
		}), k && e.cancelDefault && (d.removeAttr("title"), d.data("title", k)), a.extend(f, {
			show: function(b) {
				if (!h) {
					l ? h = a(l) : e.tip ? h = a(e.tip).eq(0) : k ? h = a(e.layout).addClass(e.tipClass).appendTo(document.body).hide().append(k) : (h = d.next(), h.length || (h = d.parent().next()));
					if (!h.length) throw "Cannot find tooltip for " + d
				}
				if (f.isShown()) return f;
				h.stop(!0, !0);
				var o = c(d, h, e);
				e.tip && h.html(d.data("title")), b = a.Event(), b.type = "onBeforeShow", g.trigger(b, [o]);
				if (b.isDefaultPrevented()) return f;
				o = c(d, h, e), h.css({
					position: "absolute",
					top: o.top,
					left: o.left
				}), n = !0, m[0].call(f, function() {
					b.type = "onShow", n = "full", g.trigger(b)
				});
				var p = e.events.tooltip.split(/,\s*/);
				h.data("__set") || (h.unbind(p[0]).bind(p[0], function() {
					clearTimeout(i), clearTimeout(j)
				}), p[1] && !d.is("input:not(:checkbox, :radio), textarea") && h.unbind(p[1]).bind(p[1], function(a) {
					a.relatedTarget != d[0] && d.trigger(r[1].split(" ")[0])
				}), e.tip || h.data("__set", !0));
				return f
			},
			hide: function(c) {
				if (!h || !f.isShown()) return f;
				c = a.Event(), c.type = "onBeforeHide", g.trigger(c);
				if (!c.isDefaultPrevented()) {
					n = !1, b[e.effect][1].call(f, function() {
						c.type = "onHide", g.trigger(c)
					});
					return f
				}
			},
			isShown: function(a) {
				return a ? n == "full" : n
			},
			getConf: function() {
				return e
			},
			getTip: function() {
				return h
			},
			getTrigger: function() {
				return d
			}
		}), a.each("onHide,onBeforeShow,onShow,onBeforeHide".split(","), function(b, c) {
			a.isFunction(e[c]) && a(f).bind(c, e[c]), f[c] = function(b) {
				b && a(f).bind(c, b);
				return f
			}
		})
	}
	a.fn.tooltip = function(b) {
		var c = this.data("tooltip");
		if (c) return c;
		b = a.extend(!0, {}, a.tools.tooltip.conf, b), typeof b.position == "string" && (b.position = b.position.split(/,?\s/)), this.each(function() {
			c = new d(a(this), b), a(this).data("tooltip", c)
		});
		return b.api ? c : this
	}
})(jQuery);
(function(a) {
	var b = a.tools.tooltip;
	b.dynamic = {
		conf: {
			classNames: "top right bottom left"
		}
	};

	function c(b) {
		var c = a(window),
			d = c.width() + c.scrollLeft(),
			e = c.height() + c.scrollTop();
		return [b.offset().top <= c.scrollTop(), d <= b.offset().left + b.width(), e <= b.offset().top + b.height(), c.scrollLeft() >= b.offset().left]
	}

	function d(a) {
		var b = a.length;
		while (b--)
			if (a[b]) return !1;
		return !0
	}
	a.fn.dynamic = function(e) {
		typeof e == "number" && (e = {
			speed: e
		}), e = a.extend({}, b.dynamic.conf, e);
		var f = e.classNames.split(/\s/),
			g;
		this.each(function() {
			var b = a(this).tooltip().onBeforeShow(function(b, h) {
				var i = this.getTip(),
					j = this.getConf();
				g || (g = [j.position[0], j.position[1], j.offset[0], j.offset[1], a.extend({}, j)]), a.extend(j, g[4]), j.position = [g[0], g[1]], j.offset = [g[2], g[3]], i.css({
					visibility: "hidden",
					position: "absolute",
					top: h.top,
					left: h.left
				}).show();
				var k = c(i);
				if (!d(k)) {
					k[2] && (a.extend(j, e.top), j.position[0] = "top", i.addClass(f[0])), k[3] && (a.extend(j, e.right), j.position[1] = "right", i.addClass(f[1])), k[0] && (a.extend(j, e.bottom), j.position[0] = "bottom", i.addClass(f[2])), k[1] && (a.extend(j, e.left), j.position[1] = "left", i.addClass(f[3]));
					if (k[0] || k[2]) j.offset[0] *= -1;
					if (k[1] || k[3]) j.offset[1] *= -1
				}
				i.css({
					visibility: "visible"
				}).hide()
			});
			b.onBeforeShow(function() {
				var a = this.getConf(),
					b = this.getTip();
				setTimeout(function() {
					a.position = [g[0], g[1]], a.offset = [g[2], g[3]]
				}, 0)
			}), b.onHide(function() {
				var a = this.getTip();
				a.removeClass(e.classNames)
			}), ret = b
		});
		return e.api ? ret : this
	}
})(jQuery);
(function(a) {
	var b = a.tools.tooltip;
	a.extend(b.conf, {
		direction: "up",
		bounce: !1,
		slideOffset: 10,
		slideInSpeed: 200,
		slideOutSpeed: 200,
		slideFade: !a.browser.msie
	});
	var c = {
		up: ["-", "top"],
		down: ["+", "top"],
		left: ["-", "left"],
		right: ["+", "left"]
	};
	b.addEffect("slide", function(a) {
		var b = this.getConf(),
			d = this.getTip(),
			e = b.slideFade ? {
				opacity: b.opacity
			} : {},
			f = c[b.direction] || c.up;
		e[f[1]] = f[0] + "=" + b.slideOffset, b.slideFade && d.css({
			opacity: 0
		}), d.show().animate(e, b.slideInSpeed, a)
	}, function(b) {
		var d = this.getConf(),
			e = d.slideOffset,
			f = d.slideFade ? {
				opacity: 0
			} : {},
			g = c[d.direction] || c.up,
			h = "" + g[0];
		d.bounce && (h = h == "+" ? "-" : "+"), f[g[1]] = h + "=" + e, this.getTip().animate(f, d.slideOutSpeed, function() {
			a(this).hide(), b.call()
		})
	})
})(jQuery);

// jQuery List DragSort v0.4.3
// License: http://dragsort.codeplex.com/license
(function(b) {
	b.fn.dragsort = function(k) {
		var d = b.extend({}, b.fn.dragsort.defaults, k),
			g = [],
			a = null,
			j = null;
		this.selector && b("head").append("<style type='text/css'>" + (this.selector.split(",").join(" " + d.dragSelector + ",") + " " + d.dragSelector) + " { cursor: move; }</style>");
		this.each(function(k, i) {
			b(i).is("table") && b(i).children().size() == 1 && b(i).children().is("tbody") && (i = b(i).children().get(0));
			var m = {
				draggedItem: null,
				placeHolderItem: null,
				pos: null,
				offset: null,
				offsetLimit: null,
				scroll: null,
				container: i,
				init: function() {
					b(this.container).attr("data-listIdx", k).mousedown(this.grabItem).find(d.dragSelector).css("cursor", "move");
					b(this.container).children(d.itemSelector).each(function(a) {
						b(this).attr("data-itemIdx", a)
					})
				},
				grabItem: function(e) {
					if (!(e.which != 1 || b(e.target).is(d.dragSelectorExclude))) {
						for (var c = e.target; !b(c).is("[data-listIdx='" + b(this).attr("data-listIdx") + "'] " + d.dragSelector);) {
							if (c == this) return;
							c = c.parentNode
						}
						a != null && a.draggedItem != null && a.dropItem();
						b(e.target).css("cursor", "move");
						a = g[b(this).attr("data-listIdx")];
						a.draggedItem = b(c).closest(d.itemSelector);
						var c = parseInt(a.draggedItem.css("marginTop")),
							f = parseInt(a.draggedItem.css("marginLeft"));
						a.offset = a.draggedItem.offset();
						a.offset.top = e.pageY - a.offset.top + (isNaN(c) ? 0 : c) - 1;
						a.offset.left = e.pageX - a.offset.left + (isNaN(f) ? 0 : f) - 1;
						if (!d.dragBetween) c = b(a.container).outerHeight() == 0 ? Math.max(1, Math.round(0.5 + b(a.container).children(d.itemSelector).size() * a.draggedItem.outerWidth() / b(a.container).outerWidth())) * a.draggedItem.outerHeight() : b(a.container).outerHeight(), a.offsetLimit = b(a.container).offset(), a.offsetLimit.right = a.offsetLimit.left + b(a.container).outerWidth() - a.draggedItem.outerWidth(), a.offsetLimit.bottom = a.offsetLimit.top + c - a.draggedItem.outerHeight();
						var c = a.draggedItem.height(),
							f = a.draggedItem.width(),
							h = a.draggedItem.attr("style");
						if (jQuery.browser.msie) f = f + 2;
						a.draggedItem.attr("data-origStyle", h ? h : "");
						d.itemSelector == "tr" ? (a.draggedItem.children().each(function() {
							b(this).width(b(this).width())
						}), a.placeHolderItem = a.draggedItem.clone().attr("data-placeHolder", !0), a.draggedItem.after(a.placeHolderItem), a.placeHolderItem.children().each(function() {
							b(this).css({
								borderWidth: 0,
								width: b(this).width() + 1,
								height: b(this).height() + 1
							}).html("&nbsp;")
						})) : (a.draggedItem.after(d.placeHolderTemplate), a.placeHolderItem = a.draggedItem.next().css({
							height: c,
							width: f
						}).attr("data-placeHolder", !0));
						a.draggedItem.css({
							position: "absolute",
							opacity: 0.8,
							"z-index": 999,
							height: c,
							width: f
						});
						b(g).each(function(a, b) {
							b.createDropTargets();
							b.buildPositionTable()
						});
						a.scroll = {
							moveX: 0,
							moveY: 0,
							maxX: b(document).width() - b(window).width(),
							maxY: b(document).height() - b(window).height()
						};
						a.scroll.scrollY = window.setInterval(function() {
							if (d.scrollContainer != window) b(d.scrollContainer).scrollTop(b(d.scrollContainer).scrollTop() + a.scroll.moveY);
							else {
								var c = b(d.scrollContainer).scrollTop();
								if (a.scroll.moveY > 0 && c < a.scroll.maxY || a.scroll.moveY < 0 && c > 0) b(d.scrollContainer).scrollTop(c + a.scroll.moveY), a.draggedItem.css("top", a.draggedItem.offset().top + a.scroll.moveY + 1)
							}
						}, 10);
						a.scroll.scrollX = window.setInterval(function() {
							if (d.scrollContainer != window) b(d.scrollContainer).scrollLeft(b(d.scrollContainer).scrollLeft() + a.scroll.moveX);
							else {
								var c = b(d.scrollContainer).scrollLeft();
								if (a.scroll.moveX > 0 && c < a.scroll.maxX || a.scroll.moveX < 0 && c > 0) b(d.scrollContainer).scrollLeft(c + a.scroll.moveX), a.draggedItem.css("left", a.draggedItem.offset().left + a.scroll.moveX + 1)
							}
						}, 10);
						a.setPos(e.pageX, e.pageY);
						b(document).bind("selectstart", a.stopBubble);
						b(document).bind("mousemove", a.swapItems);
						b(document).bind("mouseup", a.dropItem);
						d.scrollContainer != window && b(window).bind("DOMMouseScroll mousewheel", a.wheel);
						return !1
					}
				},
				setPos: function(e, c) {
					var f = c - this.offset.top,
						h = e - this.offset.left;
					d.dragBetween || (f = Math.min(this.offsetLimit.bottom, Math.max(f, this.offsetLimit.top)), h = Math.min(this.offsetLimit.right, Math.max(h, this.offsetLimit.left)));
					this.draggedItem.parents().each(function() {
						if (b(this).css("position") != "static" && (!b.browser.mozilla || b(this).css("display") != "table")) {
							var a = b(this).offset();
							f -= a.top;
							h -= a.left;
							return !1
						}
					});
					if (d.scrollContainer == window) c -= b(window).scrollTop(), e -= b(window).scrollLeft(), c = Math.max(0, c - b(window).height() + 5) + Math.min(0, c - 5), e = Math.max(0, e - b(window).width() + 5) + Math.min(0, e - 5);
					else var l = b(d.scrollContainer),
						g = l.offset(),
						c = Math.max(0, c - l.height() - g.top) + Math.min(0, c - g.top),
						e = Math.max(0, e - l.width() - g.left) + Math.min(0, e - g.left);
					a.scroll.moveX = e == 0 ? 0 : e * d.scrollSpeed / Math.abs(e);
					a.scroll.moveY = c == 0 ? 0 : c * d.scrollSpeed / Math.abs(c);
					this.draggedItem.css({
						top: f,
						left: h
					})
				},
				wheel: function(e) {
					if ((b.browser.safari || b.browser.mozilla) && a && d.scrollContainer != window) {
						var c = b(d.scrollContainer),
							f = c.offset();
						e.pageX > f.left && e.pageX < f.left + c.width() && e.pageY > f.top && e.pageY < f.top + c.height() && (f = e.detail ? e.detail * 5 : e.wheelDelta / -2, c.scrollTop(c.scrollTop() + f), e.preventDefault())
					}
				},
				buildPositionTable: function() {
					var a = this.draggedItem == null ? null : this.draggedItem.get(0),
						c = [];
					b(this.container).children(d.itemSelector).each(function(d, h) {
						if (h != a) {
							var g = b(h).offset();
							g.right = g.left + b(h).width();
							g.bottom = g.top + b(h).height();
							g.elm = h;
							c.push(g)
						}
					});
					this.pos = c
				},
				dropItem: function() {
					if (a.draggedItem != null) {
						b(a.container).find(d.dragSelector).css("cursor", "move");
						a.placeHolderItem.before(a.draggedItem);
						var e = a.draggedItem.attr("data-origStyle");
						a.draggedItem.attr("style", e);
						e == "" && a.draggedItem.removeAttr("style");
						a.draggedItem.removeAttr("data-origStyle");
						a.placeHolderItem.remove();
						b("[data-dropTarget]").remove();
						window.clearInterval(a.scroll.scrollY);
						window.clearInterval(a.scroll.scrollX);
						var c = !1;
						b(g).each(function() {
							b(this.container).children(d.itemSelector).each(function(a) {
								parseInt(b(this).attr("data-itemIdx")) != a && (c = !0, b(this).attr("data-itemIdx", a))
							})
						});
						c && d.dragEnd.apply(a.draggedItem);
						a.draggedItem = null;
						b(document).unbind("selectstart", a.stopBubble);
						b(document).unbind("mousemove", a.swapItems);
						b(document).unbind("mouseup", a.dropItem);
						d.scrollContainer != window && b(window).unbind("DOMMouseScroll mousewheel", a.wheel);
						return !1
					}
				},
				stopBubble: function() {
					return !1
				},
				swapItems: function(e) {
					if (a.draggedItem == null) return !1;
					a.setPos(e.pageX, e.pageY);
					for (var c = a.findPos(e.pageX, e.pageY), f = a, h = 0; c == -1 && d.dragBetween && h < g.length; h++) c = g[h].findPos(e.pageX, e.pageY), f = g[h];
					if (c == -1 || b(f.pos[c].elm).attr("data-placeHolder")) return !1;
					j == null || j.top > a.draggedItem.offset().top || j.left > a.draggedItem.offset().left ? b(f.pos[c].elm).before(a.placeHolderItem) : b(f.pos[c].elm).after(a.placeHolderItem);
					b(g).each(function(a, b) {
						b.createDropTargets();
						b.buildPositionTable()
					});
					j = a.draggedItem.offset();
					return !1
				},
				findPos: function(a, b) {
					for (var d = 0; d < this.pos.length; d++)
						if (this.pos[d].left < a && this.pos[d].right > a && this.pos[d].top < b && this.pos[d].bottom > b) return d;
					return -1
				},
				createDropTargets: function() {
					d.dragBetween && b(g).each(function() {
						var d = b(this.container).find("[data-placeHolder]"),
							c = b(this.container).find("[data-dropTarget]");
						d.size() > 0 && c.size() > 0 ? c.remove() : d.size() == 0 && c.size() == 0 && (b(this.container).append(a.placeHolderItem.removeAttr("data-placeHolder").clone().attr("data-dropTarget", !0)), a.placeHolderItem.attr("data-placeHolder", !0))
					})
				}
			};
			m.init();
			g.push(m)
		});
		return this
	};
	b.fn.dragsort.defaults = {
		itemSelector: "li",
		dragSelector: "li",
		dragSelectorExclude: "input, textarea, a[href]",
		dragEnd: function() {},
		dragBetween: !1,
		placeHolderTemplate: "<li>&nbsp;</li>",
		scrollContainer: window,
		scrollSpeed: 5
	}
})(jQuery);

PrisnaSocialCounterCommon.jscolor = {

	dir : '', // location of PrisnaSocialCounterCommon.jscolor directory (leave empty to autodetect)
	bindClass : 'prisna_social_counter_color_picker', // class name
	binding : true, // automatic binding via <input class="...">
	preloading : true, // use image preloading?


	install : function() {
		PrisnaSocialCounterCommon.jscolor.addEvent(window, 'load', PrisnaSocialCounterCommon.jscolor.init);
	},


	init : function() {

	},


	getDir : function() {
		if(!PrisnaSocialCounterCommon.jscolor.dir) {
			var detected = PrisnaSocialCounterCommon.jscolor.detectDir();
			PrisnaSocialCounterCommon.jscolor.dir = detected!==false ? detected : 'PrisnaSocialCounterCommon.jscolor/';
		}
		return PrisnaSocialCounterCommon.jscolor.dir;
	},

	detectDir : function() {
		var base = location.href;

		var e = document.getElementsByTagName('base');
		for(var i=0; i<e.length; i+=1) {
			if(e[i].href) { base = e[i].href; }
		}

		var e = document.getElementsByTagName('script');
		for(var i=0; i<e.length; i+=1) {
			if(e[i].src && /(^|\/)PrisnaSocialCounterCommon.jscolor\.js([?#].*)?$/i.test(e[i].src)) {
				var src = new PrisnaSocialCounterCommon.jscolor.URI(e[i].src);
				var srcAbs = src.toAbsolute(base);
				srcAbs.path = srcAbs.path.replace(/[^\/]+$/, ''); // remove filename
				srcAbs.query = null;
				srcAbs.fragment = null;
				return srcAbs.toString();
			}
		}
		return false;
	},

	bind : function() {
		var matchClass = new RegExp('(^|\\s)('+PrisnaSocialCounterCommon.jscolor.bindClass+')\\s*(\\{[^}]*\\})?', 'i');
		var e = document.getElementsByTagName('input');
		for(var i=0; i<e.length; i+=1) {
			var m;
			if(!e[i].color && e[i].id && e[i].className && (m = e[i].className.match(matchClass))) {
				var prop = {};
				if(m[3]) {
					try {
						eval('prop='+m[3]);
					} catch(eInvalidProp) {}
				}
				e[i].color = new PrisnaSocialCounterCommon.jscolor.color(e[i], prop);
			}
		}
	},

	preload : function() {
		for(var fn in PrisnaSocialCounterCommon.jscolor.imgRequire) {
			if(PrisnaSocialCounterCommon.jscolor.imgRequire.hasOwnProperty(fn)) {
				PrisnaSocialCounterCommon.jscolor.loadImage(fn);
			}
		}
	},


	images : {
		pad : [ 181, 101 ],
		sld : [ 16, 101 ],
		cross : [ 15, 15 ],
		arrow : [ 7, 11 ]
	},


	imgRequire : {},
	imgLoaded : {},


	requireImage : function(filename) {
		PrisnaSocialCounterCommon.jscolor.imgRequire[filename] = true;
	},


	loadImage : function(filename) {
		if(!PrisnaSocialCounterCommon.jscolor.imgLoaded[filename]) {
			PrisnaSocialCounterCommon.jscolor.imgLoaded[filename] = new Image();
			PrisnaSocialCounterCommon.jscolor.imgLoaded[filename].src = PrisnaSocialCounterCommon.jscolor.getDir()+filename;
		}
	},


	fetchElement : function(mixed) {
		return typeof mixed === 'string' ? document.getElementById(mixed) : mixed;
	},


	addEvent : function(el, evnt, func) {
		if(el.addEventListener) {
			el.addEventListener(evnt, func, false);
		} else if(el.attachEvent) {
			el.attachEvent('on'+evnt, func);
		}
	},


	fireEvent : function(el, evnt) {
		if(!el) {
			return;
		}
		if(document.createEvent) {
			var ev = document.createEvent('HTMLEvents');
			ev.initEvent(evnt, true, true);
			el.dispatchEvent(ev);
		} else if(document.createEventObject) {
			var ev = document.createEventObject();
			el.fireEvent('on'+evnt, ev);
		} else if(el['on'+evnt]) { // alternatively use the traditional event model (IE5)
			el['on'+evnt]();
		}
	},


	getElementPos : function(e) {
		var e1=e, e2=e;
		var x=0, y=0;
		if(e1.offsetParent) {
			do {
				x += e1.offsetLeft;
				y += e1.offsetTop;
			} while(e1 = e1.offsetParent);
		}
		while((e2 = e2.parentNode) && e2.nodeName.toUpperCase() !== 'BODY') {
			x -= e2.scrollLeft;
			y -= e2.scrollTop;
		}
		return [x, y];
	},


	getElementSize : function(e) {
		return [e.offsetWidth, e.offsetHeight];
	},


	getRelMousePos : function(e) {
		var x = 0, y = 0;
		if (!e) { e = window.event; }
		if (typeof e.offsetX === 'number') {
			x = e.offsetX;
			y = e.offsetY;
		} else if (typeof e.layerX === 'number') {
			x = e.layerX;
			y = e.layerY;
		}
		return { x: x, y: y };
	},


	getViewPos : function() {
		if(typeof window.pageYOffset === 'number') {
			return [window.pageXOffset, window.pageYOffset];
		} else if(document.body && (document.body.scrollLeft || document.body.scrollTop)) {
			return [document.body.scrollLeft, document.body.scrollTop];
		} else if(document.documentElement && (document.documentElement.scrollLeft || document.documentElement.scrollTop)) {
			return [document.documentElement.scrollLeft, document.documentElement.scrollTop];
		} else {
			return [0, 0];
		}
	},


	getViewSize : function() {
		if(typeof window.innerWidth === 'number') {
			return [window.innerWidth, window.innerHeight];
		} else if(document.body && (document.body.clientWidth || document.body.clientHeight)) {
			return [document.body.clientWidth, document.body.clientHeight];
		} else if(document.documentElement && (document.documentElement.clientWidth || document.documentElement.clientHeight)) {
			return [document.documentElement.clientWidth, document.documentElement.clientHeight];
		} else {
			return [0, 0];
		}
	},


	URI : function(uri) { // See RFC3986

		this.scheme = null;
		this.authority = null;
		this.path = '';
		this.query = null;
		this.fragment = null;

		this.parse = function(uri) {
			var m = uri.match(/^(([A-Za-z][0-9A-Za-z+.-]*)(:))?((\/\/)([^\/?#]*))?([^?#]*)((\?)([^#]*))?((#)(.*))?/);
			this.scheme = m[3] ? m[2] : null;
			this.authority = m[5] ? m[6] : null;
			this.path = m[7];
			this.query = m[9] ? m[10] : null;
			this.fragment = m[12] ? m[13] : null;
			return this;
		};

		this.toString = function() {
			var result = '';
			if(this.scheme !== null) { result = result + this.scheme + ':'; }
			if(this.authority !== null) { result = result + '//' + this.authority; }
			if(this.path !== null) { result = result + this.path; }
			if(this.query !== null) { result = result + '?' + this.query; }
			if(this.fragment !== null) { result = result + '#' + this.fragment; }
			return result;
		};

		this.toAbsolute = function(base) {
			var base = new PrisnaSocialCounterCommon.jscolor.URI(base);
			var r = this;
			var t = new PrisnaSocialCounterCommon.jscolor.URI;

			if(base.scheme === null) { return false; }

			if(r.scheme !== null && r.scheme.toLowerCase() === base.scheme.toLowerCase()) {
				r.scheme = null;
			}

			if(r.scheme !== null) {
				t.scheme = r.scheme;
				t.authority = r.authority;
				t.path = removeDotSegments(r.path);
				t.query = r.query;
			} else {
				if(r.authority !== null) {
					t.authority = r.authority;
					t.path = removeDotSegments(r.path);
					t.query = r.query;
				} else {
					if(r.path === '') { // TODO: == or === ?
						t.path = base.path;
						if(r.query !== null) {
							t.query = r.query;
						} else {
							t.query = base.query;
						}
					} else {
						if(r.path.substr(0,1) === '/') {
							t.path = removeDotSegments(r.path);
						} else {
							if(base.authority !== null && base.path === '') { // TODO: == or === ?
								t.path = '/'+r.path;
							} else {
								t.path = base.path.replace(/[^\/]+$/,'')+r.path;
							}
							t.path = removeDotSegments(t.path);
						}
						t.query = r.query;
					}
					t.authority = base.authority;
				}
				t.scheme = base.scheme;
			}
			t.fragment = r.fragment;

			return t;
		};

		function removeDotSegments(path) {
			var out = '';
			while(path) {
				if(path.substr(0,3)==='../' || path.substr(0,2)==='./') {
					path = path.replace(/^\.+/,'').substr(1);
				} else if(path.substr(0,3)==='/./' || path==='/.') {
					path = '/'+path.substr(3);
				} else if(path.substr(0,4)==='/../' || path==='/..') {
					path = '/'+path.substr(4);
					out = out.replace(/\/?[^\/]*$/, '');
				} else if(path==='.' || path==='..') {
					path = '';
				} else {
					var rm = path.match(/^\/?[^\/]*/)[0];
					path = path.substr(rm.length);
					out = out + rm;
				}
			}
			return out;
		}

		if(uri) {
			this.parse(uri);
		}

	},


	/*
	 * Usage example:
	 * var myColor = new PrisnaSocialCounterCommon.jscolor.color(myInputElement)
	 */

	color : function(target, prop) {


		this.required = true; // refuse empty values?
		this.adjust = true; // adjust value to uniform notation?
		this.transparent = false; // allow transparent as value?
		this.hash = true; // prefix color with # symbol?
		this.caps = true; // uppercase?
		this.slider = true; // show the value/saturation slider?
		this.valueElement = target; // value holder
		this.styleElement = target; // where to reflect current color
		this.hsv = [0, 0, 1]; // read-only  0-6, 0-1, 0-1
		this.rgb = [1, 1, 1]; // read-only  0-1, 0-1, 0-1

		this.pickerOnfocus = true; // display picker on focus?
		this.pickerMode = 'HSV'; // HSV | HVS
		this.pickerPosition = 'bottom'; // left | right | top | bottom
		this.pickerButtonHeight = 20; // px
		this.pickerClosable = false;
		this.pickerCloseText = 'Close';
		this.pickerButtonColor = 'ButtonText'; // px
		this.pickerFace = 1; // px
		this.pickerFaceColor = 'white'; // CSS color
		this.pickerBorder = 1; // px
		this.pickerBorderColor = '#DFDFDF'; // CSS color
		this.pickerInset = 5; // px
		this.pickerInsetColor = '#FFF'; // CSS color
		this.pickerZIndex = 10000;


		for(var p in prop) {
			if(prop.hasOwnProperty(p)) {
				this[p] = prop[p];
			}
		}


		this.hidePicker = function() {
			if(isPickerOwner()) {
				removePicker();
			}
		};


		this.showPicker = function() {
			if(!isPickerOwner()) {
				var tp = PrisnaSocialCounterCommon.jscolor.getElementPos(target); // target pos
				var ts = PrisnaSocialCounterCommon.jscolor.getElementSize(target); // target size
				var vp = PrisnaSocialCounterCommon.jscolor.getViewPos(); // view pos
				var vs = PrisnaSocialCounterCommon.jscolor.getViewSize(); // view size
				var ps = getPickerDims(this); // picker size
				var a, b, c;
				switch(this.pickerPosition.toLowerCase()) {
					case 'left': a=1; b=0; c=-1; break;
					case 'right':a=1; b=0; c=1; break;
					case 'top':  a=0; b=1; c=-1; break;
					default:     a=0; b=1; c=1; break;
				}
				var l = (ts[b]+ps[b])/2;
				/*
				var pp = [ // picker pos
					-vp[a]+tp[a]+ps[a] > vs[a] ?
						(-vp[a]+tp[a]+ts[a]/2 > vs[a]/2 && tp[a]+ts[a]-ps[a] >= 0 ? tp[a]+ts[a]-ps[a] : tp[a]) :
						tp[a],
					-vp[b]+tp[b]+ts[b]+ps[b]-l+l*c > vs[b] ?
						(-vp[b]+tp[b]+ts[b]/2 > vs[b]/2 && tp[b]+ts[b]-l-l*c >= 0 ? tp[b]+ts[b]-l-l*c : tp[b]+ts[b]-l+l*c) :
						(tp[b]+ts[b]-l+l*c >= 0 ? tp[b]+ts[b]-l+l*c : tp[b]+ts[b]-l-l*c)
				];
				* */
				var pp = [ // picker pos
					-vp[a]+tp[a]+ps[a] > vs[a] ?
						(-vp[a]+tp[a]+ts[a]/2 > vs[a]/2 && tp[a]+ts[a]-ps[a] >= 0 ? tp[a]+ts[a]-ps[a] : tp[a]) :
						tp[a],
					tp[b]+ts[b]-l+l*c >= 0 ? tp[b]+ts[b]-l+l*c : tp[b]+ts[b]-l-l*c
				];				
				drawPicker(pp[a], pp[b] + 2);
			}
		};


		this.importColor = function() {
			if(!valueElement) {
				this.exportColor();
			} 
			else {
				if(!this.adjust) {
					if(!this.fromString(valueElement.value, leaveValue)) {
						styleElement.style.backgroundColor = styleElement.jscStyle.backgroundColor;
						styleElement.style.color = styleElement.jscStyle.color;
						styleElement.style.backgroundPosition = "-1px -1px";
						this.exportColor(leaveValue | leaveStyle);
					}
				}
				else if(!this.required && /^\s*$/.test(valueElement.value)) {
					valueElement.value = '';
					styleElement.style.backgroundColor = styleElement.jscStyle.backgroundColor;
					styleElement.style.color = styleElement.jscStyle.color;
					styleElement.style.backgroundPosition = "-1px -1px";
					this.exportColor(leaveValue | leaveStyle);

				} 
				else if(this.transparent && valueElement.value == "transparent") {
					// OK
					styleElement.style.backgroundColor = "transparent";
					styleElement.style.backgroundPosition = "-1px -36px";
				}
				else if(this.fromString(valueElement.value)) {
					// OK
				} else {
					this.exportColor();
				}
			}
		};


		this.exportColor = function(flags) {
			if(!(flags & leaveValue) && valueElement) {
				var value = this.toString();
				if(this.caps) { value = value.toUpperCase(); }
				if(this.hash) { value = '#'+value; }
				valueElement.value = value;
			}
			if(!(flags & leaveStyle) && styleElement) {
				styleElement.style.backgroundColor =
					'#'+this.toString();
				styleElement.style.color =
					0.213 * this.rgb[0] +
					0.715 * this.rgb[1] +
					0.072 * this.rgb[2]
					< 0.5 ? '#FFF' : '#000';
				styleElement.style.backgroundPosition = "-1px -1px";
			}
			if(!(flags & leavePad) && isPickerOwner()) {
				redrawPad();
			}
			if(!(flags & leaveSld) && isPickerOwner()) {
				redrawSld();
			}
		};


		this.fromHSV = function(h, s, v, flags) { // null = don't change
			h<0 && (h=0) || h>6 && (h=6);
			s<0 && (s=0) || s>1 && (s=1);
			v<0 && (v=0) || v>1 && (v=1);
			this.rgb = HSV_RGB(
				h===null ? this.hsv[0] : (this.hsv[0]=h),
				s===null ? this.hsv[1] : (this.hsv[1]=s),
				v===null ? this.hsv[2] : (this.hsv[2]=v)
			);
			this.exportColor(flags);
		};


		this.fromRGB = function(r, g, b, flags) { // null = don't change
			r<0 && (r=0) || r>1 && (r=1);
			g<0 && (g=0) || g>1 && (g=1);
			b<0 && (b=0) || b>1 && (b=1);
			var hsv = RGB_HSV(
				r===null ? this.rgb[0] : (this.rgb[0]=r),
				g===null ? this.rgb[1] : (this.rgb[1]=g),
				b===null ? this.rgb[2] : (this.rgb[2]=b)
			);
			if(hsv[0] !== null) {
				this.hsv[0] = hsv[0];
			}
			if(hsv[2] !== 0) {
				this.hsv[1] = hsv[1];
			}
			this.hsv[2] = hsv[2];
			this.exportColor(flags);
		};


		this.fromString = function(hex, flags) {
			var m = hex.match(/^\W*([0-9A-F]{3}([0-9A-F]{3})?)\W*$/i);
			if(!m) {
				return false;
			} else {
				if(m[1].length === 6) { // 6-char notation
					this.fromRGB(
						parseInt(m[1].substr(0,2),16) / 255,
						parseInt(m[1].substr(2,2),16) / 255,
						parseInt(m[1].substr(4,2),16) / 255,
						flags
					);
				} else { // 3-char notation
					this.fromRGB(
						parseInt(m[1].charAt(0)+m[1].charAt(0),16) / 255,
						parseInt(m[1].charAt(1)+m[1].charAt(1),16) / 255,
						parseInt(m[1].charAt(2)+m[1].charAt(2),16) / 255,
						flags
					);
				}
				return true;
			}
		};


		this.toString = function() {
			return (
				(0x100 | Math.round(255*this.rgb[0])).toString(16).substr(1) +
				(0x100 | Math.round(255*this.rgb[1])).toString(16).substr(1) +
				(0x100 | Math.round(255*this.rgb[2])).toString(16).substr(1)
			);
		};


		function RGB_HSV(r, g, b) {
			var n = Math.min(Math.min(r,g),b);
			var v = Math.max(Math.max(r,g),b);
			var m = v - n;
			if(m === 0) { return [ null, 0, v ]; }
			var h = r===n ? 3+(b-g)/m : (g===n ? 5+(r-b)/m : 1+(g-r)/m);
			return [ h===6?0:h, m/v, v ];
		}


		function HSV_RGB(h, s, v) {
			if(h === null) { return [ v, v, v ]; }
			var i = Math.floor(h);
			var f = i%2 ? h-i : 1-(h-i);
			var m = v * (1 - s);
			var n = v * (1 - s*f);
			switch(i) {
				case 6:
				case 0: return [v,n,m];
				case 1: return [n,v,m];
				case 2: return [m,v,n];
				case 3: return [m,n,v];
				case 4: return [n,m,v];
				case 5: return [v,m,n];
			}
		}


		function removePicker() {
			delete PrisnaSocialCounterCommon.jscolor.picker.owner;
			document.getElementsByTagName('body')[0].removeChild(PrisnaSocialCounterCommon.jscolor.picker.boxB);
		}


		function drawPicker(x, y) {
			if(!PrisnaSocialCounterCommon.jscolor.picker) {
				PrisnaSocialCounterCommon.jscolor.picker = {
					box : document.createElement('div'),
					boxB : document.createElement('div'),
					pad : document.createElement('div'),
					padB : document.createElement('div'),
					padM : document.createElement('div'),
					padT : document.createElement('div'),
					sld : document.createElement('div'),
					sldB : document.createElement('div'),
					sldM : document.createElement('div'),
					btn : document.createElement('div'),
					btnS : document.createElement('span'),
					btnT : document.createTextNode(THIS.pickerCloseText)
				};
				var images_sld = THIS.transparent ? 79 : PrisnaSocialCounterCommon.jscolor.images.sld[1];
				var step = THIS.transparent ? 2 : 4;
				for(var i=0,segSize=step; i<images_sld; i+=segSize) {
					var seg = document.createElement('div');
					seg.style.height = segSize+'px';
					seg.style.fontSize = '1px';
					seg.style.lineHeight = '0';
					PrisnaSocialCounterCommon.jscolor.picker.sld.appendChild(seg);
				}
				
				PrisnaSocialCounterCommon.jscolor.picker.sldB.appendChild(PrisnaSocialCounterCommon.jscolor.picker.sld);
				PrisnaSocialCounterCommon.jscolor.picker.sldB.appendChild(PrisnaSocialCounterCommon.jscolor.picker.padT);
				PrisnaSocialCounterCommon.jscolor.picker.box.appendChild(PrisnaSocialCounterCommon.jscolor.picker.sldB);
				PrisnaSocialCounterCommon.jscolor.picker.box.appendChild(PrisnaSocialCounterCommon.jscolor.picker.sldM);
				PrisnaSocialCounterCommon.jscolor.picker.padB.appendChild(PrisnaSocialCounterCommon.jscolor.picker.pad);
				PrisnaSocialCounterCommon.jscolor.picker.box.appendChild(PrisnaSocialCounterCommon.jscolor.picker.padB);
				PrisnaSocialCounterCommon.jscolor.picker.box.appendChild(PrisnaSocialCounterCommon.jscolor.picker.padM);
				PrisnaSocialCounterCommon.jscolor.picker.btnS.appendChild(PrisnaSocialCounterCommon.jscolor.picker.btnT);
				PrisnaSocialCounterCommon.jscolor.picker.btn.appendChild(PrisnaSocialCounterCommon.jscolor.picker.btnS);
				PrisnaSocialCounterCommon.jscolor.picker.box.appendChild(PrisnaSocialCounterCommon.jscolor.picker.btn);
				PrisnaSocialCounterCommon.jscolor.picker.boxB.appendChild(PrisnaSocialCounterCommon.jscolor.picker.box);
			}

			var p = PrisnaSocialCounterCommon.jscolor.picker;

			// controls interaction
			p.box.onmouseup =
			p.box.onmouseout = function() { target.focus(); };
			p.box.onmousedown = function() { abortBlur=true; };
			p.box.onmousemove = function(e) {
				if (holdPad || holdSld) {
					holdPad && setPad(e);
					holdSld && setSld(e);
					if (document.selection) {
						document.selection.empty();
					} else if (window.getSelection) {
						window.getSelection().removeAllRanges();
					}
				}
			};
			p.padM.onmouseup =
			p.padM.onmouseout = function() { if(holdPad) { holdPad=false; PrisnaSocialCounterCommon.jscolor.fireEvent(valueElement,'change'); } };
			p.padM.onmousedown = function(e) { holdPad=true; setPad(e); };
			p.sldM.onmouseup =
			p.sldM.onmouseout = function() { if(holdSld) { holdSld=false; PrisnaSocialCounterCommon.jscolor.fireEvent(valueElement,'change'); } };
			p.sldM.onmousedown = function(e) { holdSld=true; setSld(e); };

			p.padT.onmousedown = function(e) { valueElement.value = "transparent"; THIS.importColor(); };

			p.padT.style.position = "absolute";
			p.padT.style.top = "auto";
			p.padT.style.right = "auto";
			p.padT.style.bottom = "0";
			p.padT.style.left = "auto";
			p.padT.style.width = "16px";
			p.padT.style.height = "16px";
			p.padT.style.display = "block";
			p.padT.style.cursor = 'pointer';
			p.padT.style.backgroundPosition = "-4px -80px";
			p.padT.className = "prisna_social_counter_color_picker_view";

			// picker
			var dims = getPickerDims(THIS);
			p.box.style.width = dims[0] + 'px';
			p.box.style.height = dims[1] + 'px';

			// picker border
			p.boxB.style.position = 'absolute';
			p.boxB.style.clear = 'both';
			p.boxB.style.left = x+'px';
			p.boxB.style.top = y+'px';
			p.boxB.style.zIndex = THIS.pickerZIndex;
			p.boxB.style.border = THIS.pickerBorder+'px solid';
			p.boxB.style.borderColor = THIS.pickerBorderColor;
			p.boxB.style.background = THIS.pickerFaceColor;

			// pad image
			p.pad.style.width = PrisnaSocialCounterCommon.jscolor.images.pad[0]+'px';
			p.pad.style.height = PrisnaSocialCounterCommon.jscolor.images.pad[1]+'px';

			// pad border
			p.padB.style.position = 'absolute';
			p.padB.style.left = THIS.pickerFace+'px';
			p.padB.style.top = THIS.pickerFace+'px';
			p.padB.style.border = THIS.pickerInset+'px solid';
			p.padB.style.borderColor = THIS.pickerInsetColor;

			// pad mouse area
			p.padM.style.position = 'absolute';
			p.padM.style.left = '0';
			p.padM.style.top = '0';
			p.padM.style.width = THIS.pickerFace + 2*THIS.pickerInset + PrisnaSocialCounterCommon.jscolor.images.pad[0] + PrisnaSocialCounterCommon.jscolor.images.arrow[0] + 'px';
			p.padM.style.height = p.box.style.height;
			p.padM.style.cursor = 'crosshair';

			// slider image
			p.sld.style.overflow = 'hidden';
			p.sld.style.width = PrisnaSocialCounterCommon.jscolor.images.sld[0]+'px';
			p.sld.style.height = PrisnaSocialCounterCommon.jscolor.images.sld[1]+'px';

			// slider border
			p.sldB.style.display = THIS.slider ? 'block' : 'none';
			p.sldB.style.position = 'absolute';
			p.sldB.style.right = THIS.pickerFace+'px';
			p.sldB.style.top = THIS.pickerFace+'px';
			p.sldB.style.border = THIS.pickerInset+'px solid';
			p.sldB.style.borderColor = THIS.pickerInsetColor;

			// slider mouse area
			p.sldM.style.display = THIS.slider ? 'block' : 'none';
			p.sldM.style.position = 'absolute';
			p.sldM.style.right = '0';
			p.sldM.style.top = '0';
			p.sldM.style.width = PrisnaSocialCounterCommon.jscolor.images.sld[0] + PrisnaSocialCounterCommon.jscolor.images.arrow[0] + THIS.pickerFace + 2*THIS.pickerInset + 'px';
			if (THIS.transparent)
				p.sldM.style.height = "89px";
			else
				p.sldM.style.height = p.box.style.height;
			
			try {
				p.sldM.style.cursor = 'pointer';
			} catch(eOldIE) {
				p.sldM.style.cursor = 'hand';
			}

			// "close" button
			function setBtnBorder() {
				var insetColors = THIS.pickerInsetColor.split(/\s+/);
				var pickerOutsetColor = insetColors.length < 2 ? insetColors[0] : insetColors[1] + ' ' + insetColors[0] + ' ' + insetColors[0] + ' ' + insetColors[1];
				p.btn.style.borderColor = pickerOutsetColor;
			}
			p.btn.style.display = THIS.pickerClosable ? 'block' : 'none';
			p.btn.style.position = 'absolute';
			p.btn.style.left = THIS.pickerFace + 'px';
			p.btn.style.bottom = THIS.pickerFace + 'px';
			p.btn.style.padding = '0 15px';
			p.btn.style.height = '18px';
			p.btn.style.border = THIS.pickerInset + 'px solid';
			setBtnBorder();
			p.btn.style.color = THIS.pickerButtonColor;
			p.btn.style.font = '12px sans-serif';
			p.btn.style.textAlign = 'center';
			try {
				p.btn.style.cursor = 'pointer';
			} catch(eOldIE) {
				p.btn.style.cursor = 'hand';
			}
			p.btn.onmousedown = function () {
				THIS.hidePicker();
			};
			p.btnS.style.lineHeight = p.btn.style.height;

			// load images in optimal order
			switch(modeID) {
				case 0: var padImg = 'hs.png'; break;
				case 1: var padImg = 'hv.png'; break;
			}
			p.padM.className = "prisna_social_counter_color_picker_cross";
			p.sldM.className = "prisna_social_counter_color_picker_arrow";
			p.pad.className = "prisna_social_counter_color_picker_pad";
			/*
			p.padM.style.backgroundImage = "url('"+PrisnaSocialCounterCommon.jscolor.getDir()+"cross.gif')";
			p.padM.style.backgroundRepeat = "no-repeat";
			p.sldM.style.backgroundImage = "url('"+PrisnaSocialCounterCommon.jscolor.getDir()+"arrow.gif')";
			p.sldM.style.backgroundRepeat = "no-repeat";
			p.pad.style.backgroundImage = "url('"+PrisnaSocialCounterCommon.jscolor.getDir()+padImg+"')";
			p.pad.style.backgroundRepeat = "no-repeat";
			p.pad.style.backgroundPosition = "0 0";
			* */

			// place pointers
			redrawPad();
			redrawSld();

			PrisnaSocialCounterCommon.jscolor.picker.owner = THIS;
			document.getElementsByTagName('body')[0].appendChild(p.boxB);
		}


		function getPickerDims(o) {
			var dims = [
				2*o.pickerInset + 2*o.pickerFace + PrisnaSocialCounterCommon.jscolor.images.pad[0] +
					(o.slider ? 2*o.pickerInset + 2*PrisnaSocialCounterCommon.jscolor.images.arrow[0] + PrisnaSocialCounterCommon.jscolor.images.sld[0] : 0),
				o.pickerClosable ?
					4*o.pickerInset + 3*o.pickerFace + PrisnaSocialCounterCommon.jscolor.images.pad[1] + o.pickerButtonHeight :
					2*o.pickerInset + 2*o.pickerFace + PrisnaSocialCounterCommon.jscolor.images.pad[1]
			];
			return dims;
		}


		function redrawPad() {
			// redraw the pad pointer
			switch(modeID) {
				case 0: var yComponent = 1; break;
				case 1: var yComponent = 2; break;
			}
			var x = Math.round((THIS.hsv[0]/6) * (PrisnaSocialCounterCommon.jscolor.images.pad[0]-1));
			var y = Math.round((1-THIS.hsv[yComponent]) * (PrisnaSocialCounterCommon.jscolor.images.pad[1]-1));
			PrisnaSocialCounterCommon.jscolor.picker.padM.style.backgroundPosition =
				(THIS.pickerFace+THIS.pickerInset+x - Math.floor(PrisnaSocialCounterCommon.jscolor.images.cross[0]/2)) + 'px ' +
				(THIS.pickerFace+THIS.pickerInset+y - Math.floor(PrisnaSocialCounterCommon.jscolor.images.cross[1]/2)) + 'px';

			// redraw the slider image
			var seg = PrisnaSocialCounterCommon.jscolor.picker.sld.childNodes;

			switch(modeID) {
				case 0:
					var rgb = HSV_RGB(THIS.hsv[0], THIS.hsv[1], 1);
					for(var i=0; i<seg.length; i+=1) {
						seg[i].style.backgroundColor = 'rgb('+
							(rgb[0]*(1-i/seg.length)*100)+'%,'+
							(rgb[1]*(1-i/seg.length)*100)+'%,'+
							(rgb[2]*(1-i/seg.length)*100)+'%)';
					}
					break;
				case 1:
					var rgb, s, c = [ THIS.hsv[2], 0, 0 ];
					var i = Math.floor(THIS.hsv[0]);
					var f = i%2 ? THIS.hsv[0]-i : 1-(THIS.hsv[0]-i);
					switch(i) {
						case 6:
						case 0: rgb=[0,1,2]; break;
						case 1: rgb=[1,0,2]; break;
						case 2: rgb=[2,0,1]; break;
						case 3: rgb=[2,1,0]; break;
						case 4: rgb=[1,2,0]; break;
						case 5: rgb=[0,2,1]; break;
					}
					for(var i=0; i<seg.length; i+=1) {
						s = 1 - 1/(seg.length-1)*i;
						c[1] = c[0] * (1 - s*f);
						c[2] = c[0] * (1 - s);
						seg[i].style.backgroundColor = 'rgb('+
							(c[rgb[0]]*100)+'%,'+
							(c[rgb[1]]*100)+'%,'+
							(c[rgb[2]]*100)+'%)';
					}
					break;
			}
		}


		function redrawSld() {
			// redraw the slider pointer
			switch(modeID) {
				case 0: var yComponent = 2; break;
				case 1: var yComponent = 1; break;
			}
			var y = Math.round((1-THIS.hsv[yComponent]) * 1 * (PrisnaSocialCounterCommon.jscolor.images.sld[1]-1));
			
			if (THIS.transparent)
				var y_pos = Math.round((THIS.pickerFace+THIS.pickerInset+y - Math.floor(PrisnaSocialCounterCommon.jscolor.images.arrow[1]/2)) * 79 / 101);
			else
				var y_pos = (THIS.pickerFace+THIS.pickerInset+y - Math.floor(PrisnaSocialCounterCommon.jscolor.images.arrow[1]/2));
			
			PrisnaSocialCounterCommon.jscolor.picker.sldM.style.backgroundPosition = '0 ' + y_pos + 'px';
		}


		function isPickerOwner() {
			return PrisnaSocialCounterCommon.jscolor.picker && PrisnaSocialCounterCommon.jscolor.picker.owner === THIS;
		}


		function blurTarget() {
			if(valueElement === target) {
				THIS.importColor();
			}
			if(THIS.pickerOnfocus) {
				THIS.hidePicker();
			}
		}


		function blurValue() {
			if(valueElement !== target) {
				THIS.importColor();
			}
		}


		function setPad(e) {
			var mpos = PrisnaSocialCounterCommon.jscolor.getRelMousePos(e);
			var x = mpos.x - THIS.pickerFace - THIS.pickerInset;
			var y = mpos.y - THIS.pickerFace - THIS.pickerInset;
			switch(modeID) {
				case 0: THIS.fromHSV(x*(6/(PrisnaSocialCounterCommon.jscolor.images.pad[0]-1)), 1 - y/(PrisnaSocialCounterCommon.jscolor.images.pad[1]-1), null, leaveSld); break;
				case 1: THIS.fromHSV(x*(6/(PrisnaSocialCounterCommon.jscolor.images.pad[0]-1)), null, 1 - y/(PrisnaSocialCounterCommon.jscolor.images.pad[1]-1), leaveSld); break;
			}
		}


		function setSld(e) {
			var mpos = PrisnaSocialCounterCommon.jscolor.getRelMousePos(e);
			var y = mpos.y - THIS.pickerFace - THIS.pickerInset; // xxxxxxxxxxxx
			if (THIS.transparent) {
				switch(modeID) {
					case 0: THIS.fromHSV(null, null, 1 - y/(79-1), leavePad); break;
					case 1: THIS.fromHSV(null, 1 - y/(79-1), null, leavePad); break;
				}
			}
			else {
				switch(modeID) {
					case 0: THIS.fromHSV(null, null, 1 - y/(PrisnaSocialCounterCommon.jscolor.images.sld[1]-1), leavePad); break;
					case 1: THIS.fromHSV(null, 1 - y/(PrisnaSocialCounterCommon.jscolor.images.sld[1]-1), null, leavePad); break;
				}
			}
		}


		var THIS = this;
		var modeID = this.pickerMode.toLowerCase()==='hvs' ? 1 : 0;
		var abortBlur = false;
		var
			valueElement = PrisnaSocialCounterCommon.jscolor.fetchElement(this.valueElement),
			styleElement = PrisnaSocialCounterCommon.jscolor.fetchElement(this.styleElement);
		var
			holdPad = false,
			holdSld = false;
		var
			leaveValue = 1<<0,
			leaveStyle = 1<<1,
			leavePad = 1<<2,
			leaveSld = 1<<3;

		// target
		PrisnaSocialCounterCommon.jscolor.addEvent(target, 'focus', function() {
			if(THIS.pickerOnfocus) { THIS.showPicker(); }
		});
		PrisnaSocialCounterCommon.jscolor.addEvent(target, 'blur', function() {
			if(!abortBlur) {
				window.setTimeout(function(){ abortBlur || blurTarget(); abortBlur=false; }, 0);
			} else {
				abortBlur = false;
			}
		});
		PrisnaSocialCounterCommon.jscolor.addEvent(this.styleElement, 'click', function() {
			target.focus();
			THIS.showPicker();
		});

		// valueElement
		if(valueElement) {
			var updateField = function() {
				THIS.fromString(valueElement.value, leaveValue);
			};
			PrisnaSocialCounterCommon.jscolor.addEvent(valueElement, 'keyup', updateField);
			PrisnaSocialCounterCommon.jscolor.addEvent(valueElement, 'input', updateField);
			PrisnaSocialCounterCommon.jscolor.addEvent(valueElement, 'blur', blurValue);
			valueElement.setAttribute('autocomplete', 'off');
		}

		// styleElement
		if(styleElement) {
			styleElement.jscStyle = {
				backgroundColor : styleElement.style.backgroundColor,
				color : styleElement.style.color
			};
		}

		// require images
		switch(modeID) {
			case 0: PrisnaSocialCounterCommon.jscolor.requireImage('hs.png'); break;
			case 1: PrisnaSocialCounterCommon.jscolor.requireImage('hv.png'); break;
		}
		PrisnaSocialCounterCommon.jscolor.requireImage('cross.gif');
		PrisnaSocialCounterCommon.jscolor.requireImage('arrow.gif');

		this.importColor();
	}

};