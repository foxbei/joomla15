/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

window.addEvent("domready",function(){$$("div.list-global").each(function(c){var d=c.getElement("input[name=_global]"),a=c.getElement("div.input"),b=new Fx.Slide(a,{duration:150});d.getProperty("checked")&&b.hide();d.addEvent("change",function(){if(this.getProperty("checked")){a.getElement("select").setProperty("name","");b.slideOut()}else{a.getElement("select").setProperty("name",this.getProperty("value"));b.slideIn()}})})});
