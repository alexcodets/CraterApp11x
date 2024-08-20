<template>
  <base-page class="customer-create">
    <sw-page-header :title="$t('payment_accounts.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('customers.title')" :to="`/admin/customers/${$route.params.id}/view`" />
        <sw-breadcrumb-item
          :title="$tc('payment_accounts.title', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalPaymentAccounts"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/payment-accounts/create-ACH`"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('payment_accounts.add_ach_account') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/customers/${$route.params.id}/payment-accounts/create-CC`"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('payment_accounts.add_cc_account') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">

        <sw-input-group
          :label="$t('payment_accounts.first_name')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.first_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('payment_accounts.last_name')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.last_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('payment_accounts.payment_account_type')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.payment_account_type"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('payment_accounts.address_1')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.address_1"
            type="text"
            name="name"
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
      :title="$t('payment_accounts.no_payment_account')"
      :description="$t('payment_accounts.list_of_payment_account')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        :to="`/admin/customers/${$route.params.id}/payment-accounts/create-CC`"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('payment_accounts.add_new_payment_account') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ paymentAccounts.length }}</b>
          {{ $t('general.of') }} <b>{{ totalPaymentAccounts }}</b>
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
          :label="$t('payment_accounts.name')"
          show="first_name"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.first_name') }}</span>
            <router-link
              :to="{ path: `payment-accounts/${row.id}/view-${row.payment_account_type}` }"
              class="font-medium text-primary-500"
            >
              {{ row.first_name }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payment_accounts.address_1')"
          show="address_1"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.address_1') }}</span>
            <span> {{ row.address_1 }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payment_accounts.payment_account_type')"
          show="payment_account_type"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.payment_account_type') }}</span>
            <span> {{ row.payment_account_type }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payment_accounts.default_payment_account')"
          show="default_payment_account"
        >
          <template slot-scope="row">
            <span>{{ $t('payment_accounts.default') }}</span>
            <span v-if="row.main_account">
              {{ $t('payment_accounts.default') }}
            </span>
            <span v-else>
              {{ $t('payment_accounts.no_default') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('payment_accounts.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item  @click="changeDefaultPayAccount(row.id)" v-if="!row.main_account">
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.make_default_pay_account') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="changeDefaultPayAccount(row.id)" v-else>
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.unmake_defaut_pay_account') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`payment-accounts/${row.id}/edit-${row.payment_account_type}`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`/admin/customers/${$route.params.id}/payment-accounts/${row.id}/view-${row.payment_account_type}`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removePaymentAccount(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </base-page>
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
  CheckCircleIcon,
  BadgeCheckIcon,
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
    CheckCircleIcon,
    BadgeCheckIcon
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      filters: {
        first_name: '',
        last_name: '',
        address_1: '',
        payment_account_type: '',
      },
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalPaymentAccounts && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('paymentAccounts', [
      'paymentAccounts',
      'totalPaymentAccounts',
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('paymentAccounts', [
      'fetchPaymentAccounts',
      'deletePaymentAccount',
      'defaultPayAccount',
    ]),
    ...mapActions('notification', ['showNotification']),
    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        user_id: this.$route.params.id,
        first_name: this.filters.first_name,
        last_name: this.filters.last_name,
        address_1: this.filters.address_1,
        payment_account_type: this.filters.payment_account_type,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchPaymentAccounts(data)
      this.isRequestOngoing = false

      return {
        data: response.data.payment_accounts.data,
        pagination: {
          totalPages: response.data.payment_accounts.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        first_name: '',
        last_name: '',
        adress_1: '',
        payment_account_type: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removePaymentAccount(id) {
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payment_accounts.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePaymentAccount({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('payment_accounts.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async changeDefaultPayAccount(id) {
      this.id = id
      
      let res = await this.defaultPayAccount( id )
      
      if (res.data.success) {
        window.toastr['success'](this.$tc('payment_accounts.updated_message', 1));
      }
      this.$refs.table.refresh();
    }
  },
}
</script>
