!function(e){function t(s){if(i[s])return i[s].exports;var l=i[s]={exports:{},id:s,loaded:!1};return e[s].call(l.exports,l,l.exports,t),l.loaded=!0,l.exports}var i={};return t.m=e,t.c=i,t.p="js/",t(0)}([function(e,t,i){i(4);$(function(){$.reg_slider_f=!1,$("#code").slider({width:300,height:40,sliderBg:"#e8e8e8",color:"#666",fontSize:16,bgColor:"#33CC00",textMsg:"Hold the slider to the right",successMsg:"Verification is successful",successColor:"#FFFFFF",time:400,callback:function(e){e!==!1&&($.reg_slider_f=!0)}}),$("#reg_email_send").bind("submit",function(){var e=$(this).find('input[name="email"]').val();if(e.length<1)return alert("Email is required"),!1;if($.reg_slider_f===!1)return alert("Please hold the slider to the right"),!1;if(!$('input[name="agree"]').is(":checked"))return alert("Please agree the protocol"),!1;var t=$(this).serialize();return $.post($domain+"user/index/sendRegisterEmail",t,function(t){"000"!==t.code?alert(t.message):($("#reg_email_send").hide(),$(".register-send").show(),$("#email_sended").html(e))},"json"),!1})})},,,,function(e,t){!function(e,t,i,s){var l=function(t,i){this.ele=t,this.defaults={width:300,height:34,sliderBg:"#e8e8e8",color:"#666",fontSize:12,bgColor:"#7ac23c",textMsg:"请按住滑块，拖动到最右边",successMsg:"验证成功",successColor:"#fff",time:160,callback:function(e){}},this.opts=e.extend({},this.defaults,i),this.init()};l.prototype={init:function(){this.result=!1,this.sliderBtn_left=0,this.maxLeft=this.opts.width-this.opts.height,this.render(),this.eventBind()},render:function(){var e='<div class="ui-slider-wrap"><div class="ui-slider-text ui-slider-no-select">'+this.opts.textMsg+'</div><div class="ui-slider-btn init ui-slider-no-select"></div><div class="ui-slider-bg"></div></div>';this.ele.html(e),this.initStatus()},initStatus:function(){var e=this,t=this.ele;this.slider=t.find(".ui-slider-wrap"),this.sliderBtn=t.find(".ui-slider-btn"),this.bgColor=t.find(".ui-slider-bg"),this.sliderText=t.find(".ui-slider-text"),this.slider.css({width:e.opts.width,height:e.opts.height,backgroundColor:e.opts.sliderBg}),this.sliderBtn.css({width:e.opts.height,height:e.opts.height,lineHeight:e.opts.height+"px"}),this.bgColor.css({height:e.opts.height,backgroundColor:e.opts.bgColor}),this.sliderText.css({lineHeight:e.opts.height+"px",fontSize:e.opts.fontSize,color:e.opts.color})},restore:function(){var e=this,t=e.opts.time;this.result=!1,this.initStatus(),this.sliderBtn.removeClass("success").animate({left:0},t),this.bgColor.animate({width:0},t,function(){e.sliderText.text(e.opts.textMsg)})},eventBind:function(){var e=this;this.ele.on("mousedown",".ui-slider-btn",function(t){e.result||e.sliderMousedown(t)})},sliderMousedown:function(e){var t=this,i=e.clientX,s=i-this.sliderBtn.offset().left;t.sliderMousemove(i,s),t.sliderMouseup()},sliderMousemove:function(t,s){var l=this;e(i).on("mousemove.slider",function(e){l.sliderBtn_left=e.clientX-t-s,l.sliderBtn_left<0||(l.sliderBtn_left>l.maxLeft&&(l.sliderBtn_left=l.maxLeft),l.sliderBtn.css("left",l.sliderBtn_left),l.bgColor.width(l.sliderBtn_left))})},sliderMouseup:function(){var t=this;e(i).on("mouseup.slider",function(){t.sliderBtn_left!=t.maxLeft?t.sliderBtn_left=0:(t.ele.find(".ui-slider-text").text(t.opts.successMsg).css({color:t.opts.successColor}),t.ele.find(".ui-slider-btn").addClass("success"),t.result=!0),t.sliderBtn.animate({left:t.sliderBtn_left},t.opts.time),t.bgColor.animate({width:t.sliderBtn_left},t.opts.time),e(this).off("mousemove.slider mouseup.slider"),t.opts.callback&&"function"==typeof t.opts.callback&&t.opts.callback(t.result)})}},e.fn.slider=function(t){return this.each(function(){var i=e(this),s=i.data("slider");s||(s=new l(i,t),i.data("slider",s)),"string"==typeof t&&s[t]()})}}(jQuery,window,document)}]);