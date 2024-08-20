<template>
  <div class="flex items-center justify-between mb-3">
    <div class="flex items-center" style="flex: 4">
      <label class="pr-2 mb-0 text-" align="right" v-text="taxData.name">
      </label>
      <br />
    </div>
    <div
      class="text-sm text-right"
      style="flex: 3"
      v-html="$utils.formatMoney(taxData.tax * 100, currency)"
    />
    <div
      class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer"
    />
  </div>
</template>
<script>
export default {
  props: {
    taxData: {
      type: Object,
      required: true,
    },
    currency: {
      type: [Object, String],
      required: true,
    },
  },
}
</script>
