/* Copyright (C) 2007 - 2011 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var YOOdrawer=new Class({initialize:function(b,d,g){this.setOptions({layout:"vertical",itemstyle:"top",shiftSize:50,transition:Fx.Transitions.Expo.easeOut},g);this.wrapper=$(b);this.items=$$(d);this.fx=new Fx.Elements(this.items,{wait:false,duration:600,transition:this.options.transition});if(this.options.layout!="vertical")this.options.itemstyle="left";var e=this,f={};this.items.each(function(a,c){f[c]=a.getStyle(this.options.itemstyle).toInt();a.addEvent("mouseenter",function(){e.itemFx(f,a,c)})},
this)},itemFx:function(b,d,g){var e={};d.addClass("active");this.items.each(function(f,a){var c=f.getStyle(this.options.itemstyle).toInt();if(a>=g){if(c!=b[a])e[a]=this.itemStyle(c,b[a])}else if(c!=b[a]-this.options.shiftSize)e[a]=this.itemStyle(c,b[a]-this.options.shiftSize);a!=g&&f.removeClass("active")},this);this.fx.start(e)},itemStyle:function(b,d){return this.options.layout=="vertical"?{top:[b,d]}:{left:[b,d]}}});YOOdrawer.implement(new Options);