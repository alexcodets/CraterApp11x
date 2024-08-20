<template>
  <div>
    <form action="" class="mt-6" @submit.prevent="updateSetting">
      <sw-input-group
        :label="$t('corePbx.customization.did_prefix')"
        :error="ExpensePrefixError">
        <sw-input
          v-model="expenses.did_pbx_prefix"
          :invalid="$v.expenses.did_pbx_prefix.$error"
          style="max-width: 30%"
          @input="$v.expenses.did_pbx_prefix.$touch()"
          @keyup="changeToUppercase('EXPENSE')" />
      </sw-input-group>
      
        <div class="flex mt-3 mb-4">
            <div class="relative w-12">
                <sw-switch
                v-model="expenses.did_prefix_general"
                class="absolute"
                style="top: -20px"/>
            </div>
            <div class="ml-4">
                <p class="p-0 mb-1 text-base leading-snug text-black box-title">
                  {{ $t('settings.customization.items.apply_general_prefix') }}
                </p>
            </div>
        </div>

      <sw-button
        :loading="isLoading"
        :disabled="isLoading"
        variant="primary"
        type="submit"
        class="mt-4"
        v-if="permission.update"
        >
        <save-icon v-if="!isLoading" class="mr-2" />
        {{ $t('settings.customization.save') }}
      </sw-button>
    </form>
    <h1 class="mt-4">{{$t('corePbx.customization.custom_did_categories') }}</h1>
    <sw-divider class="mt-6 mb-8" />
    <!-- <sw-divider class="mt-6 mb-8" /> -->
    <div class="flex flex-wrap justify-end mt-8 lg:flex-nowrap">
      <sw-button size="lg" variant="primary-outline" @click="addItemUnit" v-if="permission.create">
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('corePbx.customization.add_category_did') }}
      </sw-button>
    </div>
    <base-loader v-if="isLoadingDeleteCategoria" />
    <sw-table-component
      ref="table"
      variant="gray"
      :data="fetchData"
      :show-filter="false"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('corePbx.customization.name')"
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

            <sw-dropdown-item @click="editCategory(row)" v-if="permission.update">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeCategory(row.id)" v-if="permission.delete">
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
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import {
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    PencilIcon,
    TrashIcon,
    PlusIcon,
  },
  props: {
    settings: {
      type: Object,
      require: true,
      default: false,
    },
    permission: {
      type: Object,
      require: true,
    },
  },

  data() {
    return {
      expenses: {
        did_pbx_prefix: null,
        did_prefix_general: false,
      },
      isLoading: false,
      isLoadingDeleteCategoria: false,
    }
  },

  computed: {
    ExpensePrefixError() {
      if (!this.$v.expenses.did_pbx_prefix.$error) {
        return ''
      }

      if (!this.$v.expenses.did_pbx_prefix.required) {
        return this.$t('validation.required')
      }

      if (!this.$v.expenses.did_pbx_prefix.maxLength) {
        return this.$t('validation.prefix_maxlength')
      }

      if (!this.$v.expenses.did_pbx_prefix.alpha) {
        return this.$t('validation.characters_only')
      }
    },
  },

  validations: {
    expenses: {
      did_pbx_prefix: {
        required,
        maxLength: maxLength(5),
        alpha,
      },
    },
  },

  watch: {
    settings(val) { 
      this.expenses.did_pbx_prefix = val ? val.did_pbx_prefix : ''
    },
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('company', ['updateCompanySettings']),
    ...mapActions('categoriesTollF', ['fetchCategories','deleteCategory']),

    changeToUppercase(currentTab) {
      if (currentTab === 'EXPENSE') {
        this.expenses.did_pbx_prefix = this.expenses.did_pbx_prefix.toUpperCase()
        return true
      }
    },
    async updateSetting(){
      try {
        this.$v.expenses.$touch()
        if (this.$v.expenses.$invalid) return false

        this.isLoading = true
        const data = {
          settings: {
            did_pbx_prefix: this.expenses.did_pbx_prefix,
            did_prefix_general: this.expenses.did_prefix_general,
          },
        }
        await this.updateCompanySettings(data)
        window.toastr['success'](
          this.$t('corePbx.customization.did_prefix_updated')
        )

      } catch (error) {
        window.toastr['error'](
          this.$t('corePbx.customization.did_prefix_update_error')
        )
      } finally {
        this.isLoading = false
      }
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchCategories(data)
      return {
        data: response.data.categories.data,
        pagination: {
          totalPages: response.data.categories.last_page,
          currentPage: page,
          count: response.data.categories.total,
        },
      }
    },

    async addItemUnit() {
      this.openModal({
        title: this.$t('settings.expense_category.add_category'),
        componentName: 'CategoryModalTollFree',
        refreshData: this.$refs.table.refresh,
      })
    },

    async editCategory(data) {
      //console.log(data);
      this.openModal({
        title: this.$t('settings.expense_category.edit_category'),
        componentName: 'CategoryModalTollFree',
        id: data.id,
        data: data,
        refreshData: this.$refs.table.refresh,
      })
    },

    async removeCategory(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('corePbx.customization.category_confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {


          try {
            this.isLoadingDeleteCategoria = true
            await this.deleteCategory(id)
            window.toastr['success'](
              this.$t('corePbx.customization.category_deleted')
            )
            this.$refs.table.refresh()
          } catch (error) {
            window.toastr['error'](
              this.$t('corePbx.customization.category_delete_error')
            )
          } finally {
            this.isLoadingDeleteCategoria = false
          }
        }
      })
    },

  },
}
</script>