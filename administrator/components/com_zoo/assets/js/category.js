/* Copyright (C) 2007 - 2011 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

// controller: category task: default (browse)
(function($){

    var Plugin = function(){};

    $.extend(Plugin.prototype, {

		name: 'BrowseCategories',

		options: {
			url : 'index.php'
		},

		initialize: function(form, options) {
			this.options = $.extend({}, this.options, options);

			var $this = this;

			var nestedList = $('#categories');

			// no tree ?
			if (!nestedList) {
				return;
			}
			
			nestedList.nestedSortable({
				forcePlaceholderSize: true,
				handle: 'td.handle',
				items: 'li',
				placeholder: 'placeholder',
				tabSize: 25,
				tolerance: 'pointer',
				toleranceElement: '> div',
				listType: 'ul',
				start: function(event, ui) {
					ui.placeholder.height(ui.helper.height() - 2);
					ui.helper.addClass('drag');
				},
				stop: function(event, ui) {

					var token = form.find('input[value=1]:hidden:not([name=boxchecked])').attr('name');

					var data = 'option=com_zoo&controller=category&task=saveorder&format=raw&'+token+'=1&'+nestedList.nestedSortable('serialize');

					ui.item.addClass('loader').find('li').addClass('loader');
					$.ajax({
						url: $this.options.url,
						type: 'POST',
						data: data,
						success: function(data) {
							nestedList.find('li').removeClass('loader');
							$.Message(data, true);							
						},
						complete: function() {
							$this.markCollapsibles();
						}
					});

					ui.item.removeClass('drag');
					
				}
			});
			
			// Add the Collapse/Expand functionality to categories
			$('#categories').delegate('li.collapsible > div td.icon', 'click', function() {
				var li = $(this).closest('li');
				if(li.is('.collapsed')) {
					li.removeClass('collapsed').children('ul').show();
				} else {
					li.addClass('collapsed').children('ul').hide();
				}
			});

			// collapse all categories
			$('#categories-list .collapse-all').bind('click', function() {
				$('#categories li.collapsible').each (function() {
					$(this).addClass('collapsed').children('ul').hide();
				});
			});

			// expand all categories
			$('#categories-list .expand-all').bind('click', function() {
				$('#categories li.collapsible').each (function() {
					$(this).removeClass('collapsed').children('ul').show();
				});
			});

			// initially mark all collapsible categories
			$this.markCollapsibles();

		},

		markCollapsibles: function() {
			$('#categories').find('li').each(function() {
				$(this).removeClass('collapsible');
				if ($(this).has('ul').length) {
					$(this).addClass('collapsible');
				}
			});
		}

	});

    // Don't touch
	$.fn[Plugin.prototype.name] = function() {

		var args   = arguments;
		var method = args[0] ? args[0] : null;

		return this.each(function() {
			var element = $(this);

			if (Plugin.prototype[method] && element.data(Plugin.prototype.name) && method != 'initialize') {
				element.data(Plugin.prototype.name)[method].apply(element.data(Plugin.prototype.name), Array.prototype.slice.call(args, 1));
			} else if (!method || $.isPlainObject(method)) {
				var plugin = new Plugin();

				if (Plugin.prototype['initialize']) {
					plugin.initialize.apply(plugin, $.merge([element], args));
				}

				element.data(Plugin.prototype.name, plugin);
			} else {
				$.error('Method ' +  method + ' does not exist on jQuery.' + Plugin.name);
			}

		});
	};

})(jQuery);

//controller: category task: edit
(function($){

    var Plugin = function(){};

    $.extend(Plugin.prototype, {

		name: 'EditCategory',

		initialize: function(input) {
			$.each(['apply', 'save', 'saveandnew'], function(i, task) {
				$('#toolbar-' + task + ' a').bind('validate.adminForm', function(event) {
					if (input.find('input[name="name"]').val() == "") {
						input.find('span.message-name').css('display', 'inline');
						event.preventDefault();
					}
				});
			});
		}
	});

    // Don't touch
	$.fn[Plugin.prototype.name] = function() {

		var args   = arguments;
		var method = args[0] ? args[0] : null;

		return this.each(function() {
			var element = $(this);

			if (Plugin.prototype[method] && element.data(Plugin.prototype.name) && method != 'initialize') {
				element.data(Plugin.prototype.name)[method].apply(element.data(Plugin.prototype.name), Array.prototype.slice.call(args, 1));
			} else if (!method || $.isPlainObject(method)) {
				var plugin = new Plugin();

				if (Plugin.prototype['initialize']) {
					plugin.initialize.apply(plugin, $.merge([element], args));
				}

				element.data(Plugin.prototype.name, plugin);
			} else {
				$.error('Method ' +  method + ' does not exist on jQuery.' + Plugin.name);
			}

		});
	};

})(jQuery);