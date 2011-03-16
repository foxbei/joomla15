/* Copyright  2007 - 2010 YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

var Warp=Warp||{};
Warp.Follower=new Class({Implements:[Events,Options],options:{activeClass:"active",hoveredClass:"isfollowing",slider:{"class":"fancyfollower",html:"<div></div>"},effect:{transition:Fx.Transitions.Back.easeOut,duration:200}},startElement:false,initialize:function(a,b){var c=this;this.setOptions(b);this.menu=$(a);this.menu.setStyle("position","relative");this.menuitems=this.menu.getChildren();this.menuitems.addEvents({mouseenter:function(){c.slideTo(this,"enter")},mouseleave:function(){c.slideTo(c.current,"leave")},
click:function(d){c.click(d,this)}}).setStyles({position:"relative"});this.slider=(new Element(this.menuitems[0].get("tag"),this.options.slider)).inject(this.menu).fade("hide");this.fx=new Fx.Morph(this.slider,this.options.effect);this.setCurrent(this.menu.getElement("."+this.options.activeClass));if(this.current)this.startElement=this.current},click:function(a,b){this.setCurrent(b,true);this.fireEvent("click",[a,b])},setCurrent:function(a,b){if(a&&!this.current){this.slider.set("styles",{left:a.offsetLeft,
width:a.offsetWidth,height:a.offsetHeight,top:a.offsetTop});b?this.slider.fade("in"):this.slider.fade("show")}this.current&&this.current.removeClass(this.options.hoveredClass);if(a)this.current=a.addClass(this.options.hoveredClass);return this},slideTo:function(a,b){this.current||this.setCurrent(a);this.fx.cancel().start({left:[this.slider.offsetLeft,a.offsetLeft],width:[this.slider.offsetWidth,a.offsetWidth],top:[this.slider.offsetTop,a.offsetTop],height:[this.slider.offsetHeight,a.offsetHeight]});
this.isHovered=b=="leave"?false:true;if(b=="leave"&&!this.startElement){var c=this;window.setTimeout(function(){if(!c.isHovered){c.slider.fade("hide");c.current=false}},200)}else this.slider.fade("show");return this}});
