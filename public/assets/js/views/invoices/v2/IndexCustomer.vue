<template>
  <base-page>
    <sw-page-header :title="$t('invoices.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="#" :title="$tc('invoices.invoice', 2)" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalInvoices"
          size="lg"
          variant="primary-outline"
          class="ml-4"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          v-if="isSuperAdmin"
          tag-name="router-link"
          to="/customer/invoices/create"
          class="ml-4"
          size="lg"
          variant="primary"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('invoices.new_invoice') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper
        v-show="showFilters"
        class="relative grid grid-flow-col grid-rows"
      >
        <sw-input-group :label="$t('invoices.status')" class="mt-2 xl:mx-8">
          <sw-select
            v-model="filters.status"
            :options="status"
            :group-select="false"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('general.select_a_status')"
            :allow-empty="false"
            group-values="options"
            group-label="label"
            track-by="name"
            label="name"
            @remove="clearStatusSearch()"
            @select="setActiveTab"
          />
        </sw-input-group>

        <sw-input-group :label="$t('general.from')" class="mt-2">
          <base-date-picker
            v-model="filters.from_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <div
          class="
            hidden
            w-8
            h-0
            mx-4
            border border-gray-400 border-solid
            xl:block
          "
          style="margin-top: 3.5rem"
        />

        <sw-input-group :label="$t('general.to')" class="mt-2">
          <base-date-picker
            v-model="filters.to_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('invoices.invoice_number')"
          class="mt-2 xl:ml-8"
        >
          <sw-input v-model="filters.invoice_number">
            <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
          </sw-input>
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          @click="clearFilter"
          style="top: 10px; right: 15px"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition> 

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('invoices.no_invoices')"
      :description="$t('invoices.list_of_invoices')"
    >
      <moon-walker-icon class="mt-5 mb-4" />

      <sw-button
        v-if="!isCustomer"
        slot="actions"
        tag-name="router-link"
        to="/customer/invoices/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('invoices.new_invoice') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative">
      <div class="relative mt-5">
        <span v-if="IsArchivedActived == false">
          <p class="absolute right-0 m-0 text-sm" style="top: 50px">
            {{ $t('general.showing') }}: <b>{{ invoices.length }}</b>

            {{ $t('general.of') }} <b>{{ totalInvoices }}</b>
          </p>
        </span>
        <span v-else-if="IsArchivedActived == true">
          <p class="absolute right-0 m-0 text-sm" style="top: 50px">
            {{ $t('general.showing') }}: <b>{{ archived.length }}</b>

            {{ $t('general.of') }} <b>{{ totalArchived }}</b>
          </p>
        </span>
        <sw-tabs :active-tab="activeTab" @update="setStatusFilter">
          <sw-tab-item :title="$t('general.all')" filter="" />
          <sw-tab-item :title="$t('general.due')" filter="DUE" />
          <!--  <sw-tab-item :title="$t('general.draft')" filter="DRAFT" />-->
          <sw-tab-item :title="$t('general.overdue')" filter="OVERDUE" />
          <sw-tab-item :title="$t('general.completed')" filter="COMPLETED" />
          <sw-tab-item :title="$t('general.archived')" filter="ARCHIVED" />
        </sw-tabs>
      </div>

      <div
        v-show="invoices && invoices.length"
        class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12"
      >
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllInvoicesCustomer"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllInvoicesCustomer"
        />
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
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
          :label="$t('invoices.date')"
          sort-as="invoice_date"
          show="formattedInvoiceDate"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.number')"
          show="invoice_number"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.number') }}</span>
            <router-link
              :to="{ path: `invoice/${row.id}/view` }"
              class="font-medium text-primary-500"
            >   
              {{ row.invoice_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.status')"
          sort-as="status"
        >
          <template slot-scope="row">
            <span> {{ $t('invoices.status') }}</span>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
            >
              {{ row.status.replace('_', ' ') }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.paid_status')"
          sort-as="paid_status"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.paid_status') }}</span>

            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
            >
              {{ row.paid_status.replace('_', ' ') }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.amount_due')"
          sort-as="due_amount"
        >
          <template slot-scope="row">
            <span>{{ $t('invoices.amount_due') }}</span>

            <div
              v-html="$utils.formatMoney(row.due_amount, row.user.currency)"
            />
          </template>
        </sw-table-column>

        <sw-table-column :sortable="false" :filterable="false">
          <template slot-scope="row">
            <span>{{ $t('invoices.action') }}</span>

            <sw-dropdown>
              <dot-icon slot="activator" />
              <span>

                <sw-dropdown-item
                  tag-name="router-link"
                  :to="`invoice/${row.id}/view`"
                >
                  <eye-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('invoices.view') }}
                </sw-dropdown-item> 

                <sw-dropdown-item
                  v-if="
                    row.status === 'SENT' ||
                    row.status === 'VIEWED' ||
                    (row.status === 'OVERDUE')
                  "
                  @click="$router.push({name: 'paymentsCustomer.create', params: {invoiceItem: row}})"
                >
                  <credit-card-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('payments.make_payment') }}
                </sw-dropdown-item>
              </span> 
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import MoonWalkerIcon from '@/components/icon/MoonwalkerIcon'
import moment from 'moment'

