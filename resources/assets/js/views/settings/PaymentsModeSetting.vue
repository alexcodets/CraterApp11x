<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.customization.payments.payment_modes') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.customization.payments.description') }}
        </p>
      </div>
      <div class="mt-4 lg:mt-0 lg:ml-2">
        <sw-button variant="primary-outline" size="lg" @click="addPaymentMode" v-if="permissionModule.create">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('settings.customization.payments.add_payment_mode') }}
        </sw-button>
      </div>
    </div>

    <sw-table-component
      ref="table"
      variant="gray"
      :show-filter="false"
      :data="fetchData"
    >
      <sw-table-column
        :label="$t('settings.customization.payments.mode_name')"
        show="name"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.customization.payments.mode_name') }}</span>
          <span class="mt-6"> {{ row.name }}</span>
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
            <dot-icon slot="activator" class="h-5" />

            <sw-dropdown-item @click="editPaymentMode(row)" v-if="permissionModule.update">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.edit') }}
            </sw-dropdown-item>

            <sw-dropdown-item @click="removePaymentMode(row.id)" v-if="permissionModule.delete">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </sw-card>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
const { required, maxLength, alpha } = require('vuelidate/lib/validators')
import { TrashIcon, PencilIcon, PlusIcon } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
  },

  data(){
    return {
      permissionModule: {
        create: false,
        read: false,
        update: false,
        delete: false
      }
    }
  },

  created(){
    this.permissionsUserModule()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),

    ...mapActions('payment', ['deletePaymentMode', 'fetchPaymentModes']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchPaymentModes(data)

      return {
        data: response.data.paymentMethods.data,
        pagination: {
          totalPages: response.data.paymentMethods.last_page,
          currentPage: page,
          count: response.data.paymentMethods.count,
        },
      }
    },

    addPaymentMode() {
      this.openModal({
        title: this.$t('settings.customization.payments.add_payment_mode'),
        componentName: 'PaymentMode',
        refreshData: this.$refs.table.refresh,
      })
    },

    editPaymentMode(data) {
     // console.log('edit data: ', data); 
      this.openModal({
        title: this.$t('settings.customization.payments.edit_payment_mode'),
        componentName: 'PaymentMode',
        id: data.id,
        data: data,
        refreshData: this.$refs.table.refresh,
      })
    },

    removePaymentMode(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t(
          'settings.customization.payments.payment_mode_confirm_delete'
        ),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.deletePaymentMode(id)

          if (response.data.success) {
            window.toastr['success'](
              this.$t('settings.customization.payments.deleted_message')
            )
            this.id = null
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](
            this.$t('settings.customization.payments.already_in_use')
          )
        }
      })
    },
    async permissionsUserModule(){
      const data = {
         module: "payment_modes" 
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
