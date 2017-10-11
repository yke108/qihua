!function(e){function r(t){if(a[t])return a[t].exports;var n=a[t]={exports:{},id:t,loaded:!1};return e[t].call(n.exports,n,n.exports,r),n.loaded=!0,n.exports}var a={};return r.m=e,r.c=a,r.p="js/",r(0)}([function(e,r,a){$(function(){function e(){h.isFormValid()&&$(".join").addClass("join-success")}function r(){$(".join").removeClass("join-success")}function t(){var e={data:{username:o.val(),country:l.val(),company:u.val(),contact:c.val(),email:d.val(),password:m.val(),repassword:p.val(),uv_r:$("#uv_r").val()}};i.ajax(s.register,e,$(".join")).then(function(e){window.location.href=e.data.url},function(e){$("#error-tips").html(e.msg),e.data.ur_v&&$("#uv_r").val(e.data.ur_v)})}function n(){$(".container").addClass("hide-container"),setTimeout(function(){$(".country-wrap").hide()},200)}var s=a(1),i=a(3);setTimeout(function(){$("body").height($(window).height())},600),$("body").height($(window).height());var o=$('input[name="username"]'),l=$('input[name="country"]'),u=$('input[name="company"]'),c=$('input[name="contact"]'),d=$('input[name="email"]'),m=$('input[name="password"]'),p=$('input[name="repassword"]'),h=($('input[name="confirmPassword"]'),$('input[name="rules-check"]'),$("form").validator({stopOnError:!0,focusCleanup:!0,focusInvalid:!1,showOk:!1,msgMaker:!1,timely:2,rules:{memberId:function(e){return!!/^[a-zA-Z][0-9a-zA-Z]*$/.test(e.value)},memberIdNum:function(e){return!!/^.{6,18}$/.test(e.value)},checkUserName:function(e){return $.ajax({url:s.checkUserName.url,type:"post",data:{username:e.value},dataType:"json"})},password:function(e,r,a){return!/^\d+$/.test(e.value)&&(!/^[a-zA-Z]+$/.test(e.value)&&!!/^[0-9a-zA-Z]*$/.test(e.value))},passwordNum:function(e,r,a){return!!/^.{6,18}$/.test(e.value)},companyName:function(e){return!!/^.{2,100}$/.test(e.value)},name:function(e){return!!/^.{2,50}$/.test(e.value)},email:function(e){return!!/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(e.value)},checkEmail:function(e,r,a){return $.ajax({url:s.checkEmail.url,type:"post",data:{email:e.value,act:"reg"},dataType:"json"})},matchPassword:function(e){return m.val()==e.value},rulesChecked:function(e){return e.checked}},messages:{memberId:"Member Id contains letters(case-sensitive) and number,begin with a letter.",memberIdNum:"Member Id limited to 6~18 characters.",checkUserName:"Member Id had been registered.",companyName:"Company limited to 2~ 100 characters.",name:"Name limited to 2~ 50 characters.",email:"Please enter a valid email address.",checkEmail:"Email address had been registered.",password:"Password must contains letters(case-sensitive) and number.",passwordNum:"Password limited to 6~18 characters.",matchPassword:"Confirm password don't match.",rulesChecked:"Please agree the registration terms."},fields:{username:{rule:"member ID:required; memberId; memberIdNum; checkUserName;",invalid:function(e,a){$(e).parent().addClass("has-error"),$("#error-tips").html(a.msg),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),e()}},country:{rule:"country:required;",invalid:function(e,a){$(e).parent().addClass("has-error"),$("#error-tips").html("Country cannot be empty."),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),e()}},company:{rule:"company:required; companyName;",invalid:function(e,a){$(e).parent().addClass("has-error"),$("#error-tips").html(a.msg),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),e()}},contact:{rule:"name:required; name;",invalid:function(e,a){$(e).parent().addClass("has-error"),$("#error-tips").html(a.msg),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),e()}},email:{rule:"email:required; email; checkEmail;",invalid:function(e,a){$(e).parent().addClass("has-error"),$("#error-tips").html(a.msg),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),e()}},password:{rule:"password:required; password; passwordNum; ",invalid:function(e,a){$(e).parent().addClass("has-error"),$("#error-tips").html(a.msg),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),e()}},repassword:{rule:"password:required; matchPassword;",invalid:function(e,a){$(e).parent().addClass("has-error"),$("#error-tips").html(a.msg),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),e()}},rulesCheck:{rule:"rulesChecked",invalid:function(e,a){$("#error-tips").html(a.msg),r()},valid:function(r,a){$(r).parent().removeClass("has-error"),$("#error-tips").html(""),$("#error-tips").html(""),e()}}},invalid:function(e,r){$("#error-tips").html(r[0]),$(".join").removeClass("join-success")},valid:function(e){$(".join").addClass("join-success"),t()}}).data("validator"));$(".join").on("click",function(){$("form").trigger("validate")}),$(".country-select").on("click",function(){$(".container").removeClass("hide-container"),$(".country-wrap").show(),l.trigger("validate")}),$(".country-wrap").on("click","li",function(){$(this).addClass("selected").siblings().removeClass("selected"),l.val($(this).attr("data-id")),l.trigger("validate"),$(".country-select").find(".country-data").html($(this).text()).addClass("filled"),setTimeout(function(){n()},300)}),$(".country-wrap").on("click",".layer",function(){n()}),$(".rules-checkbox ").trigger("click")})},function(e,r,a){var t=a(2),n={register:{url:t.apiBasePath+"/mobile/exhibit/in170425",data:{memberid:"",country:"",company:"",name:"",email:"",password:"",uv_r:""}},invention:{url:t.apiBasePath+"/mobile/exhibit/invention",data:{invention:"",uv_r:""}},checkUserName:{url:t.apiBasePath+"/User/Index/CheckUserName",data:{username:""}},checkEmail:{url:t.apiBasePath+"/User/Index/CheckEmail",data:{email:"",act:"reg"}}};e.exports=n},function(e,r){var a={apiBasePath:"",apiMember:""};e.exports=a},function(e,r){var a={ajax:function(e,r,a){if(a){if(a.prop("disabled"))return;a.prop("disabled",!0),a.addClass("ajax-loading")}var t={url:"",type:"POST",dataType:"json"};$.ajaxSetup({beforeSend:function(e){$("#_ActionToken_").val()&&e.setRequestHeader("Actiontoken",$("#_ActionToken_").val())}});var n=$.extend(t,e,r);return $.ajax(n).then(function(e){return 200!=e.code?$.Deferred().reject(e).promise():$.Deferred().resolve(e).promise()},function(){console.log("服务器错误，请稍后再试"),$.Deferred().reject().promise()}).always(function(e){a&&(a.prop("disabled",!1),a.removeClass("ajax-loading")),600==e.code&&e.data&&e.data.token&&$("#_ActionToken_").val(e.data.token)})},getUrlParam:function(){var e={},r="",a="",t=[],n=0,s=0;if(a=location.search.substr(1),0!==a.length){for(t=a.split("&"),s=t.length;n<s;n++)r=t[n].split("=")[0],e[r]=encodeURIComponent(t[n].split("=")[1]);return e}},serializeParam:function(e){if(!e)return"";var r=[];for(var a in e){var t=e[a];"[object Array]"!=Object.prototype.toString.call(t)?r.push(a+"="+e[a]):r.push(a+"="+e[a].join(","))}return r.join("&")},param:function(e){var r={};if(0==e.length)return r;for(var a,t=0,n=e.length;t<n;t++)a=e[t],r[a.name]=a.value;return r}};e.exports=a}]);