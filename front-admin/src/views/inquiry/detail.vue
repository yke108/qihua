<template>
  <el-row>
    <el-col :span="12">
      <el-tabs value="first" type="card">

        <!-- 询盘信息 start -->
        <el-tab-pane label="Inquiry" name="first">
          <h1>Product information</h1><br>
          <el-table :data="inquiryInfo.productList" border style="width: 100%" class="tab">
            <el-table-column type="expand">
              <template scope="props">
                <el-form label-position="right" class="demo-table-expand" label-width="120px" style="width: 100%">
                  <el-form-item label="Product image："><span><img :src="props.row.image" width="100" height="100"></span></el-form-item>
                  <el-form-item label="Product title："><span>{{ props.row.title }}</span></el-form-item>
                  <el-form-item label="Unit price："><span>{{ props.row.inquiryPrice }}</span></el-form-item>
                  <el-form-item label="Unit："><span>{{ props.row.weightUnit }}</span></el-form-item>
                  <el-form-item label="Quantity："><span>ｘ{{ props.row.skuNumber }}</span></el-form-item>
                  <el-form-item label="Total price："><span style="color: red;">{{ props.row.inquiryTotalPrice }}</span></el-form-item>
                </el-form>
              </template>
            </el-table-column>
            <el-table-column prop="title" label="Title"></el-table-column>
            <el-table-column prop="inquiryPrice" label="Unit price""></el-table-column>
            <el-table-column prop="skuNumber" label="Quantity""></el-table-column>
          </el-table>
          <br>
          <h1>Inquiry information</h1>
          <el-form label-position="right" class="demo-table-expand" label-width="150px" style="width: 100%">
            <el-form-item label="Inquiry number："><span>{{inquiryInfo.inquirySn}}</span></el-form-item>
            <el-form-item label="Time quotes："><span>{{inquiryInfo.addTime}}</span></el-form-item>
            <el-form-item label="Mode of transport："><span>{{inquiryInfo.transportTip}}</span></el-form-item>
            <el-form-item label="Payment Terms："><span>{{inquiryInfo.provisionTip}}</span></el-form-item>
            <el-form-item label="Mode of payment："><span>{{inquiryInfo.paywayTip}}</span></el-form-item>
            <el-form-item label="Total price："><span>{{inquiryInfo.skuPrice}}</span></el-form-item>
            <el-form-item label="Freight："><span>{{inquiryInfo.freight}}</span></el-form-item>
            <el-form-item label="Order total price："><span style="color: red;">{{inquiryInfo.totalPrice}}</span></el-form-item>
          </el-form>
          <br>
          <h1>Message</h1><br>
          <p style="color: #8492A6;">{{inquiryInfo.message}}</p>
          <br>
          <h1>Attachment</h1><br>
          <p><a v-if="inquiryInfo.attachment" :href="inquiryInfo.attachment">Download attachment</a></p>
        </el-tab-pane>
        <!-- 询盘信息 end -->

        <!-- 报价单 start -->
        <el-tab-pane label="Quotes" name="second">
          <h1>Product information</h1><br>
          <el-table :data="quotesInfo.productList" border style="width: 100%">
            <el-table-column type="expand">
              <template scope="props">
                <el-form label-position="right" class="demo-table-expand" label-width="120px" style="width: 100%">
                  <el-form-item label="Product image："><span><img :src="props.row.image" width="100" height="100"></span></el-form-item>
                  <el-form-item label="Product title："><span>{{ props.row.title }}</span></el-form-item>
                  <el-form-item label="Unit price："><span>{{ props.row.inquiryPrice }}</span></el-form-item>
                  <el-form-item label="Unit："><span>{{ props.row.weightUnit }}</span></el-form-item>
                  <el-form-item label="Quantity："><span>ｘ{{ props.row.skuNumber }}</span></el-form-item>
                  <el-form-item label="Total price："><span style="color: red;">{{ props.row.inquiryTotalPrice }}</span></el-form-item>
                </el-form>
              </template>
            </el-table-column>
            <el-table-column prop="title" label="Title"></el-table-column>
            <el-table-column label="Unit price""><template scope="props"><el-input v-model="props.row.inquiryPrice" size="small"></el-input></template></el-table-column>
            <el-table-column label="Quantity""><template scope="props"><el-input v-model="props.row.skuNumber" size="small"></el-input></template></el-table-column>
          </el-table>
          <br>
          <h1>Quotes information</h1>
          <el-form label-position="right" class="demo-table-expand" label-width="150px" style="width: 100%">
            <el-form-item label="Mode of transport：">
              <el-select v-model="quotesInfo.transport" size="small">
                <el-option v-for="item in transports" :key="item.value" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="Payment Terms：">
              <el-select v-model="quotesInfo.provision" size="small">
                <el-option v-for="item in provisions" :key="item.value" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="Mode of payment：">
              <el-select v-model="quotesInfo.payway" size="small">
                <el-option v-for="item in payways" :key="item.value" :label="item.name" :value="item.id"></el-option>
              </el-select>
            </el-form-item>
            <el-form-item label="Total price："><span>{{ getSkuPrice() }}</span></el-form-item>
            <el-form-item label="Freight："><span><el-input v-model="quotesInfo.freight" size="small"></el-input></span></el-form-item>
            <el-form-item label="Order total price："><span style="color: red;">{{ getTotalPrice() }}</span></el-form-item>
            <el-form-item label=""><el-button type="primary" @click="submitForm">Submit Quotes</el-button></el-form-item>
          </el-form>
        </el-tab-pane>
        <!-- 报价单 end -->

        <!-- 历史报价 start -->
        <el-tab-pane label="History quotes" name="third">
          <h1>History quotes</h1><br>
          <el-table :data="quotesList" border style="width: 100%">
            <el-table-column type="expand">
              <template scope="props">

                <el-table :data="props.row.productList" border style="width: 100%" class="tab">
                  <el-table-column type="expand">
                    <template scope="props">
                      <el-form label-position="right" class="demo-table-expand" label-width="120px" style="width: 100%">
                        <el-form-item label="Product image："><span><img :src="props.row.image" width="100" height="100"></span></el-form-item>
                        <el-form-item label="Product title："><span>{{ props.row.title }}</span></el-form-item>
                        <el-form-item label="Unit price："><span>{{ props.row.inquiryPrice }}</span></el-form-item>
                        <el-form-item label="Unit："><span>{{ props.row.weightUnit }}</span></el-form-item>
                        <el-form-item label="Quantity："><span>ｘ{{ props.row.skuNumber }}</span></el-form-item>
                        <el-form-item label="Total price："><span style="color: red;">{{ props.row.inquiryTotalPrice }}</span></el-form-item>
                      </el-form>
                    </template>
                  </el-table-column>
                  <el-table-column prop="title" label="Title"></el-table-column>
                  <el-table-column prop="inquiryPrice" label="Unit price""></el-table-column>
                  <el-table-column prop="skuNumber" label="Quantity""></el-table-column>
                </el-table>

                <el-form label-position="right" class="demo-table-expand" label-width="150px" style="width: 100%">
                  <el-form-item label="Quotes number："><span>{{props.row.quotesSn}}</span></el-form-item>
                  <el-form-item label="Time quotes："><span>{{props.row.addTime}}</span></el-form-item>
                  <el-form-item label="Mode of transport："><span>{{props.row.transportTip}}</span></el-form-item>
                  <el-form-item label="Payment Terms："><span>{{props.row.provisionTip}}</span></el-form-item>
                  <el-form-item label="Mode of payment："><span>{{props.row.paywayTip}}</span></el-form-item>
                  <el-form-item label="Total price："><span>{{props.row.skuPrice}}</span></el-form-item>
                  <el-form-item label="Freight："><span>{{props.row.freight}}</span></el-form-item>
                  <el-form-item label="Order total price："><span style="color: red;">{{props.row.totalPrice}}</span></el-form-item>
                </el-form>
              </template>
            </el-table-column>
            <el-table-column prop="quotesSn" label="Quotes number"></el-table-column>
            <el-table-column prop="addTime" label="Quotes time""></el-table-column>
            <el-table-column prop="totalPrice" label="Order total price""></el-table-column>
          </el-table>
        </el-tab-pane>
        <!-- 历史报价 end -->
      </el-tabs>
    </el-col>

    <!-- 聊天 -->
    <el-col :span="12">
      <h1 style="margin: 20px">Send message</h1>
      <div id="chat-message">
        <div v-for="item in messageList">
          <div  v-if="item.type == '1'" class="chat-item">
          <p>{{item.username}} {{item.addTime}}</p>
          <p>{{item.content}}</p>
          </div>
          <div v-else class="chat-item1">
          <p>{{item.username}} {{item.addTime}}</p>
          <p>{{item.content}}</p>
          </div>
        </div>
      </div>
      <el-input type="textarea" placeholder="input content" v-model="content" style="margin: 10px; padding: 1px; width: 100%"></el-input>
      <el-button type="success" style="float: right;" @click="chat">Send</el-button>
    </el-col>
  </el-row>
  
