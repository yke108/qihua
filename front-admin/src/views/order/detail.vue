<template>
  <el-row>
    <el-col :span="24">
      <h1>Product information</h1>
      <br>
      <el-table :data="productList" border style="width: 100%">
        <el-table-column prop="title" label="Title"></el-table-column>
        <el-table-column label="Image""><template scope="props"><img :src="props.row.image" width="100" height="100"></template></el-table-column>
        <el-table-column prop="orderPrice" label="Unit price"></el-table-column>
        <el-table-column prop="skuNumber" label="Quantity""></el-table-column>
        <el-table-column prop="weightUnitTip" label="Unit"></el-table-column>
        <el-table-column prop="orderTotalPrice" label="Total price""></el-table-column>
      </el-table>
      <br>
      <h1>Order information</h1>
      <el-form label-position="right" class="demo-table-expand" label-width="150px" style="width: 100%">
        <el-form-item label="OrderSn："><span>{{orderInfo.orderSn}}</span></el-form-item>
        <el-form-item label="State：" style="color: red;"><span>{{orderInfo.stateName}}</span></el-form-item>
        <el-form-item label="UserName："><span>{{orderInfo.username}}</span></el-form-item>
        <el-form-item label="Buyer Confirmation"><span v-if="orderInfo.bconfirm == '1'">Yes</span><span v-else>No</span></el-form-item>
        <el-form-item label="Seller Confirmation"><span v-if="orderInfo.sconfirm == '1'">Yes</span><span v-else>No</span></el-form-item>
        <el-form-item label="Mode of payment："><span>{{orderInfo.payway}}</span></el-form-item>
        <el-form-item label="Payment Terms："><span>{{orderInfo.provision}}</span></el-form-item>
        <el-form-item label="Mode of transport："><span>{{orderInfo.transport}}</span></el-form-item>
        <el-form-item label="Freight："><span>{{orderInfo.freightway}}</span></el-form-item>
        <el-form-item label="Insurance By："><span>{{orderInfo.insurance}}</span></el-form-item>
        <el-form-item label="Governed By："><span>{{orderInfo.governed}}</span></el-form-item>
        <el-form-item label="Attachment："><span>
          <a v-if="orderInfo.attachment" :href="downloadUrl + '?url=' + orderInfo.attachment" target="_blank"><el-button size="small">download</el-button></a>
        </span></el-form-item>
        <el-form-item label="Special instructions："><span>{{orderInfo.message}}</span></el-form-item>
        <el-form-item label="Total price："><span>{{orderInfo.skuPrice}}</span></el-form-item>
        <el-form-item label="Freight & Other："><span>{{orderInfo.freight}}</span></el-form-item>
        <el-form-item label="Order total price："><span style="color: red;">{{orderInfo.totalPrice}}</span></el-form-item>
      </el-form>
      <h1>Download information</h1>
      <el-form label-position="right" class="demo-table-expand" label-width="150px" style="width: 100%">
        <el-form-item label="Buyer contract：">
          <span><a v-if="orderInfo.bcontract" :href="downloadUrl + '?url=' + orderInfo.bcontract" target="_blank"><el-button size="small">download</el-button></a></span>
        </el-form-item>
        <el-form-item label="Seller contract：">
          <span><a v-if="orderInfo.scontract" :href="downloadUrl + '?url=' + orderInfo.scontract" target="_blank"><el-button size="small">download</el-button></a></span>
        </el-form-item>
        <el-form-item label="Payment file：">
          <span><a v-if="orderInfo.paymentFile" :href="downloadUrl + '?url=' + orderInfo.paymentFile" target="_blank"><el-button size="small">download</el-button></a></span>
        </el-form-item>
        <el-form-item label="Shipped file：">
          <span><a v-if="orderInfo.shippedFile" :href="downloadUrl + '?url=' + orderInfo.shippedFile" target="_blank"><el-button size="small">download</el-button></a></span>
        </el-form-item>
        <el-form-item label="Receipt file：">
          <span><a v-if="orderInfo.receiptFile" :href="downloadUrl + '?url=' + orderInfo.receiptFile" target="_blank"><el-button size="small">download</el-button></a></span>
        </el-form-item>
      </el-form>
      <h1>Action information</h1>
      <!-- 确认订单 -->
      <el-form label-position="right" label-width="150px" style="width: 100%" v-if="orderInfo.state === '0'">
        <el-form-item label="Upload contract：">
          <el-upload class="upload-demo" :action="uploadFileUrl" :on-success="uploadContractSuccess" :show-file-list="false">
            <el-button size="small" type="primary">upload</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="Update State：" v-if="orderInfo.sconfirm == '0'">
          <span><el-button size="small" type="success" @click="submitForm('1')">Confirm order</el-button></span>
        </el-form-item>
      </el-form>
      <!-- 确认支付 -->
      <el-form label-position="right" label-width="150px" style="width: 100%" v-else-if="orderInfo.state === '1'">
        <el-form-item label="Upload payment file：">
          <el-upload class="upload-demo" :action="uploadFileUrl" :on-success="uploadPaymentSuccess" :show-file-list="false">
            <el-button size="small" type="primary">upload</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="Update State：">
          <span><el-button size="small" type="success" @click="submitForm('2')">Confirm payment</el-button></span>
        </el-form-item>
      </el-form>
      <!-- 确认发货 -->
      <el-form label-position="right" label-width="150px" style="width: 100%" v-else-if="orderInfo.state === '2'">
        <el-form-item label="Upload shipped file：">
          <el-upload class="upload-demo" :action="uploadFileUrl" :on-success="uploadShippedSuccess" :show-file-list="false">
            <el-button size="small" type="primary">upload</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="Update State：">
          <span><el-button size="small" type="success" @click="submitForm('3')">Confirm shipped</el-button></span>
        </el-form-item>
      </el-form>
      <!-- 确认收货 -->
      <el-form label-position="right" label-width="150px" style="width: 100%" v-else-if="orderInfo.state === '3'">
        <el-form-item label="Upload receipt file：">
          <el-upload class="upload-demo" :action="uploadFileUrl" :on-success="uploadReceiptSuccess" :show-file-list="false">
            <el-button size="small" type="primary">upload</el-button>
          </el-upload>
        </el-form-item>
        <el-form-item label="Update State：">
          <span><el-button size="small" type="success" @click="submitForm('4')">Confirm receipt</el-button></span>
        </el-form-item>
      </el-form>
      <!-- 完成的订单显示 -->
      <el-form label-position="right" label-width="150px" style="width: 100%" v-else-if="orderInfo.state === '4'">
        <span style="color:red;">The order has been completed</span>
      </el-form>
    </el-col>
  </el-row>
