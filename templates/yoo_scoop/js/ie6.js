/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

function loadIE6Fix() {

	correctPngBackground('.correct-png', 'crop');
	
	/* layout */
	
	/* typography & joomla */
	fixPngBackground('ul.arrow li, ul.checkbox li, ul.check li, ul.star li');
	fixPngBackground('blockquote.quotation, blockquote.quotation p');
	fixPngBackground('ol.disc');
	fixPngBackground('a.readmore, a.readon');

	/* menu */

	/* module */
	fixPngBackground('div.module div.badge-new, div.module div.badge-top, div.module div.badge-pick, div.module div.badge-sticker-hot, div.module div.badge-sticker-top, div.module div.badge-sticker-new');
	fixPngBackground('div.mod-polaroid div.box-b1, div.mod-polaroid div.box-b2, div.mod-polaroid div.box-b3, div.mod-polaroid div.badge-tape');
	fixPngBackground('div.mod-postit div.box-b1, div.mod-postit div.box-b2, div.mod-postit div.box-b3');

	addHover('#menu span.separator');
	addHover('#menu li');
	addHover('.module .menu span.separator');
	addHover('.module .menu li');

	DD_belatedPNG.fix('.png_bg');
}

/* Add functions on window load */
window.addEvent('domready', loadIE6Fix);
window.addEvent('load', correctPngInline);

/* Fix PNG background */
function fixPngBackground(selector) {
	$ES(selector).each(function(element){
		element.addClass('png_bg');
	});
}