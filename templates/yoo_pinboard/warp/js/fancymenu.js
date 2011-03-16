/* Copyright  2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var Warp=Warp||{};
Warp.FancyMenu=new Class({Implements:[Events,Options],initialize:function(a,b){this.setOptions({transition:Fx.Transitions.sineInOut,duration:500,wait:false,onClick:Class.empty,onEnterItem:Class.empty,onLeaveItem:Class.empty,opacity:1,mode:"move",slideOffset:30,itemSelector:"li.level1",activeSelector:"li.active",dropdownSelector:"div.dropdown"},b);var c=0;this.menu=$(a);this.items=[];this.div=[];if(this.menu){this.current=this.menu.getElement(this.options.activeSelector);var f=this;this.menu.getElements(this.options.itemSelector).each(function(d,
e){d.addEvent("click",function(h){this.clickItem(h,d)}.bind(this));this.options.mode!="move"&&this.createBackground(e,e+1);if(this.options.mode=="move"&&this.current==d)c=e}.bind(this));this.menu.addEvent("menu:enter",function(d,e){f.mouseenterItem(d,e)});this.menu.addEvent("menu:leave",function(d,e){f.mouseleaveItem(d,e)});if(this.options.mode=="move"){this.createBackground(0,c+1);if(this.current)this.setCurrent(this.current);else{var g=this.menu.getElement("li");g.addClass("active");g.addClass("current");
this.setCurrent(g)}}}},createBackground:function(a,b){var c="fancy bg"+b;this.div[a]=(new Element("div",{"class":"fancy-1"})).adopt((new Element("div",{"class":"fancy-2"})).adopt(new Element("div",{"class":"fancy-3"})));this.div[a].fx=new Fx.Morph(this.div[a],this.options);this.items[a]=(new Element("div",{"class":c})).adopt(this.div[a]).injectInside(this.menu);this.items[a].fx=new Fx.Morph(this.items[a],this.options)},setCurrent:function(a){this.items[0].setStyles({left:a.offsetLeft,width:a.offsetWidth,
visibility:"visible",opacity:this.options.opacity});this.current=a},clickItem:function(a,b){this.current||this.setCurrent(b);this.current=b;this.options.onClick(new Event(a),b)},mouseenterItem:function(a,b){if(!a._fancyactive){a._fancyactive=true;switch(this.options.mode){case "fade":this.fadeFx(a,b,true);break;case "slide":this.slideFx(a,b,true);break;default:this.moveFx(a,0)}this.fireEvent("onEnterItem",[a,b])}},mouseleaveItem:function(a,b){a._fancyactive=false;switch(this.options.mode){case "fade":this.fadeFx(a,
b,false);break;case "slide":this.slideFx(a,b,false);break;default:this.moveFx(this.current,0)}this.fireEvent("onLeaveItem",[a,b])},moveFx:function(a,b){this.current&&this.items[b].fx.start({left:[this.items[b].offsetLeft,a.offsetLeft],width:[this.items[b].offsetWidth,a.offsetWidth]})},fadeFx:function(a,b,c){if(c){this.items[b].fx.setOptions(this.options);this.items[b].fx.set({left:a.offsetLeft,width:a.offsetWidth});this.items[b].fx.start({opacity:[0,1]})}else{this.items[b].fx.setOptions({duration:this.options.duration*
2});this.items[b].fx.start({opacity:[1,0]})}},slideFx:function(a,b,c){var f=this.options.slideOffset;if(c){this.items[b].fx.set({opacity:1,left:a.offsetLeft,width:a.offsetWidth});this.div[b].fx.set({"margin-top":f});this.div[b].fx.start({"margin-top":[f,0]})}else{this.div[b].fx.set({"margin-top":0});this.div[b].fx.start({"margin-top":[0,f]})}}});
