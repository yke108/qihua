import Vue from 'vue/dist/vue.common.js'
import VueRouter from 'vue-router'
import axios from 'axios'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-default/index.css'
import jQuery from 'jquery'
import App from './App'
import Index from './views/index/index'
import GoodsList from './views/product/list'
import UnshelveList from './views/product/unshelveList'
import GoodsEdit from './views/product/edit'
import InquiryDrafting from './views/inquiry/drafting'
import InquiryConfirm from './views/inquiry/confirm'
import InquiryShip from './views/inquiry/ship'
import InquiryCompleted from './views/inquiry/completed'
import InquiryClosed from './views/inquiry/closed'
import InquiryDetail from './views/inquiry/detail'
import InquiryOrder from './views/inquiry/order'
import SystemList from './views/System/userList'
import MemberList from './views/Member/list'

import OrderStart from './views/order/start'
import OrderConfirm from './views/order/confirm'
import OrderPaid from './views/order/paid'
import OrderShip from './views/order/ship'
import OrderCompleted from './views/order/completed'
import OrderClosed from './views/order/closed'
import OrderDetail from './views/order/detail'
import EditOrder from './views/order/edit'

import Login from './views/Login.vue'
import 'font-awesome/css/font-awesome.min.css'
import Mock from './mock/mock'

import SupplyList from './views/supply/list'
import DemandList from './views/demand/list'

import NewsList from './views/content/newsList'
import PartnerList from './views/content/partnerList'
// import About from './views/content/about'
// import Law from './views/content/law'
// import Protocol from './views/content/protocol'
import Contact from './views/content/contact'

import ProdBrand from './views/data/brand'
import ProdCategory from './views/data/category'
import ProdArea from './views/data/area'
import ProdZhibiao from './views/data/zhibiao'
import ProdModel from './views/data/model'
import ProdFactory from './views/data/factory'

import '../static/UE/ueditor.config.js'
import '../static/UE/ueditor.all.min.js'
import '../static/UE/lang/zh-cn/zh-cn.js'
import '../static/UE/ueditor.parse.min.js'

Mock.mockData();
window.$ = jQuery;
Vue.use(VueRouter);// 安装路由功能
/* eslint-disable no-new */
Vue.use(VueRouter);
Vue.prototype.$http = axios;
Vue.use(ElementUI);
window.$Codes = {
  Ok: "000",
};
window.$Options = {};

var obj = {
  base_url: 'http://kwen.t.weixinren.cn',
};
Vue.prototype.$hyobj = obj;

// 后端对比cookies判断是否登录，凡接口response的header带有x-auth-token的即未登录，跳转首页。
// Vue.http.interceptors.push((request, next) => {
//   request.credentials = true;
//   next((response) => {
//     let messageHeader;
//     /* global IS_PRODUCTION:true */
//     if (IS_PRODUCTION) {
//       messageHeader = "X-Auth-Token";
//     } else {
//       messageHeader = "x-auth-token";
//     }
//     if (messageHeader in response.headers.map) {
//       router.push({path: '/login'});
//     }
//     return response
//   });
// });

