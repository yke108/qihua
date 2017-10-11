<template>
  <section>
    <el-row>
      <el-col :span="24">
        <!--表单-->
        <el-form :inline="true" :model="formInline" class="demo-form-inline">
          <el-form-item label="Keyword">
            <el-input v-model="criteria.keyword" placeholder="Keywords"></el-input>
          </el-form-item>
          <el-form-item label="Category">
            <el-cascader expand-trigger="click" change-on-select="true" clearable="true" :options="categories" v-model="criteria.category"  placeholder="Please select"></el-cascader>
          </el-form-item>
          <el-button type="primary" @click="onSubmit">Search</el-button>
          <el-button type="primary" @click="onReset">Reset</el-button>
        </el-form>
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column prop="id" label="ID" width="80"></el-table-column>
          <el-table-column prop="productCode" label="Product Code" width="150"></el-table-column>
          <el-table-column prop="title" label="Title" width="150"></el-table-column>
          <el-table-column prop="categoryList" label="Category" width="150"></el-table-column>
          <el-table-column prop="enName" label="En Name"></el-table-column>
          <el-table-column prop="price" label="Price"></el-table-column>
          <el-table-column prop="moq" label="Moq"></el-table-column>
          <el-table-column prop="inventory" label="Inventory"></el-table-column>
          <el-table-column prop="weightUnit.name" label="Unit"></el-table-column>
          <!-- <el-table-column prop="Uid" label="Company"></el-table-column> -->
          <el-table-column prop="addTime" label="AddTime" width="180"></el-table-column>
          <el-table-column prop="stateName" label="Status"></el-table-column>
          <el-table-column label="Action">
            <template scope="scope">
              <div v-if="scope.row.state === '1'">
                <el-button type="danger" size="small" @click="handleAudit(scope.$index, scope.row)">Unshelve</el-button>
              </div>
              <div v-else>
                <el-button type="danger" size="small" @click="handleAudit(scope.$index, scope.row)">Valid</el-button>
              </div>
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
    <el-dialog :title=auditTitle v-model="dialogFormVisible" size="tiny">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="Reason">
          <el-input v-model="form.reason"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSave" :loading="editLoading">确定</el-button>
          <el-button @click="dialogFormVisible = false">取消</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </section>
</template>
<script type="text/ecmascript-6">
  import { GoodsList, getCategoryList, renewStatus, changeOff } from '../../api/api';
  export default {
    data () {
      return {
        formInline: {},
        criteria: { // 当前查询条件
          keyword: '',
          category: [],
          state: '3',
        },
        criteria_default: {}, // 默认查询条件
        tableData: [], // 列表数据对象
        categories: [],
        states: [{
          value: '-1',
          label: 'All'
        }, {
          value: '1',
          label: 'Valid'
        }, {
          value: '3',
          label: 'Unshelve'
        }],
        dialogFormVisible: false,
        editLoading: false,
        form: {
          id: '',
          reason: '',
          state: '',
        },
        currentPage: 1, // 默认
        pageSize: 20, // 指定
        totalCount: 0, // 数据返回
        table_index: 0, // 当前行号
        auditTitle: '',
      };
    },
    created () {
      for (var i in this.criteria) {
        if (this.criteria_default[i] === undefined) {
          this.criteria_default[i] = this.criteria[i];
        }
      }
      this.loadData();
      this.optionsForGoods();
    },
    methods: {
      onSubmit () {
        console.log(this.criteria);
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
      handleAudit (index, row) {
        this.dialogFormVisible = true;
        this.form.id = row.id;
        this.form.reason = '';
        this.form.state = row.state;
        this.table_index = index;
        if (row.state === '1') {
          this.auditTitle = "下架原因";
        } else {
          this.auditTitle = "上架原因";
        }
      },
      handleSave () {
        if (this.form.reason === '') {
          this.$message({message: 'Reason cannot be empty!', type: 'error'});
          return false;
        }
        this.$confirm('确认提交吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          cancelButtonClass: 'cancel'
        }).then(() => {
          this.editLoading = true;
          let _this = this;
          if (this.form.state === '1') {
            changeOff(this.form).then(response => {
              if (response.code === $Codes.Ok) {
                _this.$message({message: "Success！", type: 'success'});
                this.loadData();
                _this.dialogFormVisible = false;
              } else {
                _this.$message({message: response.message, type: 'error'});
              }
            });
          } else {
            renewStatus(this.form).then(response => {
              if (response.code === $Codes.Ok) {
                _this.$message({message: "Success！", type: 'success'});
                this.loadData();
                _this.dialogFormVisible = false;
              } else {
                _this.$message({message: response.message, type: 'error'});
              }
            });
          }
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
        GoodsList(pdt).then(response => {
          if (response.code === $Codes.Ok) {
            _this.tableData = response.rows;
            _this.totalCount = response.total;
          }
          for (var i in _this.tableData) {
            if (_this.tableData[i]['state'] === '1') {
              _this.tableData[i]['stateName'] = "Valid";
            } else {
              _this.tableData[i]['stateName'] = "Unshelve"
            }
          }
        });
      },
      optionsForGoods: function() {
        let _this = this;
        if ($Options.CategoryList === undefined) { /* 分类列表 */
          getCategoryList().then(response => {
            if (response.code === $Codes.Ok) {
              $Options.CategoryList = response.data;
              _this.categories = $Options.CategoryList;
            }
          });
        } else {
          this.categories = $Options.CategoryList;
        }
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
