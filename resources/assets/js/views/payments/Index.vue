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
          v-show="totalPayments"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="'payments/create'"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('payments.add_payment') }}
        </sw-button>

        <!-- button payments failed -->
        <sw-button
          tag-name="router-link"
          to="payments-failed"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          {{ $t('failed_payment_history.title') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper
        v-show="showFilters"
        class="relative grid grid-flow-col grid-rows"
      >
        <div class="w-50 mx-0 md:mx-5">
          <sw-input-group
            :label="$t('payments.customer')"
            color="black-light"
            class="flex-1 mt-2"
          >
            <base-customer-select
              ref="customerSelect"
              @select="onSelectCustomer"
              @deselect="clearCustomerSearch"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('customers.customcode')"
            class="flex-1 mt-2 lg:mr-6"
          >
            <sw-input v-model="filters.customcode">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>

        <div class="w-50 mx-0 md:mx-5">
          <sw-input-group
            :label="$t('payments.payment_number')"
            class="flex-1 mt-2 lg:ml-6"
          >
            <sw-input
              v-model="filters.payment_number"
              :placeholder="$t(payments.payment_number)"
              name="payment_number"
            >
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
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
        </div>

        <div class="w-50 mx-0 md:mx-5">
          <sw-input-group
            :label="$t('invoices.invoice_number')"
            color="black-light"
            class="mt-2"
          >
            <sw-input v-model="filters.invoice_number" name="invoice_number">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>

        <div class="w-50 mx-0 md:mx-5">
          <!-- <div class="hidden w-8 h-0 mx-4 border border-gray-400 border-solid xl:block lg:ml-6"  style="margin-top: 3.5rem"/> -->
          <sw-input-group :label="$t('general.from')" class="mt-2">
            <base-date-picker
              v-model="filters.from_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>

        <div class="w-50 mx-0 md:mx-5">
          <sw-input-group :label="$t('general.to')" class="mt-2">
            <base-date-picker
              v-model="filters.to_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>

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
        to="/admin/payments/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('payments.add_new_payment') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ payments.length }}</b>
          {{ $t('general.of') }} <b>{{ totalPayments }}</b>
        </p>
        <!--
        <sw-transition type="fade">
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
        </sw-transition>
        -->
      </div>

      <!-- tabs for filter for status of the payments -->
      <sw-tabs
        ref="tabsStatusPayments"
        :active-tab="activeTab"
        @update="setStatusFilter"
        class="hidden md:inline"
      >
        <!-- :active-tab="activeTab" -->
        <sw-tab-item :title="$t('general.all')" filter="ALL" />
        <sw-tab-item :title="$t('general.approved')" filter="Approved" />
        <sw-tab-item
          :title="$t('general.balance_to_debit')"
          filter="balance_to_debit"
        />
        <sw-tab-item :title="$t('general.void')" filter="Void" />
        <sw-tab-item :title="$t('general.unapply')" filter="Unapply" />
        <sw-tab-item :title="$t('general.refunded')" filter="Refunded" />
        <sw-tab-item :title="$t('general.pending')" filter="Pending" />
        <sw-tab-item :title="$t('payments.returned')" filter="Returned" />
        <sw-tab-item :title="$t('estimates.declined')" filter="Declined" />
        <sw-tab-item :title="$t('general.error')" filter="Error" />
      </sw-tabs>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-7">
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
      </div>

      <sw-table-component
        ref="table"
        :data="fetchData"
        headerClass="bg-danger"
        tableClass="w-full"
        tbodyClass="border-b-2 border-gray-200 border-solid"
        :show-filter="false"
        class="-mt-12 md:mt-0"
      >
        <!--
          :filterable="false"
         -->
        <sw-table-column
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
        </sw-table-column>

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
              v-if="permissionModule.read"
            >
              {{ row.payment_number }}
            </router-link>
            <span v-else>
              {{ row.payment_number }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('payments.customer')"
          show="name"
        >
          <template slot-scope="row">
            <div v-if="permissionModule.read">
              <router-link
                :to="{ path: `customers/${row.user_id}/view` }"
                class="font-medium text-primary-500"
              >
                {{ row.name }}
              </router-link>
              <!-- <p style="font-size: 15px;"> {{ row.user.customcode }}</p> -->
            </div>
            <div v-else>
              <p style="font-size: 15px">{{ row.name }}</p>
              <!-- <p style="font-size: 15px;"> {{ row.user.customcode }}</p> -->
            </div>
          </template>
        </sw-table-column>

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

                <div
                  v-if="
                    row.payment_method &&
                    (row.payment_method.show_notes_table === 1 ||
                      row.payment_method.show_notes_table === true)
                  "
                  class="text-sm"
                >
                {{ row.notes ? row.notes : 'Not notes' }}
                </div>
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
            <div v-if="permissionModule.read">
              <div v-if="row.invoice != null">
                <router-link
                  :to="{ path: `invoices/${row.invoice_id}/view` }"
                  class="font-medium text-primary-500"
                >
                  {{ row.invoice_number }}
                </router-link>
              </div>
              <div v-else>
                {{ $t('payments.no_invoice') }}
              </div>
            </div>
            <div v-else>
              {{
                row.invoice_number != null
                  ? row.invoice_number
                  : $t('payments.no_invoice')
              }}
            </div>
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
          :sortable="true"
          :label="$t('payments.status')"
          show="transaction_status"
        />

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                v-if="permissionModule.update && row.is_payment_multiple == 0"
                tag-name="router-link"
                :to="`payments/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="permissionModule.update && row.is_payment_multiple == 1"
                tag-name="router-link"
                :to="`payments/multiple/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`payments/${row.id}/view`"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="{ path: `/admin/customers/${row.user_id}/view` }"
              >
                <users-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.go_to_customer') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="row.invoice != null"
                tag-name="router-link"
                :to="{ path: `/admin/invoices/${row.invoice_id}/view` }"
              >
                <document-text-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.go_to_invoice') }}
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
import { mapActions, mapGetters } from 'vuex'
import CapsuleIcon from '@/components/icon/CapsuleIcon'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PencilIcon,
  TrashIcon,
  HashtagIcon,
  DocumentTextIcon,
  UsersIcon,
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
    HashtagIcon,
    DocumentTextIcon,
    UsersIcon,
  },

  data() {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      showAddPayment: false,
      showAddCredit: false,
      filters: {
        customer: '',
        payment_mode: '',
        payment_number: '',
        invoice_number: '',
        transaction_status: '',
        from_date: '',
        to_date: '',
        customcode: '',
        status: { name: '', value: '' },
      },
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
      activeTab: this.$t('general.all'),
      timeout: null,
    }
  },
  watch: {
    settingsCompany: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.updateMenuVisibility();
        }
      },
    },
  },
  computed: {
    showEmptyScreen() {
      return !this.totalPayments && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),
    ...mapGetters('user', ['settingsCompany']),
    ...mapGetters('payment', [
      'selectedPayments',
      'totalPayments',
      'payments',
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

  mounted() {
    this.fetchPaymentModes({ limit: 'all' })
    this.permissionsUserModule()
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllPayments()
    }
  },

  methods: {
    ...mapActions('payment', [
      'fetchPayments',
      'selectAllPayments',
      'selectPayment',
      'deletePayment',
      'deleteMultiplePayments',
      'setSelectAllState',
      'fetchPaymentModes',
    ]),
    ...mapActions('user', ['getUserModules']),

    async fetchData({ page, filter, sort }) {
      let data = {
        customer_id: this.filters.customer ? this.filters.customer.id : '',
        payment_method_id:
          this.filters.payment_mode !== null
            ? this.filters.payment_mode.id
            : '',
        payment_number: this.filters.payment_number,
        invoice_number: this.filters.invoice_number,
        transaction_status: this.filters.transaction_status,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        customcode: this.filters.customcode,
        orderByField: sort.fieldName || 'payment_number',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchPayments(data)

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
      clearTimeout(this.timeout)
      this.timeout = setTimeout(() => {
        this.refreshTable()
      }, 900)
    },

    clearFilter() {
      this.activeTab = this.$t('general.all')

      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }

      this.filters = {
        customer: '',
        payment_mode: '',
        payment_number: '',
        transaction_status: '',
        from_date: '',
        to_date: '',
        customcode: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    onSelectCustomer(customer) {
      this.filters.customer = customer
    },

    async removePayment(id) {
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
    },

    async removeMultiplePayments() {
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
    },
    updateMenuVisibility() {
      if (!this.settingsCompany) return;

      this.showAddCredit = this.settingsCompany.enable_credit_customer === "1";
      this.showAddPayment = this.settingsCompany.enable_make_customer === "1";

    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    },

    showModel(selectedRow) {
      this.selectedRow = selectedRow
      this.$refs.Delete_modal.open()
    },

    async removeSelectedItems() {
      this.$refs.Delete_modal.close()
      await this.selectedRow.forEach((row) => {
        this.deletePayment(this.id)
      })
      this.$refs.table.refresh()
    },

    async permissionsUserModule() {
      const data = {
        module: 'payments',
      }
      const permissions = await this.getUserModules(data)
      // valida que el usuario tenga permiso para ingresar al modulo
      if (permissions.super_admin == false) {
        if (permissions.exist == false) {
          this.$router.push('/admin/dashboard')
        } else {
          const modulePermissions = permissions.permissions[0]
          if (modulePermissions == null) {
            this.$router.push('/admin/dashboard')
          } else if (modulePermissions.access == 0) {
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if (permissions.super_admin == true) {
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      } else if (permissions.exist == true) {
        const modulePermissions = permissions.permissions[0]
        if (modulePermissions.create == 1 ) {
          this.permissionModule.create = true
        }
        if (modulePermissions.update == 1) {
          this.permissionModule.update = true
        }
        if (modulePermissions.delete == 1) {
          this.permissionModule.delete = true
        }
        if (modulePermissions.read == 1) {
          this.permissionModule.read = true
        }
      }
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
        case this.$t('general.balance_to_debit'):
          this.filters.transaction_status = 'balance_to_debit'
          break
        case this.$t('general.pending'):
          this.filters.transaction_status = 'Pending'
          break
        case this.$t('general.returned'):
          this.filters.transaction_status = 'Returned'
          break
        case this.$t('general.declined'):
          this.filters.transaction_status = 'Declined'
          break
        case this.$t('general.error'):
          this.filters.transaction_status = 'Error'
          break
        default:
          this.filters.transaction_status = 'ALL'
          break
      }
      //this.refreshTable()
    },
  },
}
</script>

<style scoped>
.w-25 {
  width: 25% !important;
}
</style>
