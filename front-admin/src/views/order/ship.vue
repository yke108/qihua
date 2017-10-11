<template>
  <Order ref="ref1" :state="state"></Order>
</template>
<script type="text/ecmascript-6">
  import Order from '../../components/Order.vue';
  export default {
    components: {Order},
    data () {
      return {
        state: '3'
      };
    },
  };
</script>