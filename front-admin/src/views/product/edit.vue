<template>
  <section class="goods-form-section">
    <el-form :model="ruleForm" :rules="rules" ref="ruleForm" label-width="100px">

      <h1>商品基本信息：</h1>

      <el-form-item label="销售标题" prop="title">
        <el-input v-model="ruleForm.title"></el-input>
      </el-form-item>
      <el-form-item label="商品名称" prop="enName">
        <el-input v-model="ruleForm.enName"></el-input>
      </el-form-item>
      <el-form-item label="别名" prop="enAlias">
        <el-input v-model="ruleForm.enAlias"></el-input>
      </el-form-item>
      <el-form-item label="商品分类">
        <el-cascader expand-trigger="hover" :options="categories" v-model="ruleForm.categoryList"></el-cascader>
      </el-form-item>
      <el-form-item label="产地">
        <el-cascader expand-trigger="hover" :options="areas" v-model="ruleForm.placeList"></el-cascader>
      </el-form-item>
      <el-form-item label="品牌">
        <el-cascader expand-trigger="hover" :options="brands" v-model="ruleForm.brandId"></el-cascader>
      </el-form-item>
      <el-form-item label="生产商">
        <el-cascader expand-trigger="hover" :options="producers" v-model="ruleForm.producerId"></el-cascader>
      </el-form-item>
      <el-form-item label="货物所在地">
        <el-cascader expand-trigger="hover" :options="areas" v-model="ruleForm.seatList"></el-cascader>
      </el-form-item>

      <h1>关键指标：</h1>
      <el-form-item v-for="item in Indicators" :label="item.name">
        <el-input v-model="ruleForm.keyIndex[item.cid]"></el-input>
      </el-form-item>
      
      <h1>货品规格：</h1>
      <sku ref="skuList" :dataList="ruleForm.skuList"></sku>

      <h1>产品属性：</h1>
      <el-form-item label="CAS号" prop="cas">
        <el-input v-model="ruleForm.cas"></el-input>
      </el-form-item>

      <el-form-item label="纯度" prop="format">
        <el-input v-model="ruleForm.format"></el-input>
      </el-form-item>

      <el-form-item label="性状" prop="character">
        <el-input v-model="ruleForm.character"></el-input>
      </el-form-item>

      <el-form-item label="质量等级">
        <el-select v-model="ruleForm.qualityGradeID" placeholder="">
          <el-option v-for="item in QualityGrades" :key="item.value" :label="item.label" :value="item.value"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="打包" prop="pack">
        <el-input v-model="ruleForm.pack"></el-input>
      </el-form-item>

      <el-form-item label="EINECS NO." prop="einecsNO">
        <el-input v-model="ruleForm.einecsNO"></el-input>
      </el-form-item>

      <el-form-item label="气味" prop="smell">
        <el-input v-model="ruleForm.smell"></el-input>
      </el-form-item>

      <el-form-item label="熔点" prop="melting">
        <el-input v-model="ruleForm.melting"></el-input>
      </el-form-item>

      <el-form-item label="沸点" prop="boiling">
        <el-input v-model="ruleForm.boiling"></el-input>
      </el-form-item>

      <el-form-item label="闪点" prop="flash">
        <el-input v-model="ruleForm.flash"></el-input>
      </el-form-item>

      <el-form-item label="PH值" prop="ph">
        <el-input v-model="ruleForm.ph"></el-input>
      </el-form-item>

      <el-form-item label="密度" prop="density">
        <el-input v-model="ruleForm.density"></el-input>
      </el-form-item>

      <el-form-item label="溶解度" prop="solubility">
        <el-input v-model="ruleForm.solubility"></el-input>
      </el-form-item>

      <el-form-item label="分子量" prop="formula">
        <el-input v-model="ruleForm.formula"></el-input>
      </el-form-item>

      <el-form-item label="经营模式">
        <el-select v-model="ruleForm.model" placeholder="">
          <el-option v-for="item in models" :key="item.value" :label="item.label" :value="item.value"></el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="MSDS">
        <el-upload class="avatar-uploader" :action="uploadUrl" :show-file-list="false" :on-success="uploadMsdsSuccess">
          <img v-if="ruleForm.msds" :src="ruleForm.msds" class="avatar">
          <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
      </el-form-item>

      <el-form-item label="TDS">
        <el-upload class="avatar-uploader" :action="uploadUrl" :show-file-list="false" :on-success="uploadTdsSuccess">
          <img v-if="ruleForm.tds" :src="ruleForm.tds" class="avatar">
          <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
      </el-form-item>

      <el-form-item label="CoA">
        <el-upload class="avatar-uploader" :action="uploadUrl" :show-file-list="false" :on-success="uploadCoaSuccess">
          <img v-if="ruleForm.coa" :src="ruleForm.coa" class="avatar">
          <i v-else class="el-icon-plus avatar-uploader-icon"></i>
        </el-upload>
      </el-form-item>

      <h1>描述信息：</h1>

      <el-form-item label="简介" prop="summary">
        <el-input type="textarea" rows="5" v-model="ruleForm.summary"></el-input>
      </el-form-item>

      <el-form-item label="用途" prop="purpose">
        <el-input v-model="ruleForm.purpose"></el-input>
      </el-form-item>

      <el-form-item label="存储条件" prop="condition">
        <el-input v-model="ruleForm.condition"></el-input>
      </el-form-item>

      <h1>商品图片 (至少上传一张图片)：</h1>
      <el-form-item label="商品图片">
        <el-upload :action="uploadUrl" :file-list="imagesFileList" list-type="picture-card" :on-preview="handlePictureCardPreview" :on-remove="handleRemove" :on-success="uploadImagesSuccess"><i class="el-icon-plus"></i></el-upload>
        <el-dialog v-model="dialogVisible" size="tiny">
          <img width="100%" :src="dialogImageUrl" alt="">
        </el-dialog>
      </el-form-item>

      <h1>文本框详情：</h1>
      <el-form-item label="" prop="detail">
        <UE :defaultMsg='ruleForm.detail' :config='editorConfig' id='product1' ref="ue"></UE>
      </el-form-item>

      <h1>Q&A</h1>
      <el-form-item label="" prop="faq">
        <UE :defaultMsg='ruleForm.faq' :config='editorConfig' id='product2' ref="ue2"></UE>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="submitForm('ruleForm')">立即提交</el-button>
      </el-form-item>
    </el-form>
  </section>
