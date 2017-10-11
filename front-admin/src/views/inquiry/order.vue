<template>
  <el-row>
    <el-col :span="24">
      <h1>Product information</h1>
      <br>
      <el-table :data="orderInfo.productList" border style="width: 100%">
        <el-table-column prop="title" label="Title"></el-table-column>
        <el-table-column label="Image""><template scope="props"><img :src="props.row.image" width="100" height="100"></template></el-table-column>
        <el-table-column label="Unit price""><template scope="props"><el-input v-model="props.row.price" size="small"></el-input></template></el-table-column>
        <el-table-column label="Quantity""><template scope="props"><el-input v-model="props.row.skuNumber" size="small"></el-input></template></el-table-column>
        <el-table-column prop="weightUnitTip" label="Unit"></el-table-column>
        <el-table-column label="Total price""><template scope="props">{{(props.row.skuNumber * props.row.price).toFixed(2)}}</template></el-table-column>
      </el-table>
      <br>
      <h1>Special instructions</h1>
      <br>
      <el-input type="textarea" :rows="3" placeholder="Please inter a comment message" v-model="orderInfo.message"></el-input>
      <br><br>
      <h1>Add Attachment</h1>
      <br>
<!--       <el-upload
        class="avatar-uploader"
        :action="uploadUrl"
        :show-file-list="false"
        :on-success="handleAvatarSuccess">
        <img v-if="orderInfo.attachment" :src="orderInfo.attachment" class="avatar">
        <i v-else class="el-icon-plus avatar-uploader-icon"></i>
      </el-upload> -->
      <el-upload class="upload-demo" :action="uploadFileUrl" :on-remove="removeFile" :file-list="fileList" :on-success="handleUploadSuccess">
        <el-button size="small" type="primary">Add Attachment</el-button>
      </el-upload>
      <br><br>
      <h1>User information</h1>
      <el-form label-position="left" class="demo-table-expand" label-width="80px" style="width: 100%">
      <el-form-item label="UserId："><span>{{orderInfo.Uid}}</span></el-form-item>
      <el-form-item label="UserName："><span>{{orderInfo.username}}</span></el-form-item>
      </el-form>
      <br><br>
      <h1>Quotes information</h1>
      <el-form label-position="right" class="demo-table-expand" label-width="150px" style="width: 100%">
        <el-form-item label="Mode of payment：">
          <el-col :span="5">
          <el-select v-model="orderInfo.payway" size="small">
            <el-option v-for="item in paywayArr" :key="item.value" :label="item.name" :value="item.id"></el-option>
          </el-select>
          </el-col>
          <el-col :span="5">
            <el-input v-if="orderInfo.payway == '0'" v-model="orderInfo.paywayNote" size="small"></el-input>
          </el-col>
        </el-form-item>
        <el-form-item label="Payment Terms：">
          <el-col :span="5">
          <el-select v-model="orderInfo.provision" size="small">
            <el-option v-for="item in provisionArr" :key="item.value" :label="item.name" :value="item.id"></el-option>
          </el-select>
          </el-col>
          <el-col :span="5">
            <el-input v-if="orderInfo.provision == '0'" v-model="orderInfo.provisionNote" size="small"></el-input>
          </el-col>
        </el-form-item>
        <el-form-item label="Mode of transport：">
          <el-col :span="5">
          <el-select v-model="orderInfo.transport" size="small">
            <el-option v-for="item in transportArr" :key="item.value" :label="item.name" :value="item.id"></el-option>
          </el-select>
          </el-col>
          <el-col :span="5">
            <el-input v-if="orderInfo.transport == '0'" v-model="orderInfo.transportNote" size="small"></el-input>
          </el-col>
        </el-form-item>
        <el-form-item label="Freight：">
          <el-col :span="5">
          <el-select v-model="orderInfo.freightway" size="small">
            <el-option v-for="item in freightwayArr" :key="item.value" :label="item.name" :value="item.id"></el-option>
          </el-select>
          </el-col>
          <el-col :span="5">
            <el-input v-if="orderInfo.freightway == '0'" v-model="orderInfo.freightwayNote" size="small"></el-input>
          </el-col>
        </el-form-item>
        <el-form-item label="Insurance By：">
          <el-col :span="5">
          <el-select v-model="orderInfo.insurance" size="small">
            <el-option v-for="item in insuranceArr" :key="item.value" :label="item.name" :value="item.id"></el-option>
          </el-select>
          </el-col>
          <el-col :span="5">
            <el-input v-if="orderInfo.insurance == '0'" v-model="orderInfo.insuranceNote" size="small"></el-input>
          </el-col>
        </el-form-item>
        <el-form-item label="Governed By：" style="width: 30%"><span><el-input v-model="orderInfo.governed" size="small"></el-input></span></el-form-item>
        <el-form-item label="Total price："><span>{{ getSkuPrice() }}</span></el-form-item>
        <el-form-item label="Freight & Other："><span><el-input v-model="orderInfo.freight" size="small"></el-input></span></el-form-item>
        <el-form-item label="Order total price："><span style="color: red;">{{ getTotalPrice() }}</span></el-form-item>
        <el-form-item label=""><el-button type="primary" @click="submitForm">Start Order</el-button></el-form-item>
      </el-form>
    </el-col>
  </el-row>
  