let routes = [
  {
    path: '/',
    component: App,
    hidden: true
  },
  {
    path: '/login',
    component: Login,
    name: '',
    hidden: true
  },
  {
    path: '/index',
    component: App,
    name: 'Analysis',
    class: 'fa-area-chart',
    children: [
      {path: '/index/index', component: Index, name: 'Survey', class: 'fa-line-chart'},
    ],
  },
  {
    path: '/product',
    component: App,
    name: 'Product',
    class: 'fa-product-hunt',
    children: [
      {path: '/product/list', component: GoodsList, name: 'Product List', class: 'fa-table'},
      {path: '/product/unshelveList', component: UnshelveList, name: 'Unshelve List', class: 'fa-table'},
      {path: '/product/add/:id', component: GoodsEdit, name: 'Add product', class: 'fa-table', hidden: true}
    ]
  },
  {
    path: '/inquiry',
    component: App,
    name: 'Inquiry',
    class: 'fa-envelope-open',
    children: [
      {path: '/Inquiry/drafting', component: InquiryDrafting, name: 'Drafting', class: 'fa-table'},
      {path: '/Inquiry/confirm', component: InquiryConfirm, name: 'Confirm', class: 'fa-table'},
      {path: '/Inquiry/ship', component: InquiryShip, name: 'Ship', class: 'fa-table'},
      {path: '/Inquiry/completed', component: InquiryCompleted, name: 'Completed', class: 'fa-table'},
      {path: '/Inquiry/closed', component: InquiryClosed, name: 'Closed', class: 'fa-table'},
      {path: '/Inquiry/detail/:id', component: InquiryDetail, name: 'InquiryDetail', class: 'fa-table', hidden: true},
      {path: '/Inquiry/order/:id', component: InquiryOrder, name: 'InquiryOrder', class: 'fa-table', hidden: true},
    ]
  },
  {
    path: '/order',
    component: App,
    name: 'Order',
    class: 'fa-reorder',
    children: [
      {path: '/order/startOrder', component: OrderStart, name: 'Start', class: 'fa-table'},
      {path: '/order/confirmOrder', component: OrderConfirm, name: 'Confirm', class: 'fa-table'},
      {path: '/order/paid', component: OrderPaid, name: 'Paid', class: 'fa-table'},
      {path: '/order/shipped', component: OrderShip, name: 'Shipped', class: 'fa-table'},
      {path: '/order/complete', component: OrderCompleted, name: 'Complete', class: 'fa-table'},
      {path: '/order/closed', component: OrderClosed, name: 'Closed', class: 'fa-table'},
      {path: '/order/detail/:id', component: OrderDetail, name: 'OrderDetail', class: 'fa-table', hidden: true},
      {path: '/order/edit/:id', component: EditOrder, name: 'EditOrder', class: 'fa-table', hidden: true},
    ]
  },
  {
    path: '/supply',
    component: App,
    name: 'Supply',
    class: 'fa-handshake-o',
    children: [
      {path: '/supply/index', component: SupplyList, name: 'Supply List', class: 'fa-table'},
      {path: '/demand/index', component: DemandList, name: 'BuyOffer List', class: 'fa-table'},
    ]
  },
  {
    path: '/content',
    component: App,
    name: 'Content',
    class: 'fa-book',
    children: [
      {path: '/content/newsList', component: NewsList, name: 'Media Report', class: 'fa-table'},
      {path: '/content/partnerList', component: PartnerList, name: 'Our Partners', class: 'fa-table'},
      // {path: '/content/about', component: About, name: 'About Us', class: 'fa-table'},
      // {path: '/content/law', component: Law, name: 'Legal statement', class: 'fa-table'},
      // {path: '/content/protocol', component: Protocol, name: 'Terms of User', class: 'fa-table'},
      {path: '/content/contact', component: Contact, name: 'Contact Us', class: 'fa-table'},
    ]
  },
  {
    path: '/Member',
    component: App,
    name: 'Member',
    class: 'fa-user-circle-o',
    children: [
      {path: '/Member/index', component: MemberList, name: 'User List', class: 'fa-table'},
    ]
  },
  {
    path: '/System',
    component: App,
    name: 'System',
    class: 'fa-cog',
    children: [
      {path: '/System/index', component: SystemList, name: 'Administrator List', class: 'fa-table'},
    ]
  },
  {
    path: '/data',
    component: App,
    name: 'Data',
    class: 'fa-database',
    children: [
      {path: '/data/area', component: ProdArea, name: 'Area', class: 'fa-table'}, // 地区
      {path: '/data/table', component: ProdFactory, name: 'Producer', class: 'fa-table'}, // 生产商
      {path: '/data/brand', component: ProdBrand, name: 'Brand', class: 'fa-table'}, // 品牌
      {path: '/data/category', component: ProdCategory, name: 'Category', class: 'fa-table'}, // 分类
      {path: '/data/modelList', component: ProdModel, name: 'Business Type', class: 'fa-table'}, // 经营模式
      {path: '/data/zhibiao', component: ProdZhibiao, name: 'Indicator', class: 'fa-table'}, // 关键指标
    ]
  },
];
let router = new VueRouter({
  'linkActiveClass': 'active',
  routes
});

router.beforeEach((to, from, next) => {
  if (to.path === '/login') {
    sessionStorage.removeItem('user');
  }
  let user = JSON.parse(sessionStorage.getItem('user'));
  if (!user && to.path !== '/login') {
    next({ path: '/login' });
  } else {
    next();
  }
});

new Vue({
  el: '#app',
  router,
  components: { App }
});
