<template>
  <div class="hello">
    <tree-grid :columns="columns" :tree-structure="true" :data-source="dataSource"></tree-grid>
  </div>
</template>

<script>
import TreeGrid from '../../components/TreeGrid'
import {getBrandList} from '../../api/api';
export default {
  components: {TreeGrid},
  name: 'hello',
  data () {
    return {
      columns: [
        {
          text: '品牌名称',
          dataIndex: 'label'
        }
      ],
      dataSource: []
    }
  },
  created () {
    if ($Options.BrandList === undefined) {
      let _this = this;
      getBrandList().then(response => {
        if (response.code === $Codes.Ok) {
          $Options.BrandList = response.data;
          _this.dataSource = $Options.BrandList;
        }
      });
    } else {
      this.dataSource = $Options.BrandList;
    }
  }
}
</script>

<style scoped>
</style>
