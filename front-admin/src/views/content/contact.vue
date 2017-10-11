<template>
  <section class="form-section">
    <el-form :model="fsFrom" ref="fs" label-width="100px" class="demo-ruleForm">
      <h1 style="margin: 10px;font-weight: bold;">佛山总公司</h1>
      <el-form-item label="公司名称">
        <el-input required v-model="fs.companyName"></el-input>
      </el-form-item>
      <el-form-item label="公司地址">
        <el-input v-model="fs.address"></el-input>
      </el-form-item>
      <el-form-item label="服务热线">
        <el-input v-model="fs.serviceTel"></el-input>
      </el-form-item>
      <el-form-item label="固话">
        <el-input v-model="fs.tel"></el-input>
      </el-form-item>
      <el-form-item label="传真">
        <el-input v-model="fs.fax"></el-input>
      </el-form-item>
      <el-form-item label="邮箱">
        <el-input v-model="fs.email"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm('fs')">保存</el-button>
      </el-form-item>
    </el-form>

    <el-form :model="gzFrom" ref="gz" label-width="100px" class="demo-ruleForm" style="border-top: 1px solid #ddd;">
      <h1 style="margin: 10px;font-weight: bold;">广州分公司</h1>
      <el-form-item label="公司名称">
        <el-input required v-model="gz.companyName"></el-input>
      </el-form-item>
      <el-form-item label="公司地址">
        <el-input v-model="gz.address"></el-input>
      </el-form-item>
      <el-form-item label="服务热线">
        <el-input v-model="gz.serviceTel"></el-input>
      </el-form-item>
      <el-form-item label="固话">
        <el-input v-model="gz.tel"></el-input>
      </el-form-item>
      <el-form-item label="传真">
        <el-input v-model="gz.fax"></el-input>
      </el-form-item>
      <el-form-item label="邮箱">
        <el-input v-model="gz.email"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm('gz')">保存</el-button>
      </el-form-item>
    </el-form>

    <div :model="cpFrom" label-width="100px" class="demo-ruleForm" style="border-top: 1px solid #ddd;">
      <el-form :model="gzFrom" label-width="100px" class="demo-ruleForm">
        <h2 style="margin: 10px;">销售合作</h2>
        <el-form-item label="联系人">
          <el-input required v-model="cp.p.name"></el-input>
        </el-form-item>
        <el-form-item label="固话">
          <el-input v-model="cp.p.phone"></el-input>
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="cp.p.mail"></el-input>
        </el-form-item>
        <el-form-item label="QQ">
          <el-input v-model="cp.p.qq"></el-input>
        </el-form-item>
      </el-form>
      <el-form :model="gzFrom" label-width="100px" class="demo-ruleForm">
        <h2 style="margin: 10px;">采购合作</h2>
        <el-form-item label="联系人">
          <el-input required v-model="cp.b.name"></el-input>
        </el-form-item>
        <el-form-item label="固话">
          <el-input v-model="cp.b.phone"></el-input>
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="cp.b.mail"></el-input>
        </el-form-item>
        <el-form-item label="QQ">
          <el-input v-model="cp.b.qq"></el-input>
        </el-form-item>
      </el-form>
      <el-form :model="gzFrom" label-width="100px" class="demo-ruleForm">
        <h2 style="margin: 10px;">品牌推广</h2>
        <el-form-item label="联系人">
          <el-input required v-model="cp.e.name"></el-input>
        </el-form-item>
        <el-form-item label="固话">
          <el-input v-model="cp.e.phone"></el-input>
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="cp.e.mail"></el-input>
        </el-form-item>
        <el-form-item label="QQ">
          <el-input v-model="cp.e.qq"></el-input>
        </el-form-item>
      </el-form>
      <el-form :model="gzFrom" label-width="100px" class="demo-ruleForm">
        <h2 style="margin: 10px;">投资洽谈</h2>
        <el-form-item label="联系人">
          <el-input required v-model="cp.i.name"></el-input>
        </el-form-item>
        <el-form-item label="固话">
          <el-input v-model="cp.i.phone"></el-input>
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="cp.i.mail"></el-input>
        </el-form-item>
        <el-form-item label="QQ">
          <el-input v-model="cp.i.qq"></el-input>
        </el-form-item>
      </el-form>
      <el-form :model="gzFrom" label-width="100px" class="demo-ruleForm">
        <h2 style="margin: 10px;">客户服务</h2>
        <el-form-item label="联系人">
          <el-input required v-model="cp.k.name"></el-input>
        </el-form-item>
        <el-form-item label="固话">
          <el-input v-model="cp.k.phone"></el-input>
        </el-form-item>
        <el-form-item label="邮箱">
          <el-input v-model="cp.k.mail"></el-input>
        </el-form-item>
        <el-form-item label="QQ">
          <el-input v-model="cp.k.qq"></el-input>
        </el-form-item>
      </el-form>
      <el-form :model="gzFrom" ref="gz" label-width="100px" class="demo-ruleForm">
        <el-form-item>
          <el-button type="primary" @click="submitForm('cp')">保存</el-button>
        </el-form-item>
      </el-form>
    </div>
  </section>
