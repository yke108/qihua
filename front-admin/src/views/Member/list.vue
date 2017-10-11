<template>
  <section>
    <el-row>
      <el-col :span="24">
        <!--表单-->
        <el-form :inline="true" :model="formInline" class="demo-form-inline">
          <el-form-item label="User Name">
            <el-input v-model="criteria.username" placeholder=""></el-input>
          </el-form-item>
          <el-form-item label="Phone">
            <el-input v-model="criteria.phone" placeholder=""></el-input>
          </el-form-item>
          <el-form-item label="Start Date">
            <el-date-picker v-model="criteria.startDate" type="date" placeholder="selete date"></el-date-picker>
          </el-form-item>
          <el-form-item label="End Date">
            <el-date-picker v-model="criteria.endDate" type="date" placeholder="selete date"></el-date-picker>
          </el-form-item>
          <el-form-item label="Area">
            <el-cascader expand-trigger="click" change-on-select="true" clearable="true" :options="areas" v-model="criteria.areaId"  placeholder="Please select"></el-cascader>
          </el-form-item>
          <el-form-item label="Status">
            <el-select v-model="criteria.status" placeholder="">
              <el-option label="All" value="0"></el-option>
              <el-option label="normal" value="1"></el-option>
              <el-option label="invalid" value="2"></el-option>
            </el-select>
          </el-form-item> 
          <el-button type="primary" @click="onSubmit">查询</el-button>
          <el-button type="primary" @click="onReset">重置</el-button>
        </el-form>
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column prop="id" label="ID" width="100"></el-table-column>
          <el-table-column prop="username" label="User Name" width="180"></el-table-column>
          <el-table-column prop="phone" label="Phone" width="130"></el-table-column>
          <el-table-column prop="foxedPhone" label="FoxedPhone" width="120"></el-table-column>
          <el-table-column prop="fullName" label="Full Name" width="150"></el-table-column>
          <el-table-column prop="addressList" label="Area" width="200"></el-table-column>
          <el-table-column prop="addTime" label="Register Time" width="180"></el-table-column>
          <el-table-column prop="statusTip" label="Status" width="100"></el-table-column>
          <el-table-column label="Action">
            <template scope="scope">
              <el-button v-if="scope.row.status==1" type="primary" size="small" @click="handleEdit(scope.$index, scope.row)">Invalid</el-button>
              <el-button v-if="scope.row.status==2" type="success" size="small" @click="handleEdit(scope.$index, scope.row)">Normal</el-button>
              <el-button type="danger" size="small" @click="handleDelete(scope.$index, scope.row)">Delete</el-button>
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
    <el-dialog :title="changeStatusTitle" v-model="dialogFormVisible" size="tiny">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="reason">
          <el-input v-model="form.reason"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSave" :loading="editLoading">{{changeStatusTitle}}</el-button>
          <el-button @click="dialogFormVisible = false">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog title="Delete" v-model="dialogFormVisible1" size="tiny">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="reason">
          <el-input v-model="form.reason"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleDel" :loading="editLoading">Delete</el-button>
          <el-button @click="dialogFormVisible1 = false">Cancel</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </section> 
</template>
<script type="text/ecmascript-6">
  import { getmemberList, getAreaList, memberOperate, memberOperateDel } from '../../api/api';
  export default {
    data () {
      return {
        formInline: {},
        criteria: { // 当前查询条件
          username: '',
          phone: '',
          startDate: '',
          endDate: '',
          areaId: [],
          status: '0'
        },
        criteria_default: {}, // 默认查询条件
        tableData: [], // 列表数据对象
        areas: [],
        dialogFormVisible: false,
        dialogFormVisible1: false,
        editLoading: false,
        changeStatusTitle: '',
        form: {
          id: '',
          status: '',
          reason: '',
        },
        currentPage: 1, // 默认
        pageSize: 20, // 指定
        totalCount: 0, // 数据返回
        table_index: 0, // 当前行号
      };
    },
    created () {
      for (var i in this.criteria) {
        if (this.criteria_default[i] === undefined) {
          this.criteria_default[i] = this.criteria[i];
        }
      }
      this.loadData();

      let _this = this;
      if ($Options.AreaList === undefined) { /* 地区列表 */
        getAreaList().then(response => {
          if (response.code === $Codes.Ok) {
            $Options.AreaList = response.data;
            _this.areas = $Options.AreaList;
          }
        });
      } else {
        this.areas = $Options.AreaList;
      }
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
        this.dialogFormVisible1 = true;
        this.form.id = row.id;
        this.form.reason = '';
        this.table_index = index;
      },
      handleDel (index, row) {
        if (this.form.reason === '') {
          this.$message({message: 'Reason cannot be empty!', type: 'error'});
          return false;
        }
        this.$confirm('确认删除吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          cancelButtonClass: 'cancel'
        }).then(() => {
          this.editLoading = true;
          let _this = this;
          memberOperateDel(this.form).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
              this.loadData();
              _this.dialogFormVisible1 = false;
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
          this.editLoading = false;
        });
      },
      handleEdit (index, row) {
        this.dialogFormVisible = true;
        this.form.id = row.id;
        this.form.reason = '';
        this.form.status = row.status;
        if (this.form.status === '1') {
          this.form.status = '2';
          this.changeStatusTitle = 'Normal';
        } else {
          this.form.status = '1';
          this.changeStatusTitle = 'Invalid';
        }
        this.table_index = index;
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
          memberOperate(this.form).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
              this.loadData();
              _this.dialogFormVisible = false;
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
          this.editLoading = false;
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
        if (pdt.startDate instanceof Object) {
          let tempTime = new Date(pdt.startDate);
          pdt.startDate = tempTime.getFullYear() + '-' + (tempTime.getMonth() + 1) + '-' + tempTime.getDate();
        }
        if (pdt.endDate instanceof Object) {
          let tempTime = new Date(pdt.endDate);
          pdt.endDate = tempTime.getFullYear() + '-' + (tempTime.getMonth() + 1) + '-' + tempTime.getDate();
        }
        let _this = this;
        getmemberList(pdt).then(response => {
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
