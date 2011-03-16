/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

window.addEvent("domready",function(){$$("div.zoo-feed").each(function(a){var c=a.getElement("div.input"),b=new Fx.Slide(c,{duration:150});a=a.getChildren().filter("input");a[0].getProperty("checked")&&b.hide();a.addEvent("change",function(){b.toggle()})})});
