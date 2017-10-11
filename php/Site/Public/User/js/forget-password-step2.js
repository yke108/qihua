!function(e){function t(i){if(a[i])return a[i].exports;var s=a[i]={exports:{},id:i,loaded:!1};return e[i].call(s.exports,s,s.exports,t),s.loaded=!0,s.exports}var a={};return t.m=e,t.c=a,t.p="js/",t(0)}([function(e,t,a){var i=(a(1),a(2)),s=a(4);$(function(){function e(){var e={data:{newpass:a.val(),repass:r.val()}};s.ajax(i.forgetPassword,e).then(function(e){window.location.href=e.data.url},function(e){o.error(e.msg)})}var t=$("#form"),a=t.find('input[name="password"]'),r=t.find('input[name="repassword"]'),n=t.find(".continue"),l=$(".warn"),o={error:function(e){var t='<i class="icon-warn"></i><span id="warntips">'+e+"</span>";l.addClass("error").html(t)},hide:function(){l.hide()}};t.validator({focusCleanup:!0,msgWrapper:"div",msgMaker:function(e){var t='<i class="icon-warn"></i><span id="warntips">'+e.msg+"</span>";return t},target:function(e){return l.html(""),l},stopOnError:!0,invalid:function(e,t){$("body").scrollTop(200)},rules:{password:function(e,t,a){return!!/.{6,18}/.test(e.value)&&(!/^\d+$/.test(e.value)&&(!/^[a-z]+$/.test(e.value)&&(!/^[A-Z]+$/.test(e.value)&&!!/^[0-9a-zA-Z]{6,18}$/.test(e.value))))},repassword:function(e,t,i){return e.value==a.val()}},messages:{password:"Password should combined by 6-18 English letters ( case-sensitive ) and digital components!",repassword:"Password Mismatch!"},fields:{password:"Password:required; password;",repassword:"Repassword:required;repassword;"},valid:function(t){e()}}),n.on("click",function(){t.submit()})})},function(e,t){!function(t){"object"==typeof e&&e.exports&&(e.exports=t(jQuery))}(function(e,t){"use strict";function a(t,i){function s(){r._init(r.$el[0],i)}var r=this;return r instanceof a?(r.$el=e(t),void(r.$el.length?a.loading?e(window).on("validatorready",s):s():Q(t)&&(Y[t]=i))):new a(t,i)}function i(e,t){if(X(e)){var a,s=t?t===!0?this:t:i.prototype;for(a in e)f(a)&&(s[a]=r(e[a]))}}function s(e,t){if(X(e)){var a,i=t?t===!0?this:t:s.prototype;for(a in e)i[a]=e[a]}}function r(t){switch(e.type(t)){case"function":return t;case"array":var a=function(e){return t.msg=t[1],t[0].test(G(e))||t[1]||!1};return a.msg=t[1],a;case"regexp":return function(e){return t.test(G(e))}}}function n(t){var a,i,s;if(t&&t.tagName){switch(t.tagName){case"INPUT":case"SELECT":case"TEXTAREA":case"BUTTON":case"FIELDSET":a=t.form||e(t).closest("."+b);break;case"FORM":a=t;break;default:a=e(t).closest("."+b)}for(i in Y)if(e(a).is(i)){s=Y[i];break}return e(a).data(v)||e(a)[v](s).data(v)}}function l(e,t){var a=H(J(e,P+"-"+t));if(a)return a=new Function("return "+a)(),a?r(a):void 0}function o(e,t,a){var i=t.msg,s=t._r;return X(i)&&(i=i[s]),Q(i)||(i=J(e,C+"-"+s)||J(e,C)||(a?Q(a)?a:a[s]:"")),i}function u(e){var t;return e&&(t=R.exec(e)),t&&t[0]}function d(e){if(Q(e)||X(e)&&("error"in e||"ok"in e))return e}function c(e){return"INPUT"===e.tagName&&"checkbox"===e.type||"radio"===e.type}function p(e){return Date.parse(e.replace(/\.|\-/g,"/"))}function f(e){return/^\w+$/.test(e)}function g(e){return"#"===e.charAt(0)?e.replace(/(:|\.|\[|\])/g,"\\$1"):'[name="'+e+'"]:input'}var m,h,v="validator",y="."+v,_=".rule",w=".field",k=".form",b="nice-"+v,x="msg-box",$="aria-required",M="aria-invalid",P="data-rule",C="data-msg",O="data-tip",B="data-ok",A="data-timely",S="data-target",U="data-display",V="data-must",j="novalidate",E=":verifiable",T=/(&)?(!)?\b(\w+)(?:\[\s*(.*?\]?)\s*\]|\(\s*(.*?\)?)\s*\))?\s*(;|\|)?/g,F=/(\w+)(?:\[\s*(.*?\]?)\s*\]|\(\s*(.*?\)?)\s*\))?/,I=/(?:([^:;\(\[]*):)?(.*)/,q=/[^\x00-\xff]/g,R=/top|right|bottom|left/,N=/(?:(cors|jsonp):)?(?:(post|get):)?(.+)/i,D=/[<>'"`\\]|&#x?\d+[A-F]?;?|%3[A-F]/gim,z=e.noop,L=e.proxy,H=e.trim,W=e.isFunction,Q=function(e){return"string"==typeof e},X=function(e){return e&&"[object Object]"===Object.prototype.toString.call(e)},Z=document.documentMode||+(navigator.userAgent.match(/MSIE (\d+)/)&&RegExp.$1),J=function(e,a,i){return e&&e.tagName?i===t?e.getAttribute(a):void(null===i?e.removeAttribute(a):e.setAttribute(a,""+i)):null},G=function(t){return e(t).val()},K=window.console||{log:z,info:z},Y={},ee={debug:0,timely:1,theme:"def",ignore:"",focusInvalid:!0,beforeSubmit:z,msgWrapper:"span",msgMaker:function(t){var a;return a='<span role="alert" class="msg-wrap n-'+t.type+'">'+t.arrow,t.result?e.each(t.result,function(e,i){a+='<span class="n-'+i.type+'">'+t.icon+'<span class="n-msg">'+i.msg+"</span></span>"}):a+=t.icon+'<span class="n-msg">'+t.msg+"</span>",a+="</span>"},msgArrow:"",msgIcon:'<span class="n-icon"></span>',msgClass:"",validClass:"n-valid",invalidClass:"n-invalid"},te={def:{formClass:"n-default",msgClass:"n-right"}};e.fn[v]=function(t){var i=this,s=arguments;return i.is(":input")?i:(!i.is("form")&&(i=this.find("form")),!i.length&&(i=this),i.each(function(){var i=e(this).data(v);if(i)if(Q(t)){if("_"===t.charAt(0))return;i[t].apply(i,Array.prototype.slice.call(s,1))}else t&&(i._reset(!0),i._init(this,t));else new a(this,t)}),this)},e.fn.isValid=function(e,t){var a,i,s=n(this[0]),r=W(e);return!s||(s.checkOnly=!!t,i=s.options,a=s._multiValidate(this.is(":input")?this:this.find(E),function(t){t||!i.focusInvalid||s.checkOnly||s.$el.find("["+M+"]:first").focus(),r&&(e.length?e(t):t&&e()),s.checkOnly=!1}),r?this:a)},e.expr[":"].verifiable=function(e){var t=e.nodeName.toLowerCase();return("input"===t&&!{submit:1,button:1,reset:1,image:1}[e.type]||"select"===t||"textarea"===t)&&e.disabled===!1},e.expr[":"].filled=function(e){return!!H(G(e))},a.prototype={_init:function(t,a){var r,n,l,o=this;W(a)&&(a={valid:a}),a=a||{},l=J(t,"data-"+v+"-option"),l=l&&"{"===l.charAt(0)?new Function("return "+l)():{},n=te[a.theme||l.theme||ee.theme],r=o.options=e.extend({},ee,n,o.options,a,l),o.rules=new i(r.rules,!0),o.messages=new s(r.messages,!0),o.elements=o.elements||{},o.deferred={},o.errors={},o.fields={},o._initFields(r.fields),o.msgOpt={type:"error",pos:u(r.msgClass),wrapper:r.msgWrapper,cls:r.msgClass,style:r.msgStyle,arrow:r.msgArrow,icon:r.msgIcon,show:r.msgShow,hide:r.msgHide},Q(r.target)&&o.$el.find(r.target).addClass("msg-container"),o.$el.data(v)||(o.$el.data(v,o).addClass(b+" "+r.formClass).on("submit"+y+" validate"+y,L(o,"_submit")).on("reset"+y,L(o,"_reset")).on("showmsg"+y,L(o,"_showmsg")).on("hidemsg"+y,L(o,"_hidemsg")).on("focusin"+y+" click"+y,E,L(o,"_focusin")).on("focusout"+y+" validate"+y,E,L(o,"_focusout")),r.timely&&o.$el.on("keyup"+y+" input"+y+" compositionstart compositionend",E,L(o,"_focusout")).on("click"+y,":radio,:checkbox","click",L(o,"_focusout")).on("change"+y,'select,input[type="file"]',"change",L(o,"_focusout")),o._novalidate=J(t,j),J(t,j,j))},_guessAjax:function(t){var a=this;if(!(a.isAjaxSubmit=!!a.options.valid)){var i=(e._data||e.data)(t,"events");i&&i.valid&&e.map(i.valid,function(e){return~e.namespace.indexOf("form")?1:null}).length&&(a.isAjaxSubmit=!0)}},_initFields:function(t){var a=this,i=null===t;i&&(t=a.fields),X(t)&&e.each(t,function(e,t){if(null===t||i){var s=a.elements[e];s&&a._resetElement(s,!0),delete a.fields[e]}else a.fields[e]=Q(t)?{rule:t}:t}),a.$el.find(E).each(function(){a._parse(this)})},_parse:function(e){var t,a,i=this,s=i.options,r=e.name,n=J(e,P);if(n&&J(e,P,null),(e.id&&"#"+e.id in i.fields||!r||null!==n&&(t=i.fields[r])&&n!==t.rule&&t.key!==e.id)&&(r="#"+e.id),r)return t=i.fields[r]||{},t.key=r,t.rule=n||t.rule||"",t.display||!(t.display=J(e,U))&&s.display&&(t.display=s.display),t.rule&&((null!==J(e,V)||/match\(|checked/.test(t.rule))&&(t.must=!0),~t.rule.indexOf("required")&&(t.required=!0,J(e,$,!0)),"showOk"in t||(t.showOk=s.showOk),a=J(e,A),a?t.timely=+a:"timely"in t&&J(e,A,+t.timely),t=i._parseRule(t),t.old={}),Q(t.target)&&J(e,S,t.target),Q(t.tip)&&J(e,O,t.tip),i.fields[r]=t},_parseRule:function(a){var i=I.exec(a.rule);if(i)return a._i=0,i[1]&&(a.display=i[1]),i[2]&&(a.rules=[],i[2].replace(T,function(){var i=arguments;i[4]=i[4]||i[5],a.rules.push({and:"&"===i[1],not:"!"===i[2],or:"|"===i[6],method:i[3],params:i[4]?e.map(i[4].split(", "),function(e){return H(e)}):t})})),a},_multiValidate:function(a,i){var s=this,r=s.options;return s.hasError=!1,r.ignore&&(a=a.not(r.ignore)),a.each(function(){if(s._validate(this),s.hasError&&r.stopOnError)return!1}),i&&(s.validating=!0,e.when.apply(null,e.map(s.deferred,function(e){return e})).done(function(){i.call(s,!s.hasError),s.validating=!1})),e.isEmptyObject(s.deferred)?!s.hasError:t},_submit:function(a){function i(){var e,t;h=!0,m&&(e=m.name)?(m.name="",t=n.submit,s.$el.append('<input type="hidden" name="'+e+'" value="'+m.value+'">'),t.call(n)):n.submit()}var s=this,r=s.options,n=a.target,l="submit"===a.type&&!a.isDefaultPrevented();a.preventDefault(),h&&~(h=!1)||s.submiting||"validate"===a.type&&s.$el[0]!==n||r.beforeSubmit.call(s,n)===!1||(s.isAjaxSubmit===t&&s._guessAjax(n),r.debug&&K.log("\n<<< event: "+a.type),s._reset(),s.submiting=!0,s._multiValidate(s.$el.find(E),function(t){var a,o=t||2===r.debug?"valid":"invalid";t||(r.focusInvalid&&s.$el.find("["+M+"]:first").focus(),a=e.map(s.errors,function(e){return e})),s.submiting=!1,s.isValid=t,W(r[o])&&r[o].call(s,n,a),s.$el.trigger(o+k,[n,a]),r.debug&&K.log(">>> "+o),t&&l&&!s.isAjaxSubmit&&i()}))},_reset:function(e){var t=this;t.errors={},e&&(t.reseting=!0,t.$el.find(E).each(function(e,a){t._resetElement(a)}),delete t.reseting)},_resetElement:function(e,t){this._setClass(e,null),this.hideMsg(e),t&&J(e,$,null)},_getTimely:function(e,t){var a=J(e,A);return null!==a?+a:+t.timely},_focusin:function(e){var t,a,i=this,s=i.options,r=e.target;i.validating||"click"===e.type&&document.activeElement===r||(s.focusCleanup&&"true"===J(r,M)&&(i._setClass(r,null),i.hideMsg(r)),a=J(r,O),a?i.showMsg(r,{type:"tip",msg:a}):(J(r,P)&&i._parse(r),t=i._getTimely(r,s),8!==t&&9!==t||i._focusout(e)))},_focusout:function(a,i){var s,r,n,l,o,u,d,p=this,f=p.options,g=a.target,m=a.type,h="focusin"===m,v="validate"===m,y=0;if("compositionstart"===m&&(p.pauseValidate=!0),"compositionend"===m&&(p.pauseValidate=!1),!p.pauseValidate&&(s=p.getField(g))){if(s._e=m,r=s.old,n=G(g),!i&&c(g)&&(i=p.$el.find('input[name="'+g.name+'"]').get(0)),d=p._getTimely(i||g,f),!v){if(!d)return;if(f.ignoreBlank&&!n&&!h)return void p.hideMsg(g);if("focusout"===m){if(2===d||8===d){if(!n)return;s.isValid&&!r.showOk?p.hideMsg(g):p._makeMsg(g,s,r)}}else{if(d<2&&!a.data)return;if(l=+new Date,l-(g._ts||0)<100||"keyup"===m&&"input"===g._et)return;if(g._ts=l,g._et=m,"keyup"===m){if(o=a.keyCode,u={8:1,9:1,16:1,32:1,46:1},9===o&&!n)return;if(o<48&&!u[o])return}h||(y=d>=100?d:400)}}f.ignore&&e(g).is(f.ignore)||(clearTimeout(s._t),s.value=n,d!==t&&(s.timely=d),y?s._t=setTimeout(function(){p._validate(g,s)},y):(v&&(s.old={}),p._validate(g,s)))}},_setClass:function(t,a){var i=e(t),s=this.options;s.bindClassTo&&(i=i.closest(s.bindClassTo)),i.removeClass(s.invalidClass+" "+s.validClass),null!==a&&i.addClass(a?s.validClass:s.invalidClass)},_showmsg:function(t,a,i){var s=this,r=t.target;e(r).is(":input")?s.showMsg(r,{type:a,msg:i}):"tip"===a&&s.$el.find(E+"["+O+"]",r).each(function(){s.showMsg(this,{type:a,msg:i})})},_hidemsg:function(t){var a=e(t.target);a.is(":input")&&this.hideMsg(a)},_validatedField:function(t,a,i){var s=this,r=s.options,n=a.isValid=i.isValid=!!i.isValid,l=n?"valid":"invalid";i.key=a.key,i.ruleName=a._r,i.id=t.id,i.value=G(t),n?i.type="ok":(s.submiting&&(s.errors[a.key]=i.msg),s.isValid=!1,s.hasError=!0),s.elements[a.key]=i.element=t,s.$el[0].isValid=n?s.isFormValid():n,a.old=i,W(a[l])&&a[l].call(s,t,i),W(r.validation)&&r.validation.call(s,t,i),e(t).attr(M,!n||null).trigger(l+w,[i,s]),s.$el.triggerHandler("validation",[i,s]),s.checkOnly||(s._setClass(t,i.skip||"tip"===i.type?null:n),s._makeMsg.apply(s,arguments))},_makeMsg:function(t,a,i){(a.msgMaker||this.options.msgMaker)&&(i=e.extend({},i),"focusin"===a._e&&(i.type="tip"),this[i.showOk||i.msg||"tip"===i.type?"showMsg":"hideMsg"](t,i,a))},_validatedRule:function(a,i,s,r){i=i||c.getField(a),r=r||{};var n,l,u,d,c=this,p=c.options,f=i._r,g=i.timely||p.timely,m=9===g||8===g,h=!1;if(null===s)return void c._validatedField(a,i,{isValid:!0,skip:!0});if(s===t?u=!0:s===!0||""===s?h=!0:Q(s)?n=s:X(s)&&(s.error?n=s.error:(n=s.ok,h=!0)),l=i.rules[i._i],l.not&&(n=t,h="required"===f||!h),l.or)if(h)for(;i._i<i.rules.length&&i.rules[i._i].or;)i._i++;else u=!0;else l.and&&(i.isValid||(u=!0));u?h=!0:(h&&i.showOk!==!1&&(d=J(a,B),n=null===d?Q(i.ok)?i.ok:n:d,!Q(n)&&Q(i.showOk)&&(n=i.showOk),Q(n)&&(r.showOk=h)),h&&!m||(n=(o(a,i,n||l.msg||c.messages[f])||c.messages.fallback).replace(/\{0\|?([^\}]*)\}/,function(){return c._getDisplay(a,i.display)||arguments[1]||c.messages[0]})),h||(i.isValid=h),r.msg=n,e(a).trigger((h?"valid":"invalid")+_,[f,n])),!m||u&&!l.and||(h||i._m||(i._m=n),i._v=i._v||[],i._v.push({type:h?u?"tip":"ok":"error",msg:n||l.msg})),p.debug&&K.log("   "+i._i+": "+f+" => "+(h||n)),(h||m)&&i._i<i.rules.length-1?(i._i++,c._checkRule(a,i)):(i._i=0,m?(r.isValid=i.isValid,r.result=i._v,r.msg=i._m||"",i.value||"focusin"!==i._e||(r.type="tip")):r.isValid=h,c._validatedField(a,i,r),delete i._m,delete i._v)},_checkRule:function(a,i){var s,r,n,o=this,u=i.key,c=i.rules[i._i],p=c.method,f=G(a),g=c.params;o.submiting&&o.deferred[u]||(n=i.old,i._r=p,n&&!i.must&&!c.must&&c.result!==t&&n.ruleName===p&&n.id===a.id&&f&&n.value===f?s=c.result:(r=l(a,p)||o.rules[p]||z,s=r.call(o,a,g,i),r.msg&&(c.msg=r.msg)),X(s)&&W(s.then)?(o.deferred[u]=s,i.isValid=t,s.then(function(s,r,n){var l,u=n.responseText,p=i.dataFilter||o.options.dataFilter||d;/jsonp?/.test(this.dataType)?u=s:"{"===H(u).charAt(0)&&(u=e.parseJSON(u)),l=p.call(this,u,i),l===t&&(l=p.call(this,u.data,i)),c.data=this.data,c.result=i.old?l:t,o._validatedRule(a,i,l)},function(e,t){o._validatedRule(a,i,o.messages[t]||t)}).always(function(){delete o.deferred[u]})):o._validatedRule(a,i,s))},_validate:function(e,t){var a=this;if(!e.disabled&&null===J(e,j)&&(t=t||a.getField(e),t&&(t.rules||a._parse(e),t.rules)))return a.options.debug&&K.info(t.key),t.isValid=!0,t.required||t.must||G(e)||c(e)?(a._checkRule(e,t),t.isValid):(a._validatedField(e,t,{isValid:!0}),!0)},test:function(e,a){var i,s,r,n=this,l=F.exec(a);return l&&(s=l[1],s in n.rules&&(r=l[2]||l[3],r=r?r.split(", "):t,i=n.rules[s].call(n,e,r))),i===!0||i===t||null===i},getRangeMsg:function(e,t,a,i){function s(e,t){return d?e>t:e>=t}if(t){var r,n=this,l=a.rules[a._i],o=n.messages[l.method]||"",u=t[0].split("~"),d="false"===t[1],c=u[0],p=u[1],f="rg",g=[""],m=H(e)&&+e===+e;return 2===u.length?c&&p?(m&&s(e,+c)&&s(+p,e)&&(r=!0),g=g.concat(u),f=d?"gtlt":"rg"):c&&!p?(m&&s(e,+c)&&(r=!0),g.push(c),f=d?"gt":"gte"):!c&&p&&(m&&s(+p,e)&&(r=!0),g.push(p),f=d?"lt":"lte"):(e===+c&&(r=!0),g.push(c),f="eq"),o&&(i&&o[f+i]&&(f+=i),g[0]=o[f]),r||(l.msg=n.renderMsg.apply(null,g))}},renderMsg:function(){var e=arguments,t=e[0],a=e.length;if(t){for(;--a;)t=t.replace("{"+a+"}",e[a]);return t}},_getDisplay:function(e,t){return Q(t)?t:W(t)?t.call(this,e):""},_getMsgOpt:function(t){return e.extend({},this.msgOpt,Q(t)?{msg:t}:t)},_getMsgDOM:function(t,a){var i,s,r,n,l=e(t);if(l.is(":input")?(r=a.target||J(t,S),r&&(r=W(r)?r.call(this,t):this.$el.find(r),r.length&&(r.is(":input")?t=r.get(0):r.hasClass(x)?i=r:n=r)),i||(s=c(t)&&t.name||!t.id?t.name:t.id,i=this.$el.find(a.wrapper+"."+x+'[for="'+s+'"]'))):i=l,!i.length)if(l=this.$el.find(r||t),i=e("<"+a.wrapper+">"),i.attr("class",x+(a.cls?" "+a.cls:"")),i.attr("style",a.style||""),i.attr("for",s),c(t)){var o=l.parent();i.appendTo(o.is("label")?o.parent():o)}else n?i.appendTo(n):i[a.pos&&"right"!==a.pos?"insertBefore":"insertAfter"](l);return i},showMsg:function(t,a,i){if(t){var s,r,n,l=this,o=l.options;if(X(t)&&!t.jquery&&!a)return void e.each(t,function(e,t){var a=l.elements[e]||l.$el.find(g(e))[0];l.showMsg(a,t)});a=l._getMsgOpt(a),t=e(t).get(0),a.msg||"error"===a.type||(r=J(t,"data-"+a.type),null!==r&&(a.msg=r)),Q(a.msg)&&(e(t).is(E)&&(i=i||l.getField(t),i&&(a.style=i.msgStyle||a.style,a.cls=i.msgClass||a.cls,a.wrapper=i.msgWrapper||a.wrapper,a.target=i.target||o.target)),(s=(i||{}).msgMaker||o.msgMaker)&&(n=l._getMsgDOM(t,a),!R.test(n[0].className)&&n.addClass(a.cls),6===Z&&"bottom"===a.pos&&(n[0].style.marginTop=e(t).outerHeight()+"px"),n.html(s.call(l,a))[0].style.display="",W(a.show)&&a.show.call(l,n,a.type)))}},hideMsg:function(t,a,i){var s,r=this,n=r.options;t=e(t).get(0),a=r._getMsgOpt(a),e(t).is(E)&&(i=i||r.getField(t),i&&((i.isValid||r.reseting)&&J(t,M,null),a.wrapper=i.msgWrapper||a.wrapper,a.target=i.target||n.target)),s=r._getMsgDOM(t,a),s.length&&(W(a.hide)?a.hide.call(r,s,a.type):(s[0].style.display="none",s[0].innerHTML=null))},getField:function(e){var t,a=this;if(Q(e))t=e;else{if(J(e,P))return a._parse(e);t=e.id&&"#"+e.id in a.fields||!e.name?"#"+e.id:e.name}return a.fields[t]},setField:function(e,t){var a={};e&&(Q(e)?a[e]=t:a=e,this._initFields(a))},isFormValid:function(){var e,t,a=this.fields;for(e in a)if(t=a[e],t.rules&&(t.required||t.must||G(g(e)))&&!t.isValid)return t.isValid;return!0},holdSubmit:function(e){this.submiting=e===t||e},cleanUp:function(){this._reset(1)},destroy:function(){this._reset(1),this.$el.off(y).removeData(v),J(this.$el[0],j,this._novalidate)}},e(window).on("beforeunload",function(){this.focus()}),e(document).on("click",":submit",function(){var e,t=this;t.form&&(m=t,e=t.getAttributeNode("formnovalidate"),(e&&null!==e.nodeValue||null!==J(t,j))&&(h=!0))}).on("focusin submit validate","form,."+b,function(t){if(null===J(this,j)){var a,i=e(this);i.data(v)||(a=n(this),e.isEmptyObject(a.fields)?(J(this,j,j),i.off(y).removeData(v)):"focusin"===t.type?a._focusin(t):a._submit(t))}}),new s({fallback:"This field is not valid.",loading:"Validating..."}),new i({required:function(t,a,i){var s=this,r=H(G(t)),n=!0;if(a)if(1===a.length){if(f(a[0])){if(s.rules[a[0]]){if(!r&&!s.test(t,a[0]))return J(t,$,null),null;J(t,$,!0)}}else if(!r&&!e(a[0],s.$el).length)return null}else if("not"===a[0])e.each(a.slice(1),function(){return n=r!==H(this)});else if("from"===a[0]){var l,u=s.$el.find(a[1]),d="_validated_";return n=u.filter(function(){return!!H(G(this))}).length>=(a[2]||1),n?r||(l=null):l=o(u[0],i)||!1,e(t).data(d)||u.data(d,1).each(function(){t!==this&&s._checkRule(this,s.getField(this))}).removeData(d),l}return n&&!!r},integer:function(e,t){var a,i="0|",s="[1-9]\\d*",r=t?t[0]:"*";switch(r){case"+":a=s;break;case"-":a="-"+s;break;case"+0":a=i+s;break;case"-0":a=i+"-"+s;break;default:a=i+"-?"+s}return a="^(?:"+a+")$",new RegExp(a).test(G(e))||this.messages.integer[r]},match:function(t,a,i){if(a){var s,r,n,l,o,u,d,c,f=this,m="eq";if(1===a.length?n=a[0]:(m=a[0],n=a[1]),u=g(n),d=f.$el.find(u)[0]){if(c=f.getField(d),s=G(t),r=G(d),i._match||(f.$el.on("valid"+w+y,u,function(){e(t).trigger("validate")}),i._match=c._match=1),!i.required&&""===s&&""===r)return null;if(o=a[2],o&&(/^date(time)?$/i.test(o)?(s=p(s),r=p(r)):"time"===o&&(s=+s.replace(/:/g,""),r=+r.replace(/:/g,""))),"eq"!==m&&!isNaN(+s)&&isNaN(+r))return!0;switch(l=f.messages.match[m].replace("{1}",f._getDisplay(t,c.display||n)),m){case"lt":return+s<+r||l;case"lte":return+s<=+r||l;case"gte":return+s>=+r||l;case"gt":return+s>+r||l;case"neq":return s!==r||l;default:return s===r||l}}}},range:function(e,t,a){return this.getRangeMsg(G(e),t,a)},checked:function(e,t,a){if(c(e)){var i,s,r=this;return e.name?s=r.$el.find('input[name="'+e.name+'"]').filter(function(){var e=this;return!i&&c(e)&&(i=e),!e.disabled&&e.checked}).length:(i=e,s=i.checked),t?r.getRangeMsg(s,t,a):!!s||o(i,a,"")||r.messages.required}},length:function(e,t,a){var i=G(e),s=("true"===t[1]?i.replace(q,"xx"):i).length;return this.getRangeMsg(s,t,a,t[1]?"_2":"")},remote:function(t,a,i){if(a){var s,r=this,n=N.exec(a[0]),l=i.rules[i._i],o={},u="",d=n[3],c=n[2]||"POST",p=(n[1]||"").toLowerCase();return l.must=!0,o[t.name]=G(t),a[1]&&e.map(a.slice(1),function(e){var t,a;~e.indexOf("=")?u+="&"+e:(t=e.split(":"),e=H(t[0]),a=H(t[1])||e,o[e]=r.$el.find(g(a)).val())}),o=e.param(o)+u,!i.must&&l.data&&l.data===o?l.result:("cors"!==p&&/^https?:/.test(d)&&!~d.indexOf(location.host)&&(s="jsonp"),e.ajax({url:d,type:c,data:o,dataType:s}))}},validate:function(t,a){var i="_validated_";a&&!e(t).data(i)&&this.$el.find(e.map(a,function(e){return g(e)}).join(",")).data(i,1).trigger("validate").removeData(i)},filter:function(e,t){var a,i=G(e);a=i.replace(t?new RegExp("["+t[0]+"]","gm"):D,""),a!==i&&(e.value=a)}}),a.config=function(t){e.each(t,function(e,t){"rules"===e?new i(t):"messages"===e?new s(t):ee[e]=t})},a.setTheme=function(t,a){X(t)?e.extend(!0,te,t):Q(t)&&X(a)&&(te[t]=e.extend(te[t],a))},e[v]=a}),$.validator.config({theme:"simple_bottom",rules:{digits:[/^\d+$/,"请填写数字"],letters:[/^[a-z]+$/i,"请填写字母"],date:[/^\d{4}-\d{2}-\d{2}$/,"请填写有效的日期，格式:yyyy-mm-dd"],time:[/^([01]\d|2[0-3])(:[0-5]\d){1,2}$/,"请填写有效的时间，00:00到23:59之间"],email:[/^[\w\+\-]+(\.[\w\+\-]+)*@[a-z\d\-]+(\.[a-z\d\-]+)*\.([a-z]{2,4})$/i,"Please enter the Email Address in correct format!"],url:[/^(https?|s?ftp):\/\/\S+$/i,"请填写有效的网址"],qq:[/^[1-9]\d{4,}$/,"请填写有效的QQ号"],IDcard:[/^\d{6}(19|2\d)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)?$/,"请填写正确的身份证号码"],tel:[/^(?:(?:0\d{2,3}[\- ]?[1-9]\d{6,7})|(?:[48]00[\- ]?[1-9]\d{6}))$/,"请填写有效的电话号码"],mobile:[/^1[3-9]\d{9}$/,"Please enter the phone number in correct format!"],zipcode:[/^\d{6}$/,"请检查邮政编码格式"],chinese:[/^[\u0391-\uFFE5]+$/,"请填写中文字符"],username:[/^\w{3,12}$/,"请填写3-12位数字、字母、下划线"],password:[/^[\S]{6,16}$/,"请填写6-16位字符，不能包含空格"],accept:function(e,t){if(!t)return!0;var a=t[0],i=$(e).val();return"*"===a||new RegExp(".(?:"+a+")$","i").test(i)||this.renderMsg("只接受{1}后缀的文件",a.replace(/\|/g,","))}},messages:{0:"该字段",fallback:"Please enter the {0}in correct format!",error:"网络异常",timeout:"请求超时",required:"Please enter a Valid {0}.",remote:"{0}已被使用",integer:{"*":"请填写整数","+":"请填写正整数","+0":"请填写正整数或0","-":"请填写负整数","-0":"请填写负整数或0"},match:{eq:"{0}与{1}不一致",neq:"{0}与{1}不能相同",lt:"{0}必须小于{1}",gt:"{0}必须大于{1}",lte:"{0}不能大于{1}",gte:"{0}不能小于{1}"},range:{rg:"请填写{1}到{2}的数",gte:"请填写不小于{1}的数",lte:"请填写最大{1}的数",gtlt:"请填写{1}到{2}之间的数",gt:"请填写大于{1}的数",lt:"请填写小于{1}的数"},checked:{eq:"请选择{1}项",rg:"请选择{1}到{2}项",gte:"请至少选择{1}项",lte:"请最多选择{1}项"},length:{eq:"请填写{1}个字符",rg:"请填写{1}到{2}个字符",gte:"at least {1} ch",lte:"请最多填写{1}个字符",eq_2:"",rg_2:"",gte_2:"",lte_2:""}}})},function(e,t,a){var i=a(3),s={checkUserName:{url:i.apiBasePath+"/User/Index/CheckUserName",data:{username:""}},checkPhone:{url:i.apiBasePath+"/User/Index/CheckPhone",data:{phone:"",act:"reg"}},checkEmail:{url:i.apiBasePath+"/User/Index/CheckEmail",data:{email:""}},sendEmail:{url:i.apiBasePath+"/User/Index/sendEmail",data:{email:""}},sendSms:{url:i.apiBasePath+"/User/Index/sendSms",data:{uv_r:"",phone:""}},getMsgCode:{url:i.apiBasePath+"/User/Index/code",data:{phone:"",timestamp:"",uv_r:""}},checkMsgCode:{url:i.apiBasePath+"/User/Index/CheckMsg",data:{country:"",msgCode:""}},checkImgCode:{url:i.apiBasePath+"/User/Index/CheckVerify",data:{captcha:""}},register:{url:i.apiBasePath+"/User/Index/register",data:{country:"",username:"",phone:"",email:"",password:"",repassword:"",msgCode:""}},login:{url:i.apiBasePath+"/User/Index/login",data:{username:"",password:"",captcha:""}},forget:{url:i.apiBasePath+"/User/Index/forgetPasswordStep",data:{captcha:"",mobile:"",msgCode:""}},forgetPassword:{url:i.apiBasePath+"/User/Index/forgetPasswordStep2",data:{newpass:"",repnewpass:""}},getLoginCount:{url:i.apiBasePath+"/User/Index/getCount",data:{username:""}},getVerify:{url:i.apiBasePath+"/User/index/verify/"},editPassword:{url:i.apiBasePath+"/User/AccountSecurity/editPassword",data:{old_password:"",password:"",newpassword:""}},editPhone:{url:i.apiBasePath+"/User/AccountSecurity/bindPhone",data:{phone:"",msgCode:""}},buyOfferRelease:{url:i.apiBasePath+"/User/Buyoffer/BuyOfferRelease",data:{title:"",type:"",content:"",expire:""}},buyOfferModify:{url:i.apiBasePath+"/User/Buyoffer/modify",data:{title:"",type:"",content:"",expire:"",id:""}},delBuyOffer:{url:i.apiBasePath+"/User/Buyoffer/delBuyOffer",data:{id:""}},delCollect:{url:i.apiBasePath+"/User/Collect/delCollect",data:{id:""}},checkCompanyName:{url:i.apiBasePath+"/User/Account/CheckCompanyNameOnly",data:{companyName:""}},insertInfo:{url:i.apiBasePath+"/User/Account/InsertInfo"},sendMessage:{url:i.apiBasePath+"/User/Message/sendMessage",data:{to:"",subject:"",content:""}},replyMessage:{url:i.apiBasePath+"/User/Message/reply",data:{to:"",subject:"",content:"",reply:""}},delMessage:{url:i.apiBasePath+"/User/Message/delMail",data:{id:""}},delSystemMessage:{url:i.apiBasePath+"/User/Message/delSystem",data:{id:""}},messageMark:{url:i.apiBasePath+"/User/Message/mark",data:{id:"",read:""}},checkEmailCode:{url:i.apiBasePath+"/AccountSecurity/EmailCode",date:{code:""}},submitAuthCard:{url:i.apiBasePath+"/User/Account/submit_auth",data:{}},addSupply:{url:i.apiBasePath+"/User/Supply/addSupply ",data:{title:"",type:"",content:"",expire:""}},supplyModify:{url:i.apiBasePath+"/User/Supply/modify ",data:{title:"",type:"",content:"",expire:"",id:""}},delSupplyOffer:{url:i.apiBasePath+"/User/Supply/delSupplyOffer",data:{id:""}}};e.exports=s},function(e,t){var a={apiBasePath:"",apiMember:""};e.exports=a},function(e,t){var a={ajax:function(e,t,a){if(a){if(a.prop("disabled"))return;a.prop("disabled",!0)}var i={url:"",type:"POST",dataType:"json"};$.ajaxSetup({beforeSend:function(e){$("#_ActionToken_").val()&&e.setRequestHeader("Actiontoken",$("#_ActionToken_").val())}});var s=$.extend(i,e,t);return $.ajax(s).then(function(e){return 200!=e.code?$.Deferred().reject(e).promise():$.Deferred().resolve(e).promise()},function(){console.log("服务器错误，请稍后再试"),$.Deferred().reject().promise()}).always(function(e){a&&a.prop("disabled",!1),600==e.code&&e.data&&e.data.token&&$("#_ActionToken_").val(e.data.token)})},getUrlParam:function(){var e={},t="",a="",i=[],s=0,r=0;if(a=location.search.substr(1),0!==a.length){for(i=a.split("&"),r=i.length;s<r;s++)t=i[s].split("=")[0],e[t]=i[s].split("=")[1];return e}},serializeParam:function(e){if(!e)return"";var t=[];for(var a in e){var i=e[a];"[object Array]"!=Object.prototype.toString.call(i)?t.push(a+"="+e[a]):t.push(a+"="+e[a].join(","))}return t.join("&")},param:function(e){var t={};if(0==e.length)return t;for(var a,i=0,s=e.length;i<s;i++)a=e[i],t[a.name]=a.value;return t}};e.exports=a}]);