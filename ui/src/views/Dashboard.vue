<template>
  <div>
    <h1>{{$t('dashboard.title')}}</h1>
    <div class="col-lg-12">
      
      <!-- <div v-show="!configLoaded" class="spinner spinner-lg"></div> -->

    </div>
  </div>
</template>

<script>
export default {
  name: "Dashboard",
  props: {
    msg: String
  },
  mounted() {
  },
  data() {
    return {
      configLoaded: false,
    };
  },
  methods: {
  }
};
</script>

<style>
</style>
