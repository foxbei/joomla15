/* Copyright (C) 2007 - 2011 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

// controller: comment task: default (browse)
(function($){

    var Plugin = function(){};

    $.extend(Plugin.prototype, {

		name: 'Comment',

		options: {
			url: 'index.php?option=com_zoo&controller=comment',
			id: 'edit-comment-editor'
		},

		initialize: function(form, options) {
			this.options = $.extend({}, this.options, options);

			var $this = this;
			this.form = form;

			form.delegate('tr.comment-row .actions-links .edit, tr.comment-row .actions-links .reply', 'click', function(event) {
				event.stopPropagation();
				var task = $(this).attr('class');
				$this.modifyEvent($(this).closest('tr.comment-row'), task);
			});

			form.delegate('tr.comment-row .actions-links span', 'click', function(event) {
				event.stopPropagation();
				switch ($(this).attr('class')) {
					case 'no-spam':
						var task = 'approve';
						break;
					case 'delete':
						var task = 'remove';
						break;
					default:
						var task = $(this).attr('class');
						break;
				}
				$this.stateEvent($(this).closest('tr.comment-row'), task);
			});

		},

		modifyEvent: function(row, task) {
			$this = this;

			$.ajax({
				url: this.options.url,
				data: {
					task: task,
					format: 'raw',
					cid: row.find('input[name^="cid["]').val()
				},
				success: function(data){

					// insert editor
					$this.removeEditor();
					$this.insertEditor(row, data);

					// hide row on edit comment
					if (task == 'edit') row.hide();

					// set events
					$this.form
						.unbind('save')
						.unbind('cancel')
						.bind('save', function(event, data) {
//							if ($.parseJSON(data)) {
//								Message.show(data);
//							} else {
								$this.removeEditor();
								if (task == 'edit') {
									row.replaceWith(data);
								} else {
									$(data).insertAfter(row);
								}
								$this.stripe();
//							}
						})
						.bind('cancel', function() {
							$this.removeEditor();
						});
				}
			});
		},

		stateEvent: function(row, task) {
			$('<input type="hidden" name="cid">').val(row.find('input[name^="cid["]').val()).appendTo(this.form);
			this.form.find('input[name=task]').val(task);
			this.form.submit();
		},

		insertEditor: function(row, data) {
			var editor = row.after(data).next();

			editor.find('.actions .save').bind('click', function(event) {
				event.stopPropagation();

				var post = {};

				$.each($this.form.serializeArray(), function(i, field){
					post[field.name] = field.value;
		      	});

				$.ajax({
					type: "POST",
					url: $this.options.url,
					data: $.extend(post, {'task': 'save', 'format': 'raw'}),
					success: function(data){
						$this.form.triggerHandler('save', data);
					}
				});
			});

			editor.find('.actions .cancel').bind('click', function(event) {
				event.stopPropagation();
				$this.form.triggerHandler('cancel');
			});

			editor.find('.content textarea').focus();
		},

		removeEditor: function() {
			$('#'+this.options.id, this.form).prev('tr').show();
			$('#'+this.options.id, this.form).remove();
		},

		stripe: function() {
			$('table.stripe tbody tr').removeClass('odd even').each(function() {
				$(this).addClass($(this).is('tr:odd') ? 'odd' : 'even');
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