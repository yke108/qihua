!function(t){function e(s){if(l[s])return l[s].exports;var o=l[s]={exports:{},id:s,loaded:!1};return t[s].call(o.exports,o,o.exports,e),o.loaded=!0,o.exports}var l={};return e.m=t,e.c=l,e.p="js/",e(0)}([function(t,e){$.post($domain+"user/supply/getList",{},function(t){"000"!=t.code&&(t={}),html=template("tp-sell-list",{list:t.list}),$("#sell-list").html(html)},"json")}]);