/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var YOOgalleryfx=new Class({initialize:function(e){var a=[];$(e).getElements(".thumbnail").each(function(c,b){var d=c.getElement("img");d.setStyle("opacity",0.3);a[b]=d.get("tween",{property:"opacity",duration:700,wait:false});c.addEvents({mouseenter:function(){a[b].setOptions({duration:300});a[b].start(0.3,1)},mouseleave:function(){a[b].setOptions({duration:700});a[b].start(1,0.3)}})})}});
