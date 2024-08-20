<template>
  <div class="customer-create">
    <sw-page-header :title="$t('settings.mobile.login_logs.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
        <sw-breadcrumb-item
          :title="$tc('settings.mobile.login_logs.title', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalLogins"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>
      </template>
    </sw-page-header>
    <!-- filters -->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$tc('settings.mobile.login_logs.customer', 1)"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.customer"
            type="text"
            name="customer"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.mobile.login_logs.login_date')"
          class="flex-1 mt-3 ml-0 lg:ml-6"
        >
          <base-date-picker
            v-model="filters.session_start"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('settings.mobile.login_logs.os')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.operating_system"
            type="text"
            name="operating_system"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}
        </label>
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('settings.mobile.login_logs.no_logins')"
      :description="$t('settings.mobile.login_logs.list_of_logins_desc')"
    >
      <astronaut-icon class="mt-5 mb-4" />
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ toLogins }}</b>
          {{ $t('general.of') }} <b>{{ totalLogins }}</b>
        </p>

        <!--  <sw-transition type="fade">
          <sw-dropdown v-if="selectedCustomers.length">
            <span
              slot="activator"
              class="
                flex
                block
                text-sm
                font-medium
                cursor-pointer
                select-none
                text-primary-400
              "
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleCustomers">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('customers.deletecustomer') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition> -->
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <!--  <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllCustomers"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllCustomers"
        /> -->
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
          :label="$tc('settings.mobile.login_logs.customer', 1)"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $tc('settings.mobile.login_logs.customer', 1) }}</span>
            <span
              @click="customerDetail(row.customer[0].id)"
              class="font-medium text-primary-500 cursor-pointer"
            >
              {{ row.customer[0].customcode }} - {{ row.customer[0].name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.mobile.login_logs.date')"
          show="session_start"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.mobile.login_logs.date') }}</span>
            <span>
              {{ row.session_start }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.mobile.login_logs.system_name')"
          show="system_name"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.mobile.login_logs.system_name') }}</span>
            <span>
              {{ row.system_name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.mobile.login_logs.device_name')"
          show="device_name"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.mobile.login_logs.device_name') }}</span>
            <span>
              {{ row.device_name }}
            </span>
          </template>
        </sw-table-column>
      </sw-table-component>

      <sw-modal ref="seenNotificationModal" variant="primary">
        <template v-slot:header>
          <div
            class="absolute flex content-center justify-center w-5 cursor-pointer"
            style="top: 20px; right: 15px"
            @click="closeModal"
          >
            <x-icon />
          </div>
          <span>{{ $t('general.notification') }}</span>
        </template>
      </sw-modal>
    </div>
  </div>
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
  EyeOffIcon,
  CreditCardIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '@/components/icon/AstronautIcon'

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
    EyeOffIcon,
    CreditCardIcon,
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      totalLogins: 0,
      toLogins: 0,
      filters: {
        customer: '',
        session_start: '',
        operating_system: '',
      },
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalLogins && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedCustomers
      },
      set: function (val) {
        this.selectCustomer(val)
      },
    },
    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      },
    },
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  destroyed() {
    if (this.selectAllField) {
      this.selectAllCustomers()
    }
  },
  methods: {
    ...mapActions('mobileSettings', [
      // 'saveMobileSettings',
      'fetchMobileLogs',
    ]),

    refreshTable() {
      this.$refs.table.refresh()
    },
    customerDetail(id = null) {
      if (id) {
        this.$router.push('/admin/customers/' + id + '/view')
      }
    },
    async fetchData({ page, filter, sort }) {
      let data = {
        customer: this.filters.customer,
        session_start: this.filters.session_start,
        operating_system: this.filters.operating_system,
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchMobileLogs(data)
      this.totalLogins = response.data.mobileLoginLogs.total
      this.toLogins = response.data.mobileLoginLogs.data.length
      this.isRequestOngoing = false

      return {
        data: response.data.mobileLoginLogs.data,
        pagination: {
          totalPages: response.data.mobileLoginLogs.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        customer: '',
        session_start: '',
        operating_system: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },
    onSelectStatus(status) {
      this.filters.status = status
    },
    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },
    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result) {
          let res = await this.deleteCustomer({ ids: [id] })

          if (res.data.type === 'success') {
            window.toastr['success'](this.$tc('customers.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          window.toastr[res.data.type](res.data.message)
          return true
        }
      })
    },

    async removeMultipleCustomers() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete', 2),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let request = await this.deleteMultipleCustomers()
          if (request.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('customers.deleted_message', 2),
            })
            this.refreshTable()
          } else if (request.data.error) {
            this.showNotification({
              type: 'error',
              message: request.data.message,
            })
          }
        }
      })
    },
  },
}
</script>