import {
  PencilIcon,
  DocumentDuplicateIcon,
  CreditCardIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  PlusIcon,
  DocumentTextIcon,
  PaperAirplaneIcon,
  CheckCircleIcon,
  TrashIcon,
  XCircleIcon,
  HashtagIcon,
} from '@vue-hero-icons/solid'

import { DotsHorizontalIcon } from '@vue-hero-icons/outline'
import DropdownField from '../../../components/custom-fields/DropdownField.vue'

export default {
  components: {
    MoonWalkerIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    DotsHorizontalIcon,
    PencilIcon,
    DocumentDuplicateIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    CreditCardIcon,
    HashtagIcon,
    DropdownField,
  },

  data() {
    return {
      showFilters: false,
      currency: null,
      IsArchivedActived: false,
      status: [
        {
          label: 'Status',
          isDisable: true,
          options: [
            //{ name: 'DRAFT', value: 'DRAFT' },
            { name: 'DUE', value: 'DUE' },
            { name: 'SENT', value: 'SENT' },
            { name: 'VIEWED', value: 'VIEWED' },
            { name: 'OVERDUE', value: 'OVERDUE' },
            { name: 'COMPLETED', value: 'COMPLETED' },
          ],
        },
        {
          label: 'Paid Status',
          options: [
            { name: 'UNPAID', value: 'UNPAID' },
            { name: 'PAID', value: 'PAID' },
            { name: 'PARTIALLY PAID', value: 'PARTIALLY_PAID' },
          ],
        },
      ],

      isRequestOngoing: true,
      activeTab: this.$t('general.all'),
      filters: {
        customer: '',
        status: { name: '', value: '' },
        from_date: '',
        to_date: '',
        invoice_number: '',
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalInvoices && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('invoiceCustomer', [
      'selectedInvoices',
      'totalInvoices',
      'totalArchived',
      'invoices',
      'archived',
      'selectAllField',
    ]),

    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return (this.currentUser && this.currentUser.role == 'super admin') ? true : false
    },

    isCustomer() {
      return (this.currentUser && this.currentUser.role == 'customer') ? true : false
    },

    selectField: {
      get: function () {
        return this.selectedInvoices
      },
      set: function (val) {
        this.selectInvoiceCustomer(val)
      },
    },

    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllStateCustomer(val)
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
      this.selectAllInvoicesCustomer()
    }
  },

  methods: {

    ...mapActions('invoiceCustomer', [
      'fetchInvoicesCustomer',
      'fetchArchivedCustomer',
      'getRecord',
      'selectInvoiceCustomer',
      'resetSelectedInvoicesCustomer',
      'selectAllInvoicesCustomer',
      'deleteInvoiceCustomer',
      'deleteMultipleInvoicesCustomer',
      'sendEmailCustomer',
      'markAsSentCustomer',
      'setSelectAllStateCustomer',
      'cloneInvoiceCustomer',
      'RestoreInvoiceCustomer',
      
    ]),

    ...mapActions('customer', ['fetchCustomers']),

    ...mapActions('modal', ['openModal']),

    async sendInvoice(invoice) {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: invoice.id,
        data: invoice,
        variant: 'lg',
      })
    },

    async markInvoiceAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.invoice_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          const data = {
            id: id,
            status: 'SENT',
          }
          let response = await this.markAsSentCustomer(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](
              this.$tc('invoices.mark_as_sent_successfully')
            )
          }
        }
      })
    },

    async onCloneInvoiceCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_clone'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.cloneInvoiceCustomer({ id })

          this.refreshTable()

          if (response.data) {
            window.toastr['success'](this.$tc('invoices.cloned_successfully'))
            this.$router.push(
              `/customer/invoices/${response.data.invoice.id}/edit`
            )
          }
        }
      })
    },
    setStatusFilter(val) {
      if (this.activeInvoiceTab === val.title) {
        return true
      }
      this.activeInvoiceTab = val.title
      switch (val.title) {
        case this.$t('general.due'):
          this.filters.status = {
            name: 'DUE',
            value: 'DUE',
          }
          break

        case this.$t('general.draft'):
          this.filters.status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          break
        case this.$t('general.overdue'):
          this.filters.status = {
            name: 'OVERDUE',
            value: 'OVERDUE',
          }
          break

        case this.$t('general.completed'):
          this.filters.status = {
            name: 'COMPLETED',
            value: 'COMPLETED',
          }
          break
        case this.$t('general.archived'):
          this.filters.status = {
            name: 'ARCHIVED',
            value: 'ARCHIVED',
          }
          break

        default:
          this.filters.status = {
            name: '',
            value: '',
          }
          break
      }
      this.$refs.table.refresh()
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        customer_id: this.currentUser.id,
        status: this.filters.status.value,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        invoice_number: this.filters.invoice_number,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        v2: true,
        page,
      }
      this.isRequestOngoing = true
      let response
      if (this.filters.status.value !== 'ARCHIVED') {
        response = await this.fetchInvoicesCustomer(data)
      } else {
        response = await this.fetchArchivedCustomer()
      }
      this.isRequestOngoing = false
      this.currency = response.data.currency
      let totalCount
      if (this.filters.status.value == 'ARCHIVED') {
        totalCount = response.data.invoices.count
      } else {
        totalCount = response.data.invoices.invoiceTotalCount
      }

      return {
        data: response.data.invoices.data,
        pagination: {
          totalPages: response.data.invoices.last_page,
          currentPage: page,
          count: totalCount,
        },
      }
    },

    async fetchListArchived({ page, filter, sort }) {
      this.isRequestOngoing = true
      let response = await this.fetchArchivedCustomer()
      this.isRequestOngoing = false
      this.currency = response.data.currency

      return {
        data: response.data.invoices.data,
        pagination: {
          totalPages: response.data.invoices.last_page,
          currentPage: page,
          count: response.data.invoices.invoiceTotalCount,
        },
      }
    },

    setFilters() {
      this.resetSelectedInvoicesCustomer()
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        customer: '',
        status: '',
        from_date: '',
        to_date: '',
        invoice_number: '',
      }

      this.activeTab = this.$t('general.all')
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },
    async removeInvoice(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoiceCustomer({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.deleted_message'))
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          window.toastr['error'](res.data.error)
          return true
        }
        this.resetSelectedInvoicesCustomer()
      })
    },

    async removeMultipleInvoices() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteMultipleInvoicesCustomer()

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          if (res.data) {
            this.$refs.table.refresh()
            this.resetSelectedInvoicesCustomer()
            window.toastr['success'](this.$tc('invoices.deleted_message', 2))
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },
    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },
    setActiveTab(val) {
      switch (val.value) {
        case 'DRAFT':
          this.activeTab = this.$t('general.draft')
          break
        case 'DUE':
          this.activeTab = this.$t('general.due')
          break
        case 'ARCHIVED':
          this.activeTab = this.$t('general.archived')
          break
        default:
          this.activeTab = this.$t('general.all')
          break
      }
    },
    /* CONFIRMAR DID / INSERT PBX SERVICES*/
    async Restore(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.did.confirm'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (Confirm) => {
        if (Confirm) {
          let res = await this.RestoreInvoiceCustomer(row)
          if (res.data.success) {
            window.toastr['success'](
              this.$tc('invoices.restore', 1)
            )
            this.$refs.table.refresh()
            return true
          }
          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('packages.user_attached_message'),
              this.$t('general.action_failed')
            )
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
