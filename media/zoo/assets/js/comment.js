/* Copyright (C) 2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

Zoo.Comment=new Class({Implements:[Options],initialize:function(a){this.setOptions({cookiePrefix:"zoo-comment_",cookieLifetime:15552E3},a);var d=this,b=$("respond");(a=b.getElement(".actions .cancel"))&&a.addEvent("click",function(c){(new Event(c)).stop();b.injectInside($("comments").getElement(".comments"));b.getElement("input[name=parent_id]").setProperty("value",0)});(a=b.getElement("form"))&&a.addEvent("submit",function(){this.getElement("span.submit-message").addClass("submitting")});(a=b.getElement("a.facebook-connect"))&&
a.addEvent("click",function(){d.setLoginCookie("facebook")});(a=b.getElement("a.facebook-logout"))&&a.addEvent("click",function(){d.setLoginCookie("")});(a=b.getElement("a.twitter-connect"))&&a.addEvent("click",function(){d.setLoginCookie("twitter")});(a=b.getElement("a.twitter-logout"))&&a.addEvent("click",function(){d.setLoginCookie("")});$$("#comments .comment").each(function(c){c.getElement(".reply a").addEvent("click",function(e){(new Event(e)).stop();b.injectInside(c);b.getElement("input[name=parent_id]").setProperty("value",
c.getProperty("id").replace(/comment-/i,""))})})},setLoginCookie:function(a){Cookie.set(this.options.cookiePrefix+"login",a,{duration:this.options.cookiePrefix/86400,path:"/"})},refreshPage:function(){window.location.reload(true)}});
