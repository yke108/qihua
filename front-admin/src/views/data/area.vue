<template>
  <div class="hello">
    <tree-grid :columns="columns" :tree-structure="true" :data-source="dataSource"></tree-grid>
  </div>
</template>

<script>
import TreeGrid from '../../components/TreeGrid'
import {getAreaList} from '../../api/api';
export default {
  components: {TreeGrid},
  name: 'hello',
  data () {
    return {
      columns: [
        {
          text: '地区名称',
          dataIndex: 'label'
        }
      ],
      dataSource: []
    }
  },
  created () {
    if ($Options.AreaList === undefined) {
      let _this = this;
      getAreaList().then(response => {
        if (response.code === $Codes.Ok) {
          $Options.AreaList = response.data;
          _this.dataSource = $Options.AreaList;
        }
      });
    } else {
      this.dataSource = $Options.AreaList;
    }
  }
}
</script>

<style scoped>
</style>
