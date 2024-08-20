<template>
  <div class="flex items-center justify-between mb-0">
    <div class="flex items-center ml-4" style="flex: 6">
      <h6 class="pr-2 mb-0 text-" align="left" v-text="taxData.name"/>
      <br />
    </div>

    <div
      v-if="taxData.tax * 100 > 0.00"
      class="text-sm text-right mr-3"
      style="flex: 3"
      v-html="$utils.formatMoney(taxData.tax * 100, currency)"
    />
    <div
      v-else
      class="text-sm text-right mr-3"
      style="flex: 3"     
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
