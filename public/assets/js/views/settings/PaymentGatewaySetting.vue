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
        :sortable="true"
        :label="$t('settings.payment_gateways.payment_gateway')"
        show="name"
      >
        <!-- <template slot-scope="row"> -->
          <template slot-scope="row">
          <span class="mt-6">{{ $t('settings.payment_gateways.payment_gateway') }}}}</span>
          <span class=""><img :src="row.url_img" width="150px"></span>
          </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.payment_gateways.payment_gateway_name')"
      >
        <template slot-scope="row">
          <span>{{ $t('settings.payment_gateways.payment_gateway_name') }}</span>
          <span class="">{{ row.name }}</span>
        </template>
      </sw-table-column>

      <sw-table-column
        :sortable="true"
        :filterable="true"
        :label="$t('settings.payment_gateways.payment_gateway_description')"
      >
      <!-- slot-scope="row" -->
        <template slot-scope="row">
          <span>{{
            $t('settings.payment_gateways.payment_gateway_description')
          }}</span>
          <span class="mt-2 text-sm leading-snug text-gray-500">{{ row.description }}</span><br>
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

            <sw-dropdown-item
              :to="`/admin/settings/${row.name}`"
              tag-name="router-link"
            >
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.manage') }}
            </sw-dropdown-item>

            <sw-dropdown-item v-if="row.status == 'I'" @click="changeStatus(row.id)">
              <check-circle-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.activate') }}
            </sw-dropdown-item>

            <sw-dropdown-item v-if="row.status == 'A'" @click="changeStatus(row.id)">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.deactivate') }}
            </sw-dropdown-item>
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
      }
    }
  },  

  methods: {
    ...mapActions('modal', ['openModal']),

    ...mapActions('paymentGateways', ['fetchPaymentGateways', 'updatePaymentGatewaysStatus', 'updatePaymentGatewaysDefault']),

    async fetchData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchPaymentGateways(data)

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

  },
}
</script>
