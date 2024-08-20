<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.tax_types.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.tax_types.description') }}
        </p>
      </div>
    <!-- </div> -->
    
      <div class="flex flex-wrap justify-between lg:flex-nowrap">
        <div class="mt-4 lg:mt-0 lg:ml-2">
          <sw-button size="lg" variant="primary-outline" @click="openTaxModal" v-if="permissionModule.create">
            <plus-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('settings.tax_types.add_new_tax') }}
          </sw-button>
        </div>

        <div class="mt-4 lg:mt-0 lg:ml-2">
          <sw-button size="lg" variant="primary-outline" @click="goToTaxCategories" v-if="permissionModule.create">
            <!-- <plus-icon class="w-6 h-6 mr-1 -ml-2" /> -->
            {{ $t('settings.tax_types.see_tax_categories_list') }}
          </sw-button>
        </div>
      </div>
    </div>

    <sw-table-component
      ref="table"
      :show-filter="false"
      :data="fetchData"
      table-class="table"
      variant="gray"
    >
      <sw-table-column
        :sortable="true"
        :label="$t('settings.tax_types.tax_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.tax_name') }}</span>
          <span class="mt-6">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.tax_types.compound_tax')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.compound_tax') }}</span>
          <sw-badge
            :bg-color="
              $utils.getBadgeStatusColor(row.compound_tax ? 'YES' : 'NO')
                .bgColor
            "
            :color="
              $utils.getBadgeStatusColor(row.compound_tax ? 'YES' : 'NO').color
            "
          >
            {{ row.compound_tax ? 'Yes' : 'No'.replace('_', ' ') }}
          </sw-badge>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.tax_types.percent')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.tax_types.percent') }}</span>
          {{ row.percent }} %
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
            <dot-icon slot="activator" />

            <sw-dropdown-item @click="editTax(row.id)" v-if="permissionModule.update">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removeTax(row.id)" v-if="permissionModule.delete">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>

    </sw-table-component>

    <sw-divider class="my-8" />

    <div class="mt-4 lg:mt-0 lg:ml-2">
        <sw-button size="lg" variant="primary-outline" @click="viewTaxAgencyList" v-if="permissionModule.read">
          <!-- <plus-icon class="w-6 h-6 mr-1 -ml-2" /> -->
          {{ $t('settings.tax_types.see_tax_agency_list') }}
        </sw-button>
      </div>

      <br>

    <div class="flex mt-2" v-if="permissionModule.update">
      <div class="relative w-12">
        <sw-switch
          v-model="formData.tax_per_item"
          class="absolute"
          style="top: -20px"
          @change="setTax"
        />
      </div>    

      <div class="ml-4" >
        <p class="p-0 mb-1 text-base leading-snug text-black box-title">
          {{ $t('settings.tax_types.tax_per_item') }}
        </p>

        <p
          class="p-0 m-0 text-xs leading-4 text-gray-500"
          style="max-width: 480px"
        >
          {{ $t('settings.tax_types.tax_setting_description') }}
        </p>
      </div>
    </div>
    
    <div class="flex mt-4" v-if="permissionModule.update && formData.retention_platform_active">
      <div class="relative w-12">
        <sw-switch
          v-model="formData.retention_active"
          class="absolute"
          style="top: -20px"
          @change="setTax"
        />
      </div>    

      <div class="ml-4">
        <p class="p-0 mb-1 text-base leading-snug text-black box-title">
          {{ $t('settings.tax_types.retention_active') }}
        </p>
      </div>
    </div>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
  },

  data() {
    return {
      formData: {
        tax_per_item: false,
        retention_active: false,
        retention_platform_active: false,
      },
      isRequestOnGoing: false,
      permissionModule:{
        create: false,
        delete: false,
        read: false,
        update: false
      },
    }
  },
  created(){
    this.permissionsUserModule()
  },
  computed: {
    ...mapGetters('taxType', ['taxTypes', 'getTaxTypeById']),
  },

  mounted() {
    this.getTaxSetting()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('taxType', [
      'indexLoadData',
      'deleteTaxType',
      'fetchTaxType',
      'fetchTaxTypes',
    ]),
    ...mapActions('company', ['fetchCompanySettings', 'updateCompanySettings']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchTaxTypes(data)

      return {
        data: response.data.taxTypes.data,
        pagination: {
          totalPages: response.data.taxTypes.last_page,
          currentPage: page,
          count: response.data.taxTypes.count,
        },
      }
    },

    async getTaxSetting() {
      let response = await this.fetchCompanySettings(['tax_per_item', 'retention_active', 'retention_platform_active'])
      if (response.data) {
        this.formData.tax_per_item = response.data.tax_per_item === 'YES'
        this.formData.retention_active = response.data.retention_active === 'YES'
        this.formData.retention_platform_active = response.data.retention_platform_active === 'YES'
      }
    },

    async setTax(val) {
      let data = {
        settings: {
          tax_per_item: this.formData.tax_per_item ? 'YES' : 'NO',
          retention_active: this.formData.retention_active ? 'YES' : 'NO',
        },
      }
      let response = await this.updateCompanySettings(data)
      if (response.data) {
        window.toastr['success'](this.$t('general.setting_updated'))
      }
    },

    async removeTax(id, index) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('settings.tax_types.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deleteTaxType(id)
          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.tax_types.deleted_message')
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](this.$t('settings.tax_types.already_in_use'))
        }
      })
    },

    openTaxModal() {
      this.openModal({
        title: this.$t('settings.tax_types.add_tax'),
        componentName: 'TaxTypeModal',
        refreshData: this.$refs.table.refresh,
      })
    },

    goToTaxCategories() {
      // pushear vista tax categories
      this.$router.push('/admin/settings/tax-categories');
    },
    viewTaxAgencyList() {
      this.$router.push('/admin/settings/tax-agency');
    },
    async editTax(id) {
      let response = await this.fetchTaxType(id)
      this.openModal({
        title: this.$t('settings.tax_types.edit_tax'),
        componentName: 'TaxTypeModal',
        id: id,
        data: response.data.taxType,
        refreshData: this.$refs.table.refresh,
      })
    },

    async permissionsUserModule(){
      const data = {
         module: "tax_types" 
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if(permissions.super_admin == false){
        if(permissions.exist == false ){
          this.$router.push('/admin/dashboard')
        }else {
         const modulePermissions = permissions.permissions[0]
          if(modulePermissions == null){
            this.$router.push('/admin/dashboard')
          }else if(modulePermissions.access == 0 ){
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if(permissions.super_admin == true){
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      }else if(permissions.exist == true ){
        const modulePermissions = permissions.permissions[0]
        if(modulePermissions.create == 1){
            this.permissionModule.create = true
        }
        if(modulePermissions.update == 1){
            this.permissionModule.update = true
        }
        if(modulePermissions.delete == 1){
            this.permissionModule.delete = true
        }
        if(modulePermissions.read == 1){
            this.permissionModule.read = true
        }
      }
    }
  },
}
</script>
