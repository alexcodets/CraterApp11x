<template>
  <sw-card variant="setting-card">
    <base-page class="customer-create">
      <sw-page-header :title="$t('auxpay.name')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            :title="$t('settings.payment_gateways.title')"
            to="/admin/settings/payment-gateways"
          />
          <sw-breadcrumb-item :title="$tc('auxpay.name')" to="#" active />
        </sw-breadcrumb>

        <template slot="actions">
          <sw-button
            v-show="totalPaypals"
            size="lg"
            variant="primary-outline"
            @click="toggleFilter"
          >
            {{ $t('general.filter') }}
            <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
          </sw-button>

      

          <sw-button
            tag-name="router-link"
            to="AuxVault/create"
            size="lg"
            variant="primary"
            class="ml-4"
          >
            <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
            {{ $t('auxpay.add') }}
          </sw-button>
        </template>
      </sw-page-header>

      <sw-empty-table-placeholder
        v-show="showEmptyScreen"
        :title="$t('auxpay.no_authorize')"
        :description="$t('auxpay.list_of_authorize')"
      >
        <astronaut-icon class="mt-5 mb-4" />

        <sw-button
          slot="actions"
          tag-name="router-link"
          to="/admin/settings/AuxVault/create"
          size="lg"
          variant="primary-outline"
        >
          {{ $t('auxpay.add') }}
        </sw-button>
      </sw-empty-table-placeholder>

      <div v-show="!showEmptyScreen" class="relative table-container">
        <div
          class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
        >
          <p class="text-sm">
            {{ $t('general.showing') }}: <b>{{ paypals.length }}</b>
            {{ $t('general.of') }} <b>{{ totalPaypals }}</b>
          </p>
        </div>

        <sw-table-component
          ref="table"
          :show-filter="false"
          :data="fetchData"
          table-class="table"
        >


        <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('authorize.name')"
            show="name"
          >
            <template slot-scope="row">
              <span>{{ $t('authorize.name') }}</span>
              <router-link
                :to="{ path: `AuxVault/${row.id}/view` }"
                class="font-medium text-primary-500"
              >
                {{ row.name }}
              </router-link>
            </template>
          </sw-table-column>


          <sw-table-column
            :sortable="true"
            :filterable="true"
            :label="$t('paypal.merchant_id')"
            show="merchant_id"
          >
            <template slot-scope="row">
              <span>{{ $t('paypal.merchant_id') }}</span>
              <router-link
                :to="{ path: `AuxVault/${row.id}/view` }"
                class="font-medium text-primary-500"
              >
                {{ row.MerchantIdDecrypted }}
              </router-link>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('paypal.currency')"
            show="currency"
          >
            <template slot-scope="row">
              <span>{{ $t('paypal.currency') }}</span>
              <span v-html="row.currency"> </span>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('paypal.status')"
            show="default"
          >
            <template slot-scope="row">
              <span>{{ $t('paypal.status') }}</span>
              <div v-if="row.default == 1">

                <sw-badge
                :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                :color="$utils.getBadgeStatusColor('COMPLETED').color"
                class="px-3 py-1"
              >
                {{ $t('auxpay.default') }}
              </sw-badge>
              </div>
              <div v-if="row.default == 0">

                <sw-badge
                :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                :color="$utils.getBadgeStatusColor('OVERDUE').color"
                class="px-3 py-1"
              >
                {{ $t('auxpay.default_not') }}
              </sw-badge>
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="true"
            :label="$t('auxpay.production')"
            show="default"
          >
            <template slot-scope="row">
              <span>{{ $t('auxpay.production') }}</span>
              <div v-if="row.production == 1">
                {{ $t('auxpay.production') }}
              </div>
              <div v-if="row.production == 0">
                {{ $t('auxpay.development') }}
              </div>
            </template>
          </sw-table-column>

          <sw-table-column
            :sortable="false"
            :filterable="false"
            cell-class="action-dropdown"
          >
            <template slot-scope="row">
              <span> {{ $tc('paypal.action') }} </span>

              <sw-dropdown>
                <dot-icon slot="activator" />

                <sw-dropdown-item
                  :to="`AuxVault/${row.id}/edit`"
                  tag-name="router-link"
                >
                  <pencil-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.edit') }}
                </sw-dropdown-item>

                
              <sw-dropdown-item
                :to="`AuxVault/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>
                <sw-dropdown-item @click="setDefault(row)">
        
                  <plus-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('auxpay.set_default') }}
                </sw-dropdown-item>
             

                <sw-dropdown-item @click="removePaypal(row.id)">
                  <trash-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.delete') }}
                </sw-dropdown-item>
              </sw-dropdown>
            </template>
          </sw-table-column>
        </sw-table-component>
      </div>
    </base-page>
  </sw-card>