</template>
<script>
  import { getOrderDetail, uploadFileUrl, downloadUrl, uploadContract, confirmOrder, changeOrderState, uploadOrderFile } from '../../api/api';
  export default {
    data() {
      return {
        orderInfo: {},
        productList: [],
        orderId: '0',
        uploadFileUrl: uploadFileUrl,
        downloadUrl: downloadUrl,
      };
    },
    created () {
      this.orderId = this.$route.params.id;
      this.loadData();
    },
    methods: {
      submitForm(parm) {
        // 确认订单
        if (parm === '1') {
          confirmOrder({orderId: this.orderId}).then(response => {
            if (response.code === $Codes.Ok) {
              this.$message({message: "Success", type: 'success'});
            } else {
              this.$message({message: response.message, type: 'error'});
            }
          });
        } else if (parm === '2' || parm === '3' || parm === '4') {
          changeOrderState({orderId: this.orderId, state: parm}).then(response => {
            if (response.code === $Codes.Ok) {
              this.$message({message: "Success", type: 'success'});
            } else {
              this.$message({message: response.message, type: 'error'});
            }
          });
        }
        this.loadData();
      },
      loadData() {
        let _this = this;
        getOrderDetail({orderId: this.orderId}).then(response => {
          if (response.code === $Codes.Ok) {
            _this.orderInfo = response.orderInfo;
            _this.productList = response.productList;
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
      // 上传合同
      uploadContractSuccess(res, file) {
        this.orderInfo.scontract = res.url;
        uploadContract({orderId: this.orderId, scontract: this.orderInfo.scontract}).then(response => {
          if (response.code === $Codes.Ok) {
            this.$message({message: response.message, type: 'success'});
          } else {
            this.$message({message: response.message, type: 'error'});
          }
        });
      },
      // 上传支付凭证
      uploadPaymentSuccess(res, file) {
        this.orderInfo.paymentFile = res.url;
        uploadOrderFile({orderId: this.orderId, file: this.orderInfo.paymentFile, field: 'paymentFile'}).then(response => {
          if (response.code === $Codes.Ok) {
            this.$message({message: response.message, type: 'success'});
          } else {
            this.$message({message: response.message, type: 'error'});
          }
        });
      },
      // 上传发货凭证
      uploadShippedSuccess(res, file) {
        this.orderInfo.shippedFile = res.url;
        uploadOrderFile({orderId: this.orderId, file: this.orderInfo.shippedFile, field: 'shippedFile'}).then(response => {
          if (response.code === $Codes.Ok) {
            this.$message({message: response.message, type: 'success'});
          } else {
            this.$message({message: response.message, type: 'error'});
          }
        });
      },
      // 上传收货凭证
      uploadReceiptSuccess(res, file) {
        this.orderInfo.receiptFile = res.url;
        uploadOrderFile({orderId: this.orderId, file: this.orderInfo.receiptFile, field: 'receiptFile'}).then(response => {
          if (response.code === $Codes.Ok) {
            this.$message({message: response.message, type: 'success'});
          } else {
            this.$message({message: response.message, type: 'error'});
          }
        });
      },
    }
  }
</script>

<style>
  
</style>