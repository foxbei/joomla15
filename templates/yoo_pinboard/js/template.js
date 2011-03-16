/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var WarpTemplate = {
		
	start: function() {
		
		
		/* Accordion menu */
		new Warp.AccordionMenu('div#middle ul.menu li.toggler', 'ul.accordion', { accordion: 'slide' });

		/* Fancy menu */
		new Warp.FancyMenu($E('ul', 'menu'), { mode: 'move', transition: Fx.Transitions.Expo.easeOut, duration: 700 });

		/* Dropdown menu */
		if(!window.ie6 && !window.ie7) { 
			new Warp.Menu('div#menu li.parent', { mode: 'height', transition: Fx.Transitions.Expo.easeOut }); 
		}

		/* Morph: main menu */
		var enterColor = '#c39600';
		var leaveColor = '#323232';
		
		var menuEnter = { 'color': enterColor };
		var menuLeave = { 'color': leaveColor };
		
		new Warp.Morph('div#menu li.level1', menuEnter, menuLeave,
		{ transition: Fx.Transitions.linear, duration: 300 },
		{ transition: Fx.Transitions.sineIn, duration: 700 }, '.level1');
		
		/* Morph: level2 and deeper items of main menu (drop down) */
		new Warp.Morph('div#menu ul.level2 a', menuEnter, menuLeave,
			{ transition: Fx.Transitions.expoOut, duration: 300},
			{ transition: Fx.Transitions.sineIn, duration: 500 });
		
		/* Morph: level1 subline of main menu */
		var enterColor = '#aa7800';
		var leaveColor = '#323232';
		
		var menuEnter = { 'color': enterColor };
		var menuLeave = { 'color': leaveColor };
		
		new Warp.Morph('div#menu li.level1', menuEnter, menuLeave,
		{ transition: Fx.Transitions.linear, duration: 300 },
		{ transition: Fx.Transitions.sineIn, duration: 700 }, 'span.sub');
		
		 /* Morph: sub menu */
		var enterColor = '#d25028';
		var leaveColor = '#323232';
		
		var submenuEnter = { 'color': enterColor};
		var submenuLeave = { 'color': leaveColor};
		
		new Warp.Morph('div#middle ul.menu a, div#middle ul.menu span.separator', submenuEnter, submenuLeave,
		{ transition: Fx.Transitions.expoOut, duration: 100},
		{ transition: Fx.Transitions.sineIn, duration: 700 });

		/* Smoothscroll */
		new SmoothScroll({ duration: 500, transition: Fx.Transitions.Expo.easeOut });
		
		/* Match height of div tags */
		Warp.Base.matchHeight('div.topbox div.deepest', 0, 40);
		Warp.Base.matchHeight('div.bottombox div.deepest', 0, 40);
		Warp.Base.matchHeight('div.maintopbox div.deepest', 0, 40);
		Warp.Base.matchHeight('div.mainbottombox div.deepest', 0, 40);
		Warp.Base.matchHeight('div.contenttopbox div.deepest', 0, 40);
		Warp.Base.matchHeight('div.contentbottombox div.deepest', 0, 40);
	}

};

/* Add functions on window load */
window.addEvent('domready', WarpTemplate.start);


