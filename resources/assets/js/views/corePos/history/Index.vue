<template class="p-0">
  <base-page>
    <sw-page-header :title="$t('general.corepos_history')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />

        <sw-breadcrumb-item to="#" :title="$tc('general.corepos_history')" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button size="lg" variant="primary-outline" @click="toggleFilter">
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="relative grid grid-flow-col grid-rows">

        <div class="w-50" style="margin-left: 1em; margin-right: 1em">

          <sw-input-group :label="$tc('customers.customer', 1)" class="mt-2" style="min-width: 300px">
            <base-customer-select ref="customerSelect" @select="onSelectCustomer" @deselect="clearCustomerSearch" />
          </sw-input-group>

          <sw-input-group :label="$t('general.item')" class="mt-2" style="min-width: 300px">
            <sw-select ref="baseSelect" v-model="filters.item" :options="items" :searchable="true" :show-labels="true"
              :multiple="false" :placeholder="$t('general.item')" class="mt-1" label="name" track-by="id" />
          </sw-input-group>

          <sw-input-group :label="$t('coreposhistory.document_number')" class="mt-2">
            <sw-input v-model="filters.document_number">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

        </div>

        <div class="w-50" style="margin-left: 1em; margin-right: 1em">


          <sw-input-group :label="$t('general.user')" style="min-width: 300px">
            <sw-select ref="baseSelect" v-model="filters.user" :options="usersOptions" :searchable="true"
              :show-labels="true" :multiple="false" :placeholder="$t('general.select_user')" class="mt-1" label="name"
              track-by="id" />
          </sw-input-group>



          <sw-input-group :label="$t('coreposhistory.action')" class="mt-2" style="min-width: 300px">
            <sw-select v-model="filters.action" :options="actions" :group-select="false" :searchable="true"
              :show-labels="false" :placeholder="$t('coreposhistory.action')" :allow-empty="false" track-by="name"
              label="name" />
          </sw-input-group>


        </div>




        <div class="w-25" style="margin-left: 1em; margin-right: 1em">


          <sw-input-group :label="$t('general.from')" class="mt-2">
            <base-date-picker v-model="filters.from_date" :calendar-button="true" calendar-button-icon="calendar" />
          </sw-input-group>


          <sw-input-group :label="$t('general.to')" class="mt-2">
            <base-date-picker v-model="filters.to_date" :calendar-button="true" calendar-button-icon="calendar" />
          </sw-input-group>

        </div>


        <div class="w-50" style="margin-left: 1em; margin-right: 1em"></div>

        <label class="absolute text-sm leading-snug text-black cursor-pointer" style="top: 10px; right: 15px"
          @click="clearFilter">{{ $t('general.clear_all') }}</label>
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <sw-empty-table-placeholder v-show="showEmptyScreen" :title="$t('estimates.no_estimates')"
      :description="$t('estimates.list_of_estimates')">
      <moon-walker-icon class="mt-5 mb-4" />
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div class="relative flex items-center justify-between h-10 mt-5 list-none">
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ totalLine }}</b>
          {{ $t('general.of') }} <b>{{ totalRecord }}</b>
        </p>
      </div>
    </div>

    <div class="relative table-container">
      <sw-table-component ref="table" :show-filter="false" :data="fetchData" table-class="table" >
        <sw-table-column :sortable="true" :filterable="true" :label="$t('coreposhistory.record_date')" show="created_at">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.record_date') }}</span>
            {{ row.formattedCreatedAt }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="false" :filterable="true" :label="$t('coreposhistory.record_type')"
          show="record_type">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.record_type') }}</span>
            {{ $t(row.formattedDocumentNumberType) }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :filterable="true" :label="$t('coreposhistory.document_number')"
          show="document_number">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.document_number') }}</span>
            {{ row.document_number }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :filterable="true" :label="$t('coreposhistory.customer')" show="customer_id">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.customer') }}</span>
            {{ row.formattedDocumentCustomer }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :filterable="true" :label="$t('coreposhistory.user')" show="creator_id">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.user') }}</span>
            {{ row.formattedDocumentCreator }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :filterable="true" :label="$t('coreposhistory.cashregister')"
          show="cash_register_id">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.cashregister') }}</span>
            {{ row.formattedDocumentCashRegister }}
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :filterable="true" :label="$t('coreposhistory.action')" show="action">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.action') }}</span>

            <div v-if="row.formattedDocumentColorAction == 'V'">
              <sw-badge :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                :color="$utils.getBadgeStatusColor('COMPLETED').color" class="px-3 py-1">
                {{ $t(row.formattedDocumentLangAction) }}
              </sw-badge>
            </div>

            <div v-if="row.formattedDocumentColorAction == 'P'">
              <sw-badge :bg-color="$utils.getBadgeStatusColor('VIEWED').bgColor"
                :color="$utils.getBadgeStatusColor('VIEWED').color" class="px-3 py-1">
                {{ $t(row.formattedDocumentLangAction) }}
              </sw-badge>
            </div>

            <div v-if="row.formattedDocumentColorAction == 'R'">
              <sw-badge :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                :color="$utils.getBadgeStatusColor('OVERDUE').color" class="px-3 py-1">
                {{ $t(row.formattedDocumentLangAction) }}
              </sw-badge>
            </div>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="false" :filterable="true" :label="$t('coreposhistory.information')"
          show="information">
          <template slot-scope="row">
            <span>{{ $t('coreposhistory.information') }}</span>
            <div v-if="row.item_id != null">
              <p><b> {{ $t('coreposhistory.item') }}: </b> {{ row.formattedDocumentItems }}</p>
              <p><b> {{ $t('coreposhistory.cant') }}: </b> {{ row.item_quantity }}</p>
              <p><b> {{ $t('coreposhistory.price') }}: </b>
                <div v-html="$utils.formatMoney(row.item_price, defaultCurrency)" />
              </p>

              <p><b> {{ $t('coreposhistory.total') }}: </b>
                <div v-html="$utils.formatMoney(row.item_total, defaultCurrency)" />
              </p>
            </div>

            <div v-if="row.tax_id != null">
              <p><b> {{ $t('coreposhistory.tax') }}: </b> {{ row.formattedDocumentTaxtype }}</p>
              <p><b> {{ $t('coreposhistory.tax_percent') }}: </b> {{ row.tax_type_percent }}</p>
              <p><b> {{ $t('coreposhistory.tax_amount') }}: </b>
                <div v-html="$utils.formatMoney(row.tax_type_amount, defaultCurrency)" />
              </p>

            </div>

            <div v-if="row.discount_type != null && row.discount_amount != null">
              <p><b> {{ $t('coreposhistory.discount') }}: </b> </p>
              <p><b> {{ $t('coreposhistory.discount_type') }}: </b> {{ row.discount_type }}</p>
              <p><b> {{ $t('coreposhistory.discount_amount') }}: </b> {{ row.discount_amount }} </p>

            </div>
            <div v-if="row.tables != null">

              <p><b> {{ $t('coreposhistory.tables') }}: </b> {{ row.tables }}</p>
              <p><b> {{ $t('coreposhistory.cant') }}: </b> {{ row.qty_persons }} </p>

            </div>


            <div v-if="row.notes != null">

              <p><b> {{ $t('coreposhistory.notes') }}: </b> {{ row.notes }}</p>

            </div>



          </template>
        </sw-table-column>

      </sw-table-component>
    </div>


  </base-page>
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import moment from 'moment'

import MoonWalkerIcon from '@/components/icon/MoonwalkerIcon'
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
  UsersIcon,
} from '@vue-hero-icons/solid'

import { DotsHorizontalIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    MoonWalkerIcon,
    DotsHorizontalIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    CheckCircleIcon,
    PaperAirplaneIcon,
    DocumentTextIcon,
    XCircleIcon,
    EyeIcon,
    HashtagIcon,
    UsersIcon,
  },

  data() {
    return {
      showFilters: false,
      users: [],
      items: [],
      actions: [
        {
          name: this.$tc('coreposhistory.hold_created'),
          value: 'hold_created',
        },
        {
          name: this.$tc('coreposhistory.hold_create_completed'),
          value: 'hold_create_completed',
        },
        {
          name: this.$tc('coreposhistory.hold_create_item'),
          value: 'hold_create_item',
        },
        {
          name: this.$tc('coreposhistory.hold_create_discount'),
          value: 'hold_create_discount',
        },
        {
          name: this.$tc('coreposhistory.hold_create_tax'),
          value: 'hold_create_tax',
        },
        {
          name: this.$tc('coreposhistory.hold_edit_tax_amount'),
          value: 'hold_edit_tax_amount',
        },
        {
          name: this.$tc('coreposhistory.hold_edit_item'),
          value: 'hold_edit_item',
        },
        {
          name: this.$tc('coreposhistory.hold_item_without_changes'),
          value: 'hold_item_without_changes',
        },
        {
          name: this.$tc('coreposhistory.hold_edited_print'),
          value: 'hold_edited_print',
        },
        {
          name: this.$tc('coreposhistory.hold_created_print'),
          value: 'hold_created_print',
        },
        {
          name: this.$tc('coreposhistory.hold_create_note'),
          value: 'hold_create_note',
        },
        {
          name: this.$tc('coreposhistory.hold_create_table'),
          value: 'hold_create_table',
        },
      ],
      isRequestOngoing: true,
      activeTab: this.$t('general.all'),
      totalRecord: 0,
      totalLine: 0,

      filters: {
        user: '',
        from_date: '',
        to_date: '',
        customer: '',
        item: '',
        document_number: '',
        action: '',
      },
      timeout: null,
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
        createInvoice: false,
      },
    }
  },

  computed: {
    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('customer', ['customers']),

    ...mapGetters('estimate', ['usersOptions']),

    showEmptyScreen() {
      //return !this.totalEstimates && !this.isRequestOngoing
      return false
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedEstimates
      },
      set: function (val) {
        this.selectEstimate(val)
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

    async created() { },
  },

  watch: {
    filters: {
      handler() {
        this.setFilters()
      },
      deep: true,
    },
  },

  destroyed() { },

  async mounted() {
    this.getUsersOptions()
    const response_items = await this.getItems()
    //  this.items = [...response_items.data.items]
  },

  methods: {
    ...mapActions('corePoshistory', ['fetchCorePOsHistory']),
    ...mapActions('customer', ['fetchCustomers']),
    ...mapActions('user', ['getUserModules']),

    ...mapActions('estimate', ['getUsersOptions']),

    ...mapActions('item', ['getItems', 'getItemsByFilters']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    clearFilter() {
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }
      this.filters = {
        user: '',
        from_date: '',
        to_date: '',
        customer: '',
        item: '',
        document_number: '',
        action: '',
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

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      //this.refreshTable()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        user_id: this.filters.user ? this.filters.user.id : '',
        customer_id: this.filters.customer ? this.filters.customer.id : '',
        item_id: this.filters.item ? this.filters.item.id : '',
        action: this.filters.action ? this.filters.action.value : '',
        from_date: this.filters.from_date ? this.filters.from_date : '',
        to_date: this.filters.to_date ? this.filters.to_date : '',
        document_number: this.filters.document_number
          ? this.filters.document_number
          : '',
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        limit: 10,
        page: page,
      }

      this.isRequestOngoing = true
      const response = await this.fetchCorePOsHistory(data)
     // console.log(response)
      //console.log(response.data.data.last_page)
      //console.log(response.data.data.data.last_page)

      this.isRequestOngoing = false

      this.totalRecord = response.data.coreposcount
      this.totalLine = response.data.data.data.length

      return {
        data: response.data.data.data,
        pagination: {
          totalPages: response.data.data.last_page,
          currentPage: page,
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
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }
      this.filters = {
        user: '',
        from_date: '',
        to_date: '',
        customer: '',
        item: '',
        document_number: '',
        action: '',
      }

      this.clearCustomerSearch()
    },
  },
}
</script>