/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

if (MooTools.More.version == '1.2.4.2') {

	// fixed with 1.2.4.4
	Sortables.prototype.getClone = function(event, element){
		if (!this.options.clone) return new Element('div').inject(document.body);
		if ($type(this.options.clone) == 'function') return this.options.clone.call(this, event, element, this.list);
		var clone = element.clone(true).setStyles({
			margin: '0px',
			position: 'absolute',
			visibility: 'hidden',
			'width': element.getStyle('width')
		});
		//prevent the duplicated radio inputs from unchecking the real one
		if (clone.get('html').test('radio')) {
			clone.getElements('input[type=radio]').each(function(input, i) {
				input.set('name', 'clone_' + i);
			});
		}

		return clone.inject(this.list).setPosition(element.getPosition(element.getOffsetParent()));
	}
}

// create namespace
var Zoo = {};

// notification messages
var Message = {
	
	show: function(data, erroronly) {
		var options = JSON.decode(data, true);
	
		// show notify
		if (options) {
			if (options.group == 'info') {
				if (erroronly) return;
				Notify.Smoke($merge(options, {'suffix': 'info', 'duration': 2}));
				return;
			} else if (options.group == 'error'){
				Notify.Bezel($merge(options, {'suffix': 'error', 'sticky': true}));
				return;
			}
		}

		// redirect on error
		window.location = 'index.php';
	}
	
};

// add dom ready events
window.addEvent('domready', function(){
	
	Zoo.attachParameterAccordion();
	
	// add auto submit
	$$('select.auto-submit').addEvent('change', function(){
		document.adminForm.submit();
	});

	// stripe tables
	$$('table.stripe tbody tr').each(function(tr, i) {
		tr.addClass(i % 2 ? 'even' : 'odd');
	});

	// check all
	var boxchecked = $(document).getElement('input[name=boxchecked]');
	$$('input.check-all').each(function(input, i) {
		input.addEvent('click', function(){
			var count = 0;
			var value = input.get('value');
			$$('input[name^=cid]').each(function(checkbox, i) {
				checkbox.set('checked', value);
				if (value) count++;
			});
			boxchecked.set('value', count);
		});
	});

	// check single
	$$('input[name^=cid]').each(function(checkbox, i) {
		checkbox.addEvent('click', function(){
			if (this.get('value')){
				boxchecked.value++;
			} else {
				boxchecked.value--;
			}
		});
	});

});

// add parameter accordion
Zoo.attachParameterAccordion = function () {
	var accordion = new Accordion($('parameter-accordion'), 'h3.toggler', 'div.content', { 
			opacity: false,
			onActive: function(toggler, element) {toggler.addClass('active');},
			onBackground: function(toggler, element){toggler.removeClass('active');}
		});
	
	accordion.addEvent('onComplete', function(){
		accordion.elements.each(function(elm, i){
			if (elm.getStyle('height') != '0px') {
				elm.setStyle('height', null);
			}
		});
	});
};

// add zoo string methods
Zoo.String = {};
Zoo.String.tidymap  = {"[\xa0\u2002\u2003\u2009]": " ", "\xb7": "*", "[\u2018\u2019]": "'", "[\u201c\u201d]": '"', "\u2026": "...", "\u2013": "-", "\u2014": "--", "\uFFFD": "&raquo;"};
Zoo.String.special  = ['\'', 'À','à','Á','á','Â','â','Ã','ã','Ä','ä','Å','å','A','a','A','a','C','c','C','c','Ç','ç','Č','č','D','d','Ð','d', 'È','è','É','é','Ê','ê','Ë','ë','E','e','E','e', 'G','g','Ì','ì','Í','í','Î','î','Ï','ï', 'L','l','L','l','L','l', 'Ñ','ñ','N','n','N','n','Ò','ò','Ó','ó','Ô','ô','Õ','õ','Ö','ö','Ø','ø','o','R','r','R','r','Š','š','S','s','S','s', 'T','t','T','t','T','t','Ù','ù','Ú','ú','Û','û','Ü','ü','U','u', 'Ÿ','ÿ','ý','Ý','Ž','ž','Z','z','Z','z', 'Þ','þ','Ð','ð','ß','Œ','œ','Æ','æ','µ'];
Zoo.String.standard = ['-', 'A','a','A','a','A','a','A','a','Ae','ae','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d', 'E','e','E','e','E','e','E','e','E','e','E','e','G','g','I','i','I','i','I','i','I','i','L','l','L','l','L','l', 'N','n','N','n','N','n', 'O','o','O','o','O','o','O','o','Oe','oe','O','o','o', 'R','r','R','r', 'S','s','S','s','S','s','T','t','T','t','T','t', 'U','u','U','u','U','u','Ue','ue','U','u','Y','y','Y','y','Z','z','Z','z','Z','z','TH','th','DH','dh','ss','OE','oe','AE','ae','u'];
Zoo.String.slugify  = function (txt) {

	txt = txt.toString();

	$each(this.tidymap, function(value, key) {txt = txt.replace(new RegExp(key, 'g'), value);});

	this.special.each(function(ch, i) {txt = txt.replace(new RegExp(ch, 'g'), this.standard[i]);}.bind(this));

	return txt.trim().replace(/\s+/g,'-').toLowerCase().replace(/[^\u0370-\u1FFF\u4E00-\u9FAFa-z0-9\-]/g,'').replace(/[-]+/g, '-').replace(/^[-]+/g, '').replace(/[-]+$/g, '');

};