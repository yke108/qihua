!function(i){function t(r){if(n[r])return n[r].exports;var o=n[r]={exports:{},id:r,loaded:!1};return i[r].call(o.exports,o,o.exports,t),o.loaded=!0,o.exports}var n={};return t.m=i,t.c=n,t.p="js/",t(0)}([function(i,t){function n(){$.post($domain+"/Inquiry/getInquiryList",{},function(i){"000"!=i.code&&(i={});var t=template("tp-inquiry-list",{list:i.list});$("#inquiry-list").html(t),$(".inquiry-view").click(function(){var i=$(this).attr("href"),t=$(this).attr("rid");return window.location.href=i+"#"+t,!1}),$(".inquiry-close").click(function(){var i=$(this).attr("rid");return $.post($domain+"/Inquiry/closeInquiry",{inquiryId:i},function(i){alert(i.message),"000"==i.code&&n()},"json"),!1})},"json")}$.sess={listType:1,page:1},$(function(){n()})}]);