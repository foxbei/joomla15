/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var WarpTemplate = {
		
	start: function() {
		
		/* Accordion menu */
		new Warp.AccordionMenu('div#middle ul.menu li.toggler', 'ul.accordion', { accordion: 'slide' });

		/* Fancy menu */
		//new YOOFancyMenu($E('ul', 'menu'), { mode: 'fade', transition: Fx.Transitions.linear, duration: 500 });

		/* Dropdown menu */
		new Warp.Menu('div#menu li.parent', { mode: 'height', transition: Fx.Transitions.Expo.easeOut });		
		
		/* Morph: color settings */
		var page = $('page');
		
		$("menu").getElements("li.level1").each(function(li){
			for(var item in Warp.Settings.itemColors){
				if(li.hasClass(item)) {
					li.addClass(Warp.Settings.itemColors[item]);
					
					if(li.hasClass('active')){
						page.addClass(Warp.Settings.itemColors[item]);
					}
				}
			}
		});
		
		
		var enterColor = '#B45046';
		if (page.hasClass('green'))     enterColor = '#97AF82';
		if (page.hasClass('pink'))      enterColor = '#B995B1';
		if (page.hasClass('orange'))    enterColor = '#D1934E';
		if (page.hasClass('blue'))      enterColor = '#639FB7';
		if (page.hasClass('yellow'))    enterColor = '#AEAC57';
		if (page.hasClass('lilac'))     enterColor = '#87829D';
		if (page.hasClass('turquoise')) enterColor = '#789696';
		if (page.hasClass('black'))     enterColor = '#3C372D';
		
		/* Morph: main menu - level1 (background) */
		var menuEnter = { 'background-color': enterColor };
		var menuLeave = { 'background-color': '#E6E6D2' };
		
		new Warp.Morph('div#menu li.level1', menuEnter, menuLeave,
			{ transition: Fx.Transitions.linear, duration: 100, ignore: '.active'},
			{ transition: Fx.Transitions.sineIn, duration: 300 }, '.level1');

		/* Morph: main menu - level2 and deeper (background) */
		menuEnter = { 'margin-left': 0, 'margin-right': 0, 'text-indent': 20 };
		menuLeave = { 'margin-left': 5, 'margin-right': 5, 'text-indent': 15 };
		
		var selector = 'div#menu li.level2 a, div#menu li.level2 span.separator';
		/* fix for Opera because Mootools 1.1 is not compatible with latest Opera version */
		if (window.opera) { selector = 'div#menu li.item1 li.level2 a, div#menu li.item1 li.level2 span.separator, div#menu li.item2 li.level2 a, div#menu li.item2 li.level2 span.separator, div#menu li.item3 li.level2 a, div#menu li.item3 li.level2 span.separator, div#menu li.item4 li.level2 a, div#menu li.item4 li.level2 span.separator, div#menu li.item5 li.level2 a, div#menu li.item5 li.level2 span.separator, div#menu li.item6 li.level2 a, div#menu li.item6 li.level2 span.separator, div#menu li.item7 li.level2 a, div#menu li.item7 li.level2 span.separator'; }
		
		new Warp.Morph(selector, menuEnter, menuLeave,
			{ transition: Fx.Transitions.expoOut, duration: 0},
			{ transition: Fx.Transitions.sineIn, duration: 200 });
		
		/* Morph: main menu - level1 (color) */
		menuEnter = { 'color': '#ffffff' };
		menuLeave = { 'color': '#323232' };
		
		new Warp.Morph('div#menu li.level1', menuEnter, menuLeave,
			{ transition: Fx.Transitions.linear, duration: 0, ignore: '.active'},
			{ transition: Fx.Transitions.sineIn, duration: 200 }, '.level1');

		/* Morph: main menu - level1 subline (color) */
		menuEnter = { 'color': '#ffffff' };
		menuLeave = { 'color': '#646464' };
		
		new Warp.Morph('div#menu li.level1', menuEnter, menuLeave,
			{ transition: Fx.Transitions.linear, duration: 0, ignore: '.active'},
			{ transition: Fx.Transitions.sineIn, duration: 200 }, 'span.subtitle');

		/* Morph: sub menu (left/right) */
		submenuEnter = { 'margin-left': 0, 'margin-right': 0, 'padding-left': 5 };
		submenuLeave = { 'margin-left': 5, 'margin-right': 5, 'padding-left': 0 };

		new Warp.Morph('div#middle ul.menu a, div#middle ul.menu span.separator', submenuEnter, submenuLeave,
			{ transition: Fx.Transitions.expoOut, duration: 0 },
			{ transition: Fx.Transitions.sineIn, duration: 200 });

		/* Morph: module (hover) */
		var moduleEnter = { 'background-color': '#F5F5E6'};
		var moduleLeave = { 'background-color': '#ffffff'};

		new Warp.Morph('div.mod-hover div.box-2', moduleEnter, moduleLeave,
			{ transition: Fx.Transitions.expoOut, duration: 100 },
			{ transition: Fx.Transitions.sineIn, duration: 300 });

		/* Smoothscroll */
		new SmoothScroll({ duration: 500, transition: Fx.Transitions.Expo.easeOut });

		/* Match height of div tags */

		Warp.Base.matchHeight('div.topbox div.deepest', 40);
		Warp.Base.matchHeight('div.bottombox div.deepest', 40);
		Warp.Base.matchHeight('div.maintopbox div.deepest', 40);
		Warp.Base.matchHeight('div.mainbottombox div.deepest', 40);
		Warp.Base.matchHeight('div.contenttopbox div.deepest', 40);
		Warp.Base.matchHeight('div.contentbottombox div.deepest', 40);
	}

};

/* Add functions on window load */
window.addEvent('domready', WarpTemplate.start);
