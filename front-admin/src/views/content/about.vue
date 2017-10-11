<template>
  <section class="form-section">
    <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
      <el-form-item label="About Us">
        <UE :defaultMsg=about :config=editorConfig id='about1' ref="ue"></UE>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm">保存</el-button>
      </el-form-item>
    </el-form>
  </section>
</template>
<script>
  import { aboutUs, editAboutUs } from '../../api/api';
  import UE from '../../components/ue/ue.vue';
  export default {
    components: {UE},
    data() {
      return {
        about: '',
        editorConfig: {
          initialFrameWidth: 1000,
          initialFrameHeight: 600,
        },
      };
    },
    created () {
      let _this = this;
      let pdt = {};
      aboutUs(pdt).then(response => {
        if (response.code === $Codes.Ok) {
          _this.about = response.data.about;
          this.$refs.ue.setUEContent(_this.about);
        }
      });
    },
    methods: {
      submitForm() {
        let pdt = {
          type: 1,
          content: this.$refs.ue.getUEContent(),
        };
        let _this = this;
        editAboutUs(pdt).then(response => {
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