</template>
<script>
  import { ajaxWriteOrder, postOrder, uploadFileUrl } from '../../api/api';
  export default {
    data() {
      return {
        orderInfo: {},
        paywayArr: {},
        provisionArr: {},
        transportArr: {},
        freightwayArr: {},
        insuranceArr: {},
        inquiryId: '0',
        uploadFileUrl: uploadFileUrl,
        fileList: [],
      };
    },
    created () {
      this.inquiryId = this.$route.params.id;
      this.loadData();
    },
    methods: {
      submitForm() {
        console.log(this.orderInfo);
        this.$confirm('Make sure submit?', 'Tips', {
          confirmButtonText: 'confirm',
          cancelButtonText: 'cancel',
          type: 'warning'
        }).then(() => {
          let _this = this;
          postOrder(this.orderInfo).then(response => {
            if (response.code === $Codes.Ok) {
              this.$message({message: "Success", type: 'success'});
            } else {
              this.$message({message: response.message, type: 'error'});
            }
            _this.$router.push({ path: '/Inquiry/drafting' });
          });
        });
      },
      loadData() {
        let _this = this;
        ajaxWriteOrder({inquiryId: this.inquiryId}).then(response => {
          if (response.code === $Codes.Ok) {
            _this.orderInfo = response.orderInfo;
            _this.paywayArr = response.paywayArr;
            _this.provisionArr = response.provisionArr;
            _this.transportArr = response.transportArr;
            _this.freightwayArr = response.freightwayArr;
            _this.insuranceArr = response.insuranceArr;
          } else {
            this.$message({message: response.message, type: 'error'});
          }
        });
      },
      getSkuPrice() {
        var skuPrice = 0;
        for (var i in this.orderInfo.productList) {
          skuPrice += parseFloat(this.orderInfo.productList[i].price) * parseFloat(this.orderInfo.productList[i].skuNumber);
        }
        return skuPrice.toFixed(2);
      },
      getTotalPrice() {
        var totalPrice = this.getSkuPrice();
        if (this.orderInfo.freight) {
          totalPrice = parseFloat(totalPrice) + parseFloat(this.orderInfo.freight);
        }
        return parseFloat(totalPrice).toFixed(2);
      },
      handleUploadSuccess(res, file) {
        this.orderInfo.attachment = res.url;
        this.fileList = [{name: file.name, url: res.url}];
      },
      removeFile(res, fileList) {
        this.orderInfo.attachment = '';
        this.fileList = [];
      },
    }
  }
</script>

<style>
  
</style>