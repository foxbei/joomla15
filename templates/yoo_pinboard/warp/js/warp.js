/* Copyright  2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var Warp=Warp||{};Warp.Base={matchHeight:function(e,d){var c=0,b=document.getElements(e);b.each(function(a){c=Math.max(c,a.getSize().y)});if(d)c=Math.max(c,d);b.each(function(a){var i=a.getStyle("padding-top").toInt()+a.getStyle("padding-bottom").toInt()+a.getStyle("border-top-width").toInt()+a.getStyle("border-bottom-width").toInt();a.setStyle(window.ie6?"height":"min-height",c-i+"px")})}};
Warp.Morph=new Class({Implements:Options,initialize:function(e,d,c,b,a,i){this.setOptions({duration:500,transition:Fx.Transitions.expoOut,wait:false,ignore:""},b);var f=this.options,j=null;if($chk(f.ignore))j=$$(f.ignore);document.getElements(e).each(function(g){if(!(j&&j.contains(g))){var k=[],l=[g];if(i)l=g.getElementsBySelector(i);l.each(function(h,m){k[m]=new Fx.Morph(h,f)});g.addEvent("mouseenter",function(){k.each(function(h){h.setOptions(f,b).start(d)})});g.addEvent("mouseleave",function(){k.each(function(h){h.setOptions(f,
a).start(c)})})}})}});Warp.BackgroundFx=new Class({Implements:Options,initialize:function(e){function d(){c.start({"background-color":a[b]});if(b+1>=a.length)b=0;else b++}this.setOptions({transition:Fx.Transitions.linear,duration:9E3,wait:false,colors:["#FFFFFF","#999999"]},e);var c=(new Element(document.body)).effects(this.options),b=0,a=this.options.colors;d.periodical(this.options.duration*2);d()}});
