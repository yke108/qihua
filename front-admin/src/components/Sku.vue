<template>
  <div>
    <template v-for="(sku, index) in dataList" :label="''" :prop="'products.' + index + '.name'">
      <div class="sku-grid">
      <el-form-item label="规格名称">
        <el-input v-model="sku.specification" style="width: 300px"></el-input>
      </el-form-item>
      <el-form-item label="包装规格">
        <el-input v-model="sku.packWeight" style="width: 150px" :disabled="packWeightDisabled(sku)"></el-input><span> Kg / </span>
        <el-select v-model="sku.weightUnit" placeholder="" style="width: 100px" @change="selectUnit(sku)">
          <el-option v-for="item in units" :key="item.value" :label="item.name" :value="item.id"></el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="参考价">
        <el-input v-model="sku.price" style="width: 150px"></el-input><span> $ / {{ getUnitName(sku.weightUnit) }}</span>
      </el-form-item>
      <el-form-item label="库存状态">
        <el-radio class="radio" v-model="sku.inventoryType" label="1">有货</el-radio>
        <el-radio class="radio" v-model="sku.inventoryType" label="2">缺货</el-radio>
      </el-form-item>
      <el-form-item label="设置库存" v-if="sku.inventoryType == 1">
        <el-radio class="radio" v-model="sku.inventory" label="1">不设置库存数</el-radio>
        <el-radio class="radio" v-model="sku.inventory" label="2">设置库存数</el-radio>
      </el-form-item>
      <el-form-item label="库存数量" v-if="sku.inventoryType == 1 && sku.inventory == 2">
        <el-input v-model="sku.inventoryNum" style="width: 300px"></el-input><span> {{ getUnitName(sku.weightUnit) }}</span>
      </el-form-item>
      <el-form-item label="最低起订量">
        <el-input v-model="sku.moq" style="width: 300px"></el-input><span> {{ getUnitName(sku.weightUnit) }}</span>
      </el-form-item>
      <el-form-item label="" v-if="getSkuCount() > 1">
        <el-button type="danger" @click.prevent="removeSku(index)">删除</el-button>
      </el-form-item>
      </div>
    </template>
    <el-form-item label=''>
      <el-button type="success" @click="addSku">新增货品规格</el-button>
    </el-form-item>
  </div>
</template>
<script>
  import { getWeightUnit } from '../api/api';
  export default {
    name: 'prlist',
    props: {
      dataList: {
        type: Array,
        default: []
      }
    },
    data() {
      return {
        product_default: {
          specification: ' ',
          price: '',
          weightUnit: '1',
          moq: '',
          inventoryType: '1',
          inventory: '1',
          inventoryNum: '',
          packWeight: 1000,
          skuId: 0,
        },
        units: [],
        unitNames: [],
      };
    },
    created () {
      if ($Options.UnitList === undefined) {
        let _this = this;
        getWeightUnit().then(response => {
          if (response.code === $Codes.Ok) {
            $Options.UnitList = response.data;
            _this.units = $Options.UnitList;
          }
        });
      } else {
        this.units = $Options.UnitList;
      }
      if (this.dataList.length === 0) {
        this.addSku();
      }
    },
    methods: {
      removeSku(index) {
        if (index !== -1) {
          this.dataList.splice(index, 1)
        }
      },
      addSku() {
        this.dataList.push(Object.assign({}, this.product_default));
      },
      getUnitName(id) {
        for (let i in this.units) {
          if (this.units[i].id === id) {
            return this.units[i].name;
          }
        }
        return '';
      },
      selectUnit(sku) {
        let temp = false;
        for (let i in this.units) {
          if (this.units[i].id === sku.weightUnit) {
            if (this.units[i].packWeight !== 0) {
              sku.packWeight = this.units[i].packWeight;
              temp = true;
              break;
            }
          }
        }
        if (!temp) {
          sku.packWeight = '';
        }
      },
      getSkuCount() {
        let num = 0;
        for (let i in this.dataList) {
          num = i;
        }
        return Number(num) + 1;
      },
      packWeightDisabled(sku) {
        if (sku.weightUnit < 4) {
          return true;
        } else {
          return false;
        }
      },
    },
  }
</script>
<style scoped>
.sku-grid {
  width: 500px;
  border: 1px solid #e5e5e5;
  padding: 10px; margin: 10px;
}
</style>
