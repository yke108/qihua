<template>
  <section class="form-section">
    <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px" class="demo-ruleForm">
      <el-form-item label="Title" prop="title">
        <el-input v-model="ruleForm.title"></el-input>
      </el-form-item>
      <el-form-item label="Result" prop="way">
        <el-checkbox-group v-model="ruleForm.way">
          <el-checkbox label="审核不通过" value="0"></el-checkbox>
          <el-checkbox label="审核通过/恢复通过" value="1"></el-checkbox>
          <el-checkbox label="撤销通过" value="4"></el-checkbox>
        </el-checkbox-group>
      </el-form-item>
      <el-form-item label="Reason" prop="reason">
        <el-input type="textarea" v-model="ruleForm.reason"></el-input>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="submitForm('ruleForm')">Send</el-button>
        <el-button @click="resetForm('ruleForm')">Reset</el-button>
      </el-form-item>
    </el-form>
  </section>
</template>
<script>
  export default {
    data() {
      return {
        forbidden: true,
        redStar: false,
        ruleForm: {
          title: '',
          type: '',
          way: [],
          date: '',
          period: '',
          starDate: '',
          endDate: '',
          delivery: false,
          other: '',
        },
        rules: {
          period: [],
          starDate: [],
          endDate: [],
          reason: [
            {required: true, message: '请填写其他信息', trigger: 'blur'}
          ]
        }
      };
    },
    methods: {
      submitForm(formName) {
        this.$refs[formName].validate((valid) => {
          if (valid) {
            let para = Object.assign({}, this.ruleForm);
            console.log(para);
            this.$message({
              message: "提交成功，请在控制台查看json!！",
              type: 'success'
            });
          } else {
            return false;
          }
        });
      },
      resetForm(formName) {
        this.$refs[formName].resetFields();
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
