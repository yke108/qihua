<template>
  <Inquiry ref="ref1" :state="state"></Inquiry>
</template>
<script type="text/ecmascript-6">
  import Inquiry from '../../components/Inquiry.vue';
  export default {
    components: {Inquiry},
    data () {
      return {
        state: '2'
      };
    },
  };
</script>