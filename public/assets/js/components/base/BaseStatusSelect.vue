<template>
  <div class="item-selector">
    <sw-select
      ref="baseSelect"
      v-model="statusSelect"
      :options="status"
      :show-labels="false"
      :placeholder="$t('customers.type_or_click')"
      label="text"
      class="multi-select-item mt-2"
      @value="onTextChange"
      @select="(val) => $emit('select', val)"
      @remove="deselectStatus"
    />
  </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
  data() {
    return {
      statusSelect: null,
      status: [
        {
          value: 'A',
          text: 'Active',
        },
        {
          value: 'I',
          text: 'Inactive',
        },
        {
          value: 'F',
          text: 'Archive',
        },
      ],
      loading: false,
    }
  },

  created() {
    this.fetchCustomers()
  },

  methods: {
    ...mapActions('customer', ['fetchCustomers']),
    async searchCustomers(search) {
      this.loading = true

      await this.fetchCustomers({ search })

      this.loading = false
    },
    onTextChange(val) {
      this.searchCustomers(val)
    },
    checkCustomers(val) {
      if (!this.customers.length) {
        this.fetchCustomers()
      }
    },
    deselectStatus() {
      this.statusSelect = null
      this.$emit('deselect')
    },
  },
}
</script>
