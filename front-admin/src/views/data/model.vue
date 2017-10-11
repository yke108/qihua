<template>
  <section>
    <el-row>
      <el-col :span="24">
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column prop="id" label="ID"></el-table-column>
          <el-table-column prop="text" label="Name"></el-table-column>
          <el-table-column label="操作">
            <template scope="scope">
              <el-button type="primary" size="small" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
              <el-button type="danger" size="small" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-col>
    </el-row>
    <el-dialog title="修改指标信息" v-model="dialogFormVisible" size="tiny">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="名称">
          <el-input v-model="form.title"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSave" :loading="editLoading">修改</el-button>
          <el-button @click="dialogFormVisible = false">取消</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </section>
</template>
<script type="text/ecmascript-6">
  import { modelList } from '../../api/api';
  export default {
    data () {
      return {
        tableData: [], // 列表数据对象
        dialogFormVisible: false,
        editLoading: false,
        form: {
          keyword: '',
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
          let pdt = {id: row.id};
          let _this = this;
          $.post(this.$hyobj.base_url + this.deleteUrl, pdt, function(response) {
            if (response.code === ERR_OK) {
              _this.$message({
                message: "操作成功！",
                type: 'success'
              });
              _this.loadData();
            } else {
              _this.$message({
                message: response.message,
                type: 'success'
              });
            }
          }, 'json');
        });
      },
      handleEdit (index, row) {
        this.dialogFormVisible = true;
        this.form = Object.assign({}, row);
        this.table_index = index;
      },
      handleSave () {
        this.$confirm('确认提交吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          cancelButtonClass: 'cancel'
        }).then(() => {
          this.editLoading = true;
          let date = this.form.date;
          if (typeof date === "object") {
            date = [date.getFullYear(), (date.getMonth() + 1), (date.getDate())].join('-');
            this.form.date = date
          }
          let _this = this;
          $.post(this.$hyobj.base_url + this.editUrl, this.form, function(response) {
            if (response.code === ERR_OK) {
              _this.tableData.splice(_this.table_index, 1, response.data);
              _this.$message({
                message: "操作成功！",
                type: 'success'
              });
              _this.editLoading = false;
              _this.dialogFormVisible = false;
            } else {
              this.$message({
                message: response.message,
                type: 'success'
              });
            }
          }, 'json');
        }).catch(() => {

        });
      },
      loadData: function() {
        let _this = this;
        modelList().then(response => {
          if (response.code === $Codes.Ok) {
            _this.tableData = response.rows;
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
