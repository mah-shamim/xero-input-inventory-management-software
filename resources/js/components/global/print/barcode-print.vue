<template>
  <div class="row" id="barcodePrint">
    <div class="col-md-3" v-for="n in items" md="4" :key="n">
      <barcode :height="vHeight" width="1" :value="printValue"></barcode>
    </div>
  </div>
</template>
<script>
export default {
  name: "barcode-print",
  props: {
    numberOfPrint: {
      required: true,
      default: "0",
    },
    printValue: {
      type: String,
      required: true,
      default: "",
    },
    height: {
      default: "50",
    },
  },
  computed: {
    items() {
      if (this.numberOfPrint) {
        let val = Number(this.numberOfPrint);
        return parseInt(val);
      }
    },
    vHeight() {
      if (this.height) {
        let val = Number(this.height);
        return parseInt(val);
      }
    },
  },
};
</script>
<style></style>