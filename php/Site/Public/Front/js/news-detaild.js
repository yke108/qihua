!function(n){function t(o){if(e[o])return e[o].exports;var i=e[o]={exports:{},id:o,loaded:!1};return n[o].call(i.exports,i,i.exports,t),i.loaded=!0,i.exports}var e={};return t.m=n,t.c=e,t.p="js/",t(0)}([function(n,t,e){e(1);$(function(){$(".nav .nav-sort").hover(function(){$(this).find(".nav-sort-list").show()},function(){$(this).find(".nav-sort-list").hide()})}),$(document).ready(function(){var n=0,t=/\d+/.exec(window.location.hash);null==t?(t=/\/id\/(\d+)/.exec(window.location.pathname),n=t[1]):n=t[0],$.post($domain+"content/getNewsdetail",{id:n},function(n){if(console.log(n),"000"==n.code){var t=template("news-detaild",n);$("#news-detaild-content").html(t)}},"json")})},function(n,t){$.initHeader=function(){$(".header .header-search form .header-search-sort").hover(function(){$(this).addClass("on"),$(this).find(".header-search-sort-list").slideDown()},function(){$(this).removeClass("on"),$(this).find(".header-search-sort-list").slideUp()}),$(".header-search-sort-list p a").on("click",function(){var n=$(this).text();$(".header .header-search form .header-search-sort span em").text(n),$(".header-search-sort-list").slideUp()})},$.initnav=function(){$(".nav .nav-sort .nav-sort-list").hover(function(){$(this).find("ol").show()},function(){$(this).find("ol").hide(),$(this).find("ul li").removeClass("on")}),$(".nav .nav-sort .nav-sort-list ul li").hover(function(){var n=$(this).index();$(this).addClass("on").siblings().removeClass("on"),$(".nav .nav-sort .nav-sort-list ol li").eq(n).show().siblings().hide()})},$("#tp-nav-box").length>0&&$.post($domain+"index/getcategory",{},function(n){"000"==n.code&&($("#nav-box").html(template("tp-nav-box",n)),$.initnav())},"json")}]);