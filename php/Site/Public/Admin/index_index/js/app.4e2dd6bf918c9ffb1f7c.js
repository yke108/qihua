webpackJsonp([1],{"+WQv":function(e,t){},"/joN":function(e,t,a){"use strict";function r(e){a("+WQv")}var n=a("Juf0"),l=a("dcdk"),i=a("VU/8"),o=r,s=i(n.a,l.a,o,null,null);t.a=s.exports},"2js9":function(e,t){},"4poY":function(e,t){},"9W5b":function(e,t,a){"use strict";var r=a("q2lo");t.a={data:function(){return{value5:3.7}},components:{"v-charts":r.a}}},GMSx:function(e,t,a){"use strict";function r(e){a("PC/f")}var n=a("pSPx"),l=a("MlDK"),i=a("VU/8"),o=r,s=i(n.a,l.a,o,null,null);t.a=s.exports},JXTs:function(e,t,a){"use strict";function r(e){a("KT8q")}var n=a("9W5b"),l=a("N7g9"),i=a("VU/8"),o=r,s=i(n.a,l.a,o,null,null);t.a=s.exports},Juf0:function(e,t,a){"use strict";var r=a("woOf"),n=a.n(r);t.a={data:function(){return{period:[{required:!0,message:"请选择重复周期",trigger:"change"}],starDate:[{type:"date",required:!0,message:"请选择开始日期",trigger:"change"}],endDate:[{type:"date",required:!0,message:"请选择结束日期",trigger:"change"}],pickerOptionsStart:{disabledDate:function(e){return e.getTime()<14882976e5||e.getTime()>=Date.now()}},pickerOptionsOver:{disabledDate:function(e){return e.getTime()<14882976e5||e.getTime()>=Date.now()}},forbidden:!0,redStar:!1,ruleForm:{name:"",type:"",way:[],date:"",period:"",starDate:"",endDate:"",delivery:!1,other:""},rules:{name:[{required:!0,message:"请输入待办事项名称",trigger:"blur"},{min:3,max:20,message:"长度在 3 到 20 个字符",trigger:"blur"}],type:[{required:!0,message:"请选择待办事项类型",trigger:"change"}],way:[{type:"array",required:!0,message:"请至少选择一个提醒方式",trigger:"change"}],date:[{type:"date",required:!0,message:"请选择提醒时间",trigger:"change"}],period:[],starDate:[],endDate:[],other:[{required:!0,message:"请填写其他信息",trigger:"blur"}]}}},methods:{getDate:function(e){var t=e,a=t.split(" "),r=a[0].split("-");return new Date(r[0],r[1]-1,r[2])},starTimeChang:function(e){var t=this.getDate(e);this.pickerOptionsOver={disabledDate:function(e){return e.getTime()<=t||e.getTime()>=Date.now()}}},endTimeChang:function(e){var t=this.getDate(e);this.pickerOptionsStart={disabledDate:function(e){return e.getTime()<14882976e5||e.getTime()>=t}}},changeOnOff:function(e){!0===e?(this.forbidden=!1,this.redStar=!0,this.$set(this.rules,"starDate",this.starDate),this.$set(this.rules,"endDate",this.endDate),this.$set(this.rules,"period",this.period),this.$refs.ruleForm.validate()):(this.forbidden=!0,this.redStar=!1,this.$set(this.rules,"starDate",[{type:"date",required:!1,trigger:"change"}]),this.$set(this.rules,"endDate",[{type:"date",required:!1,trigger:"change"}]),this.$set(this.rules,"period",[{}]),this.$refs.ruleForm.validate())},submitForm:function(e){var t=this;this.$refs[e].validate(function(e){if(!e)return!1;var a=n()({},t.ruleForm);console.log(a),t.$message({message:"提交成功，请在控制台查看json!！",type:"success"})})},resetForm:function(e){this.$refs[e].resetFields()}}}},KNmx:function(e,t,a){"use strict";var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("el-row",{staticClass:"container",staticStyle:{height:"100%"}},[a("v-header",{attrs:{user:e.user}}),e._v(" "),a("el-col",{staticClass:"main",attrs:{span:24}},[a("el-row",[a("el-menu",{staticClass:"el-menu-vertical-demo hy_menu el-col el-col-3",attrs:{"default-active":"2"},on:{select:e.selectItem,open:e.handleOpen,close:e.handleClose}},e._l(e.items,function(t){return a("div",{key:t.id},[a("el-submenu",{attrs:{index:t.id}},[a("template",{slot:"title"},[a("i",{class:t.cls}),e._v(e._s(t.txt))]),e._v(" "),e._l(t.children,function(t){return a("p",{key:t.id},[a("el-menu-item",{attrs:{index:t.id}},[e._v(e._s(t.txt))])],1)})],2)],1)})),e._v(" "),a("section",{staticClass:"contentCon",staticStyle:{width:"100%",height:"100%",position:"fixed"}},[a("el-col",{staticClass:"content-wrapper",staticStyle:{height:"100%"},attrs:{span:21,offset:3}},[a("transition",[a("iframe",{staticStyle:{height:"100%"},attrs:{src:"/test/index.php/index/main",id:"ifr100"}})])],1)],1)],1)],1)],1)},n=[],l={render:r,staticRenderFns:n};t.a=l},KT8q:function(e,t){},M93x:function(e,t,a){"use strict";function r(e){a("mi2t")}var n=a("xJD8"),l=a("KNmx"),i=a("VU/8"),o=r,s=i(n.a,l.a,o,null,null);t.a=s.exports},MlDK:function(e,t,a){"use strict";var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("section",[a("el-row",[a("el-col",{attrs:{span:24}},[a("el-form",{staticClass:"demo-form-inline",attrs:{inline:!0,model:e.formInline}},[a("el-form-item",{attrs:{label:"姓名"}},[a("el-input",{attrs:{placeholder:"姓名"},model:{value:e.formInline.user.name,callback:function(t){e.formInline.user.name=t},expression:"formInline.user.name"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"年份"}},[a("el-date-picker",{attrs:{align:"right",type:"year",placeholder:"选择年份"},model:{value:e.formInline.user.date,callback:function(t){e.formInline.user.date=t},expression:"formInline.user.date"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"地址"}},[a("el-cascader",{attrs:{"expand-trigger":"hover",options:e.options},model:{value:e.formInline.user.address,callback:function(t){e.formInline.user.address=t},expression:"formInline.user.address"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"籍贯"}},[a("el-select",{attrs:{placeholder:"请选择"},model:{value:e.formInline.user.place,callback:function(t){e.formInline.user.place=t},expression:"formInline.user.place"}},e._l(e.places,function(e){return a("el-option",{attrs:{label:e.label,value:e.value}})}))],1),e._v(" "),a("el-button",{attrs:{type:"primary"},on:{click:e.onSubmit}},[e._v("查询")]),e._v(" "),a("a",{staticStyle:{float:"right",color:"#169bd5","font-size":"14px","padding-top":"7px"},attrs:{href:"javascript:;",id:"download",download:"download.csv"},on:{click:function(t){e.download()}}},[e._v("导出数据")])],1),e._v(" "),a("el-table",{staticStyle:{width:"100%"},attrs:{data:e.tableData,border:""}},[a("el-table-column",{attrs:{type:"selection"}}),e._v(" "),a("el-table-column",{attrs:{prop:"date",label:"出生日期",width:"180"}}),e._v(" "),a("el-table-column",{attrs:{prop:"name",label:"姓名",width:"180"}}),e._v(" "),a("el-table-column",{attrs:{prop:"address",label:"地址"}}),e._v(" "),a("el-table-column",{attrs:{label:"操作"},scopedSlots:e._u([{key:"default",fn:function(t){return[a("el-button",{attrs:{type:"primary",size:"small"},on:{click:function(a){e.handleEdit(t.$index,t.row)}}},[e._v("编辑")]),e._v(" "),a("el-button",{attrs:{type:"danger",size:"small"},on:{click:function(a){e.handleDelete(t.$index,t.row)}}},[e._v("删除")])]}}])})],1),e._v(" "),a("div",{staticClass:"block"},[a("el-pagination",{attrs:{"current-page":e.currentPage,"page-size":100,layout:"prev, pager, next, jumper",total:1e3},on:{"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}})],1)],1)],1),e._v(" "),a("el-dialog",{attrs:{title:"修改个人信息",size:"tiny"},model:{value:e.dialogFormVisible,callback:function(t){e.dialogFormVisible=t},expression:"dialogFormVisible"}},[a("el-form",{ref:"form",attrs:{model:e.form,"label-width":"80px"}},[a("el-form-item",{attrs:{label:"姓名"}},[a("el-input",{model:{value:e.form.name,callback:function(t){e.form.name=t},expression:"form.name"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"地址"}},[a("el-input",{model:{value:e.form.address,callback:function(t){e.form.address=t},expression:"form.address"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"出生日期"}},[a("el-date-picker",{staticStyle:{width:"100%"},attrs:{type:"date",placeholder:"选择日期"},model:{value:e.form.date,callback:function(t){e.form.date=t},expression:"form.date"}})],1),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary",loading:e.editLoading},on:{click:e.handleSave}},[e._v("修改")]),e._v(" "),a("el-button",{on:{click:function(t){e.dialogFormVisible=!1}}},[e._v("取消")])],1)],1)],1)],1)},n=[],l={render:r,staticRenderFns:n};t.a=l},N7g9:function(e,t,a){"use strict";var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("section",{staticClass:"chart-container"},[a("el-row",[a("div",{staticClass:"show-section top"},[a("span",[e._v("今日系统安全指数：")]),e._v(" "),a("el-rate",{attrs:{disabled:"","show-text":"","text-color":"#ff9900","text-template":"{value}"},model:{value:e.value5,callback:function(t){e.value5=t},expression:"value5"}})],1),e._v(" "),a("div",{staticClass:"show-section"},[a("v-charts")],1)])],1)},n=[],l={render:r,staticRenderFns:n};t.a=l},NHnr:function(e,t,a){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var r=a("7+uW"),n=a("/ocq"),l=a("M93x"),i=a("q8zI"),o=(a.n(i),a("zL8q")),s=a.n(o),c=a("JXTs"),d=a("GMSx"),u=a("/joN"),m=a("e0XP"),p=(a.n(m),a("mtWM")),f=a.n(p);r.default.config.productionTip=!1,r.default.prototype.$http=f.a;var h=[{path:"/",component:l.a,children:[{path:"/index",component:c.a,name:"概况",class:"fa-line-chart"},{path:"/table",component:d.a,name:"列表",class:"fa-table"},{path:"/form",component:u.a,name:"表单",class:"fa-newspaper-o"}]}],v=new n.a({linkActiveClass:"active",routes:h});r.default.use(n.a),r.default.use(s.a),new r.default({el:"#app",router:v,template:"<App/>",components:{App:l.a}})},OK80:function(e,t,a){"use strict";function r(e){a("2js9")}var n=a("sd3e"),l=a("f/Gs"),i=a("VU/8"),o=r,s=i(n.a,l.a,o,null,null);t.a=s.exports},"PC/f":function(e,t){},dcdk:function(e,t,a){"use strict";var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("section",{staticClass:"form-section"},[a("el-form",{ref:"ruleForm",staticClass:"demo-ruleForm",attrs:{model:e.ruleForm,rules:e.rules,"label-width":"100px"}},[a("el-form-item",{attrs:{label:"待办事项",prop:"name"}},[a("el-input",{model:{value:e.ruleForm.name,callback:function(t){e.ruleForm.name=t},expression:"ruleForm.name"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"事项类型",prop:"type"}},[a("el-radio-group",{model:{value:e.ruleForm.type,callback:function(t){e.ruleForm.type=t},expression:"ruleForm.type"}},[a("el-radio",{attrs:{label:"1"}},[e._v("常规")]),e._v(" "),a("el-radio",{attrs:{label:"2"}},[e._v("紧急")])],1)],1),e._v(" "),a("el-form-item",{attrs:{label:"提醒方式",prop:"way"}},[a("el-checkbox-group",{model:{value:e.ruleForm.way,callback:function(t){e.ruleForm.way=t},expression:"ruleForm.way"}},[a("el-checkbox",{attrs:{label:"短信"}}),e._v(" "),a("el-checkbox",{attrs:{label:"电话"}}),e._v(" "),a("el-checkbox",{attrs:{label:"邮件"}}),e._v(" "),a("el-checkbox",{attrs:{label:"微信"}})],1)],1),e._v(" "),a("el-form-item",{attrs:{label:"提醒时间",required:""}},[a("el-row",[a("el-form-item",{attrs:{prop:"date"}},[a("el-time-picker",{staticStyle:{width:"100%"},attrs:{type:"fixed-time",placeholder:"选择提醒时间"},model:{value:e.ruleForm.date,callback:function(t){e.ruleForm.date=t},expression:"ruleForm.date"}})],1)],1)],1),e._v(" "),a("el-form-item",{attrs:{label:"重复提醒",prop:"delivery"}},[a("el-switch",{attrs:{"on-text":"","off-text":""},on:{change:e.changeOnOff},model:{value:e.ruleForm.delivery,callback:function(t){e.ruleForm.delivery=t},expression:"ruleForm.delivery"}})],1),e._v(" "),a("el-form-item",{attrs:{label:"重复时间",required:e.redStar}},[a("el-col",{attrs:{span:11}},[a("el-form-item",{attrs:{prop:"starDate"}},[a("el-date-picker",{staticStyle:{width:"100%"},attrs:{disabled:e.forbidden,type:"date",placeholder:"选择开始日期","picker-options":e.pickerOptionsStart},on:{change:e.starTimeChang},model:{value:e.ruleForm.starDate,callback:function(t){e.ruleForm.starDate=t},expression:"ruleForm.starDate"}})],1)],1),e._v(" "),a("el-col",{staticClass:"line",attrs:{span:1}},[e._v(" -")]),e._v(" "),a("el-col",{attrs:{span:12}},[a("el-form-item",{attrs:{prop:"endDate"}},[a("el-date-picker",{staticStyle:{width:"100%"},attrs:{disabled:e.forbidden,type:"date",placeholder:"选择结束日期","picker-options":e.pickerOptionsOver},on:{change:e.endTimeChang},model:{value:e.ruleForm.endDate,callback:function(t){e.ruleForm.endDate=t},expression:"ruleForm.endDate"}})],1)],1)],1),e._v(" "),a("el-form-item",{attrs:{label:"重复周期",prop:"period",required:e.redStar}},[a("el-select",{attrs:{disabled:e.forbidden,placeholder:"请选择重复周期"},model:{value:e.ruleForm.period,callback:function(t){e.ruleForm.period=t},expression:"ruleForm.period"}},[a("el-option",{attrs:{label:"每日提醒",value:"1"}}),e._v(" "),a("el-option",{attrs:{label:"每周提醒",value:"2"}}),e._v(" "),a("el-option",{attrs:{label:"每月提醒",value:"3"}}),e._v(" "),a("el-option",{attrs:{label:"工作日提醒",value:"4"}})],1)],1),e._v(" "),a("el-form-item",{attrs:{label:"其他信息",prop:"other"}},[a("el-input",{attrs:{type:"textarea"},model:{value:e.ruleForm.other,callback:function(t){e.ruleForm.other=t},expression:"ruleForm.other"}})],1),e._v(" "),a("el-form-item",[a("el-button",{attrs:{type:"primary"},on:{click:function(t){e.submitForm("ruleForm")}}},[e._v("立即创建")]),e._v(" "),a("el-button",{on:{click:function(t){e.resetForm("ruleForm")}}},[e._v("重置")])],1)],1)],1)},n=[],l={render:r,staticRenderFns:n};t.a=l},e0XP:function(e,t){},"f/Gs":function(e,t,a){"use strict";var r=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",[r("header",[r("img",{staticClass:"header-logo",attrs:{src:a("n0hG"),alt:"logo"}}),e._v(" "),r("ul",{staticClass:"header-operations"},[r("li",[r("span",{staticClass:"header-lang is-active"},[e._v(e._s(e.user.name))]),e._v(" "),r("span",{staticClass:"header-opacity"},[e._v("("+e._s(e.user.identity)+")")])]),e._v(" "),r("li")])])])},n=[],l={render:r,staticRenderFns:n};t.a=l},mi2t:function(e,t){},n0hG:function(e,t){e.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAALQAAABBCAMAAABPTH6qAAAAqFBMVEUAAAD///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8j1z1tAAAAN3RSTlMA8MBQkNH7X0AQCjfqBuYVAvXXLw7csBv34ZZ6RR/HnXVLzKOLbyy7rYZq8sS4f2RZO7SoKVMlWCtwQAAABN9JREFUaN7Vmtl22jAQQIcANhgvYMySsARoCAkQskf//2dtIYxnkal6Tvug+xYd21xtoxk78D/oB4bwAl4wp84b8IJn6jz+Ah/IalT6AF5wS50HEfhAy1Cm4ANJTp3fwQsW1Hk3Ah+Y9aj0GrzgjTqvuuADdUPpgA9E19T5BrzgjjrHbfCBex8TpT11vgIvYIlS8AE+kMVU+hG8wMdE6dVQluADSZM6P4AX2BOlok2I4F9hkJZdIJgCwJWppAm/GbFEaQEnZmNbEMywJe7CiSE2FVCSq3urpZcBda6Di/SQNSXoYpN+0dkJHv85lHQMYXZZ+n7HzzUX6SVreoUTU8Oo6TAzgSNtbLiFkhtDmFyUzljSswUX6YLdc8uKGD1aKRl8WaI9A5LELIZekk7mvH9O0nNrorQwgvppJuniO23ONTb0ZZqLdC5I/+DHmoO0fHwIJ0Y7IzigIbLkC6bXBWRoGDfV0hNDaICTdLdmCHsAIN3X0ftN/0TTEiSyQOyIpEr6U6xNLf1WF7S4XXDPT0i1LhMWHPNjE0bGO0C2RvBZId2nD3zv2qQbwMA1Kn+22zSaAjcdMsInCLGVEQzt0qOUXpOAmzSLHNeRGCgVCx/xLwwXIenCmQ+c2vM0BJlNOqKdu4rATXohAsSJdmyT3uoRfKdVWgoI9i3EPbC2ST+YknkEbtLtQE6hPhjwkhu8np3kG51ndXH+MpyzlUWaRoi8gArpTci5YnYz+lpdP3lebvWAROZY5iwA0/KWET7nnkprYiVdxY+eLUZfMefnMgxjSN4M8PwakTWv4uWEBMQ7Jq1puEo3XscyJQR44UGwjA5fuG0XNxial+R8/CbqkYk44JLvXpYO+q7SPCnc/b4vSvkV3R2mX18ocz5Gx9GEJ4xA48k1y/amTFqzd5aG0BDSmYhqcZvc/rjFOFGc+7p81yf1hjWluBa5tCZ0lJZRL29/BTIQYy/ehuXP789PyOVPklW+5KlJ8QfpuHCWhgbbd0/qzWkd56GGfrhUB9hHfFUyQcuIZ+uhltY6WrrZ4NRVcjTQGWVmJCNcqrpIAMh53I/GIqES9231XtSHiyYho5takq6BzmcxOUQ2us5at49ssGGmpXsduhD27tIQrdCHDUIGR3Cv0Yj7IBof1XGqmSjpYMqTsNBdGrLBd7/l9wuV+GPsehaNS15nIbrqUoX3E92L7tIwS/VA54mYcIzNxzuEUKbrLE1HSIfq+Y1qac19DRc0G1BM89XqHYhBtNdZlqpLF7FPbC+6S0Prl9l1xcuxuaEsbGv3vaLOklWXKAL0VO6VdNyUtNCsHuRsW88AaRhK37YO1rJ8sPMppXGokVBKa+qArHe6+AadQsVwogjEatV1lmZol+6Ic9Fd+tZaealN98P2vWOciDrL7Oold9gaZFJaD3XjL6Q7uh1JbaH0QGslrLPklhBn6tou3WE5qpO03m1PwBiKCrwqUnVTlmpZnr1S0nqo987S4aUPRRP5nhXESf6iXlpmFfd/KGk9z6GjdBHzg5rTUmMqTvIv+V5qBZQ+fXQNeaWTWSvJI4C3WjVL2+fatABOVEOmgITYOPi+LMWWAzDyUgj+GR8Bnx4v2BgWCrzgUxx5PuDlP00cROrvA/zb2xa8gFVOTT/+dWkq3kL7gP//QNgbwf/mJ4R8gtd4SgqRAAAAAElFTkSuQmCC"},pSPx:function(e,t,a){"use strict";var r=a("pFYg"),n=a.n(r),l=a("woOf"),i=a.n(l);t.a={data:function(){return{formInline:{user:{name:"",date:"",address:[],place:""}},tableData:[],options:[],places:[],dialogFormVisible:!1,editLoading:!1,form:{name:"",address:"",date:""},currentPage:4,table_index:999}},created:function(){var e=this;this.$http.get("/api/getTable").then(function(t){t=t.data,"000"===t.code&&(e.tableData=t.datas)}),this.$http.get("/api/getOptions").then(function(t){t=t.data,"000"===t.code&&(e.options=t.datas,e.places=t.places)})},methods:{onSubmit:function(){this.$message("模拟数据，这个方法并不管用哦~")},handleDelete:function(e,t){this.tableData.splice(e,1),this.$message({message:"操作成功！",type:"success"})},handleEdit:function(e,t){this.dialogFormVisible=!0,this.form=i()({},t),this.table_index=e},handleSave:function(){var e=this;this.$confirm("确认提交吗？","提示",{confirmButtonText:"确定",cancelButtonText:"取消",cancelButtonClass:"cancel"}).then(function(){e.editLoading=!0;var t=e.form.date;"object"===(void 0===t?"undefined":n()(t))&&(t=[t.getFullYear(),t.getMonth()+1,t.getDate()].join("-"),e.form.date=t),e.tableData.splice(e.table_index,1,e.form),e.$message({message:"操作成功！",type:"success"}),e.editLoading=!1,e.dialogFormVisible=!1}).catch(function(){})},download:function(){for(var e=document.getElementById("download"),t="姓名,出生日期,地址\n",a=0;a<this.tableData.length;a++){var r=this.tableData[a];t+=r.name+","+r.date+","+r.address,t+="\n"}t=encodeURIComponent(t),e.href="data:text/csv;charset=utf-8,\ufeff"+t,e.download="download.csv"},handleSizeChange:function(e){console.log("每页 "+e+" 条")},handleCurrentChange:function(e){this.currentPage=e,console.log("当前页: "+e)}}}},q2lo:function(e,t,a){"use strict";function r(e){a("4poY")}var n=a("qTnG"),l=a("q8rw"),i=a("VU/8"),o=r,s=i(n.a,l.a,o,null,null);t.a=s.exports},q8rw:function(e,t,a){"use strict";var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"chart-container"},[a("el-row",[a("el-col",{staticClass:"chart chart_left",attrs:{span:12}},[a("div",{staticStyle:{height:"650px"},attrs:{id:"userChart"}},[e._v("图表加载失败")])]),e._v(" "),a("el-col",{staticClass:"chart",attrs:{span:12}},[a("div",{staticStyle:{height:"650px"},attrs:{id:"userDoChart"}},[e._v("图表加载失败")])])],1)],1)},n=[],l={render:r,staticRenderFns:n};t.a=l},q8zI:function(e,t){},qTnG:function(e,t,a){"use strict";var r=a("pFYg"),n=a.n(r),l=a("XLwt"),i=a.n(l);t.a={data:function(){return{}},methods:{getUserChartInit:function(){var e=i.a.init(document.getElementById("userChart"));e.showLoading();var t={title:{text:"用户来源"},tooltip:{trigger:"axis",axisPointer:{type:"cross",label:{backgroundColor:"#6a7985"}}},legend:{data:["华东","华北","华南","西部","其他"]},toolbox:{feature:{saveAsImage:{}}},grid:{left:"3%",right:"4%",bottom:"3%",containLabel:!0},xAxis:[{type:"category",boundaryGap:!1,data:["周一","周二","周三","周四","周五","周六","周日"]}],yAxis:[{type:"value"}],series:[{name:"华东",type:"line",stack:"总量",areaStyle:{normal:{}},data:[120,132,101,134,90,230,210]},{name:"华北",type:"line",stack:"总量",areaStyle:{normal:{}},data:[220,182,191,234,290,330,310]},{name:"华南",type:"line",stack:"总量",areaStyle:{normal:{}},data:[150,232,201,154,190,330,410]},{name:"西部",type:"line",stack:"总量",areaStyle:{normal:{}},data:[320,332,301,334,390,330,320]},{name:"其他",type:"line",stack:"总量",label:{normal:{show:!0,position:"top"}},areaStyle:{normal:{}},data:[820,932,901,934,1290,1330,1320]}]};e.setOption(t),e.hideLoading()},getUserDoChartInit:function(){function e(e,t){return i.a.util.map(e,function(e,a){return{id:a+"pie",type:"pie",center:t.convertToPixel("calendar",e),label:{normal:{formatter:"{c}",position:"inside"}},radius:s,data:[{name:"工作",value:Math.round(24*Math.random())},{name:"娱乐",value:Math.round(24*Math.random())},{name:"睡觉",value:Math.round(24*Math.random())}]}})}function t(e,t){return i.a.util.map(e,function(e,a){return{id:a+"pie",center:t.convertToPixel("calendar",e)}})}var a=i.a.init(document.getElementById("userDoChart"));a.showLoading();var r={},l=null,o=[80,80],s=30,c=function(){for(var e=+i.a.number.parseDate("2017-02-01"),t=+i.a.number.parseDate("2017-03-01"),a=[],r=e;r<t;r+=864e5)a.push([i.a.format.formatTime("yyyy-MM-dd",r),Math.floor(1e4*Math.random())]);return a}();if(l={tooltip:{},title:{text:"每日用户行为"},legend:{data:["工作","娱乐","睡觉"],bottom:20},toolbox:{feature:{saveAsImage:{}}},calendar:{top:"middle",left:"center",orient:"vertical",cellSize:o,yearLabel:{show:!1,textStyle:{fontSize:30}},dayLabel:{margin:20,firstDay:1,nameMap:["星期日","星期一","星期二","星期三","星期四","星期五","星期六"]},monthLabel:{show:!1},range:["2017-02"]},series:[{id:"label",type:"scatter",coordinateSystem:"calendar",symbolSize:1,label:{normal:{show:!0,formatter:function(e){return i.a.format.formatTime("dd",e.value[0])},offset:[-o[0]/2+10,-o[1]/2+10],textStyle:{color:"#000",fontSize:14}}},data:c}]},!r.inNode){var d;setTimeout(function(){d=!0,a.setOption({series:e(c,a)})},10),r.onresize=function(){d&&a.setOption({series:t(c,a)})}}l&&"object"===(void 0===l?"undefined":n()(l))&&a.setOption(l,!0),a.hideLoading()}},mounted:function(){this.$nextTick(function(){this.getUserChartInit(),this.getUserDoChartInit()})}}},sd3e:function(e,t,a){"use strict";t.a={props:{user:{}},data:function(){return{}}}},xJD8:function(e,t,a){"use strict";var r=a("OK80");t.a={data:function(){return{user:window.$hy.user,items:window.$hy.menu_items}},beforeCreate:function(){"/"===this.$route.path&&this.$router.push({path:"/index"})},components:{"v-header":r.a},methods:{selectItem:function(e,t){var a=window.$hy.menu_items,r={};if(void 0!=a){for(var n in a){var l=a[n];if(void 0!=l.children)for(var i in l.children){var o=l.children[i];o.id==e&&(r=o)}}a=r.children}document.getElementById("ifr100").src=r.url}}}}},["NHnr"]);