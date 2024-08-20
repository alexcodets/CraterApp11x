<template>
  <tr class="box-border bg-white border border-gray-200 border-solid rounded-b">
    <td colspan="5" class="p-0 text-left align-top">
      <table class="w-full">
        <colgroup>
          <col style="width: 40%" />
          <col style="width: 30%" />
          <col style="width: 30%" />
        </colgroup>
        <tbody>
          <tr>
            <td class="px-5 py-4 text-left align-top">
              <div class="flex justify-start">
                <div class="flex items-center justify-center w-12 h-5 mt-2 text-gray-400 cursor-move handle">
                  <drag-icon />
                </div>
                <custom-did-select
                  ref="customDidSelect"
                  :custom-did="customDid"
                  @search="searchVal"
                  @select="onSelectCustomDid"
                  @unselect="unselectCustomDid"
                  @onSelectCustomDid="isSelected = true"
                />
              </div>
            </td>
            <td class="px-5 py-4 text-center">
              <div class="items-center text-sm">
                <span>
                    <div>{{ customDid.category }}</div>
                </span>
              </div>
            </td>
            <td class="px-5 py-4 text-right align-top">
              <div class="flex items-center justify-end text-sm">
                <span>
                  <div>{{ customDid.rate_per_minute }}</div>
                </span>

                <div class="flex items-center justify-center w-6 h-10 mx-2 cursor-pointer">
                  <trash-icon
                    v-if="showRemoveItemIcon"
                    class="h-5 text-gray-700"
                    @click="removeCustomDid"
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
import CustomDidSelect from "./CustomDidSelect";
import { TrashIcon, ViewGridIcon, ChevronDownIcon } from '@vue-hero-icons/solid'
import DragIcon from '@/components/icon/DragIcon'

export default {
  components: {
    CustomDidSelect,
    TrashIcon,
    ViewGridIcon,
    ChevronDownIcon,
    DragIcon,
  },
  props: {
    customDidData: {
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
    customDidGroup: {
      type: Array,
      default: null,
    },
  },
  data() {
    return {
      customDidSelect : null,
      customDid: { ...this.customDidData },
      isSelected: false,
    }
  },
  computed: {
    ...mapGetters('didtollfree', ['tollfree']),

    ...mapGetters('modal', ['modalActive']),

    showRemoveItemIcon() {
      return this.customDidGroup.length !== 1;
    },
  },
  watch: {
    customDid: {
      handler: 'updateCustomDid',
      deep: true,
    },
    modalActive(val) {
      if (!val) {
        this.isSelected = false
      }
    },
  },
  created() {
    window.hub.$on('newCustomDid', (val) => {
      if (!this.customDid.custom_did_id && this.modalActive && this.isSelected) {
        this.onSelectCustomDid(val)
      }
    })
  },
  methods: {

    searchVal(val) {
      this.customDid.prefijo = val
    },

    unselectCustomDid() {
      this.customDid = {
        id: this.customDid.id,
        custom_did_id: null,
        custom_did_group_id: null,
        prefijo: null,
        category: null
      }
      this.$nextTick(() => {
        this.$refs.customDidSelect.$refs.baseSelect.$refs.search.focus()
      })
    },

    onSelectCustomDid(customDid) {
      this.customDid.prefijo = customDid.prefijo
      this.customDid.rate_per_minute = customDid.rate_per_minute
      this.customDid.custom_did_id = customDid.id
      this.customDid.category = customDid.category_name

      this.$emit('checkExists', this.index, customDid)
    },

    updateCustomDid() {
      this.$emit('update', {
        index: this.index,
        customDid: {
          ...this.customDid
        },
      })
    },

    removeCustomDid() {
      this.$emit('remove', this.index)
    },

  }

}
</script>

<style scoped>

</style>