<template>
  <el-row class="container" style="height: 100%">
    <v-header :user="user"></v-header>
    <el-col :span="24" class="main">
      <el-row>
        <el-menu :default-active="$route.path" class="mar-l el-menu-vertical-demo hy-menu el-col el-col-4" light router>
          <template v-for="(item,index) in $router.options.routes" v-if="!item.hidden">
          	<el-submenu :index="item.path">
          	  <template slot="title"><i class="fa" :class="item.class"></i>{{item.name}}</template>
          	  <template v-for="(item_sub, index_sub) in item.children">
              	<el-menu-item v-if="!item_sub.hidden" :index="item_sub.path" ><i class="fa" :class="item_sub.class"></i>{{item_sub.name}}</el-menu-item>
              </template>
            </el-submenu>
          </template>
        </el-menu>
        <section class="contentCon">
          <el-col :span="20" :offset="4" class="content-wrapper">
            <transition>
              <router-view></router-view>
            </transition>
          </el-col>
        </section>
      </el-row>
    </el-col>
  </el-row>
</template>
<script>
  import header from './views/header/header.vue'
  export default {
    data () {
      return {
        user: {}
      };
    },
    created () {
      this.user = JSON.parse(sessionStorage.getItem('user'));
    },
    beforeCreate () {},
    components: {
      'v-header': header
    }
  };
</script>
<style>


  /* fa图标右侧需要流出空白 elementUI图标已自带样式 */
  i.fa {
    vertical-align: baseline;
    margin-right: 10px;
  }
  body {
    font-family: Helvetica Neue, Helvetica, PingFang SC, Hiragino Sans GB, Microsoft YaHei, SimSun, sans-serif;
    /*background-color: #F2F2F2;*/
    background-color: #FFF;
  }
  .hy-menu {
    top: 80px;
    bottom: 0;
    background-color: #E6E6E6;
    position: fixed;
    overflow-y: auto;
  }

  .container {
    padding-top: 80px;
    height: 100%;
  }

  .container .main {
    padding: 0;
  }

  .container ul li.el-menu-item {
    font-size: 100%;
  }

  .container .mar-l {
    padding: 0;
  }

  .container .content-wrapper {
    padding: 20px;
  }

</style>