</template>
<script>
  import { inquiryDetail, postQuotes, getMessageList, sendMessage } from '../../api/api';
  export default {
    data() {
      return {
        inquiryId: '',
        inquiryInfo: {},
        quotesInfo: {},
        quotesList: [],
        transports: {},
        payways: {},
        provisions: {},
        messageList: [],
        content: '',
      };
    },
    created () {
      this.inquiryId = this.$route.params.id;
      this.loadData();
      this.getMsgList();
      setInterval(this.getMsgList, 5000);
    },
    methods: {
      submitForm(formName) {
        this.$confirm('Make sure submit?', 'Tiips', {
          confirmButtonText: 'confirm',
          cancelButtonText: 'cancel',
          type: 'warning'
        }).then(() => {
          postQuotes(this.quotesInfo).then(response => {
            if (response.code === $Codes.Ok) {
              this.$message({message: response.message, type: 'success'});
              this.loadData();
            } else {
              this.$message({message: response.message, type: 'error'});
            }
          });
        });
      },
      loadData() {
        let _this = this;
        inquiryDetail({inquiryId: this.inquiryId}).then(response => {
          if (response.code === $Codes.Ok) {
            _this.inquiryInfo = response.inquiryInfo;
            _this.quotesInfo = response.quotesInfo;
            _this.quotesList = response.quotesList;
            _this.transports = response.transports;
            _this.payways = response.payways;
            _this.provisions = response.provisions;
          } else {
            this.$message({message: response.message, type: 'error'});
          }
        });
      },
      getSkuPrice() {
        var skuPrice = 0;
        for (var i in this.quotesInfo.productList) {
          skuPrice += parseFloat(this.quotesInfo.productList[i].inquiryPrice) * parseFloat(this.quotesInfo.productList[i].skuNumber);
        }
        return skuPrice.toFixed(2);
      },
      getTotalPrice() {
        var totalPrice = this.getSkuPrice();
        totalPrice = parseFloat(totalPrice) + parseFloat(this.quotesInfo.freight);
        return totalPrice.toFixed(2);
      },
      chat() {
        sendMessage({inquiryId: this.inquiryId, content: this.content}).then(response => {
          if (response.code === $Codes.Ok) {
            this.$message({message: response.message, type: 'success'});
            this.content = '';
            this.getMsgList();
          } else {
            this.$message({message: response.message, type: 'error'});
          }
        });
      },
      getMsgList() {
        let _this = this;
        getMessageList({inquiryId: this.inquiryId}).then(response => {
          if (response.code === $Codes.Ok) {
            _this.messageList = response.data;
            window.setTimeout(_this.scrollBottom, 500);
          }
        });
      },
      scrollBottom() {
        var chatBox = document.getElementById('chat-message');
        chatBox.scrollTop = chatBox.scrollHeight;
      },
    }
  }
</script>

<style>
  #chat-message {
    width: 100%; 
    height: 500px; 
    background-color: rgb(230,230,230);
    margin: 10px; 
    padding: 1px;
    overflow: auto;
  }
  #chat-message .chat-item {
    margin: 5px;
    background-color: #FFF;
    padding: 5px;
  }
  #chat-message .chat-item1 {
    margin: 5px;
    background-color: #C4E1FF;
    padding: 5px;
  }
  #chat-message p {
    margin-bottom: 5px; 
  }
  .el-row {
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }
  }
  .el-col {
    border-radius: 4px;
  }
  .bg-purple-dark {
    background: #99a9bf;
  }
  .bg-purple {
    background: #d3dce6;
  }
  .bg-purple-light {
    background: #e5e9f2;
  }
  .grid-content {
    border-radius: 4px;
    min-height: 36px;
  }
  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }
    .demo-table-expand {
    font-size: 0;
  }
  .demo-table-expand label {
    width: 90px;
    color: #99a9bf;
  }
  .demo-table-expand .el-form-item {
    margin-right: 0;
    margin-bottom: 0;
  }
</style>