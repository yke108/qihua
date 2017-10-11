<template>
  <section class="form-section">
    <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
      <el-form-item label="Title" prop="title">
        <el-input v-model="title"></el-input>
      </el-form-item>
      <el-form-item label="content">
        <UE :defaultMsg=content :config=editorConfig id='protocol1' ref="ue"></UE>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm">保存</el-button>
      </el-form-item>
    </el-form>
  </section>
</template>
<script>
  import { getProtocol, editProtocol } from '../../api/api';
  import UE from '../../components/ue/ue.vue';
  export default {
    components: {UE},
    data() {
      return {
        title: '',
        content: '',
        editorConfig: {
          initialFrameWidth: 1000,
          initialFrameHeight: 600,
        },
      };
    },
    created () {
      let _this = this;
      let pdt = {};
      getProtocol(pdt).then(response => {
        if (response.code === $Codes.Ok) {
          _this.title = response.data.title;
          _this.content = response.data.content;
          this.$refs.ue.setUEContent(_this.content);
        }
      });
    },
    methods: {
      submitForm() {
        let pdt = {
          title: this.title,
          content: this.$refs.ue.getUEContent(),
        };
        let _this = this;
        editProtocol(pdt).then(response => {
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
</script>
<style>
  .form-section {
    padding: 10px;
    width: 500px;
  }
</style>
