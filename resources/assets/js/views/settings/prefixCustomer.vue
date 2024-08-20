<template>
  <div class="relative">
    <base-loader v-if="isRequestOnGoing" :show-bg-overlay="true" />
    <sw-card>
      <sw-tabs class="p-2">
        <sw-tab-item :title="'Prefix'">
          <customization-tab :settings="settings"/>
        </sw-tab-item>
      </sw-tabs>
    </sw-card>
  </div>
</template>

<script>
import CustomizationTab from './customization-tabs/CustomizationTab'

export default {
  data() {
    return {
      settings: {},
      isRequestOnGoing: false,
    }
  },
  components: {
    CustomizationTab
  },
  created() {
    this.isRequestOnGoing = true
      this.isRequestOnGoing = false
  }
}
</script>
