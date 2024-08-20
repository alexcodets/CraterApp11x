<template>
  <div class="flex-1 text-sm">
    <div
      v-if="prefix.prefix_id"
      class="relative flex items-center h-10 pl-2 bg-gray-100 border border-gray-200 border-solid rounded"
    >
      {{ prefix.name }}

      <span
        class="absolute text-gray-400 cursor-pointer"
        style="top: 8px; right: 10px"
        @click="unselectPrefix"
      >
        <x-circle-icon class="h-5"/>
      </span>
    </div>
    <sw-select
      v-else
      ref="baseSelect"
      v-model="prefixSelect"
      :options="internacionals"
      :loading="loading"
      :show-labels="false"
      :preserve-search="true"
      :initial-search="prefix.name"
      :invalid="invalid"
      :placeholder="$t('corePbx.prefix_groups.select_a_prefix_name')"
      label="name"
      class="multi-select-item"
      @value="onTextChange"
      @select="onSelect"
    >
      <div slot="afterList">
        <button
          type="button"
          class="flex items-center justify-center w-full p-3 bg-gray-200 border-none outline-none"
          @click="openDestinationModal"
        >
          <shopping-cart-icon
            class="h-5 mr-2 -ml-2 text-center text-primary-400"
          />
          <label class="ml-2 text-sm leading-none text-primary-400">
            {{ $t('corePbx.prefix_groups.add_new_prefix') }}
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
    prefix: {
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
      prefixSelect: null,
      loading: false,
    }
  },
  computed: {
    ...mapGetters('internacionalrate', ['internacionals']),
  },
  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('internacionalrate', ['fetchInternacionals']),

    async searchPrefix(search) {
      let data = {
        search,
        filter: {
          name: search,
        },
        orderByField: '',
        orderBy: '',
        page: 1,
      }

      if (this.prefix) {
        data.prefix_id = this.prefix.prefix_id
      }

      this.loading = true
      await this.fetchInternacionals(data)
      this.loading = false
    },

    unselectPrefix() {
      this.prefixSelect = null
      this.$emit('unselect')
    },

    onTextChange(val) {
      this.searchPrefix(val)
      this.$emit('search', val)
    },

    openDestinationModal() {
      this.$emit('onSelectPrefix')
      this.openModal({
        title: this.$t('corePbx.internacional.add_custom_destination'),
        componentName: 'CustomDestinationModal',
        data: {},
      })
    },

    onSelect(val) {
      this.$emit('select', val)
      this.fetchInternacionals()
    },

  },
}
</script>

<style scoped>

</style>