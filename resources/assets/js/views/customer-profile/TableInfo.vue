<template>
  <div>
  <sw-card v-if="showServices || showPBX || showInvoices || showQuotes || showPayments" class="flex flex-col mt-8">
    <sw-tabs :active-tab="activeMainTab" @update="setMainTabFilter">
      <sw-tab-item v-if="showServices" :title="$t('customers.services')" filter="Services" />
      <sw-tab-item v-if="showPBX" :title="$t('customers.services_pbx')" filter="PBX Services" />
      <sw-tab-item v-if="showInvoices" :title="$tc('invoices.invoice', 2)" filter="Invoices" />
      <sw-tab-item v-if="showQuotes" :title="$tc('estimates.estimate', 2)" filter="Estimates" />
      <sw-tab-item v-if="showPayments" :title="$tc('payments.payment', 2)" filter="Payments" />
    </sw-tabs>

    <div v-if="activeMainTab === 'Services'">
      <div class="text-grey-darkest">
        <sw-tabs :active-tab="activeTab" @update="setStatusFilter">
          <sw-tab-item :title="$t('customers.active')" filter="A" />
          <sw-tab-item :title="$t('customers.pending')" filter="P" />
          <sw-tab-item :title="$t('customers.suspend')" filter="S" />
          <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
        </sw-tabs>
      </div>
      <sw-table-component v-if="activeMainTab === 'Services'" ref="table" key="Services" :show-filter="false"
        :data="fetchServicesData" table-class="table">
        <sw-table-column :sortable="true" :label="$t('services.service_number')" show="code">
          <template slot-scope="row">
            <span>{{ $t('services.service_number') }}</span>

            <router-link :to="`/customer/service/${row.customer_package_id}/view`" class="font-medium text-primary-500">
              {{ row.code }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="'APPLICABLE SERVICE'" show="name" />

        <sw-table-column :sortable="true" :label="$t('customers.amount')" sort-as="total">
          <template slot-scope="row">
            <span>{{ $t('customers.amount') }}</span>
            <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('customers.term')">
          <template slot-scope="row">
            <span>{{ $t('customers.term') }}</span>
            <span>{{ capitalizeFirstLetter(row.term) }}</span>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('customers.activation_date')" sort-as="activation_date"
          show="formattedActivationDate" />

        <sw-table-column :sortable="true" sort-as="customer_packages.renewal_date" :label="$t('customers.renewal_date')"
          show="formattedRenewalDate" />

        <sw-table-column :sortable="false" :filterable="false" cell-class="action-dropdown no-click">
          <template slot-scope="row">
            <span>{{ $t('general.actions') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />
              <sw-dropdown-item :to="`/customer/service/${row.id}/view`" tag-name="router-link">
                <cog-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.manage') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>

    <div v-if="activeMainTab === 'PBX Services'">
      <div class="text-grey-darkest">
        <sw-tabs :active-tab="activeServicesPbxTab" @update="setServicesPbxStatusFilter">
          <sw-tab-item :title="$t('customers.active')" filter="A" />
          <sw-tab-item :title="$t('customers.pending')" filter="P" />
          <sw-tab-item :title="$t('customers.suspend')" filter="S" />
          <sw-tab-item :title="$t('customers.cancelled')" filter="C" />
        </sw-tabs>
      </div>

      <sw-table-component v-if="activeMainTab === 'PBX Services'" ref="services_pbx_table" :show-filter="false"
        key="PBX Services" :data="fetchPbxServicesData" table-class="table">
        <sw-table-column :sortable="true" :label="$t('services.service_number')" show="pbx_services_number">
          <template slot-scope="row">
            <span>{{ $t('services.service_number') }}</span>

            <router-link :to="`/customer/pbx-service/${row.id}/view`" class="font-medium text-primary-500">
              {{ row.pbx_services_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$tc('packages.package', 1)" show="pbx_package_name" />

        <sw-table-column :sortable="true" :label="$t('customers.amount')" sort-as="total">
          <template slot-scope="row">
            <span>{{ $t('customers.amount') }}</span>
            <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('customers.term')">
          <template slot-scope="row">
            <span>{{ $t('customers.term') }}</span>
            <span>{{ capitalizeFirstLetter(row.term) }}</span>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('customers.activation_date')" sort-as="pbx_services.date_begin"
          show="formattedActivationDate" />

        <sw-table-column :sortable="true" sort-as="pbx_services.renewal_date" :label="$t('customers.renewal_date')"
          show="formattedRenewalDate" />

        <sw-table-column :sortable="false" :filterable="false" cell-class="action-dropdown no-click">
          <template slot-scope="row">
            <span>{{ $t('general.actions') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />
              <sw-dropdown-item :to="`/customer/pbx-service/${row.id}/view`" tag-name="router-link">
                <cog-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.manage') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>

    <div v-if="activeMainTab === 'Invoices'">
      <sw-tabs :active-tab="activeInvoiceTab" @update="setInvoiceStatusFilter">
        <sw-tab-item :title="$t('general.all')" filter="" />
        <sw-tab-item :title="$t('general.due')" filter="DUE" />
        <sw-tab-item :title="$t('general.overdue')" filter="OVERDUE" />
        <sw-tab-item :title="$t('general.completed')" filter="COMPLETED" />
        <sw-tab-item :title="$t('general.archived')" filter="ARCHIVED" />
      </sw-tabs>
      <sw-table-component v-if="activeMainTab === 'Invoices'" key="Invoices" ref="invoices_table" :show-filter="false"
        :data="fetchInvoicesData" table-class="table">
        <sw-table-column :sortable="true" :label="$t('invoices.date')" sort-as="invoice_date"
          show="formattedInvoiceDate" />

        <sw-table-column :sortable="true" :label="$t('invoices.number')" show="invoice_number">
          <template slot-scope="row">
            <span>{{ $t('invoices.number') }}</span>
            <router-link :to="`#`" class="font-medium text-primary-500">
              {{ row.invoice_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('invoices.status')" sort-as="status">
          <template slot-scope="row">
            <span>{{ $t('invoices.status') }}</span>

            <sw-badge :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color">
              {{ row.status.replace('_', ' ') }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('invoices.paid_status')" sort-as="paid_status">
          <template slot-scope="row">
            <span>{{ $t('invoices.paid_status') }}</span>

            <sw-badge :bg-color="$utils.getBadgeStatusColor(row.paid_status).bgColor"
              :color="$utils.getBadgeStatusColor(row.paid_status).color">
              {{ row.paid_status.replace('_', ' ') }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('invoices.total')" sort-as="total">
          <template slot-scope="row">
            <span>{{ $t('invoices.total') }}</span>
            <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('invoices.amount_due')" sort-as="due_amount">
          <template slot-scope="row">
            <span>{{ $t('invoices.amount_due') }}</span>
            <div v-html="$utils.formatMoney(row.due_amount, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column :sortable="false" :filterable="false" cell-class="action-dropdown no-click">
          <template slot-scope="row">
            <span>{{ $t('invoices.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />
              <span>
                <sw-dropdown-item tag-name="router-link" :to="`invoice/${row.id}/view`">
                  <eye-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('invoices.view') }}
                </sw-dropdown-item>

                <sw-dropdown-item v-if="row.status === 'SENT' || row.status === 'VIEWED' || (row.status === 'OVERDUE')"
                  @click="$router.push({ name: 'paymentsCustomer.create', params: { invoiceItem: row } })">
                  <credit-card-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('payments.make_payment') }}
                </sw-dropdown-item>
              </span>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>

    <div v-if="activeMainTab === 'Quotes'">
      <sw-tabs :active-tab="activeEstimateTab" @update="setEstimateStatusFilter">
        <sw-tab-item :title="'Pending'" filter="SENT" />
        <sw-tab-item :title="$t('general.all')" filter="" />
      </sw-tabs>
      <sw-table-component v-if="activeMainTab === 'Quotes'" key="Quotes" ref="estimates_table" :show-filter="false"
        :data="fetchEstimatesData" table-class="table">
        <sw-table-column :sortable="true" :label="$t('estimates.date')" sort-as="estimate_date"
          show="formattedEstimateDate" />

        <sw-table-column :sortable="true" :label="$tc('estimates.estimate', 1)" show="estimate_number">
          <template slot-scope="row">
            <span>{{ $tc('estimates.estimate', 1) }}</span>
            <router-link tag-name="router-link" :to="`estimates/${row.id}/view`" class="font-medium text-primary-500">
              {{ row.estimate_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('estimates.status')" show="status">
          <template slot-scope="row">
            <span>{{ $t('estimates.status') }}</span>
            <sw-badge :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color" class="px-3 py-1">
              {{ row.status }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('estimates.total')" sort-as="total">
          <template slot-scope="row">
            <span>{{ $t('estimates.total') }}</span>
            <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column :sortable="false" :filterable="false" cell-class="action-dropdown">
          <template slot-scope="row">
            <span>{{ $t('estimates.action') }}</span>
            <sw-dropdown containerClass="w-56">
              <dot-icon slot="activator" />
              <sw-dropdown-item tag-name="router-link" :to="`estimates/${row.id}/view`">
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item v-if="row.status !== 'ACCEPTED'" @click="onMarkAsAccepted(row.id)">
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_accepted') }}
              </sw-dropdown-item>

              <sw-dropdown-item v-if="row.status !== 'REJECTED'" @click="onMarkAsRejected(row.id)">
                <x-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.mark_as_rejected') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>

    <div v-if="activeMainTab === 'Payments'">
      <sw-tabs :active-tab="activePaymentTransactionTab" @update="setPaymentsStatusFilter">
        <sw-tab-item :title="$t('general.all')" filter="" />
        <sw-tab-item :title="$t('general.void')" filter="Void" />
        <sw-tab-item :title="$t('general.unapply')" filter="Unapply" />
        <sw-tab-item :title="$t('general.refunded')" filter="Refunded" />
        <sw-tab-item :title="$t('general.approved')" filter="Approved" />
      </sw-tabs>
      <sw-table-component v-if="activeMainTab === 'Payments'" key="Payments" ref="payments_table" :show-filter="false"
        :data="fetchPaymentsData" table-class="table">
        <sw-table-column :sortable="true" :label="$t('payments.date')" sort-as="payment_date"
          show="formattedPaymentDate" />

        <sw-table-column :sortable="true" :label="$t('payments.payment_number')" show="payment_number">
          <template slot-scope="row">
            <span>{{ $t('payments.payment_number') }}</span>
            <router-link :to="`#`" class="font-medium text-primary-500">
              {{ row.payment_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('payments.payment_mode')" show="payment_mode">
          <template slot-scope="row">
            <span>{{ $t('payments.payment_mode') }}</span>
            <span>{{ row.payment_mode ? row.payment_mode : 'Not selected' }}</span>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('payments.invoice')" sort-as="invoice_id" show="invoice_number">
          <template slot-scope="row">
            <span>{{ $t('invoices.invoice_number') }}</span>
            <span>{{ row.invoice_number ? row.invoice_number : 'No Invoice' }}</span>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('payments.amount')">
          <template slot-scope="row">
            <span>{{ $t('payments.amount') }}</span>
            <div v-html="$utils.formatMoney(row.amount, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column :sortable="false" :filterable="false" cell-class="action-dropdown">
          <template slot-scope="row">
            <span>{{ $t('payments.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </sw-card>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  XCircleIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  PencilIcon,
  HashtagIcon,
  CogIcon,
  CreditCardIcon
} from '@vue-hero-icons/solid'

export default {
  components: {
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    EyeIcon,
    XCircleIcon,
    DocumentTextIcon,
    PaperAirplaneIcon,
    CheckCircleIcon,
    TrashIcon,
    PencilIcon,
    HashtagIcon,
    CogIcon,
    CreditCardIcon
  },
  data() {
    return {
      timeout: null,
      activeTab: this.$t('customers.active'),
      activeInvoiceTab: this.$t('general.all'),
      activeEstimateTab: this.$t('general.all'),
      activeServicesPbxTab: this.$t('customers.active'),
      activePaymentTransactionTab: this.$t('general.all'),
      status: { name: 'Active', value: 'A' },
      invoice_status: { name: '', value: '' },
      estimate_status: { name: 'SENT', value: 'SENT' },
      services_pbx_status: { name: 'Active', value: 'A' },
      payments_transaction_status: "ALL",
      showPayments: false,
      showQuotes: false,
      showInvoices: false,
      showReports: false,
      showServices: false,
      showPBX: false,
      activeMainTab: ''
    }
  },
  created() {
    if (this.showServices === '1') {

    }
  },
  computed: {
    ...mapGetters('user', ['currentUser', 'settingsCompany']),
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
    settingsCompany: {
      immediate: true,
      handler(newVal) {
        if (newVal) {
          this.updateMenuVisibility();
        }
      },
    },
  },
  methods: {
    ...mapActions('customerProfile', [
      'fetchServices',
      'fetchInvoices',
      'fetchEstimates',
      'fetchExpenses',
      'fetchPayments',
      'fetchPbxServices',
    ]),

    ...mapActions('estimateCust', [
      'markAsAccepted',
      'markAsRejected',
    ]),

    capitalizeFirstLetter(string) {
      return string.charAt(0).toUpperCase() + string.slice(1)
    },

    async fetchServicesData({ page, filter, sort }) {

      let data = {
        customer_id: this.currentUser.id,
        status: this.status.value,
        orderByField: sort.fieldName || 'customer_packages.id',
        orderBy: sort.order || 'desc',
        page,
      }

      let res = await window.axios.post(`/api/v1/customer/services`, data)

      return {
        data: res.data.services.data,
        pagination: {
          totalPages: res.data.services.last_page,
          currentPage: page,
        },
      }

    },
    updateMenuVisibility() {
      if (!this.settingsCompany) return;
      this.showServices = this.settingsCompany.enable_service_customer === "1";
      this.showPBX = this.settingsCompany.enable_pbxservice_customer === "1";
      this.showInvoices = this.settingsCompany.enable_invoice_customer === "1";
      this.showQuotes = this.settingsCompany.enable_quotes_customer === "1";
      this.showReports = this.settingsCompany.enable_report_customer === "1";
      this.showPayments = this.settingsCompany.enable_payment_customer === "1";
      if (!this.settingsCompany) return;

      if (this.showServices) {
        this.activeMainTab = 'Services';
        return;
      }
      if (this.showPBX) {
        this.activeMainTab = 'PBX';
        return;
      }
      if (this.showInvoices) {
        this.activeMainTab = 'Invoices';
        return;
      }
      if (this.showQuotes) {
        this.activeMainTab = 'Quotes';
        return;
      }
      if (this.showPayments) {
        this.activeMainTab = 'Payments';
        return;
      }

    },
    setStatusFilter(val) {
      if (this.activeTab === val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('customers.active'):
          this.status = {
            name: 'Active',
            value: 'A',
          }
          break

        case this.$t('customers.pending'):
          this.status = {
            name: 'Pending',
            value: 'P',
          }
          break

        case this.$t('customers.suspend'):
          this.status = {
            name: 'Suspend',
            value: 'S',
          }
          break

        case this.$t('customers.cancelled'):
          this.status = {
            name: 'Cancelled',
            value: 'C',
          }
          break
      }

      this.$refs.table.refresh()
    },

    async fetchInvoicesData({ page, filter, sort }) {
      let data = {
        status: this.invoice_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchInvoices(data)
      let list = response.data.invoicesList.data.map((invoice) => {
        return {
          ...invoice,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.invoicesList.last_page,
          currentPage: page,
          count: response.data.invoicesList.count,
        },
      }
    },

    setInvoiceStatusFilter(val) {
      if (this.activeInvoiceTab === val.title) {
        return true
      }
      this.activeInvoiceTab = val.title
      switch (val.title) {
        case this.$t('general.due'):
          this.invoice_status = {
            name: 'DUE',
            value: 'DUE',
          }
          break

        case this.$t('general.draft'):
          this.invoice_status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          break
        case this.$t('general.overdue'):
          this.invoice_status = {
            name: 'OVERDUE',
            value: 'OVERDUE',
          }
          break

        case this.$t('general.completed'):
          this.invoice_status = {
            name: 'COMPLETED',
            value: 'COMPLETED',
          }
          break
        case this.$t('general.archived'):
          this.invoice_status = {
            name: 'ARCHIVED',
            value: 'ARCHIVED',
          }
          break

        default:
          this.invoice_status = {
            name: '',
            value: '',
          }
          break
      }
      this.$refs.invoices_table.refresh()
    },
    setMainTabFilter(val) {

      if (this.activeMainTab === val.title) {
        return true;
      }

      this.activeMainTab = val.title;
      switch (val.title) {
        case this.$t('customers.services'):
          this.mainTabStatus = {
            name: 'Services',
            value: 'services',
          };
          break;
        case this.$t('customers.services_pbx'):
          this.mainTabStatus = {
            name: 'PBX Services',
            value: 'pbx',
          };
          break;
        case this.$tc('invoices.invoice', 2):
          this.mainTabStatus = {
            name: 'Invoices',
            value: 'invoices',
          };
          break;
        case this.$tc('estimates.estimate', 2):
          this.mainTabStatus = {
            name: 'Quotes',
            value: 'estimates',
          };
          break;
        case this.$tc('payments.payment', 2):
          this.mainTabStatus = {
            name: 'Payments',
            value: 'payments',
          };
          break;
      }

    },

    async fetchEstimatesData({ page, filter, sort }) {
      let data = {
        status: this.estimate_status.value,
        from_date: '',
        to_date: '',
        estimate_number: '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      let response = await this.fetchEstimates(data)

      let list = response.data.estimates.data.map((estimate) => {
        return {
          ...estimate,
        }
      })

      return {
        data: list,
        pagination: {
          totalPages: response.data.estimates.last_page,
          currentPage: page,
          count: response.data.estimates.count,
        },
      }
    },

    setEstimateStatusFilter(val) {

      if (this.activeEstimateTab === val.title) {
        return true
      }
      this.activeEstimateTab = val.title

      switch (val.title) {
        case 'Pending':
          this.estimate_status = {
            name: 'SENT',
            value: 'SENT',
          }
          break

        default:
          this.estimate_status = {
            name: 'All',
            value: '',
          }
          break
      }
      this.$refs.estimates_table.refresh()
    },

    async onMarkAsAccepted(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_accepted'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id,
            status: 'ACCEPTED',
          }

          let response = await this.markAsAccepted(data)

          if (response.data) {
            this.$refs.estimates_table.refresh()
            window.toastr['success'](
              this.$tc('estimates.marked_as_accepted_message')
            )
          }
        }
      })
    },

    async onMarkAsRejected(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_rejected'),
        icon: '/assets/icon/times-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (markedAsRejected) => {
        if (markedAsRejected) {
          const data = {
            id: id,
            status: 'REJECTED',
          }

          let response = await this.markAsRejected(data)

          if (response.data) {
            this.$refs.estimates_table.refresh()
            window.toastr['success'](
              this.$tc('estimates.marked_as_rejected_message')
            )
          }
        }
      })
    },

    async fetchExpensesData({ page, filter, sort }) {
      let data = {
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchExpenses(data)
      return {
        data: response.data.expenses.data,
        pagination: {
          totalPages: response.data.expenses.last_page,
          currentPage: page,
          count: response.data.expenses.count,
        },
      }
    },

    async fetchPaymentsData({ page, filter, sort }) {
      let data = {
        transaction_status: this.payments_transaction_status,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchPayments(data)
      return {
        data: response.data.payments.data,
        pagination: {
          totalPages: response.data.payments.last_page,
          currentPage: page,
          count: response.data.payments.count,
        },
      }
    },

    async fetchPbxServicesData({ page, filter, sort }) {
      let data = {
        status: this.services_pbx_status.value,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      let response = await this.fetchPbxServices(data)
      return {
        data: response.data.pbxServices.data,
        pagination: {
          totalPages: response.data.pbxServices.last_page,
          currentPage: page,
          count: response.data.pbxServices.count,
        },
      }
    },

    setServicesPbxStatusFilter(val) {
      if (this.activeServicesPbxTab === val.title) {
        return true
      }
      this.activeServicesPbxTab = val.title
      switch (val.title) {
        case this.$t('customers.active'):
          this.services_pbx_status = {
            name: 'Active',
            value: 'A',
          }
          break

        case this.$t('customers.pending'):
          this.services_pbx_status = {
            name: 'Pending',
            value: 'P',
          }
          break

        case this.$t('customers.suspend'):
          this.services_pbx_status = {
            name: 'Suspend',
            value: 'S',
          }
          break

        case this.$t('customers.cancelled'):
          this.services_pbx_status = {
            name: 'Cancelled',
            value: 'C',
          }
          break
      }

      this.$refs.payments_table.refresh()
    },

    setPaymentsStatusFilter(val) {
      if (this.activePaymentTransactionTab == val.title) {
        return true
      }
      this.activePaymentTransactionTab = val.title
      switch (val.title) {
        default:
          this.payments_transaction_status = "Void"
          break
        case this.$t('general.unapply'):
          this.payments_transaction_status = "Unapply"
          break
        case this.$t('general.refunded'):
          this.payments_transaction_status = "Refunded"
          break
        case this.$t('general.approved'):
          this.payments_transaction_status = "Approved"
          break
        case this.$t('general.all'):
          this.payments_transaction_status = "ALL"
          break

      }
      this.$refs.payments_table.refresh()
    },
    setFilters() {
      clearTimeout(this.timeout)
      this.timeout = setTimeout(() => {
        // this.refreshTable()
      }, 900)
    },
  },
}
</script>

<style lang="scss">
// Dropdown
.tab {
  overflow: hidden;
}

.tab-content {
  max-height: 0;
  transition: all 0.5s;
}

input:checked+.tab-label .test {
  background-color: #000;
}

input:checked+.tab-label .test svg {
  transform: rotate(180deg);
  stroke: #fff;
}

input:checked+.tab-label::after {
  transform: rotate(90deg);
}

input:checked~.tab-content {
  max-height: 400vh;
}
</style>
