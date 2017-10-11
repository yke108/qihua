<template>
  <section>
    <el-row>
      <el-col :span="24">
        <block>
        <el-button type="success" style="float: right;" @click="handleEdit(0, {})"><i class="el-icon-plus"></i> Add</el-button>
        </block>
        <!--表格-->
        <el-table :data="tableData" border style="width: 100%">
          <el-table-column prop="text" label="Title"></el-table-column>
          <el-table-column label="Image">
            <template scope="scope">
              <img width='219' height='105' :src='scope.row.img'>
            </template>
          </el-table-column>
          <el-table-column label="Action">
            <template scope="scope">
              <el-button type="danger" size="small" @click="handleDelete(scope.$index, scope.row)">Delete</el-button>
              <el-button type="primary" size="small" v-show="scope.row.state != 3" @click="handleEdit(scope.$index, scope.row)">Modify</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-col>
    </el-row>
    <el-dialog :title="editTitle" v-model="dialogFormVisible" size="small">
      <el-form ref="form" :model="form" label-width="80px">
        <el-form-item label="Title">
          <el-input v-model="form.text"></el-input>
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
        <el-form-item>
          <el-button type="primary" @click="handleSave()">Send</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </section>
</template>
<script type="text/ecmascript-6">
  import { partnerList, addpartner, deletePartner, uploadUrl } from '../../api/api';
  export default {
    data () {
      return {
        tableData: [],
        dialogFormVisible: false,
        editLoading: false,
        form: {
          id: '',
          text: '',
          img: '',
        },
        uploadUrl: uploadUrl,
        editTitle: '',
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
          deletePartner(pdt).then(response => {
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
        this.form.id = row.id;
        this.form.text = row.text;
        this.form.img = row.img;
        this.dialogFormVisible = true;
        if (row.id === undefined) {
          this.editTitle = 'Add Infomation';
        } else {
          this.editTitle = 'Modify Infomation';
        }
      },
      handleSave () {
        if (!this.form.text) {
          this.$message({message: '标题不能为空', type: 'error'});
          return false;
        }
        if (!this.form.img) {
          this.$message({message: '图标不能为空', type: 'error'});
          return false;
        }
        this.$confirm('确认提交吗？', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          cancelButtonClass: 'cancel'
        }).then(() => {
          this.editLoading = true;
          let pdt = {
            id: this.form.id,
            text: this.form.text,
            img: this.form.img
          };
          let _this = this;
          console.log(pdt);
          addpartner(pdt).then(response => {
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
      loadData: function() {
        var pdt = {};
        let _this = this;
        partnerList(pdt).then(response => {
          if (response.code === $Codes.Ok) {
            _this.tableData = response.data;
          }
        });
      }
    }
  };
</script>
