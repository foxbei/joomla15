/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

Warp.Menu=new Class({initialize:function(f,g){this.setOptions({mode:"default",duration:600,transition:Fx.Transitions.linear,wait:false},g);var b={width:0,height:0,opacity:0};switch(this.options.mode){case "width":b={width:0,opacity:0};break;case "height":b={height:0,opacity:0}}$$(f).each(function(c){var a=c.getElement("ul");if(a){var d=new Fx.Styles(a,this.options),h=a.getStyles("width","height","opacity");a.setStyles(b);c.addEvents({mouseenter:function(){var e=c.getParent();e.getStyle("overflow")==
"hidden"&&e.setStyle("overflow","visible");d.element.setStyle("overflow","hidden");d.start(h)},mouseleave:function(){d.stop();a.setStyles(b)}})}}.bind(this))}});Warp.Menu.implement(new Options);
