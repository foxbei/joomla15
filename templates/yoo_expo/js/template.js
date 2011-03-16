/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function($){

	/* Accordion menu */
	$('.menu-accordion').accordionMenu({ mode:'slide' });

	/* Dropdown menu */
	$('#menu').dropdownMenu({ mode: 'slide', dropdownSelector: 'div.dropdown:first'});

	/* Smoothscroller */
	$('a[href="#page"]').smoothScroller({ duration: 500 });

	/* Spotlight */
	$('.spotlight').spotlight({fade: 300});

	/* Match height of div tags */
	var matchHeight = function() {
		$('div.headerbox div.deepest').matchHeight(20);
		$('div.topbox div.deepest').matchHeight(20);
		$('#bottom div.bottombox div.deepest').matchHeight(20);
		$('div.maintopbox div.deepest').matchHeight(20);
		$('div.mainbottombox div.deepest').matchHeight(20);
		$('div.contenttopbox div.deepest').matchHeight(20);
		$('div.contentbottombox div.deepest').matchHeight(20);
		$('#middle, #left, #right').matchHeight(20);
		$('#mainmiddle, #contentleft, #contentright').matchHeight(20);
	};
		
	matchHeight();

	$(window).bind('load', matchHeight);
});