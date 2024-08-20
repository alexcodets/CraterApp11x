<template>
  <base-page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('invoices.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
          <sw-breadcrumb-item to="#" :title="$tc('invoices.invoice', 2)" active />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end gato">
        <sw-button v-show="totalInvoices" size="lg" variant="primary-outline" class="w-full md:w-auto md:ml-4 mb-2 md:mb-0 gato" @click="toggleFilter">
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          size="lg"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0 gato"
          @click="openImportModal"

        >
          <upload-icon class="h-4 mr-1 -ml-2 font-bold"/>
          {{ $t('general.import') }}
        </sw-button>

          <sw-button v-if="permissionModule.create && isSuperAdmin" tag-name="router-link" to="/admin/invoices/create" class="w-full md:w-auto md:ml-4 mb-2 md:mb-0" size="lg"
            variant="primary">
            <plus-icon class="w-6 h-6 mr-1 -ml-2 gatos" />
            {{ $t('invoices.new_invoice') }}
          </sw-button>
      </div>
    </div>


    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="relative grid grid-flow-col grid-rows">
        <div class="w-50 mx-0 md:mx-5">
          <sw-input-group :label="$tc('customers.customer', 1)" class="mt-2" style="min-width: 300px">
            <base-customer-select ref="customerSelect" @select="onSelectCustomer" @deselect="clearCustomerSearch" />
          </sw-input-group>

          <sw-input-group :label="$t('customers.customcode')" class="mt-2" style="min-width: 300px">
            <sw-input v-model="filters.customcode">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

          <sw-button @click="exportInvoices" class="mt-4" size="lg" variant="primary" :disabled="isDownloadReport">
            <plus-icon class="w-6 h-6 mr-1 -ml-2" />
            {{ $t('invoices.btn_export_invoices') }}
          </sw-button>
        </div>

        <div class="w-25 mx-0 md:mx-5">
          <sw-input-group :label="$t('invoices.invoice_number')" class="mt-2 xl:ml-8">
            <sw-input v-model="filters.invoice_number">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

          <sw-input-group :label="$t('invoices.status')" class="mt-2 xl:mx-8">
            <sw-select v-model="filters.status" :options="status" :group-select="false" :searchable="true"
              :show-labels="false" :placeholder="$t('general.select_a_status')" :allow-empty="false"
              group-values="options" group-label="label" track-by="name" label="name" @remove="clearStatusSearch()"
              @select="setActiveTab" />
          </sw-input-group>
        </div>

        <div class="w-25 mx-0 md:mx-5">
          <sw-input-group :label="$t('general.from')" class="mt-2">
            <base-date-picker v-model="filters.from_date" :calendar-button="true" calendar-button-icon="calendar" />
          </sw-input-group>

          <sw-input-group :label="$t('general.to')" class="mt-2">
            <base-date-picker v-model="filters.to_date" :calendar-button="true" calendar-button-icon="calendar" />
          </sw-input-group>
        </div>

        <!-- Select add days, week, month -->
        <div class="w-25 mx-0 md:mx-5">
          <sw-input-group :label="$t('invoices.label_range_dates')" class="mt-2 xl:mx-8">
            <sw-select v-model="selectRangeFromDateToDate" :options="optionRangeTimeFromDateTodate" :searchable="true"
              track-by="name" :show-labels="false"  :allow-empty="true"
              @input="calculateRangeFromDateToDate" label="name" >
            </sw-select>
          </sw-input-group>

        </div>

        <label class="absolute text-sm leading-snug text-black cursor-pointer" @click="clearFilter"
          style="top: 10px; right: 15px">{{ $t('general.clear_all') }}</label>

      </sw-filter-wrapper>
    </slide-y-up-transition>

                      <!--------------------------------------------->
                      <!-- 🚨 |  Component to display an empty   |--->
                      <!-- 🚀 |  table placeholder when no       |--->
                      <!-- 🔄 |  invoices are available          |--->
                      <!-- 📊 | Start of Empty Table Placeholder |--->

                            <sw-empty-table-placeholder
                              v-show="showEmptyScreen"
                              :title="$t('invoices.no_invoices')"
                              :description="$t('invoices.list_of_invoices')">

                              <moon-walker-icon class="mt-5 mb-4" />  

                              <sw-button 
                                slot="actions"
                                tag-name="router-link"
                                to="/admin/invoices/create"
                                size="lg"
                                variant="primary-outline">
                                
                                <plus-icon class="w-6 h-6 mr-1 -ml-2" />
                                {{ $t('invoices.new_invoice') }}
                                
                              </sw-button>

                            </sw-empty-table-placeholder>
                      <!--------------------✨ | End ------------------------->



      <!--------------------------------------------------------------------->
      <!-- 📋 | Component for displaying invoices and their status       |--->
      <!-- 🕵️‍♂️ | Shows invoice count, filters, and actions based on       |--->
      <!-- 📊 | current view (active/inactive archived) and selected     |--->
      <!-- 🔄 | invoices                                                 |--->
      <!-- 🔍 | Conditional rendering based on invoice data availability |--->
      <!-- ✨ | Start of Invoice Management Display Component            |--->
<div v-show="!showEmptyScreen" class="relative">

        <!-- 📅 | Section for displaying invoice counts and filters          |--->
        <!-- 🗂️ | Shows the number of invoices or archived invoices based on |--->
        <!-- 🔢 | whether archived view is active, with appropriate counts   |--->
        <!-- 🔄 | Display tabs for filtering invoices based on status        |--->
        <div class="mt-5">

          <!-- 📊 | Display counts based on whether archived view is active |--->
          <span v-if="IsArchivedActived == false">
            <p class="absolute -mt-3 md:mt-12 text-sm">
              {{ $t('general.showing') }}: <b>{{ invoices.length }}</b>
              {{ $t('general.of') }} <b>{{ totalInvoices }}</b>
            </p>
          </span>
          <span v-else-if="IsArchivedActived == true">
            <p class="absolute -mt-5 text-sm">
              {{ $t('general.showing') }}: <b>{{ archived.length }}</b>
              {{ $t('general.of') }} <b>{{ totalArchived }}</b>
            </p>
          </span>

          <!-- 📑 | Tabs for filtering invoices based on status              |--->
          <!-- 🔄 | Provides options to filter by different invoice statuses |--->
          <sw-tabs ref="tabsStatusInvoice" class="hidden md:inline" :active-tab="activeTab" @update="setStatusFilter">
            <sw-tab-item :title="$t('general.all')" filter="" />
            <sw-tab-item :title="$t('general.due')" filter="DUE" />
            <sw-tab-item :title="$t('general.overdue')" filter="OVERDUE" />
            <sw-tab-item :title="$t('general.completed')" filter="COMPLETED" />
            <sw-tab-item :title="$t('general.save_as_draft')" filter="SAVE_DRAFT" />
            <sw-tab-item :title="$t('general.archived')" filter="ARCHIVED" />
          </sw-tabs>
        </div>

        <!-- 🔽 | Dropdown menu for actions on selected invoices          |--->
        <!-- 🔄 | Allows bulk actions like deleting selected invoices     |--->
        <sw-transition type="fade">
          <sw-dropdown v-if="selectedInvoices.length" class="absolute float-right -mt-3 md:mt-2">
            <span slot="activator" class="flex block text-sm font-medium cursor-pointer select-none text-primary-400" style="font-size: 17px">
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>
            <sw-dropdown-item @click="removeMultipleInvoices">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>

        <!-- ☑️ | Checkboxes for selecting all invoices                  |--->
        <!-- 📍 | Different visibility for small and medium devices     |--->
        <div v-show="invoices && invoices.length" class="absolute z-10 items-center pl-4 select-none md:mt-10">
          <sw-checkbox v-model="selectAllFieldStatus" variant="primary" size="sm" class="hidden md:inline" @change="selectAllInvoices" />
          <sw-checkbox v-model="selectAllFieldStatus" :label="$t('general.select_all')" variant="primary" size="sm" class="md:hidden mt-7" @change="selectAllInvoices" />
        </div>
      <!-- ✨ | End of Invoice Management Display Component              |--->



            <!----------------------------------------------------------------------->
            <!-- 📋 | Component to display a table of invoices                   |--->
            <!-- 🔢 | Shows columns for selecting invoices, date, number,        |--->
            <!-- 📅 | customer, status, paid status, total, and amount due       |--->
            <!-- 🔄 | Columns are sortable and some include conditional          |--->
            <!-- 🚀 | formatting and links based on permissions and data         |--->
            <!-- ✨ | Start of Invoices Table Component                          |--->

            <sw-table-component ref="table" :data="fetchData" class="row-container back-green expanded">
  <!-- ✅ | Column for selecting invoices with checkboxes | -->
  <sw-table-column>
    <div slot-scope="row" class="flex items-start">
      <sw-checkbox :id="row.id" v-model="selectField" :value="row.id" variant="primary" size="sm" />
    </div>
  </sw-table-column>

   <!-- 💰 | Column for displaying total amount -->
   <sw-table-column class="responsive-design hidden"> 
    <template slot-scope="row"  class="responsive-design" id="fon">
      <div class="flex flex-col items-start transition-all duration-300 total-container responsive-design">
        <div v-html="$utils.formatMoney(row.total, row.user.currency)" class="total-amount" />
      </div>
      <div :class="row.isExpanded ? 'expanded' : 'collapsed'" class="flex flex-col items-start transition-all duration-300 due-container responsive-design">
        <div v-html="$utils.formatMoney(row.due_amount, row.user.currency)" class="due-amount" />
      </div>
      <div class="num-sta-pai-phone responsive-design" v-if="row.isExpanded">
      <span class="num-label mb-2 bold">{{ $t('number') }}</span>
      <span  class="sta-label mb-2 bold">{{ $t('estimates.status') }}</span>
      <span class="pai-label bold">{{ $t('invoices.paid_status') }}</span>
    </div>
    </template>
  </sw-table-column>



  <!-- 📅 | Column for displaying invoice date, sortable by invoice_date | -->
  <sw-table-column :sortable="true" :label="$t('invoices.date')" sort-as="invoice_date" class="d-1">
    <div slot-scope="row" class="flex items-start">
      <span class="date">{{ row.formattedInvoiceDate }}</span>
    </div>
  </sw-table-column>

  <!-- #️⃣ | Column for displaying invoice number -->
  <sw-table-column :sortable="true" :label="$t('number')" show="invoice_number" class="num tab">
    <template slot-scope="row">
      <div class="flex flex-col transition-all duration-300 num tab">
        <router-link :to="{ path: `invoices/${row.id}/view/${row.deleted_at}` }" class="font-medium text-primary-500 num tab">
          {{ row.invoice_number }}
        </router-link>
      </div>
    </template>
  </sw-table-column>

  <!-- 📊 | Column for displaying invoice status -->
  <sw-table-column :sortable="true" :label="$t('estimates.status')" show="status" class="sta tab">
    <template slot-scope="row">
      <div class="flex flex-col transition-all duration-300 sta tab">
        <sw-badge :bg-color="$utils.getBadgeStatusColor(row.status).bgColor" :color="$utils.getBadgeStatusColor(row.status).color" class="px-3 py-1 sta tab">
          {{ row.status }}
        </sw-badge>
      </div>
    </template>
  </sw-table-column>

  <!-- 💵 | Column for displaying paid status -->
  <sw-table-column :sortable="true" :label="$t('invoices.paid_status')" sort-as="paid_status" class="pai tab">
    <template slot-scope="row">
      <div class="flex flex-col transition-all duration-300 pai tab">
        <sw-badge :bg-color="$utils.getBadgeStatusColor(row.status).bgColor" :color="$utils.getBadgeStatusColor(row.status).color" class="pai tab">
          {{ row.paid_status.replace('_', ' ') }}
        </sw-badge>
      </div>
    </template>
  </sw-table-column>

  <!-- 💰 | Column for displaying total amount -->
  <sw-table-column :sortable="true" :label="$t('invoices.total')" sort-as="total">
    <template slot-scope="row">
      <div class="flex flex-col items-start transition-all duration-300 total-container">
        <div v-html="$utils.formatMoney(row.total, row.user.currency)" class="total-amount pc" />
      </div>
    </template>
  </sw-table-column>

  <!-- 💸 | Column for displaying amount due -->
  <sw-table-column :sortable="true" :label="$t('invoices.amount_due')" sort-as="due_amount">
    <template slot-scope="row">
      <div class="flex flex-col items-start transition-all duration-300 due-container">
        <div v-html="$utils.formatMoney(row.due_amount, row.user.currency)" class="due-amount pc" />
      </div>
    </template>
  </sw-table-column>

  
<!-- 🔄 | Column for expand/collapse button -->
<sw-table-column :sortable="false" filterable="false" cell-class="no-click" class="expan responsive-design">
  <template slot-scope="row">
    <span class="date responsive-design">{{ row.formattedInvoiceDate }}</span>
    <div :class="row.isExpanded ? 'expanded' : 'collapsed'" class="row-container transition-all duration-300 ease-in-out responsive-design">
      <a @click="toggleExpand(row)" class="expand-button text-blue-700 cursor-pointer ex flex items-center">
        <svg 
          class="icon"
          :class="{ 'rotate-180': row.isExpanded }"
          width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"
        >
          <path d="M19 9l-7 7-7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <span class="ml-2">{{ row.isExpanded ? 'Less' : 'More' }}</span>
      </a>
    </div>
    <div class="num-sta-pai-phone responsive-design" v-if="row.isExpanded">
      <router-link :to="{ path: `invoices/${row.id}/view/${row.deleted_at}` }" class="font-medium text-primary-500 num mb-2">
        {{ row.invoice_number }}
      </router-link>
      <sw-badge :bg-color="$utils.getBadgeStatusColor(row.status).bgColor" :color="$utils.getBadgeStatusColor(row.status).color" class="px-3 py-1 sta mb-2">
        {{ row.status }}
      </sw-badge>
      <sw-badge :bg-color="$utils.getBadgeStatusColor(row.status).bgColor" :color="$utils.getBadgeStatusColor(row.status).color" class="pai">
        {{ row.paid_status.replace('_', ' ') }}
      </sw-badge>
    </div>
  </template>
</sw-table-column>



<sw-table-column :sortable="false" :filterable="false">
          <template slot-scope="row">
            <sw-dropdown>
              <dot-icon slot="activator" />
              <span v-if="IsArchivedActived != true">
                <div v-if="permissionModule.update">
                  <sw-dropdown-item v-if="isSuperAdmin && row.noeditable == 0" tag-name="router-link"
                    :to="`invoices/${row.id}/edit`">
                    <pencil-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t('general.edit') }}
                  </sw-dropdown-item>
                </div>

                <div v-if="permissionModule.read">
                  <sw-dropdown-item v-if="isSuperAdmin" tag-name="router-link" :to="`invoices/${row.id}/view`">
                    <eye-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t('invoices.view') }}
                  </sw-dropdown-item>

                  <sw-dropdown-item v-else tag-name="router-link" :to="`invoice/${row.id}/view`">
                    <eye-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t('invoices.view') }}
                  </sw-dropdown-item>
                </div>

                <sw-dropdown-item v-if="(row.status == 'DRAFT' || row.status == 'SAVE_DRAFT') &&
                  isSuperAdmin
                  " @click="sendInvoice(row)">
                  <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('invoices.send_invoice') }}
                </sw-dropdown-item>

                
              <sw-dropdown-item
               v-if="row.status != 'DRAFT'"
                @click="sendSMSInvoice(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.send_invoice_sms') }}
              </sw-dropdown-item>

                <sw-dropdown-item v-if="row.status != 'DRAFT' &&
                  row.status != 'SAVE_DRAFT' &&
                  isSuperAdmin
                  " @click="sendInvoice(row)">
                  <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('invoices.resend_invoice') }}
                </sw-dropdown-item>

                <sw-dropdown-item v-if="(row.status == 'DRAFT' || row.status == 'SAVE_DRAFT') &&
                  isSuperAdmin
                  " @click="markInvoiceAsSent(row.id)">
                  <check-circle-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('invoices.mark_as_sent') }}
                </sw-dropdown-item>

                <div v-if="permissionModule.createPayments">
                  <sw-dropdown-item v-if="row.status === 'SENT' ||
                    row.status === 'VIEWED' ||
                    (row.status === 'OVERDUE' && isSuperAdmin)
                    " tag-name="router-link" :to="`/admin/payments/${row.id}/create`">
                    <credit-card-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t('payments.record_payment') }}
                  </sw-dropdown-item>
                </div>

                <div v-if="permissionModule.createPayments && activate_pay_button">
                  <sw-dropdown-item v-if="row.status === 'SENT' ||
                    row.status === 'VIEWED' ||
                    (row.status === 'OVERDUE' && isSuperAdmin)
                    " tag-name="router-link"
                    :to="`/admin/payments/multiple/customer/${row.user_id}/invoice/${row.id}/create`">
                    <credit-card-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t('payments.pos_record_payment') }}
                  </sw-dropdown-item>
                </div>

                <sw-dropdown-item v-if="!isSuperAdmin" tag-name="router-link" :to="`#`">
                  <credit-card-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('payments.record_payment') }}
                </sw-dropdown-item>

                <div v-if="permissionModule.create">
                  <sw-dropdown-item v-if="isSuperAdmin" @click="onCloneInvoice(row.id)">
                    <document-duplicate-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t('invoices.clone_invoice') }}
                  </sw-dropdown-item>
                </div>

                <div v-if="permissionModule.delete">
                  <sw-dropdown-item v-if="isSuperAdmin &&
                    row.status != 'COMPLETED' &&
                    row.paid_status === 'UNPAID'
                    " @click="removeInvoice(row.id)">
                    <trash-icon class="h-5 mr-3 text-gray-600" />
                    {{ $t('general.delete') }}
                  </sw-dropdown-item>
                </div>
                <div v-if="permissionModule.delete">
                  <div v-if="row.inv_avalara_bool">
                    <sw-dropdown-item @click="removeInvoiceAvalara(row)">
                      <trash-icon class="h-5 mr-3 text-gray-600" />
                      {{ $t('invoices.invoice_delete_avalara') }}
                    </sw-dropdown-item>
                  </div>
                </div>
              </span>
              <span v-if="IsArchivedActived">
                <sw-dropdown-item v-if="row.is_recuperable" @click="Restore(row)">
                  <save-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('general.restore') }}
                </sw-dropdown-item>

                <sw-dropdown-item v-if="row.inv_avalara_bool == 1 &&
                  row.avalara_invoice != null &&
                  row.avalara_invoice.status != 3 &&
                  row.avalara_invoice.status != 2
                  " @click="AvalaraVoid(row)">
                  <trash-icon class="h-5 mr-3 text-gray-600" />
                  Avalara Void
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
      isDownloadReport: false,
      activate_pay_button: false,
      showFilters: false,
      currency: null,
      IsArchivedActived: false,
      dynamicTables: [], // Array para mantener datos dinámicos de las tablas
      visibleRows: {}, // Objeto para hacer seguimiento de la visibilidad de filas por índice de tabla y fila
      status: [
        {
          label: 'Status',
          isDisable: true,
          options: [
            { name: 'DRAFT', value: 'DRAFT' },
            { name: 'DUE', value: 'DUE' },
            { name: 'SENT', value: 'SENT' },
            { name: 'VIEWED', value: 'VIEWED' },
            { name: 'OVERDUE', value: 'OVERDUE' },
            { name: 'COMPLETED', value: 'COMPLETED' },
            { name: 'SAVE AS DRAFT', value: 'SAVE_DRAFT' },
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
        customcode: '',
      },
      timeout: null,
      permissionModule: {
        create: false,
        createPayments: false,
        update: false,
        delete: false,
        read: false,
      },
      optionRangeTimeFromDateTodate: [
        { name: this.$t('invoices.range_dates.today'), value: 'today' },
        { name: this.$t('invoices.range_dates.yesterday'), value: 'yesterday' },
        { name: this.$t('invoices.range_dates.last_week'), value: 'last_week' },
        { name: this.$t('invoices.range_dates.month'), value: 'month' },
        { name: this.$t('invoices.range_dates.last_month'), value: 'last_month' },
      ],
      selectRangeFromDateToDate: null,
      filter_export: {
        order_by: "invoice_number",
        order: "desc"
      }
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalInvoices && !this.isRequestOngoing
    },

    buttonText() {
      return this.isRowVisible ? 'Less' : 'More';
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('invoice', [
      'selectedInvoices',
      'totalInvoices',
      'totalArchived',
      'invoices',
      'archived',
      'selectAllField',
    ]),
    ...mapGetters('user', ['currentUser']),

    isSuperAdmin() {
      return this.currentUser.role == 'super admin' ? true : false
    },

    selectField: {
      get: function () {
        return this.selectedInvoices
      },
      set: function (val) {
        this.selectInvoice(val)
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
      this.selectAllInvoices()
    }
  },

  mounted() {
    this.setFiltersUrlQuery()
    this.permissionsUserModule()
  },


  methods: {
    toggleExpand(row){
      this.$set(row,'isExpanded',!row.isExpanded);
    },
    ...mapActions('invoice', [
      'fetchInvoices',
      'fetchArchived',
      'getRecord',
      'selectInvoice',
      'resetSelectedInvoices',
      'selectAllInvoices',
      'deleteInvoice',
      'deleteMultipleInvoices',
      'sendEmail',
      'markAsSent',
      'setSelectAllState',
      'cloneInvoice',
      'RestoreInvoice',
      'AvalaraVoidFetch',
      'AvalaraVoidFetchStatus',
      'fetchExportReportInvoices',
    ]),
    ...mapActions('customer', ['fetchCustomers']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('modal', ['openModal']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('company', ['fetchCompanySettings']),
    ...mapActions('modules', ['getModules']),

    async sendInvoice(invoice) {
      this.openModal({
        title: this.$t('invoices.send_invoice'),
        componentName: 'SendInvoiceModal',
        id: invoice.id,
        data: invoice,
        variant: 'lg',
      })
    },

    async sendSMSInvoice(invoice) {
      this.openModal({
        title: this.$t('invoices.send_invoice_sms'),
        componentName: 'SendInvoiceSMSModal',
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
          let response = await this.markAsSent(data)
          this.refreshTable()
          if (response.data) {
            window.toastr['success'](
              this.$tc('invoices.mark_as_sent_successfully')
            )
          }
        }
      })
    },

    async onCloneInvoice(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.confirm_clone'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let response = await this.cloneInvoice({ id })

          this.refreshTable()

          if (response.data) {
            window.toastr['success'](this.$tc('invoices.cloned_successfully'))
            this.$router.push(
              `/admin/invoices/${response.data.invoice.id}/edit`
            )
          }
        }
      })
    },

    setStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.due'):
          this.IsArchivedActived = false
          this.filters.status = {
            name: 'DUE',
            value: 'DUE',
          }
          this.$router.push({
            query: {
              status: 'DUE',
            },
          })
          break

        case this.$t('general.draft'):
          this.IsArchivedActived = false
          this.filters.status = {
            name: 'DRAFT',
            value: 'DRAFT',
          }
          this.$router.push({
            query: {
              status: 'DRAFT',
            },
          })
          break

        case this.$t('general.completed'):
          this.IsArchivedActived = false
          this.filters.status = {
            name: 'COMPLETED',
            value: 'COMPLETED',
          }
          this.$router.push({
            query: {
              status: 'COMPLETED',
            },
          })
          break

        case this.$t('general.save_as_draft'):
          this.IsArchivedActived = false
          this.filters.status = {
            name: 'SAVE AS DRAFT',
            value: 'SAVE_DRAFT',
          }
          this.$router.push({
            query: {
              status: 'SAVE_DRAFT',
            },
          })
          break

        case this.$t('general.archived'):
          this.IsArchivedActived = true
          this.filters.status = {
            name: 'ARCHIVED',
            value: 'ARCHIVED',
          }
          this.$router.push({
            query: {
              status: 'ARCHIVED',
            },
          })
          break

        case this.$t('general.overdue'):
          this.IsArchivedActived = false
          this.filters.status = {
            name: 'OVERDUE',
            value: 'OVERDUE',
          }
          this.$router.push({
            query: {
              status: 'OVERDUE',
            },
          })
          break
        default:
          this.IsArchivedActived = false
          this.filters.status = {
            name: '',
            value: '',
          }
          this.$router.push({
            query: {},
          })
          break
      }
      // this.refreshTable()
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        customer_id: this.filters.customer ? this.filters.customer.id : '',
        status: this.filters.status?.value,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        invoice_number: this.filters.invoice_number,
        customcode: this.filters.customcode,
        orderByField: sort.fieldName || 'invoice_number',
        orderBy: sort.order || 'desc',
        v2: true,
        page,
      }

      this.filter_export.order_by = sort.fieldName || 'invoice_number'
      this.filter_export.order = sort.order || 'desc'

      this.isRequestOngoing = true
      let response

      if (this.IsArchivedActived != true) {
        response = await this.fetchInvoices(data)
      } else {
        response = await this.fetchArchived(data)
      }

      this.isRequestOngoing = false
      this.currency = response.data.currency
      let totalCount
      if (this.IsArchivedActived != true) {
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
      };
    },

    toggleRowVisibility(rowId, tableIndex) {
      // Crear entrada de tabla si no existe
      if (!this.visibleRows[tableIndex]) {
        this.$set(this.visibleRows, tableIndex, {});
      }
      // Alternar la visibilidad de la fila específica en la tabla específica
      this.$set(this.visibleRows[tableIndex], rowId, !this.visibleRows[tableIndex][rowId]);
    },
    
    isRowVisible(rowId, tableIndex) {
      // Verificar si la fila específica en la tabla específica es visible
      return !!(this.visibleRows[tableIndex] && this.visibleRows[tableIndex][rowId]);
    },

    async fetchListArchived({ page, filter, sort }) {
      this.isRequestOngoing = true
      let response = await this.fetchArchived()
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
      clearTimeout(this.timeout)
      this.timeout = setTimeout(() => {
        this.resetSelectedInvoices()
        this.refreshTable()
      }, 900)
    },

    clearFilter() {
      // Seteando la pestaña principal (All)
      this.selectRangeFromDateToDate = null
      this.activeTab = this.$t('general.all')
      this.IsArchivedActived = false
      this.$router.push({
        query: {},
      })
      //
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }
      this.filters = {
        customer: '',
        status: { name: '', value: '' },
        from_date: '',
        to_date: '',
        invoice_number: '',
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
          let res = await this.deleteInvoice({ ids: [id] })

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
        this.resetSelectedInvoices()
      })
    },

    async removeInvoiceAvalara(row) {
      this.id = row.id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('invoices.confirm_delete_avalara'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (value) => {
        if (value) {
          let res = await this.deleteInvoice({ ids: [row.id] })
          if (res.data.success) {
            window.toastr['success']('invoices.deleted_message')
            let resVoidAvalara = await this.AvalaraVoidFetch({
              data: row,
              id: row.id,
            })
            if (resVoidAvalara.data.success == true) {
              window.toastr['success'](
                this.$tc('invoices.deleted_message_avalara')
              )
              this.$refs.table.refresh()
            } else if (resVoidAvalara.data.success == false) {
              window.toastr['error'](
                this.$tc('invoices.something_went_wrong_avalara')
              )
              this.$refs.table.refresh()
            }
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
        this.resetSelectedInvoices()
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
          let res = await this.deleteMultipleInvoices()

          if (res.data.error === 'payment_attached') {
            window.toastr['error'](
              this.$t('invoices.payment_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }

          if (res.data) {
            this.$refs.table.refresh()
            this.resetSelectedInvoices()
            window.toastr['success'](this.$tc('invoices.deleted_message', 2))
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    },

    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },
    setActiveTab(val) {
      switch (val.value) {
        /*case 'DRAFT':
          this.activeTab = this.$t('general.draft')
          this.$router.push({
            query: {
              status: 'DRAFT',
            },
          })
          break
        */
        case 'COMPLETED':
          this.activeTab = this.$t('general.completed')
          this.$router.push({
            query: {
              status: 'COMPLETED',
            },
          })
          break
        case 'DUE':
          this.activeTab = this.$t('general.due')
          this.$router.push({
            query: {
              status: 'DUE',
            },
          })
          break
        case 'OVERDUE':
          this.activeTab = this.$t('general.overdue')
          this.$router.push({
            query: {
              status: 'OVERDUE',
            },
          })
          break

        case 'SAVE_DRAFT':
          this.activeTab = this.$t('general.save_as_draft')
          this.$router.push({
            query: {
              status: 'SAVE_DRAFT',
            },
          })
          break

        case 'ARCHIVED':
          this.activeTab = this.$t('general.archived')
          this.$router.push({
            query: {
              status: 'ARCHIVED',
            },
          })
          break
        default:
          this.activeTab = this.$t('general.all')
          this.$router.push({
            query: {},
          })
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
          let res = await this.RestoreInvoice(row)
          if (res.data.success) {
            window.toastr['success'](this.$tc('invoices.restore', 1))
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
    setFiltersUrlQuery() {
      if (this.$route.query.status && this.$route.query.status == 'DUE') {
        this.setStatusFilter({ title: this.$t('general.due') })
        this.setActiveTab({
          name: 'DUE',
          value: 'DUE',
        })
      }
      if (this.$route.query.status && this.$route.query.status == 'OVERDUE') {
        this.setStatusFilter({ title: this.$t('general.overdue') })
        this.setActiveTab({
          name: 'OVERDUE',
          value: 'OVERDUE',
        })
      }
      if (this.$route.query.status && this.$route.query.status == 'COMPLETED') {
        this.setStatusFilter({ title: this.$t('general.completed') })
        this.setActiveTab({
          name: 'COMPLETED',
          value: 'COMPLETED',
        })
      }

      if (
        this.$route.query.status &&
        this.$route.query.status == 'SAVE_DRAFT'
      ) {
        this.setStatusFilter({ title: this.$t('general.save_as_draft') })
        this.setActiveTab({
          name: 'SAVE AS DRAFT',
          value: 'SAVE_DRAFT',
        })
      }

      if (this.$route.query.status && this.$route.query.status == 'ARCHIVED') {
        this.setStatusFilter({ title: this.$t('general.archived') })
        this.setActiveTab({
          name: 'ARCHIVED',
          value: 'ARCHIVED',
        })
      }
    },

    async permissionsUserModule() {
      const data = {
        module: 'invoices',
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
      } else if (
        permissions.exist == true &&
        permissions.permissions[0] != null
      ) {
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

      const dataPayments = {
        module: 'payments',
      }
      const permissionsPayments = await this.getUserModules(dataPayments)

      if (permissionsPayments.super_admin == true) {
        this.permissionModule.createPayments = true
      } else if (permissionsPayments.exist == true) {
        const modulePermissions = permissionsPayments.permissions[0]
        if (modulePermissions == null) {
          this.permissionModule.createPayments = false
        } else if (modulePermissions.create == 1) {
          this.permissionModule.createPayments = true
        }
      }

      const modules = ['corePOS']
      const modulesArray = await this.getModules(modules)
      var moduleCorePos = null


      if (typeof modulesArray.modules != 'undefined') {
        moduleCorePos = modulesArray.modules.find(
          (element) => element.name === 'corePOS'
        )
      }

      if (moduleCorePos && moduleCorePos.status == 'A') {
        let res = await this.fetchCompanySettings(['activate_pay_button'])
        this.activate_pay_button =
          res.data.activate_pay_button == '0' ? false : true
      } else {
        this.activate_pay_button = false
      }


    },
    // Avalara Void
    async AvalaraVoid(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: 'This will remove the invoice in avalara',
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (Confirm) => {
        if (Confirm) {
          let res = await this.AvalaraVoidFetch(row)

          if (res.data.success) {
            window.toastr['success'](res.data.message)
            this.$refs.table.refresh()
            return true
          } else {
            window.toastr['error'](res.data.message)
            return true
          }
        }
      })
    },


    async exportInvoices() {
      let data = {
        customer_id: this.filters.customer ? this.filters.customer.id : '',
        status: this.filters.status?.value,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        invoice_number: this.filters.invoice_number,
        customcode: this.filters.customcode,
        order_by: this.filter_export.order_by || 'invoice_number',
        order: this.filter_export.order || 'desc',
      }

      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('invoices.export_report_invoices'),
        icon: '/assets/icon/alert-svgrepo-com.svg',
        buttons: true,
        dangerMode: false,
      }).then(async (Confirm) => {
        this.isDownloadReport = true
        if (Confirm) {
          try {

           // console.log(data)
            let resExportReportInvoices = await this.fetchExportReportInvoices(data)

            if (resExportReportInvoices) {
              this.isDownloadReport = false
            } else {
              this.isDownloadReport = false

            }
          } catch (error) {
          //  console.log('error export invoices')
           // console.log(error)
            this.isDownloadReport = false
          }
        }else{
          this.isDownloadReport = false
        }
      })
    },


    openImportModal() {

      this.openModal({
        title: this.$t('invoices.import_invoice'),
        componentName: 'InvoiceImportModal',
        data: {},
        variant: 'lg',
        refreshData: this.$refs.table.refresh,
      })


    },


    calculateRangeFromDateToDate() {

      let data = this.selectRangeFromDateToDate
      switch (data.value) {
        case "today":
          this.filters.from_date = moment().format("YYYY-MM-DD")
          this.filters.to_date = moment().format("YYYY-MM-DD")
          this.refreshTable()
          break;
        case "yesterday":
          this.filters.from_date =  moment().subtract(1, 'days').startOf('day').format("YYYY-MM-DD");
          this.filters.to_date = moment().subtract(1, 'days').endOf('day').format("YYYY-MM-DD");
          this.refreshTable()

          break;
        case "last_week":
          this.filters.from_date = moment().subtract(1, 'week').startOf('week').format("YYYY-MM-DD");
          this.filters.to_date = moment().subtract(1, 'week').endOf('week').format("YYYY-MM-DD");
          this.refreshTable()

          break;
        case "month":
          this.filters.from_date = moment().startOf('month').format("YYYY-MM-DD");
          this.filters.to_date = moment().endOf('month').format("YYYY-MM-DD");
          this.refreshTable()

          break;
        case "last_month":
          this.filters.from_date =  moment().subtract(1, 'month').startOf('month').format("YYYY-MM-DD");
          this.filters.to_date =  moment().subtract(1, 'month').endOf('month').format("YYYY-MM-DD");
          this.refreshTable()

          break;

        default:
          break;
      }

    }
  },

}
</script>

<style scoped>
@media (min-width: 768px) {
.responsive-design{
  display: none !important;
}
}

  @media (max-width: 768px) {


.fon{
  background-color: red;
}
.expanded {
  margin-bottom: 50px;
  transition: margin-bottom 0.3s ease-in-out;
}


  .icon {
  transition: transform 0.3s ease-in-out;
}

.rotate-180 {
  transform: rotate(180deg);
}

.expand-button {
  display: flex;
  align-items: center;
  justify-content: center;
}
  span.num-label.mb-2.bold {
    font-weight: bold;
}
span.sta-label.mb-2.bold{
  font-weight: bold;
}
span.pai-label.bold{
  font-weight: bold;
}
  .num-sta-pai-phone{
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .tab{

    display: none;
  }
  .flex.expanded {
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: space-between;
  }
  span.date {
    display: none;
}
.total-amount.pc {
    display: none;
}
.due-amount.pc {
    display: none;
}

.total-amount {
    font-size: 30px;
    color: green;
    font-weight: bold;
    margin-bottom: 5px;
}
.due-amount {
    color: red;
}
 
  .flex.flex-col.items-start.transition-all.duration-300.total-container {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: space-around;
}
.flex.flex-col.items-start.transition-all.duration-300.due-container {
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    justify-content: center;
}
  }
</style>