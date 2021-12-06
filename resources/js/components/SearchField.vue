// resources/assets/js/components/SearchField.vue

<template>
  <div class="input-group">
    <div class="input-group-prepend">
      <span class="input-group-text"><i class="fa fa-search"></i></span>
    </div>
    <input
      type="text"
      class="form-control"
      placeholder="Search..."
      v-model="value"
    />
  </div>
</template>

<script>
export default {
  data() {
    return {
      value: ""
    };
  },
  watch: {
    value(value) {
      this.$emit("onchange", value);
    }
  }
};
</script>