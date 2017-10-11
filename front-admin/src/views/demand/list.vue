<template>
  <section>
    <el-row>
      <el-col :span="24">
        <!--表单-->
        <el-form :inline="true" :model="criteria" class="demo-form-inline">
          <el-form-item label="Title">
            <el-input v-model="criteria.title" placeholder=""></el-input>
          </el-form-item>
          <el-form-item label="UserName">
            <el-input v-model="criteria.username" placeholder=""></el-input>
          </el-form-item>
          <el-form-item label="Number">
            <el-input v-model="criteria.number" placeholder=""></el-input>
          </el-form-item>
          <el-form-item label="Type">
            <el-cascader expand-trigger="hover" :options="types" v-model="criteria.type" placeholder=""></el-cascader>
          </el-form-item>
          <el-form-item label="State">
            <el-cascader expand-trigger="hover" :options="states" v-model="criteria.state" placeholder=""></el-cascader>
          </el-form-item>
          <el-button type="primary" @click="onSubmit">Search</el-button>
          <a href="javascript:;" id="download" style="float: right;color: #169bd5;font-size: 14px;padding-top: 7px" @click="download()" download="download.csv">Export data</a>
        </el-form>
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column prop="number" label="Number" width="180"></el-table-column>
          <el-table-column prop="title" label="Title"></el-table-column>
          <el-table-column prop="type" label="Type"></el-table-column>
          <el-table-column prop="createTime" label="CreateTime"></el-table-column>
          <el-table-column prop="updateTime" label="UpdateTime"></el-table-column>
          <el-table-column prop="stateTip" label="StateTip"></el-table-column>
          <el-table-column label="操作">
            <template scope="scope">
              <el-button type="danger" size="small" @click="handleView(scope.$index, scope.row)">View</el-button>
              <el-button type="primary" size="small" v-show="scope.row.state != 3" @click="handleEdit(scope.$index, scope.row)">Verify</el-button>
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
    <el-dialog title="Modify Infomation" v-model="dialogFormVisible" size="tiny">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="Title">
          <el-input v-model="form.title" :readonly="true"></el-input>
        </el-form-item>
        <el-form-item label="Result">
          <el-radio-group v-model="form.state">
            <el-radio :label="0" v-show="form.st === 2">审核不通过</el-radio>
            <el-radio :label="1" v-show="form.st === 2">审核通过</el-radio>
            <el-radio :label="1" v-show="form.st === 4 || form.st === 0">恢复通过</el-radio>
            <el-radio :label="4" v-show="form.st === 1">撤销通过</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="Reason">
          <el-input type="textarea" v-model="form.reason"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="handleSave()">Send</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </section>
</template>
<script type="text/ecmascript-6">
  import { DemandList, DemandReview } from '../../api/api';
  export default {
    data () {
      return {
        criteria: { // 当前查询条件
          number: '',
          title: '',
          type: '',
          state: '',
        },
        criteria_default: {}, // 默认查询条件
        tableData: [],
        types: [],
        states: [],
        dialogFormVisible: false,
        editLoading: false,
        form: {
          title: '',
          reason: '',
          state: '',
        },
        currentPage: 1,
        pageSize: 20,
        table_index: 1,
        totalCount: 0,
      };
    },
    created () {
      this.types = [
        {value: '0', label: 'All'},
        {value: '1', label: 'Product demand'},
        {value: '2', label: 'Formula demand'},
        {value: '3', label: 'Patent demand'},
        {value: '4', label: 'Technology demand'},
      ];
      this.states = [
        {value: '', label: 'All'},
        {value: '0', label: 'Audit Disapproved'},
        {value: '1', label: 'Effective'},
        {value: '2', label: 'Audit Pending'},
        {value: '3', label: 'Expired'},
        {value: '4', label: 'Revoke'},
      ];
      this.loadData();
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
      handleView (index, row) {

      },
      handleEdit (index, row) {
        this.dialogFormVisible = true;
        this.form.id = row.id;
        this.form.title = row.title;
        this.form.st = parseInt(row.state);
        this.table_index = index;
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
            state: this.form.state,
            reason: this.form.reason
          };
          let _this = this;
          DemandReview(pdt).then(response => {
            if (response.code === $Codes.Ok) {
              _this.$message({message: "Success！", type: 'success'});
            } else {
              _this.$message({message: response.message, type: 'error'});
            }
          });
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
          pdt = Object.assign(pdt, this.criteria);
        }
        pdt.page = this.currentPage;
        pdt.pagesize = this.pageSize;
        let _this = this;
        DemandList(pdt).then(response => {
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
