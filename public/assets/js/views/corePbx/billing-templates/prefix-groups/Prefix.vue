<template>
  <tr class="box-border bg-white border border-gray-200 border-solid rounded-b">
    <td colspan="5" class="p-0 text-left align-top">
      <table class="w-full">
        <colgroup>
          <col style="width: 30%" />
          <col style="width: 20%" />
          <col style="width: 30%" />
          <col style="width: 20%" />
        </colgroup>
        <tbody>
          <tr>
            <td class="px-5 py-4 text-left align-top">
              <div class="flex justify-start">
                <div class="flex items-center justify-center w-12 h-5 mt-2 text-gray-400 cursor-move handle">
                  <drag-icon />
                </div>
                <prefix-select
                  ref="prefixSelect"
                  :prefix="prefix"
                  @search="searchVal"
                  @select="onSelectPrefix"
                  @unselect="unselectPrefix"
                  @onSelectPrefix="isSelected = true"
                />
              </div>
            </td>
            <td class="px-5 py-4 text-center">
              <div class="items-center text-sm">
                <span>
                    <div>{{ prefix.prefix }}</div>
                </span>
              </div>
            </td>
            <td class="px-5 py-4 text-center">
              <div class="items-center text-sm">
                <span>
                    <div>{{ prefix.country_name }}</div>
                </span>
              </div>
            </td>
            <td class="px-5 py-4 text-right align-top">
              <div class="flex items-center justify-end text-sm">
                <span>
                  <div>{{ prefix.rate_per_minute }}</div>
                </span>

                <div class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer">
                  <trash-icon
                    v-if="showRemoveItemIcon"
                    class="h-5 text-gray-700"
                    @click="removePrefix"
                  />
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </td>
  </tr>
</template>

<script>

import { mapActions, mapGetters } from 'vuex'
import PrefixSelect from "./PrefixSelect";
import { TrashIcon, ViewGridIcon, ChevronDownIcon } from '@vue-hero-icons/solid'
import DragIcon from '@/components/icon/DragIcon'

export default {
  components: {
    PrefixSelect,
    TrashIcon,
    ViewGridIcon,
    ChevronDownIcon,
    DragIcon,
  },
  props: {
    prefixData: {
      type: Object,
      default: null,
    },
    index: {
      type: Number,
      default: null,
    },
    type: {
      type: String,
      default: '',
    },
    prefixGroup: {
      type: Array,
      default: null,
    },
  },
  data() {
    return {
      prefixSelect : null,
      prefix: { ...this.prefixData },
      isSelected: false,
    }
  },
  computed: {
    ...mapGetters('internacionalrate', ['internacionals']),

    ...mapGetters('modal', ['modalActive']),

    showRemoveItemIcon() {
      return this.prefixGroup.length !== 1;
    },
  },
  watch: {
    prefix: {
      handler: 'updatePrefix',
      deep: true,
    },
    modalActive(val) {
      if (!val) {
        this.isSelected = false
      }
    },
  },
  created() {
    window.hub.$on('newPrefix', (val) => {
      if (!this.prefix.prefix_id && this.modalActive && this.isSelected) {
        this.onSelectPrefix(val)
      }
    })
  },
  methods: {
    searchVal(val) {
      this.prefix.name = val
    },

    unselectPrefix() {
      this.prefix = {
        id: this.prefix.id,
        prefix_id: null,
        prefix_group_id: null,
        prefix: null,
        name: null,
        country_name: null
      }
      this.$nextTick(() => {
        this.$refs.prefixSelect.$refs.baseSelect.$refs.search.focus()
      })
    },

    onSelectPrefix(prefix) {
      this.prefix.prefix = prefix.prefix
      this.prefix.rate_per_minute = prefix.rate_per_minute
      this.prefix.prefix_id = prefix.id
      this.prefix.country_id = prefix.country_id
      this.prefix.country_name = prefix.countryName
      this.prefix.name = prefix.name

      this.$emit('checkExists', this.index, prefix)
    },

    updatePrefix() {
      this.$emit('update', {
        index: this.index,
        prefix: {
          ...this.prefix
        },
      })
    },

    removePrefix() {
      this.$emit('remove', this.index)
    },
  }
}
</script>

<style scoped>

</style>