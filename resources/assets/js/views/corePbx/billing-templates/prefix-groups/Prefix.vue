<template>
  <tr class="box-border bg-white border border-gray-200 border-solid rounded-b">
    <td colspan="7" class="p-0 text-left align-top">
      <table class="w-full">
        <colgroup>
          <col style="width: 15%" />
          <col style="width: 25%" />
          <col style="width: 15%" />
          <col style="width: 15%" />
          <col style="width: 15%" />
          <col style="width: 15%" />
        </colgroup>
        <tbody>
          <tr>
            <!-- TYPE PREFIX -->
            <td class="px-5 py-4 text-center">
              <div class="flex items-center justify-start text-sm">
                <div class="w-5 h-5 mt-2 text-gray-400 cursor-move handle mr-2">
                  <drag-icon />
                </div>
                <span class="whitespace-nowrap"> {{ showFromTo(prefix.typecustom) }}</span>
              </div>
            </td>
            <td class="px-5 py-4 text-left align-top">
              <div class="flex justify-start">
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
            <!-- FROM / TO -->
            <td class="px-5 py-4 text-center">
              <div class="items-center text-sm">
                <span>
                    <div>{{ `${prefix.from || '' }${prefix.to ? ' / ':''}${prefix.to || ''}` }}</div>
                </span>
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
                  <div v-if="prefix.rate_per_minute != null">
                    {{ defaultCurrency.symbol + ' ' + prefix.rate_per_minute }}
                  </div>                 
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

    ...mapGetters('company', ['defaultCurrency']),

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
        typecustom: null,
        from: null,
        to: null,
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
      this.prefix.typecustom = prefix.typecustom
      this.prefix.from = prefix.from
      this.prefix.to = prefix.to

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
    showFromTo(fromTo){
      if( fromTo == 'P' ) return 'Prefix'
      else if(fromTo == 'FT') return 'From / To'
      else return ''
    }
  }
}
</script>

<style scoped>

</style>