<template>
  <sw-card variant="setting-card">
    <div slot="header" class="flex flex-wrap justify-between lg:flex-nowrap">
      <div>
        <h6 class="sw-section-title">
          {{ $t('settings.payment_gateways.title') }}
        </h6>
        <p
          class="mt-2 text-sm leading-snug text-gray-500"
          style="max-width: 680px"
        >
          {{ $t('settings.payment_gateways.description') }}
        </p>
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
        class="margin-width"
        :sortable="false"
        :label="$t('settings.payment_gateways.payment_gateway')"
        show="url_img"
      >
        <!-- <template slot-scope="row"> -->
          <template slot-scope="row" class="mt-6 margin-width">
          <span class="mt-6 margin-width">{{ $t('settings.payment_gateways.payment_gateway') }}</span>
          <span class=""><img :src="row.url_img" width="150px" class="margin-span"></span>
          </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.payment_gateways.payment_gateway_name')"
        show="name"
      >
        <template slot-scope="row" class="lobo">
          <span>{{ $t('settings.payment_gateways.payment_gateway_name') }}</span>
          <div>
            <span>{{ row.name }}</span>
          </div>          
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.payment_gateways.payment_gateway_description')"
        show="description"
      >
      <!-- slot-scope="row" -->
        <template slot-scope="row" class="lobo">
          <span>{{
            $t('settings.payment_gateways.payment_gateway_description')
          }}</span>
          <span class="mt-2 text-sm leading-snug text-gray-500">{{ row.description }}</span><br>
        </template>
      </sw-table-column>

      <sw-table-column
          :sortable="true"
          :label="$t('avalara.item.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('avalara.item.status') }}</span>
            <sw-badge
              :bg-color="
                $utils.getBadgeStatusColor(row.status == 'A' ? 'A' : 'I').bgColor
              "
              :color="
                $utils.getBadgeStatusColor(row.status == 'A' ? 'A' : 'I').color
              "
            >
              {{ row.status == 'A' ? 'Active' : 'Inactive' }}
            </sw-badge>
          </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :label="$t('authorize.default')"
        show="default"    
      >
        <template slot-scope="row">
          <span>{{ $t('authorize.default') }}</span>
          
            <div class="relative w-12">
              <sw-switch
                :disabled="false"
                v-model="row.default"
                class="absolute"
                style="top: -20px"
                @change="updateDefault(row)"
              />
            </div>
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
            <div v-if="permissionModule.update">
              <sw-dropdown-item
                :to="`/admin/settings/${row.name}`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.manage') }}
              </sw-dropdown-item>              
            </div>
          </sw-dropdown>
        </template>
      </sw-table-column>
    </sw-table-component>
  </sw-card>
</template>

<script>
import { mapActions } from 'vuex'
import { TrashIcon, PencilIcon, PlusIcon, CheckCircleIcon  } from '@vue-hero-icons/solid'

export default {
  components: {
    TrashIcon,
    PencilIcon,
    PlusIcon,
    CheckCircleIcon,
  },
  
  data() {
    return {      
      data: {
        company_id: '',
        default: '',
        deleted_at: '',
        description: '',
        id: '',
        name: '',
        slug: '',
        status: '',
        url_img: '',
      },
      permissionModule:{
        create: false,
        update: false,
        delete: false,
        read: false,
      }
    }
  },  

  created(){
    this.permissionsUserModule()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),

    ...mapActions('paymentGateways', ['fetchPaymentGateways', 'updatePaymentGatewaysStatus', 'updatePaymentGatewaysDefault', 'fetchPaymentGatewaysIndex']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchPaymentGatewaysIndex(data)
     // console.log("respuesta: ", response);
      this.data = JSON.parse(JSON.stringify(response.data.payment_gateways))
      //console.log(this.data);
      return {
        data: response.data.payment_gateways,
        pagination: {
          totalPages: response.data.payment_gateways.last_page,
          currentPage: page,
          count: response.data.payment_gateways.count,
        },
      }
    },

    async changeStatus(id) {
      let res = await this.updatePaymentGatewaysStatus(id)
      window.toastr['success'](this.$tc('settings.payment_gateways.success_status'))
      location.reload()
    },

    async updateDefault( { id } ) {
      // validar si el id tiene la variable default en true no continuar
      // const indexGateway = this.data.findIndex(item => item.id === id)
      // if(this.data[indexGateway].default) {
      //   this.data[indexGateway].default 
      //   return 
      // }

      this.data.forEach(data => data.id === id ? data.default = true : data.default = false);
      
      await this.updatePaymentGatewaysDefault(this.data)
      window.toastr['success'](this.$tc('settings.payment_gateways.success_default'))
      // refresh table
      this.$refs.table.refresh()
    },

    
async permissionsUserModule(){
    const data = {
       module: "payment_gateways" 
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

<style>

@media (max-width: 600px) {
    .margin-width{
    max-width: 300px;
    margin-top: -2px;
    }

    .margin-span{
      margin-left: 50%;
    }

    .lobo{
      padding-top: 100px !important;
      padding-bottom: 150px !important;
    }

    .table-component td:not(:first-child) {
        padding-bottom: 50px;
        text-overflow: ellipsis;
    }

    .table-component td:nth-last-child(3) {
      border-bottom-left-radius: 5px !important;
      padding-bottom: 30px;
    }

    .table-component td:nth-last-child(2) {
      padding-bottom: 30px;
    }

    .switch:checked + .switch-label {
      margin-left: 50px;
    }

    .switch-label {
      margin-left: 50px;
    }
}
</style>