</template>
<script>
  import { aboutUs, editContact, editCooperate } from '../../api/api';
  export default {
    data() {
      return {
        fs: {
          companyName: '',
          address: '',
          serviceTel: '',
          tel: '',
          fax: '',
          email: '',
        },
        gz: {
          companyName: '',
          address: '',
          serviceTel: '',
          tel: '',
          fax: '',
          email: '',
        },
        cp: {}
      };
    },
    created () {
      let _this = this;
      let pdt = {};
      aboutUs(pdt).then(response => {
        if (response.code === $Codes.Ok) {
          _this.fs = response.data.fs;
          _this.gz = response.data.gz;
          _this.cp = {
            p: response.data.p,
            b: response.data.b,
            e: response.data.e,
            i: response.data.i,
            k: response.data.k,
          }
        }
      });
    },
    methods: {
      submitForm(act) {
        if (act === 'fs') {
          let pdt = this.fs;
          pdt.type = 1;
          if (!pdt.companyName.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '公司名不能为空', type: 'error'});
            return false;
          }
          if (!pdt.address.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '公司地址不能为空', type: 'error'});
            return false;
          }
          if (!pdt.serviceTel.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '服务热线不能为空', type: 'error'});
            return false;
          }
          if (!pdt.tel.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '固话不能为空', type: 'error'});
            return false;
          }
          if (!pdt.fax.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '传真不能为空', type: 'error'});
            return false;
          }
          if (!pdt.email.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '邮箱不能为空', type: 'error'});
            return false;
          }
          let _this = this;
          editContact(pdt).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
              this.loadData();
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
        } else if (act === 'gz') {
          let pdt = this.gz;
          pdt.type = 2;
          if (!pdt.companyName.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '公司名不能为空', type: 'error'});
            return false;
          }
          if (!pdt.address.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '公司地址不能为空', type: 'error'});
            return false;
          }
          if (!pdt.serviceTel.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '服务热线不能为空', type: 'error'});
            return false;
          }
          if (!pdt.tel.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '固话不能为空', type: 'error'});
            return false;
          }
          if (!pdt.fax.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '传真不能为空', type: 'error'});
            return false;
          }
          if (!pdt.email.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '邮箱不能为空', type: 'error'});
            return false;
          }
          let _this = this;
          editContact(pdt).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
              this.loadData();
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
        } else if (act === 'cp') {
          let pdt = {};
          pdt.provider = this.cp.p.name;
          pdt.providephone = this.cp.p.phone;
          pdt.providemail = this.cp.p.mail;
          pdt.provideqq = this.cp.p.qq;
          if (!pdt.provider.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '商务合作-联系人不能为空', type: 'error'});
            return false;
          }
          if (!pdt.providephone.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '商务合作-固话不能为空', type: 'error'});
            return false;
          }
          if (!pdt.providemail.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '商务合作-邮箱不能为空', type: 'error'});
            return false;
          }
          if (!pdt.provideqq.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '商务合作-QQ不能为空', type: 'error'});
            return false;
          }
          pdt.buyer = this.cp.b.name;
          pdt.buyphone = this.cp.b.phone;
          pdt.buymail = this.cp.b.mail;
          pdt.buyqq = this.cp.b.qq;
          if (!pdt.buyer.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '采购合作-联系人不能为空', type: 'error'});
            return false;
          }
          if (!pdt.buyphone.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '采购合作-固话不能为空', type: 'error'});
            return false;
          }
          if (!pdt.buymail.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '采购合作-邮箱不能为空', type: 'error'});
            return false;
          }
          if (!pdt.buyqq.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '采购合作-QQ不能为空', type: 'error'});
            return false;
          }
          pdt.extender = this.cp.e.name;
          pdt.extendphone = this.cp.e.phone;
          pdt.extendmail = this.cp.e.mail;
          pdt.extendqq = this.cp.e.qq;
          if (!pdt.extender.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '品牌推广-联系人不能为空', type: 'error'});
            return false;
          }
          if (!pdt.extendphone.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '品牌推广-固话不能为空', type: 'error'});
            return false;
          }
          if (!pdt.extendmail.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '品牌推广-邮箱不能为空', type: 'error'});
            return false;
          }
          if (!pdt.extendqq.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '品牌推广-QQ不能为空', type: 'error'});
            return false;
          }
          pdt.invest = this.cp.i.name;
          pdt.investphone = this.cp.i.phone;
          pdt.investmail = this.cp.i.mail;
          pdt.investqq = this.cp.i.qq;
          if (!pdt.invest.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '投资洽谈-联系人不能为空', type: 'error'});
            return false;
          }
          if (!pdt.investphone.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '投资洽谈-固话不能为空', type: 'error'});
            return false;
          }
          if (!pdt.investmail.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '投资洽谈-邮箱不能为空', type: 'error'});
            return false;
          }
          if (!pdt.investqq.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '投资洽谈-QQ不能为空', type: 'error'});
            return false;
          }
          pdt.kefu = this.cp.k.name;
          pdt.kefuphone = this.cp.k.phone;
          pdt.kefumail = this.cp.k.mail;
          pdt.kefuqq = this.cp.k.qq;
          if (!pdt.kefu.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '客户服务-联系人不能为空', type: 'error'});
            return false;
          }
          if (!pdt.kefuphone.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '客户服务-固话不能为空', type: 'error'});
            return false;
          }
          if (!pdt.kefumail.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '客户服务-邮箱不能为空', type: 'error'});
            return false;
          }
          if (!pdt.kefuqq.replace(/(^\s*)|(\s*$)/g, "")) {
            this.$message({message: '客户服务-QQ不能为空', type: 'error'});
            return false;
          }
          let _this = this;
          editCooperate(pdt).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
              this.loadData();
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
        }
      }
    }
  }
</script>
<style>
  .form-section {
    padding: 10px;
    width: 500px;
  }
  .el-form {
    padding: 10px;
    margin: 10px;
  }
</style>
