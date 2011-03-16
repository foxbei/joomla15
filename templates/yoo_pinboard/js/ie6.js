/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

function loadIE6Fix() {

	correctPngBackground('.correct-png', 'crop');

	/* layout */
	fixPngBackground('div#page-header div.page-header-t, div#page-header div.page-header-b');
	fixPngBackground('div#search');
	fixPngBackground('div#page-body');
	fixPngBackground('div#breadcrumbs a, div#breadcrumbs span');
	fixPngBackground('div#main div.main-tr, div#main div.main-r, div#main div.main-bl, div#main div.main-br, div#main div.main-b');
	
	/* typography & joomla */
	fixPngBackground('ul.arrow li, ul.checkbox li, ul.check li, ul.star li');
	fixPngBackground('blockquote.quotation, blockquote.quotation p');
	fixPngBackground('ol.disc');
	fixPngBackground('a.readmore, a.readon');
	fixPngBackground('div.joomla div.item-tr, div.joomla div.item-r, div.joomla div.item-bl, div.joomla div.item-br, div.joomla div.item-b');

	/* menu */
	fixPngBackground('div#menu li.level1, div#menu a.level1, div#menu span.level1');
	fixPngBackground('div#menu li.fancy div.fancy-3');
	fixPngBackground('div#menu ul.menu ul');
	
	/* module */
	fixPngBackground('div.module div.badge-new, div.module div.badge-top, div.module div.badge-pick, div.module div.badge-magnetblack, div.module div.badge-magnetblack2, div.module div.badge-magnetwhite, div.module div.badge-magnetwhite2, div.module div.badge-magnetorange, div.module div.badge-magnetsmiley, div.module div.badge-pin');
	fixPngBackground('div.mod-headerbar div.badge-soldier');	
	fixPngBackground('div.mod-polaroid div.box-b1, div.mod-polaroid div.box-b2, div.mod-polaroid div.box-b3, div.mod-polaroid div.badge-tape');
	fixPngBackground('div.mod-postit div.box-b1, div.mod-postit div.box-b2, div.mod-postit div.box-b3');
	fixPngBackground('div.mod-postit2 div.box-t1, div.mod-postit2 div.box-1, div.mod-postit2 div.box-b1, div.mod-postit2 div.box-b2, div.mod-postit2 div.box-b3');
	fixPngBackground('div.mod-polaroid2 div.box-t1, div.mod-polaroid2 div.box-1, div.mod-polaroid2 div.box-b1, div.mod-polaroid2 div.box-b2, div.mod-polaroid2 div.box-b3, div.mod-polaroid2 div.badge-tape');
	fixPngBackground('div.mod-tile div.box-t2, div.mod-tile div.box-2, div.mod-tile div.box-b1, div.mod-tile div.box-b2, div.mod-tile div.box-b3');
	fixPngBackground('div.mod-note div.box-t1, div.mod-note div.box-t2, div.mod-note div.box-1, div.mod-note div.box-b1, div.mod-note div.box-b2, div.mod-note div.box-b3');
	fixPngBackground('div.mod-cardboard div.box-t1, div.mod-cardboard div.box-t2, div.mod-cardboard div.box-t3, div.mod-cardboard div.box-1, div.mod-cardboard div.box-2, div.mod-cardboard div.box-b1, div.mod-cardboard div.box-b2, div.mod-cardboard div.box-b3');
	fixPngBackground('div.mod-print div.box-t1, div.mod-print div.box-t2, div.mod-print div.box-1, div.mod-print div.box-2, div.mod-print div.box-b1, div.mod-print div.box-b2, div.mod-print div.box-b3');
	fixPngBackground('div.mod-clip div.box-t1, div.mod-clip div.box-t2, div.mod-clip div.box-1, div.mod-clip div.box-b1, div.mod-clip div.box-b2, div.mod-clip div.box-b3, div.mod-clip div.badge-paperclip');

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