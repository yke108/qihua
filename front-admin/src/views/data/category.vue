<template>
  <div class="hello">
    <tree-grid :columns="columns" :tree-structure="true" :data-source="dataSource"></tree-grid>
  </div>
</template>

<script>
import TreeGrid from '../../components/TreeGrid'
import {DataCategory} from '../../api/api';
export default {
  components: {TreeGrid},
  name: 'hello',
  data () {
    return {
      columns: [
        {
          text: '类别名称',
          dataIndex: 'label'
        }
      ],
      dataSource: []
    }
  },
  created () {
    if ($Options.CategoryList === undefined) {
      let _this = this;
      DataCategory().then(response => {
        if (response.code === $Codes.Ok) {
          $Options.CategoryList = response.data;
          _this.dataSource = $Options.CategoryList;
        }
      });
    } else {
      this.dataSource = $Options.CategoryList;
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
</style>
