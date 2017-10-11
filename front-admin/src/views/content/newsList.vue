<template>
  <section>
    <el-row>
      <el-col :span="24">
        <block>
        <el-button type="success" style="float: right;" @click="handleEdit(0, {})"><i class="el-icon-plus"></i> Add</el-button>
        </block>
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column prop="title" label="Title"></el-table-column>
          <el-table-column prop="referer" label="Referer"></el-table-column>
          <el-table-column prop="reportDate" label="Report Date"></el-table-column>
          <el-table-column prop="username" label="created User"></el-table-column>
          <el-table-column label="Action">
            <template scope="scope">
              <el-button type="danger" size="small" @click="handleDelete(scope.$index, scope.row)">Delete</el-button>
              <el-button type="primary" size="small" v-show="scope.row.state != 3" @click="handleEdit(scope.$index, scope.row)">Modify</el-button>
            </template>
          </el-table-column>
        </el-table>
        <div class="block">
          <el-pagination
            @current-change="handleCurrentChange"
            :current-page="currentPage"
            :page-size="pageSize"
            layout="prev, pager, next, jumper"
            :total="totalCount">
          </el-pagination>
        </div>
      </el-col>
    </el-row>
    <el-dialog :title="editTitle" v-model="dialogFormVisible" size="large">
      <el-form ref="form" :model="form" label-width="100px">
        <el-form-item label="Title">
          <el-input v-model="form.title"></el-input>
        </el-form-item>
        <el-form-item label="Image">
          <el-upload
            class="avatar-uploader"
            :action="uploadUrl"
            :show-file-list="false"
            :on-success="handleAvatarSuccess">
            <img v-if="form.img" :src="form.img" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon"></i>
          </el-upload>
          <span style="color: red;">要求：图片宽219px,高105px</span>
        </el-form-item>
        <el-form-item label="Content">
          <UE :defaultMsg=form.content :config=editorConfig id='news1' ref="ue"></UE>
        </el-form-item>
        <el-form-item label="Referer">
          <el-input v-model="form.referer"></el-input>
        </el-form-item>
        <el-form-item label="Report Date">
          <el-date-picker
            v-model="form.reportDate"
            type="date"
            placeholder="selete date">
          </el-date-picker>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSave()">Send</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </section>
</template>
<script type="text/ecmascript-6">
  import { getNewsList, editNews, deleteNews, uploadUrl } from '../../api/api';
  import UE from '../../components/ue/ue.vue';
  export default {
    components: {UE},
    data () {
      return {
        tableData: [],
        dialogFormVisible: false,
        editLoading: false,
        form: {
          id: '',
          title: '',
          referer: '',
          reportDate: '',
          content: '',
          img: '',
        },
        currentPage: 1,
        pageSize: 20,
        table_index: 1,
        totalCount: 0,
        uploadUrl: uploadUrl,
        editTitle: '',
        editorConfig: {
          initialFrameWidth: null,
          initialFrameHeight: 350,
        },
      };
    },
    created () {
      this.loadData();
    },
    methods: {
      handleDelete (index, row) {
        this.$confirm('确认删除吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          cancelButtonClass: 'cancel'
        }).then(() => {
          let pdt = {
            id: row.id,
          };
          let _this = this;
          deleteNews(pdt).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
              this.loadData();
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
        }).catch(() => {

        });
      },
      handleEdit (index, row) {
        this.dialogFormVisible = true;
        if (row.id !== undefined) {
          this.form.id = row.id;
          this.form.title = row.title;
          this.form.referer = row.referer;
          this.form.reportDate = row.reportDate;
          this.form.content = row.content;
          this.form.img = row.img;
          this.editTitle = 'Modify Infomation';
          this.$refs.ue.setUEContent(this.form.content);
        } else {
          this.form.id = 0;
          this.form.title = "";
          this.form.referer = "";
          this.form.reportDate = "";
          this.form.content = "";
          this.form.img = "";
          this.editTitle = 'Add Infomation';
          this.$refs.ue.setUEContent(this.form.content);
        }
        console.log(this.editTitle);
      },
      handleSave () {
        this.$confirm('确认提交吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          cancelButtonClass: 'cancel'
        }).then(() => {
          this.editLoading = true;
          let pdt = {
            id: this.form.id,
            title: this.form.title,
            content: this.$refs.ue.getUEContent(),
            referer: this.form.referer,
            reportDate: this.form.reportDate,
            img: this.form.img
          };
          let _this = this;
          let tempTime = new Date(pdt.reportDate);
          pdt.reportDate = tempTime.getFullYear() + '-' + (tempTime.getMonth() + 1) + '-' + tempTime.getDate();
          editNews(pdt).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
              this.loadData();
              _this.dialogFormVisible = false;
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
        }).catch(() => {

        });
      },
      handleAvatarSuccess(res, file) {
        this.form.img = res.url;
      },
      handleCurrentChange(val) {
        if (this.currentPage !== val) {
          this.currentPage = val;
        }
        this.loadData();
      },
      loadData: function() {
        var pdt = {};
        pdt.page = this.currentPage;
        pdt.pagesize = this.pageSize;
        let _this = this;
        getNewsList(pdt).then(response => {
          if (response.code === $Codes.Ok) {
            _this.tableData = response.rows;
            _this.totalCount = response.total;
          }
        });
      }
    }
  };
</script>
<style>
  .el-pagination {
    text-align: center;
    margin-top: 30px;
  }
  .el-message-box__btns .cancel {
    float: right;
    margin-left: 10px;
  }
</style>
