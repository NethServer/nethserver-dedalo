<template>
  <div>
    <h1>{{$t('settings.title')}}</h1>
    <div v-if="!configLoaded" class="spinner spinner-lg"></div>
    <div v-if="configLoaded">
      <form class="form-horizontal" v-on:submit.prevent="btSaveClick">
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "Settings",
  mounted() {
  },
  data() {
    return {
      configLoaded: false,
    }
  },
  methods: {
  }
};
</script>

<style scoped>
</style>