</template>
<script>
import { mapActions, mapGetters } from 'vuex'
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
 
  TrashIcon,
  PencilIcon,
  EyeIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '../../components/icon/AstronautIcon'

export default {
  components: {
    AstronautIcon,
    ChevronDownIcon,
  
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
    PlusIcon,
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      totalRecords: 0,
      filters: {
        currency: '',
        merchant_id: '',
        email: '',
        public_key: '',
        enviroment: '',
      },
      data: {
        currency: '',
        id: '',
        merchant_id: '',
        email: '',
        enviroment: '',
        status: '',
        public_key: '',
      },
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalRecords && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('paypal', ['paypals', 'totalPaypals']),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('paypal', [
      'fetchPaypals',
      'deletePaypal',
      'updatePaypalStatus',
    ]),
    ...mapActions('notification', ['showNotification']),

    ...mapActions('auxvault', ['fetchAuxVault','deleteAuxvault','updatedefaultAuxvault']),

    refreshTable() {
      this.$refs.table.refresh()
    },
    async fetchData({ page, filter, sort }) {
      this.isRequestOngoing = true
      let filters = {}
      if (this.filters.currency && this.filters.currency != '')
        filters.currency = this.filters.currency
      if (this.filters.merchant_id && this.filters.merchant_id != '')
        filters.merchant_id = this.filters.merchant_id
      if (this.filters.email && this.filters.email != '')
        filters.email = this.filters.email
      if (this.filters.public_key && this.filters.public_key != '')
        filters.public_key = this.filters.public_key
      if (this.filters.enviroment && this.filters.enviroment != '')
        filters.enviroment = this.filters.enviroment
      //console.log(filters);

      let response = await this.fetchAuxVault()
      this.isRequestOngoing = false

     // console.log(response.data)
      this.data = response.data.data.data
      //console.log(response.data.data.data)
      //console.log( response.data.dataTotalCount)
      this.totalRecords = response.data.dataTotalCount
      //console.log(this.totalRecords)
      return {
        data: response.data.data.data,
        pagination: {
          totalPages: response.data.data.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        currency: '',
        payment_API: '',
        payment_account_validation_mode: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },
    async updateStatus() {
      let i = 0
      this.data.forEach((data) => {
        if (data.status == true) {
          i++
        }
      })
      if (i > 1) {
        window.toastr['error'](this.$tc('paypal.status_error'))
        this.refreshTable()
      }
      if (i < 1) {
        window.toastr['error'](this.$tc('paypal.status_select'))
        this.refreshTable()
      }
      if (i == 1) {
        let response = await this.updatePaypalStatus(this.data)
        window.toastr['success'](this.$tc('paypal.success_status'))
        this.refreshTable()
      }
    },
    selectStatus(id, row) {
      this.data.forEach((data) => {
        if (data.id === id) {
          data.status = row.status
        }
      })
    },
    async setDefault(auth){
     // console.log(auth);
      let res = await this.updatedefaultAuxvault(auth);
     // console.log(res );
      if (res.data.success){
        window.toastr['success'](this.$tc(res.data.response))
        this.refreshTable();
        return;

      } else {
        window.toastr['error'](this.$tc(res.data.response))
      }
    },
    async removePaypal(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('auxpay.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteAuxvault({ ids: id })

          if (res.status === 200) {
            window.toastr['success'](this.$tc('auxpay.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },
  },
}
</script>
