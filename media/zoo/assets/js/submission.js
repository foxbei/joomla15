/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

Zoo.SubmissionMysubmissions=new Class({Implements:[Options],initialize:function(a){this.setOptions({msgDelete:"Are you sure you want to delete this submission?"},a);a=$("yoo-zoo");a.getElements("ul.submissions > li").each(function(b){b.getElement("h3.toggler").addEvent("click",function(){b.getElement("div.preview").toggleClass("hidden")})});a.getElements("a.delete-item").each(function(b){b.addEvent("click",function(c){confirm(this.options.msgDelete)||(new Event(c)).stop()}.bind(this))}.bind(this))}});