</template>
<script>
  import { addProduct, editProduct, getCategoryList, getBrandList, getAreaList, getProducerList, getCompanyModelList, getQualityGrade, getIndicatorList, getProductInfo, uploadUrl } from '../../api/api';
  import Sku from '../../components/Sku.vue';
  import UE from '../../components/ue/ue.vue';
  export default {
    components: {UE, Sku},
    data() {
      return {
        config: {
          initialFrameWidth: null,
          initialFrameHeight: 350
        },
        forbidden: true,
        redStar: false,
        categories: [],
        brands: [],
        areas: [],
        producers: [],
        seatList: [],
        models: [],
        QualityGrades: [],
        Indicators: [],
        id: '0',
        uploadUrl: uploadUrl,
        dialogVisible: false,
        dialogImageUrl: '',
        imagesFileList: [],
        ruleForm: {
          title: '',
          enName: '',
          enAlias: '',
          categoryList: [],
          producerId: [],
          brandId: [],
          placeList: [],
          seatList: [],
          keyIndex: {},
          cas: '',
          format: '',
          character: '',
          qualityGradeID: '',
          pack: '',
          einecsNO: '',
          smell: '',
          melting: '',
          boiling: '',
          flash: '',
          ph: '',
          density: '',
          solubility: '',
          formula: '',
          model: '',
          msds: '',
          tds: '',
          coa: '',
          summary: '',
          purpose: '',
          condition: '',
          images: [],
          skuList: [], // 货品规格列表
          detail: '',
        },
        rules: {
          title: [
            {required: true, message: '请输入销售标题', trigger: 'blur'},
            {min: 3, max: 250, message: '长度在 3 到 50 个字符', trigger: 'blur'}
          ],
          enName: [
            {required: true, message: '请输入商品名称', trigger: 'blur'},
            {min: 3, max: 250, message: '长度在 3 到 50 个字符', trigger: 'blur'}
          ],
        },
        editorConfig: {
          initialFrameWidth: null,
          initialFrameHeight: 350,
        },
      };
    },
    created () {
      this.optionsForGoods();
    },
    methods: {
      uploadMsdsSuccess(res, file) {
        this.ruleForm.msds = res.url;
      },
      uploadTdsSuccess(res, file) {
        this.ruleForm.tds = res.url;
      },
      uploadCoaSuccess(res, file) {
        this.ruleForm.coa = res.url;
      },
      handleRemove(file, fileList) {
        this.ruleForm.images.splice($.inArray(file.url, this.ruleForm.images), 1);
      },
      handlePictureCardPreview(file) {
        this.dialogImageUrl = file.url;
        this.dialogVisible = true;
      },
      uploadImagesSuccess(res, file, fileList) {
        this.ruleForm.images.push(res.url);
        file.url = res.url;
      },
      submitForm(formName) {
        this.ruleForm.detail = this.$refs.ue.getUEContent();
        this.ruleForm.faq = this.$refs.ue2.getUEContent();
        this.$refs[formName].validate((valid) => {
          if (valid) {
            let para = Object.assign({}, this.ruleForm);
            para.skuList = this.$refs.skuList.dataList;
            para.brandId = para.brandId[1];
            para.producerId = para.producerId[1];
            para.categoryList = para.categoryList.join(',');
            para.placeList = para.placeList.join(',');
            para.seatList = para.seatList.join(',');
            para.images = para.images.join(',');
            if (para.categoryList === '') {
              this.$message({message: "请选择分类", type: 'success'});
              return false;
            }
            if (para.placeList === '') {
              this.$message({message: "请选择产地", type: 'success'});
              return false;
            }
            if (para.brandId === undefined) {
              this.$message({message: "请选择品牌", type: 'success'});
              return false;
            }
            if (para.producerId === undefined) {
              this.$message({message: "请选择生产商", type: 'success'});
              return false;
            }
            if (para.seatList === '') {
              this.$message({message: "请选择货物所在地", type: 'success'});
              return false;
            }
            if (para.images === '') {
              this.$message({message: "至少上传一张商品图片", type: 'success'});
              return false;
            }
            let _this = this;
            if (this.id > 0) {
              para.id = this.id;
              console.log(para);
              editProduct(para).then(data => {
                if (data.code === $Codes.Ok) {
                  _this.$message({message: "编辑成功！", type: 'success'});
                } else {
                  this.$message({
                    message: data.message,
                    type: 'error'
                  });
                }
              });
            } else {
              addProduct(para).then(data => {
                if (data.code === $Codes.Ok) {
                  _this.$message({message: "添加成功！", type: 'success'});
                  _this.$router.push({ path: '/product/list' });
                } else {
                  this.$message({
                    message: data.message,
                    type: 'error'
                  });
                }
              });
            }
          } else {
            return false;
          }
        });
      },
      optionsForGoods: function() {
        let _this = this;
        this.id = this.$route.params.id;
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
        if ($Options.BrandList === undefined) { /* 品牌列表 */
          getBrandList().then(response => {
            if (response.code === $Codes.Ok) {
              $Options.BrandList = response.data;
              _this.brands = $Options.BrandList;
            }
          });
        } else {
          this.brands = $Options.BrandList;
        }
        if ($Options.ProducerList === undefined) { /* 品牌列表 */
          getProducerList().then(response => {
            if (response.code === $Codes.Ok) {
              $Options.ProducerList = response.data;
              _this.producers = $Options.ProducerList;
            }
          });
        } else {
          this.producers = $Options.ProducerList;
        }
        if ($Options.ModelList === undefined) { /* 品牌列表 */
          getCompanyModelList().then(response => {
            if (response.code === $Codes.Ok) {
              $Options.ModelList = response.data;
              _this.models = $Options.ModelList;
            }
          });
        } else {
          this.models = $Options.ModelList;
        }
        if ($Options.QualityGradeList === undefined) { /* 品牌列表 */
          getQualityGrade().then(response => {
            if (response.code === $Codes.Ok) {
              $Options.QualityGradeList = response.data;
              _this.QualityGrades = $Options.QualityGradeList;
            }
          });
        } else {
          this.QualityGrades = $Options.QualityGradeList;
        }
        if ($Options.IndicatorList === undefined) { /* 品牌列表 */
          getIndicatorList().then(response => {
            if (response.code === $Codes.Ok) {
              $Options.IndicatorList = response.data;
              _this.Indicators = $Options.IndicatorList;
            }
          });
        } else {
          this.Indicators = $Options.IndicatorList;
        }
        // 编辑商品页面信息
        if (this.id > 0) {
          let para = Object.assign({});
          para.id = this.id;
          getProductInfo(para).then(response => {
            if (response.code === $Codes.Ok) {
              _this.ruleForm = response.data;
              if (Array.prototype.isPrototypeOf(_this.ruleForm.keyIndex) && _this.ruleForm.keyIndex.length === 0) {
                _this.ruleForm.keyIndex = {};
              }
              let tempArr = [];
              for (let i in _this.ruleForm.images) {
                tempArr[i] = {name: _this.ruleForm.images[i], url: _this.ruleForm.images[i]}
              }
              _this.imagesFileList = tempArr;
              _this.$refs.ue.setUEContent(_this.ruleForm.detail);
              _this.$refs.ue2.setUEContent(_this.ruleForm.faq);
            }
          });
        }
      }
    }
  }
</script>
<style>
  .goods-form-section {
    padding: 10px;
    width: 100%;
  }
  .goods-form-section h1{
    margin-bottom: 22px;
    font-weight: bold;
  }
  .avatar-uploader .el-upload {
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
  }
  .avatar-uploader .el-upload:hover {
    border-color: #20a0ff;
  }
  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 178px;
    height: 178px;
    line-height: 178px;
    text-align: center;
  }
  .avatar {
    width: 178px;
    height: 178px;
    display: block;
  }
</style>
