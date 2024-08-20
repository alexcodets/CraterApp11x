<template>
  <div class="flex-1 text-sm">
    <div
      v-if="customDid.custom_did_id"
      class="relative flex items-center h-10 pl-2 bg-gray-100 border border-gray-200 border-solid rounded"
    >
      {{ customDid.prefijo }}

      <span
        class="absolute text-gray-400 cursor-pointer"
        style="top: 8px; right: 10px"
        @click="unselectCustomDId"
      >
        <x-circle-icon class="h-5"/>
      </span>
    </div>
    <sw-select
      v-else
      ref="baseSelect"
      v-model="customDidSelect"
      :options="tollfree"
      :loading="loading"
      :show-labels="false"
      :preserve-search="true"
      :initial-search="customDid.prefijo"
      :invalid="invalid"
      :placeholder="$t('corePbx.custom_did_groups.select_a_custom_did')"
      label="prefijo"
      class="multi-select-item"
      @value="onTextChange"
      @select="onSelect"
    >
      <div slot="afterList">
        <button
          type="button"
          class="flex items-center justify-center w-full p-3 bg-gray-200 border-none outline-none"
          @click="openCustomModal"
        >
          <shopping-cart-icon
            class="h-5 mr-2 -ml-2 text-center text-primary-400"
          />
          <label class="ml-2 text-sm leading-none text-primary-400">
            {{ $t('corePbx.custom_did_groups.add_new_custom_did') }}
          </label>
        </button>
      </div>
    </sw-select>
  </div>
</template>

<script>

import { mapActions, mapGetters } from 'vuex'
import { XCircleIcon, ShoppingCartIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    XCircleIcon,
    ShoppingCartIcon,
  },
  props: {
    customDid: {
      type: Object,
      required: true,
    },
    invalid: {
      type: Boolean,
      required: false,
      default: false,
    },
  },
  data() {
    return {
      customDidSelect: null,
      loading: false,
    }
  },
  computed: {
    ...mapGetters('didtollfree', ['tollfree']),
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('didtollfree', ['fetchDIDTOLLFREEs']),

    async searchCustomDid(search) {
      let data = {
        search,
        filter: {
          prefijo: search,
        },
        orderByField: '',
        orderBy: '',
        page: 1,
      }

      if (this.customDid) {
        data.custom_did_id = this.customDid.custom_did_id
      }

      this.loading = true
      await this.fetchDIDTOLLFREEs(data)
      this.loading = false
    },

    unselectCustomDId() {
      this.customDidSelect = null
      this.$emit('unselect')
    },

    onTextChange(val) {
      this.searchCustomDid(val)
      this.$emit('search', val)
    },

    openCustomModal() {
      this.$emit('onSelectCustomDid')
      this.openModal({
        title: this.$t('corePbx.didFree.add_custom_did'),
        componentName: 'CustomDidModal',
        data: {},
      })
    },

    onSelect(val) {
      this.$emit('select', val)
      this.fetchDIDTOLLFREEs()
    },

  }
}
</script>

<style scoped>

</style>