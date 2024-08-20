<template>
  <base-page class="payments">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('payments.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
          <sw-breadcrumb-item
            to="#"
            :title="$tc('payments.payment', 2)"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalPaymentsCustomers"
          variant="primary-outline"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          v-if="this.enableaddcredit"
          tag-name="router-link"
          to="payments/addcredit"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('customers.add_credit') }}
        </sw-button>

        <sw-button
          v-if="this.enablemakepayment"
          tag-name="router-link"
          to="payments/create"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('payments.add_payment') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <!-- <sw-input-group
          :label="$t('payments.customer')"
          color="black-light"
          class="flex-1 mt-2"
        >
          <base-customer-select
            ref="customerSelect"
            @select="onSelectCustomer"
            @deselect="clearCustomerSearch"
          />
        </sw-input-group> -->

        <sw-input-group
          :label="$t('payments.payment_number')"
          class="flex-1 mt-2 lg:ml-6"
        >
          <sw-input
            v-model="filters.payment_number"
            :placeholder="$t(payments.payment_number)"
            name="payment_number"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('payments.payment_mode')"
          class="flex-1 mt-2 lg:ml-6"
        >
          <sw-select
            v-model="filters.payment_mode"
            :options="paymentModes"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('payments.payment_mode')"
            label="name"
          />
        </sw-input-group>

        <sw-input-group :label="$t('general.from')" class="flex-1 mt-2 lg:ml-6">
          <base-date-picker
            v-model="filters.from_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <div
          class="hidden w-8 h-0 mx-4 border border-gray-400 border-solid xl:block"
          style="margin-top: 3.5rem"
        />

        <sw-input-group :label="$t('general.to')" class="flex-1 mt-2 lg:ml-6">
          <base-date-picker
            v-model="filters.to_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder
      v-if="showEmptyScreen"
      :title="$t('payments.no_payments')"
      :description="$t('payments.list_of_payments')"
    >
      <capsule-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/customer/payments/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('payments.add_new_payment') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div class="relative mt-5">
        <p class="absolute left-0 md:mt-12 text-sm">
          {{ $t('general.showing') }}: <b>{{ paymentsCustomers.length }}</b>
          {{ $t('general.of') }} <b>{{ paymentsCustomers.length }}</b>
        </p>

        <sw-tabs
          ref="tabsStatusPayments"
          @update="setStatusFilter"
          class="hidden md:inline"
        >
          <!-- :active-tab="activeTab" -->
          <sw-tab-item :title="$t('general.all')" filter="ALL" />
          <sw-tab-item :title="$t('general.void')" filter="Void" />
          <sw-tab-item :title="$t('general.unapply')" filter="Unapply" />
          <sw-tab-item :title="$t('general.refunded')" filter="Refunded" />
          <sw-tab-item :title="$t('general.approved')" filter="Approved" />
        </sw-tabs>

        <!-- <sw-transition type="fade">
          <sw-dropdown v-if="selectedPayments.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultiplePayments">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition> -->
      </div>

      <!--  <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllPayments"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllPayments"
        />
      </div> -->

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
        <!-- <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="relative block">
            <sw-checkbox
              :id="row.id"
              v-model="selectField"
              :value="row.id"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column> -->

        <sw-table-column
          :sortable="true"
          :label="$t('payments.date')"
          sort-as="payment_date"
          show="formattedPaymentDate"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('payments.payment_number')"
          show="payment_number"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.payment_number') }}</span>
            <router-link
              :to="{ path: `payments/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.payment_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payments.customer')"
          show="name"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('payments.payment_mode')"
          show="payment_mode"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.payment_mode') }}</span>
            <span>
              <div
                v-if="row.payment_method_id == null && row.invoice_id != null"
              >
                {{ $t('general.balance_to_debit') }}
              </div>
              <div v-if="row.payment_method_id != null">
                {{ row.payment_mode ? row.payment_mode : 'Not selected' }}
              </div>
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payments.invoice')"
          sort-as="invoice_id"
          show="invoice_id"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.invoice_number') }}</span>
            <span>
              {{ row.invoice_number ? row.invoice_number : 'No Invoice' }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payments.amount')"
          show="amount"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.amount') }}</span>
            <div v-html="$utils.formatMoney(row.amount, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <!-- <sw-dropdown-item
                tag-name="router-link"
                :to="`payments/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item> -->

              <sw-dropdown-item
                tag-name="router-link"
                :to="`payments/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <!-- <sw-dropdown-item @click="removePayment(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item> -->
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters, mapState } from 'vuex'
import CapsuleIcon from '@/components/icon/CapsuleIcon'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    CapsuleIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    EyeIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      activeTab: this.$t('general.all'),
      timeout: null,
      enablemakepayment: false,
      enableaddcredit: false,
      filters: {
        customer: '',
        payment_mode: '',
        payment_number: '',
        from_date: '',
        to_date: '',
        transaction_status: 'ALL',
      },
    }
  },
  async created() {
    if ( this.settingsCompany.enable_payment_customer === "0") {
      this.$router.push('./views/errors/404.vue')
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalPaymentsCustomers && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),
    ...mapGetters('customerProfile', ['loggedInCustomer']),
    ...mapState('user', [ 'settingsCompany']),

    ...mapGetters('payment', [
      'selectedPayments',
      'totalPayments',
      'payments',
      'selectAllField',
      'paymentModes',
    ]),
    ...mapGetters('paymentCust', [
      'selectedPayments',
      'totalPaymentsCustomers',
      'paymentsCustomers',
      'selectAllField',
      'paymentModes',
    ]),

    selectField: {
      get: function () {
        return this.selectedPayments
      },
      set: function (val) {
        this.selectPayment(val)
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

  async mounted() {
    this.fetchPaymentModes({ limit: 'all' })

    let res = await this.fetchCompanySettings([
      'enable_credit_customer',
      'enable_make_customer',
    ])
    //  console.log(res)

    if (res && res.data) {
      //  console.log(res.data)
      //console.log(res.data.hasOwnProperty('enable_credit_customer'))
      //console.log(res.data.hasOwnProperty('enable_make_customer'))
      if (res.data.hasOwnProperty('enable_credit_customer')) {
        if (
          res.data.enable_credit_customer == 1 ||
          res.data.enable_credit_customer == true
        ) {
          this.enableaddcredit = true
          //  console.log("credit")
        }
      }

      if (res.data.hasOwnProperty('enable_make_customer')) {
        if (
          res.data.enable_make_customer == 1 ||
          res.data.enable_make_customer == true
        ) {
          this.enablemakepayment = true
          //console.log("payment")
        }
      }
    }

    //console.log(this.enableaddcredit)
    //console.log(this.enablemakepayment)
  },

  /* destroyed() {
    if (this.selectAllField) {
      this.selectAllPayments()
    }
  }, */

  methods: {
    ...mapActions('paymentCust', [
      'fetchPaymentsCustomers',
      /* 'selectAllPayments', */
      'selectPayment',
      /* 'deletePayment', */
      'deleteMultiplePayments',
      'setSelectAllState',
      'fetchPaymentModes',
    ]),

    ...mapActions('company', ['fetchCompanySettings']),
    /* ...mapActions('payment', [
      'fetchPayments',
      // 'selectAllPayments',
      'selectPayment',
      // 'deletePayment',
      'deleteMultiplePayments',
      'setSelectAllState',
      'fetchPaymentModes',
    ]), */

    async fetchData({ page, filter, sort }) {
      let data = {
        transaction_status: this.filters.transaction_status,
        customer_id: this.filters.customer ? this.filters.customer.id : '',
        payment_method_id:
          this.filters.payment_mode !== null
            ? this.filters.payment_mode.id
            : '',
        payment_number: this.filters.payment_number,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchPaymentsCustomers(data)
      this.isRequestOngoing = false

      return {
        data: response.data.payments.data,
        pagination: {
          totalPages: response.data.payments.last_page,
          currentPage: page,
          count: response.data.payments.count,
        },
      }
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      /* if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      } */

      this.filters = {
        /* customer: '', */
        payment_mode: '',
        payment_number: '',
        from_date: '',
        to_date: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    /*  onSelectCustomer(customer) {
      this.filters.customer = customer
    }, */

    /*     async removePayment(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePayment({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          window.toastr['error'](res.data.message)
          return true
        }
      })
    }, */

    /*  async removeMultiplePayments() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('payments.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultiplePayments()
          if (request.data.success) {
            window.toastr['success'](this.$tc('payments.deleted_message', 2))
            this.$refs.table.refresh()
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    }, */

    /* async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    }, */

    showModel(selectedRow) {
      this.selectedRow = selectedRow
      this.$refs.Delete_modal.open()
    },

    setStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.void'):
          this.filters.transaction_status = 'Void'
          break
        case this.$t('general.unapply'):
          this.filters.transaction_status = 'Unapply'
          break
        case this.$t('general.refunded'):
          this.filters.transaction_status = 'Refunded'
          break
        case this.$t('general.approved'):
          this.filters.transaction_status = 'Approved'
          break
        default:
          this.filters.transaction_status = 'ALL'
          break
      }
      this.refreshTable()
    },

    /*  async removeSelectedItems() {
      this.$refs.Delete_modal.close()
      await this.selectedRow.forEach((row) => {
        this.deletePayment(this.id)
      })
      this.$refs.table.refresh()
    }, */
  },
}
</script>
