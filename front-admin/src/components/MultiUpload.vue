<template>
<div>
  <el-upload
    action="https://jsonplaceholder.typicode.com/posts/"
    list-type="picture-card"
    :on-preview="handlePictureCardPreview"
    :multiple="true"
    :file-list="fileList"
    :on-remove="handleRemove">
    <i class="el-icon-plus"></i>
  </el-upload>
  <el-dialog v-model="dialogVisible" size="tiny">
    <img width="100%" :src="dialogImageUrl" alt="">
  </el-dialog>
</div>
</template>
<script>
  export default {
    name: 'mup',
    data() {
      return {
        dialogImageUrl: '',
        dialogVisible: false,
        fileList: []
      };
    },
    props: {
      defaultFileList: {
        type: Array
      }
    },
    mounted() {
      this.fileList = this.defaultFileList;
    },
    methods: {
      handleRemove(file, fileList) {
        console.log(file, fileList);
      },
      getFileList() {
        var str = '';
        for (var i in this.fileList) {
          var fobj = this.fileList[i];
          if (fobj.url === undefined || fobj.url.length < 3) {
            continue;
          }
          if (str !== '') {
            str += ',';
          }
          str += fobj.url;
        }
        return str;
      },
      handlePictureCardPreview(file) {
        this.dialogImageUrl = file.url;
        this.dialogVisible = true;
      }
    }
  }
</script>