<template>
  <sw-card variant="setting-card">
  <base-page class="customer-create">
    <sw-page-header :title="$t('paypal.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('settings.payment_gateways.title')" to="/admin/settings/payment-gateways" />
        <sw-breadcrumb-item
          :title="$tc('paypal.title', 2)"
          to="#"
          active
        />
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
          @click="updateStatus"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          {{ $t('paypal.update_status') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="paypal/create"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('paypal.new_paypal') }}
        </sw-button>
      </template>
    </sw-page-header>
    
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">

        <sw-input-group
          :label="$t('paypal.currency')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.currency"
            type="text"
            name="currency"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('paypal.merchant_id')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.merchant_id"
            type="text"
            name="merchant_id"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <br><br>

        <sw-input-group
          :label="$t('paypal.email')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.email"
            type="text"
            name="email"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('paypal.public_key')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.public_key"
            type="text"
            name="public_key"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('paypal.enviroment')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.enviroment"
            type="text"
            name="enviroment"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('paypal.no_authorize')"
      :description="$t('paypal.list_of_authorize')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/settings/paypal/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('paypal.add_new_paypal') }}
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
              :to="{ path: `paypal/${row.id}/view` }"
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
              :to="{ path: `paypal/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.merchant_id }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('paypal.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('paypal.status') }}</span>
            
              <div class="relative w-12">
                <sw-switch
                  v-model="row.status"
                  class="absolute"
                  style="top: -33px"
                  @change="selectStatus(row.id, row)"
                />
              </div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('paypal.currency')"
          show="currency"
        >
          <template slot-scope="row">
            <span>{{ $t('paypal.currency') }}</span>
            <span v-html="row.currency">
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('paypal.email')"
          show="email"
        >
          <template slot-scope="row">
            <span>{{ $t('paypal.email') }}</span>
            <span v-html="row.email">
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('paypal.public_key')"
          show="public_key"
        >
          <template slot-scope="row">
            <span>{{ $t('paypal.public_key') }}</span>
            <span v-html="row.public_key">
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('paypal.enviroment')"
          show="enviroment"
        >
          <template slot-scope="row">
            <span>{{ $t('paypal.enviroment') }}</span>
            <span v-html="row.enviroment"></span>
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
                :to="`paypal/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`paypal/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
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
} from '@vue-hero-icons/solid'
import AstronautIcon from '../../components/icon/AstronautIcon'

export default {
  components: {
    AstronautIcon,
    ChevronDownIcon,
    PlusSmIcon,
    FilterIcon,
    XIcon,
    TrashIcon,
    PencilIcon,
    EyeIcon,
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
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
      }
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalPaypals && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('paypal', [
      'paypals',
      'totalPaypals',
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    }
  },
  methods: {
    ...mapActions('paypal', [
      'fetchPaypals',
      'deletePaypal',
      'updatePaypalStatus',
    ]),
    ...mapActions('notification', ['showNotification']),
    refreshTable() {
      this.$refs.table.refresh()
    },
    async fetchData({ page, filter, sort }) {

      this.isRequestOngoing = true
      let filters = {};
      if(this.filters.currency && this.filters.currency != '') 
        filters.currency = this.filters.currency
      if(this.filters.merchant_id && this.filters.merchant_id != '') 
        filters.merchant_id = this.filters.merchant_id
      if(this.filters.email && this.filters.email != '') 
        filters.email = this.filters.email
      if(this.filters.public_key && this.filters.public_key != '') 
        filters.public_key = this.filters.public_key
      if(this.filters.enviroment && this.filters.enviroment != '') 
        filters.enviroment = this.filters.enviroment
      //console.log(filters);
      let response = await this.fetchPaypals(filters)
      this.isRequestOngoing = false

      this.data = response.data.paypal.data

      return {
        data: response.data.paypal.data,
        pagination: {
          totalPages: response.data.paypal.last_page,
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
      this.data.forEach(data => {
        if (data.status == true) {
          i++
        }
      });
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
      this.data.forEach(data => {
        if (data.id === id) {
          data.status = row.status
        }
      });
    },
    async removePaypal(id) {
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('paypal.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePaypal({ ids: id })

          if (res.status === 200) {
            window.toastr['success'](this.$tc('paypal.deleted_message', 1))
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
