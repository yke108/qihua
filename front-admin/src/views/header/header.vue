<template>
  <div>
    <header>
      <img src="./logo.png" alt="Keywa" class="header-logo">
      <ul class="header-operations">
        <li>
          <span class="header-lang is-active">{{user.realname}}</span>
          <span class="header-opacity">({{user.department}} / {{user.identity}})</span>
          <router
        </li>
        <li>
          <span class="header-lang is-active" @click="logout">退出登录</span>
        </li>
      </ul>
    </header>
  </div>
</template>
<script>
  import { requestLogout } from '../../api/api';
  export default {
    props: {
      user: {
      }
    },
    data () {
      return {
      };
    },
    methods: {
      logout () {
        let _this = this;
        this.$confirm('确认退出吗?', '提示', {type: 'warning'}).then(() => {
          var logoutParams = {};
          requestLogout(logoutParams).then(data => {
            if (data.code === $Codes.Ok) {
              sessionStorage.removeItem('user');
              _this.$router.push('/login');
            } else {
              _this.$message('操作失败', 'error');
            }
          });
        }).catch(() => {

        });
      }
    },
  };
</script>
<style>
  header {
    background-color: rgb(32, 160, 255);
    height: 80px;
    position: absolute;
    width: 100%;
    top: 0;
    left: 0;
    padding: 0 20px;
    z-index: 999;
    box-sizing: border-box;
    position: fixed;
  }
  .header-logo {
    display: inline-block;
    vertical-align: middle;
  }
  .header-operations {
    display: inline-block;
    float: right;
    padding-right: 30px;
    height: 100%;
  }
  .header-operations li {
    color: #fff;
    display: inline-block;
    vertical-align: middle;
    padding: 0 10px;
    margin: 0 10px;
    line-height: 80px;
    cursor: pointer;
  }
  .header-operations li:first-child{
    cursor: default
  }
  .header-opacity{
    opacity: .7;
  }
  .header-operations:after, header:after {
    display: inline-block;
    content: "";
    height: 100%;
    vertical-align: middle;
  }
</style>
