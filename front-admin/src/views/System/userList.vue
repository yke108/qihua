<template>
  <section>
    <el-row>
      <el-col :span="24">
        <!--表单-->
        <el-form :inline="true" :model="formInline" class="demo-form-inline">
          <el-form-item label="姓名">
            <el-input v-model="criteria.name" placeholder="姓名"></el-input>
          </el-form-item>
          <el-form-item label="年份">
            <el-date-picker v-model="criteria.date" align="right" type="year" placeholder="选择年份">
            </el-date-picker>
          </el-form-item>
          <el-form-item label="地址">
            <el-cascader expand-trigger="hover" :options="options" v-model="criteria.address"></el-cascader>
          </el-form-item>
          <el-form-item label="籍贯">
            <el-select v-model="criteria.place" placeholder="请选择">
              <el-option v-for="item in places" :label="item.label" :value="item.value"></el-option>
            </el-select>
          </el-form-item>
          <el-button type="primary" @click="onSubmit">查询</el-button>
          <el-button type="primary" @click="onReset">重置</el-button>
        </el-form>
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column type="selection"></el-table-column>
          <el-table-column prop="date" label="出生日期" width="180"></el-table-column>
          <el-table-column prop="name" label="姓名" width="180"></el-table-column>
          <el-table-column prop="address" label="地址"></el-table-column>
          <el-table-column label="操作">
            <template scope="scope">
              <el-button type="primary" size="small" @click="handleEdit(scope.$index, scope.row)">编辑</el-button>
              <el-button type="danger" size="small" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
            </template>
          </el-table-column>
        </el-table>
        <div class="block">
          <el-pagination
            @current-change="handleCurrentChange"
            :current-page="currentPage"
            :page-size="pageSize"
            layout="total, prev, pager, next, jumper"
            :total="totalCount">
          </el-pagination>
        </div>
      </el-col>
    </el-row>
    <el-dialog title="修改个人信息" v-model="dialogFormVisible" size="tiny">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="姓名">
          <el-input v-model="form.name"></el-input>
        </el-form-item>
        <el-form-item label="地址">
          <el-input v-model="form.address"></el-input>
        </el-form-item>
        <el-form-item label="出生日期">
          <el-date-picker type="date" placeholder="选择日期" v-model="form.date" style="width: 100%;" ></el-date-picker>
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
  const ERR_OK = "000";
  export default {
    data () {
      return {
        formInline: {},
        criteria: { // 当前查询条件
          name: '',
          date: '',
          address: [],
          place: ''
        },
        criteria_default: {}, // 默认查询条件
        tableData: [], // 列表数据对象
        options: [],
        places: [],
        dialogFormVisible: false,
        editLoading: false,
        form: {
          name: '',
          address: '',
          date: '',
        },
        currentPage: 1, // 默认
        pageSize: 20, // 指定
        totalCount: 0, // 数据返回
        table_index: 0, // 当前行号
        dataUrl: '/test/getData', // 获取列表数据
        editUrl: '/test/edit', // 编辑数据
        deleteUrl: '/test/del', // 删除数据
        optionsUrl: '/test/getOptions', // 获取查询相关选项
      };
    },
    created () {
      for (var i in this.criteria) {
        if (this.criteria_default[i] === undefined) {
          this.criteria_default[i] = this.criteria[i];
        }
      }
      this.loadData();
      this.$http.get(this.$hyobj.base_url + this.optionsUrl).then((response) => {
        response = response.data;
        if (response.code === ERR_OK) {
          this.options = response.datas;
          this.places = response.places;
        }
      });
    },
    methods: {
      onSubmit () {
        if (this.currentPage !== 1) {
          this.currentPage = 1;
        } else {
          this.loadData();
        }
      },
      onReset () {
        for (var i in this.criteria) {
          this.criteria[i] = this.criteria_default[i];
        }
      },
      handleDelete (index, row) {
        this.$confirm('确认删除吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          cancelButtonClass: 'cancel'
        }).then(() => {
          let pdt = {id: row.id};
          let _this = this;
          $.get(this.$hyobj.base_url + this.deleteUrl, pdt, function(response) {
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
      handleCurrentChange(val) {
        if (this.currentPage !== val) {
          this.currentPage = val;
        }
        this.loadData();
      },
      loadData: function() {
        var pdt = {};
        if (undefined !== this.criteria) {
          pdt = this.criteria;
        }
        pdt.page = this.currentPage;
        pdt.pagesize = this.pageSize;
        let _this = this;
        $.get(this.$hyobj.base_url + this.dataUrl, pdt, function(response) {
          if (response.code === ERR_OK) {
            _this.tableData = response.datas;
            _this.totalCount = response.totalCount;
          }
        }, 'json');
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
