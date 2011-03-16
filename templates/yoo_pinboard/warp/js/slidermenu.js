/* Copyright  2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var Warp=Warp||{};
Warp.SliderMenu=new Class({Implements:Options,initialize:function(e,l,m){this.setOptions({widthSliderPx:105,widthSliderOpenPx:165},m);e=$E(e);var a=$ES(l),g=new Fx.Elements(a,{wait:false,duration:200,transition:Fx.Transitions.quadOut}),f=this.options.widthSliderPx,h=this.options.widthSliderOpenPx,i=parseInt(f-(h-f)/(a.length-1));a.each(function(b,d){b.addEvent("mouseenter",function(){var c={};c[d]={width:[b.getStyle("width").toInt(),h]};a.each(function(n,j){if(d!=j){var k=n.getStyle("width").toInt();if(k!=
i)c[j]={width:[k,i]}}});g.start(c)})});e.addEvent("mouseleave",function(){var b={};a.each(function(d,c){b[c]={width:[d.getStyle("width").toInt(),f]}});g.start(b)})}});
