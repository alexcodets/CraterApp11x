<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateExpensesSetting">
      <sw-input-group
        :label="$t('settings.customization.items.item_prefix')"
        :error="ItemPrefixError"
      >
        <sw-input
          v-model="items.item_prefix"
          :invalid="$v.items.item_prefix.$error"
          style="max-width: 30%"
          @input="$v.items.item_prefix.$touch()"
          @keyup="changeToUppercase()"
        />
      </sw-input-group>

      <div class="flex mt-3 mb-4">
        <div class="relative w-12">
          <sw-switch
            v-model="items.item_prefix_general"
            class="absolute"
            style="top: -20px"
          />
        </div>
        <div class="ml-4">
          <p class="p-0 mb-1 text-base leading-snug text-black box-title">
            {{ $t('settings.customization.items.apply_general_prefix') }}
          </p>
        </div>
      </div>

      <sw-button variant="primary" type="submit" class="mt-4">
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>

    <div class="flex flex-wrap justify-end mt-8 lg:flex-nowrap">
      <sw-button size="lg" variant="primary-outline" @click="addItemUnit">
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('settings.customization.items.add_item_unit') }}
      </sw-button>
    </div>

    <sw-table-component
      ref="table"
      variant="gray"
      :data="fetchData"
      :show-filter="false"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.customization.items.unit_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.customization.items.unit_name') }}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="false"
        :filterable="false"
        cell-class="action-dropdown"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.action') }}</span>
          <sw-dropdown>
            <dot-icon slot="activator" class="h-5 mr-3 text-primary-800" />

            <sw-dropdown-item @click="editItemUnit(row)">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeItemUnit(row.id)">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </div>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')

export default {
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
  },

  data() {
    return {
      items: {
        item_prefix: null,
        itme_prefix_general: null,
      },
      isLoading: false,
    }
  },

  computed: {
    ItemPrefixError() {
      if (!this.$v.items.item_prefix.$error) {
        return ''
      }

      if (!this.$v.items.item_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.items.item_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.items.item_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  validations: {
    items: {
      item_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  watch: {
    settings(val) {
      this.items.item_prefix = val ? val.item_prefix : '' //val.items.item_prefix como estaba
    },
  },

  components: {
    TrashIcon,
    PlusIcon,
    PencilIcon,
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('company', ['updateCompanySettings']),
    ...mapActions('item', ['deleteItemUnit', 'fetchItemUnits']),
     ...mapActions('item', ['setPrefix']),

    changeToUppercase() {
      this.items.item_prefix = this.items.item_prefix.toUpperCase()
      return true
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchItemUnits(data)

      return {
        data: response.data.units.data,
        pagination: {
          totalPages: response.data.units.last_page,
          currentPage: page,
          count: response.data.units.count,
        },
      }
    },

    async addItemUnit() {
      this.openModal({
        title: this.$t('settings.customization.items.add_item_unit'),
        componentName: 'ItemUnit',
        refreshData: this.$refs.table.refresh,
      })
    },

    async editItemUnit(data) {
      this.openModal({
        title: this.$t('settings.customization.items.edit_item_unit'),
        componentName: 'ItemUnit',
        id: data.id,
        data: data,
        refreshData: this.$refs.table.refresh,
      })
    },

    async removeItemUnit(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.customization.items.item_unit_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteItemUnit(id)

          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.items.deleted_message')
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](
            this.$t('settings.customization.items.already_in_use')
          )
        }
      })
    },

    async updateExpensesSetting() {
      this.$v.items.$touch()

      if (this.$v.items.$invalid) {
        return false
      }

      let data = {
        settings: {
          item_prefix: this.items.item_prefix,
        },
      }

      if (this.updateSetting(data)) {
        window.toastr['success'](
          this.$t('settings.customization.items.item_setting_updated_general')
        )
      }
    },

    async updateSetting(data) {
      this.isLoading = true
      let res = await this.updateCompanySettings(data)
      //console.log(this.items.item_prefix_general)
      if (this.items.item_prefix_general) {
       let res = await this.setPrefix(this.items)
      }

      if (res.data.success) {
        this.isLoading = false
        return true
      }

      return false
    },
  },
}
</script>
