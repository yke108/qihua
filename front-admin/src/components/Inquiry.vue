<template>
  <section>
    <el-row>
      <el-col :span="24">
        <!--表单-->
        <el-form :inline="true" :model="formInline" class="demo-form-inline">
          <el-form-item label="Keyword">
            <el-input v-model="criteria.keyword" placeholder="keyword"></el-input>
          </el-form-item>
          <el-form-item label="User">
            <el-input v-model="criteria.username" placeholder="username"></el-input>
          </el-form-item>
          <el-form-item label="Start Date">
            <el-date-picker v-model="criteria.startDate" type="date" placeholder="selete date"></el-date-picker>
          </el-form-item>
          <el-form-item label="End Date">
            <el-date-picker v-model="criteria.endDate" type="date" placeholder="selete date"></el-date-picker>
          </el-form-item>
          <el-button type="primary" @click="onSubmit">Search</el-button>
          <el-button type="primary" @click="onReset">Reset</el-button>
        </el-form>
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column prop="inquiryId" label="ID" width="80"></el-table-column>
          <el-table-column prop="inquirySn" label="InquirySn" width="150"></el-table-column>
          <el-table-column prop="username" label="Username" width="150"></el-table-column>
          <el-table-column prop="country" label="Country" width="120"></el-table-column>
          <el-table-column prop="skuPrice" label="Price" width="110"></el-table-column>
          <el-table-column prop="freight" label="Freight" width="100"></el-table-column>
          <el-table-column prop="totalPrice" label="Total fee" width="110"></el-table-column>
          <el-table-column prop="addTime" label="Add Time" width="160"></el-table-column>
          <el-table-column prop="state" label="State" width="100"></el-table-column>
          <el-table-column prop="adminName" label="Assign" width="100"></el-table-column>
          <el-table-column label="Action">
            <template scope="scope">
              <router-link :to="{ name: 'InquiryDetail', params: { id: scope.row.inquiryId }}"><el-button type="primary" size="small">View</el-button></router-link>
              <router-link v-if="scope.row.orderSign === '0'" :to="{ name: 'InquiryOrder', params: { id: scope.row.inquiryId }}"><el-button type="primary" size="small">Start Order</el-button></router-link>
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
  </section>
</template>
<script type="text/ecmascript-6">
  import { inquiryList } from '../api/api';
  export default {
    props: {
      state: {
        type: String,
        default: '0'
      }
    },
    data () {
      return {
        formInline: {},
        criteria: { // 当前查询条件
          keyword: '',
          username: '',
          startDate: '',
          endDate: '',
          state: '-1',
        },
        criteria_default: {}, // 默认查询条件
        tableData: [], // 列表数据对象
        dialogFormVisible: false,
        editLoading: false,
        currentPage: 1, // 默认
        pageSize: 20, // 指定
        totalCount: 0, // 数据返回
      };
    },
    created () {
      this.criteria.state = this.state;
      for (var i in this.criteria) {
        if (this.criteria_default[i] === undefined) {
          this.criteria_default[i] = this.criteria[i];
        }
      }
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
      handleEdit () {
        // 跳转
      },
      handleCurrentChange(val) {
        if (this.currentPage !== val) {
          this.currentPage = val;
          this.loadData();
        }
      },
      loadData: function() {
        var pdt = {};
        if (undefined !== this.criteria) {
          pdt = this.criteria;
        }
        pdt.page = this.currentPage;
        pdt.pageSize = this.pageSize;
        if (pdt.startDate instanceof Object) {
          let tempTime = new Date(pdt.startDate);
          pdt.startDate = tempTime.getFullYear() + '-' + (tempTime.getMonth() + 1) + '-' + tempTime.getDate();
        }
        if (pdt.endDate instanceof Object) {
          let tempTime = new Date(pdt.endDate);
          pdt.endDate = tempTime.getFullYear() + '-' + (tempTime.getMonth() + 1) + '-' + tempTime.getDate();
        }
        let _this = this;
        inquiryList(pdt).then(response => {
          if (response.code === $Codes.Ok) {
            _this.tableData = response.list;
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
