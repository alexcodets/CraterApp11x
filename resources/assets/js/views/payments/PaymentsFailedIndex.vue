<template>
  <base-page class="payments">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header
        :title="$t('failed_payment_history.title')"
      ></sw-page-header>
   

      <!-- enable filters -->
      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          tag-name="router-link"
          :to="`/admin/payments`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
          size="lg"
        >
          {{ $t('general.go_to_payments') }}
        </sw-button>

        <sw-button
          v-if="!isCustomer"
          tag-name="router-link"
          :to="`/admin/customers/${customerId}/view`"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
          size="lg"
        >
          {{ $t('general.go_to_customer') }}
        </sw-button>

        <sw-button
          v-show="total_items"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <div class="w-25">
          <sw-input-group
            :label="$t('failed_payment_history.payment_gateway')"
            color="black-light"
            class="flex-1 mt-2 lg:mr-6"
          >
            <sw-select
              v-model="filters.payment_gateways"
              :options="payment_gateways"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('failed_payment_history.payment_gateway')"
              label="name"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('failed_payment_history.payment_number')"
            class="flex-1 mt-2"
          >
            <sw-input v-model="filters.payment_number">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>

        <div class="w-25">
          <sw-input-group
            :label="$t('failed_payment_history.invoice')"
            class="flex-1 mt-2 ml-3"
          >
            <sw-input v-model="filters.invoice_number">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

          <sw-input-group
            :label="$t('failed_payment_history.customer')"
            class="flex-1 mt-2 ml-3"
            v-if="isCustomer"
          >
            <sw-input v-model="filters.customer"> </sw-input>
          </sw-input-group>
        </div>

        <div class="w-25">
          <sw-input-group
            :label="$t('general.from')"
            class="flex-1 mt-2 lg:ml-6"
          >
            <base-date-picker
              v-model="filters.from_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>

          <sw-input-group :label="$t('general.to')" class="flex-1 mt-2 lg:ml-6">
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

    <!-- <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <div class="w-25">
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

        <div class="w-25">
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
        </div>

        <div class="w-25">
          <sw-input-group
            :label="$t('invoices.invoice_number')"
            color="black-light"
            class="flex-1 mt-2 lg:ml-10"
            style="padding-right: 8%"
          >
            <sw-input
              v-model="filters.invoice_number"
              :placeholder="$t('invoices.invoice_number')"
              name="invoice_number"
            />
          </sw-input-group>
        </div>

        <div class="w-25">
          <sw-input-group
            :label="$t('general.from')"
            class="flex-1 mt-2 lg:ml-10"
            style="padding-right: 8%"
          >
            <base-date-picker
              v-model="filters.from_date"
              :calendar-button="true"
              calendar-button-icon="calendar"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('general.to')"
            class="flex-1 mt-2 lg:ml-6"
            style="padding-left: 3%"
          >
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
-->
    <div class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ items_per_page }}</b>
          {{ $t('general.of') }} <b>{{ total_items }}</b>
        </p>
      </div>
    </div>
    <sw-table-component
      class="flex"
      ref="table"
      :data="fetchData"
      headerClass="bg-danger"
      tableClass="w-full"
      tbodyClass="border-b-2 border-gray-200 border-solid"
      :show-filter="false"
    >
      <!-- date -->
      <sw-table-column
        :sortable="true"
        :label="$t('failed_payment_history.date')"
        sort-as="date"
        show="date"
      />

      <!-- transaction number  -->
      <sw-table-column
        :sortable="true"
        :label="$t('failed_payment_history.transaction_number')"
        sort-as="transaction_number"
        show="transaction_number"
      >
        <template slot-scope="row">
          <span>{{ $t('failed_payment_history.transaction_number') }}</span>
          <span>
            {{
              row.transaction_number
                ? row.transaction_number
                : 'Transaction number not generated by payment gateway'
            }}
          </span>
        </template>
      </sw-table-column>

      <!-- customer -->
      <sw-table-column
        :sortable="true"
        :label="$t('failed_payment_history.customer')"
        show="name"
      >
        <template slot-scope="row">
          <router-link
            v-if="row.customer_id"
            :to="{ path: `customers/${row.customer_id}/view` }"
            class="font-medium text-primary-500"
          >
            {{ row.name }}
          </router-link>
          <span v-else>
            {{ row.name }}
          </span>
        </template>
      </sw-table-column>

      <!-- payment gateway -->
      <sw-table-column
        :sortable="true"
        :label="$t('failed_payment_history.payment_gateway')"
        show="payment_gateway"
      >
      </sw-table-column>

      <!-- amount -->
      <sw-table-column
        :sortable="true"
        :label="$t('failed_payment_history.amount')"
        sort-as="amount"
        show="amount"
      >
        <template slot-scope="row">
          <span>{{ $t('failed_payment_history.amount') }}</span>
          <div v-html="$utils.formatMoney(row.amount, row)" />
        </template>
      </sw-table-column>

      <!-- payment number -->
      <sw-table-column
        :sortable="true"
        :label="$t('failed_payment_history.payment_number')"
        show="payment_number"
      >
        <template slot-scope="row">
          <span>{{ $t('failed_payment_history.transaction_number') }}</span>
          <span>
            {{
              row.payment_number ? row.payment_number : `For customer's credit`
            }}
          </span>
        </template>
      </sw-table-column>

      <!-- invoice -->
      <sw-table-column
        :sortable="true"
        sort-as="invoice_number"
        :label="$t('failed_payment_history.invoice')"
      >
        <template slot-scope="row">
          <div v-if="row.invoice == null">
            <span>N/A</span>
          </div>
          <div v-else>
            <router-link
              :to="{ path: `invoices/${row.invoice.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.invoice.invoice_number }}
            </router-link>
          </div>
        </template>
      </sw-table-column>

      <!-- description -->
      <sw-table-column
        :sortable="true"
        :label="$t('failed_payment_history.description')"
        show="description"
      >
        <template slot-scope="row">
          <span>{{ $t('failed_payment_history.description') }}</span>
          <span>
            {{
              row.description
                ? row.description
                : 'Connection failure with Payment Gateway'
            }}
          </span>
        </template>
      </sw-table-column>
    </sw-table-component>
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
  CreditCardIcon,
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
    CreditCardIcon,
    UsersIcon,
  },

  data() {
    return {
      isCustomer: true,
      timeout: null,
      showFilters: false,
      isRequestOngoing: true,
      items_per_page: null,
      total_items: null,
      filters: {
        payment_gateways: {
          value: '',
          name: '',
        },
        from_date: '',
        to_date: '',
        payment_number: '',
        invoice_number: '',
        customer: '',
      },
      payment_gateways: [
        {
          value: 'Authorize',
          name: 'Authorize',
        },
        {
          value: 'Paypal',
          name: 'Paypal',
        },
      ],
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
    }
  },
  computed: {
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  created() {
    this.isCustomer = this.$route.query.customer_id ? false : true
    this.customerId = this.$route.query.customer_id
      ? this.$route.query.customer_id
      : ''
  },
  methods: {
    ...mapActions('failedPaymentHistory', ['fetchFailedPaymentHistory']),
    ...mapActions('user', ['getUserModules']),

    async fetchData({ page, filter, sort }) {
      let data = {
        payment_gateway: this.filters.payment_gateways
          ? this.filters.payment_gateways.value
          : '',
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        payment_number: this.filters.payment_number,
        invoice_number: this.filters.invoice_number,
        customer: this.filters.customer ? this.filters.customer : '',
        customerId: this.customerId,
        orderByField: sort.fieldName || 'date',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchFailedPaymentHistory(data)
      this.isRequestOngoing = false
      this.items_per_page = response.data.failed_payment_history.per_page
      this.total_items = response.data.failed_payment_history.total
      return {
        data: response.data.failed_payment_history.data,
        pagination: {
          totalPages: response.data.failed_payment_history.last_page,
          currentPage: page,
          count: response.data.failedPaymentHistoryTotalCount.total,
        },
      }
    },
    setFilters() {
      clearTimeout(this.timeout)
      this.timeout = setTimeout(() => {
        this.refreshTable()
      }, 900)
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    clearFilter() {
      this.filters = {
        payment_gateways: {
          value: '',
          name: '',
        },
        from_date: '',
        to_date: '',
        payment_number: '',
        invoice_number: '',
        customer: '',
      }
    },

    refreshTable() {
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
        if (modulePermissions.create == 1) {
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
  },
}
</script>

<style scoped>
.w-25 {
  width: 25% !important;
}
</style>
