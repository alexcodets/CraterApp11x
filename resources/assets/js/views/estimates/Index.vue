<template>
  <base-page>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <sw-page-header :title="$t('estimates.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />

        <sw-breadcrumb-item
          to="#"
          :title="$tc('estimates.estimate', 2)"
          active
        />
      </sw-breadcrumb>
    </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalEstimates"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="estimates/create"
          size="lg"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('estimates.new_estimate') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper
        v-show="showFilters"
        class="relative grid grid-flow-col grid-rows"
      >
        <div class="w-50" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group :label="$tc('customers.customer', 1)" class="mt-2" style="min-width:300px">
            <base-customer-select
              ref="customerSelect"
              @select="onSelectCustomer"
              @deselect="clearCustomerSearch"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('customers.customcode')"
            color="black-light"
            class="mt-2"
            style="min-width:300px"
          >
            <sw-input v-model="filters.customcode">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>

        <div class="w-25" style="margin-left: 1em; margin-right: 2em">
          <sw-input-group
            :label="$t('estimates.estimate_number')"
            color="black-light"
            class="mt-2 xl:ml-8"
          >
            <sw-input v-model="filters.estimate_number">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>

          <sw-input-group :label="$t('estimates.status')" class="mt-2 xl:mx-8">
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
        </div>

        <div class="w-25" style="margin-left: 1em; margin-right: 1em">

        <sw-input-group
          :label="$t('general.from')"
          color="black-light"
          class="mt-2"
        >
          <base-date-picker
            v-model="filters.from_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>



        <sw-input-group
          :label="$t('general.to')"
          color="black-light"
          class="mt-2"
        >
          <base-date-picker
            v-model="filters.to_date"
            :calendar-button="true"
            calendar-button-icon="calendar"
          />
        </sw-input-group>
      </div>

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
      :title="$t('estimates.no_estimates')"
      :description="$t('estimates.list_of_estimates')"
    >
      <moon-walker-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/estimates/create"
        size="lg"
        variant="primary-outline"

      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('estimates.add_new_estimate') }}
      </sw-button>

    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative">
      <div class="relative mt-5">
        <p class="absolute m-0 text-sm md:mt-12">
          {{ $t('general.showing') }}: <b>{{ estimates.length }}</b>
          {{ $t('general.of') }} <b>{{ totalEstimates }}</b>
        </p>

        <sw-tabs
          :active-tab="activeTab"
          class="mb-10 hidden md:inline"
          @update="setStatusFilter"
        >
          <sw-tab-item :title="$t('general.all')" filter="" />
          <sw-tab-item :title="$t('general.draft')" filter="DRAFT" />
          <sw-tab-item :title="$t('general.sent')" filter="SENT" />
          <sw-tab-item :title="$t('general.viewed')" filter="VIEWED" />
          <sw-tab-item :title="$t('general.expired')" filter="EXPIRED" />
          <sw-tab-item :title="$t('general.accepted')" filter="ACCEPTED" />
          <sw-tab-item :title="$t('general.rejected')" filter="REJECTED" />
        </sw-tabs>

        <sw-transition type="fade">
          <sw-dropdown
            v-if="selectedEstimates.length"
            class="absolute float-right mt-2"
          >
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

            <sw-dropdown-item @click="removeMultipleEstimates">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div
        v-show="estimates && estimates.length"
        class="absolute z-10 items-center pl-4 select-none md:mt-12"
      >
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllEstimates"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden mt-8"
          @change="selectAllEstimates" 
        />
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
        class="-mt-6 md:mt-5"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="flex items-center">
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
          :label="$t('estimates.date')"
          sort-as="estimate_date"
          show="formattedEstimateDate"
        />

        <sw-table-column
          :sortable="true"
          :label="$tc('estimates.estimate_number', 1)"
          show="estimate_number"
        >
          <template slot-scope="row">
            <span>{{ $tc('estimates.estimate', 1) }}</span>
            <router-link
              :to="{ path: `estimates/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
              >
              {{ row.estimate_number }}
            </router-link>
            <span v-else >
              {{ row.estimate_number }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$tc('estimates.customer')"
          sort-as="name"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $tc('estimates.customer') }}</span>
            <router-link
              :to="{ path: `customers/${row.user_id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
              >
              {{ row.name }}
            </router-link>
            <span v-else >
              {{ row.name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('estimates.customer_num')"
          sort-as="customcode"
          show="customcode"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('estimates.status')"
          show="status"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.status') }}</span>
            <sw-badge
              :bg-color="$utils.getBadgeStatusColor(row.status).bgColor"
              :color="$utils.getBadgeStatusColor(row.status).color"
              class="px-3 py-1"
            >
              {{ row.status }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('invoices.total')"
          sort-as="total"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.total') }}</span>
            <div v-html="$utils.formatMoney(row.total, row.user.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('estimates.action') }} </span>
            <sw-dropdown containerClass="w-56">
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`estimates/${row.id}/edit`"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeEstimate(row.id)"
              v-if="permissionModule.delete">
                <trash-icon class="h-5 mr-3 text-gray-600"
                />
                {{ $t('general.delete') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`estimates/${row.id}/view`"
                v-if="permissionModule.read "
                >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <!---->
              <sw-dropdown-item
                tag-name="router-link"
                :to="{ path: `/admin/customers/${row.user_id}/view` }"
              >
                <users-icon class="h-5 mr-3 text-gray-600" />
                {{ $t("general.go_to_customer") }}
             </sw-dropdown-item>

             <sw-dropdown-item
                tag-name="router-link"
                :to="{ path: `/admin/users/${row.assigne_user_id}/view` }"
              >
                <users-icon class="h-5 mr-3 text-gray-600" />
                {{ $t("general.go_to_asiggned") }}
             </sw-dropdown-item>
             <!---->

              <sw-dropdown-item @click="convertInToinvoice(row.id)"
              v-if="permissionModule.createInvoice">
                <document-text-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.convert_to_invoice') }}
              </sw-dropdown-item>

              <div v-if="permissionModule.update">
                <sw-dropdown-item
                v-if="row.status == 'DRAFT'"
                @click="onMarkAsSent(row.id)"
                >
                  <check-circle-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('estimates.mark_as_sent') }}
                </sw-dropdown-item>
              </div>

              <sw-dropdown-item
                v-if="row.status !== 'SENT'"
                @click="sendEstimate(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.send_estimate') }}
              </sw-dropdown-item>

              <!-- resend estimte -->
              <sw-dropdown-item
                v-if="row.status == 'SENT' || row.status == 'VIEWED'"
                @click="sendEstimate(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.resend_estimate') }}
              </sw-dropdown-item>

                 <!-- sms estimte -->
                 <sw-dropdown-item
                
                @click="sendSMSEstimate(row)"
              >
                <paper-airplane-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('estimates.sendsms_estimate') }}
              </sw-dropdown-item>

              <div v-if="permissionModule.update">
                <sw-dropdown-item
                v-if="row.status !== 'ACCEPTED' || row.status !== 'DRAFT'"
                @click="onMarkAsAccepted(row.id)"
                >
                  <check-circle-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('estimates.mark_as_accepted') }}
                </sw-dropdown-item>
              </div>

              <div v-if="permissionModule.update">
                <sw-dropdown-item
                v-if="row.status == 'DRAFT' || row.status == 'SENT' || row.status == 'VIEWED'"
                @click="onMarkAsRejected(row.id)"
                >
                  <x-circle-icon class="h-5 mr-3 text-gray-600" />
                  {{ $t('estimates.mark_as_rejected') }}
                </sw-dropdown-item>
              </div>
            </sw-dropdown>
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
  UsersIcon
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
    UsersIcon
  },

  data() {
    return {
      showFilters: false,
      currency: null,
      status: [
        {
          label: 'Status',
          isDisable: true,
          options: [
            { name: 'DRAFT', value: 'DRAFT' },
            { name: 'SENT', value: 'SENT' },
            { name: 'VIEWED', value: 'VIEWED' },
            { name: 'EXPIRED', value: 'EXPIRED' },
            { name: 'ACCEPTED', value: 'ACCEPTED' },
            { name: 'REJECTED', value: 'REJECTED' },
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
        estimate_number: '',
        customcode: '',
      },
      timeout: null,
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
        createInvoice: false
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalEstimates && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('customer', ['customers']),

    ...mapGetters('estimate', [
      'selectedEstimates',
      'totalEstimates',
      'estimates',
      'selectAllField',
    ]),

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
  },

  watch: {
    filters: {
      handler() {
        this.setFilters()
      },
      deep: true,
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllEstimates()
    }
  },

  mounted() {
    this.permissionsUserModule()
    this.setFiltersUrlQuery()
  },

  methods: {
    ...mapActions('estimate', [
      'fetchEstimates',
      'resetSelectedEstimates',
      'getRecord',
      'selectEstimate',
      'selectAllEstimates',
      'deleteEstimate',
      'deleteMultipleEstimates',
      'markAsSent',
      'convertToInvoice',
      'setSelectAllState',
      'markAsAccepted',
      'markAsRejected',
      'sendEmail',
    ]),

    ...mapActions('user', ['getUserModules']),
    ...mapActions('modal', ['openModal']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        customer_id:
          this.filters.customer === ''
            ? this.filters.customer
            : this.filters.customer.id,
        status: this.filters.status.value,
        from_date: this.filters.from_date,
        to_date: this.filters.to_date,
        estimate_number: this.filters.estimate_number,
        customcode: this.filters.customcode,
        orderByField: sort.fieldName || 'estimate_number',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchEstimates(data)

      this.isRequestOngoing = false

      this.currency = response.data.currency

      return {
        data: response.data.estimates.data,
        pagination: {
          totalPages: response.data.estimates.last_page,
          currentPage: page,
          count: response.data.estimates.count,
        },
      }
    },

    setStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }

      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.draft'):
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

        case this.$t('general.sent'):
          this.filters.status = {
            name: 'SENT',
            value: 'SENT',
          }
          this.$router.push({
            query: {
              status: 'SENT',
            },
          })
          break

        case this.$t('general.viewed'):
          this.filters.status = {
            name: 'VIEWED',
            value: 'VIEWED',
          }
          this.$router.push({
            query: {
              status: 'VIEWED',
            },
          })
          break

        case this.$t('general.expired'):
          this.filters.status = {
            name: 'EXPIRED',
            value: 'EXPIRED',
          }
          this.$router.push({
            query: {
              status: 'EXPIRED',
            },
          })
          break

        case this.$t('general.accepted'):
          this.filters.status = {
            name: 'ACCEPTED',
            value: 'ACCEPTED',
          }
          this.$router.push({
            query: {
              status: 'ACCEPTED',
            },
          })
          break

        case this.$t('general.rejected'):
          this.filters.status = {
            name: 'REJECTED',
            value: 'REJECTED',
          }
          this.$router.push({
            query: {
              status: 'REJECTED',
            },
          })
          break

        default:
          this.filters.status = {
            name: '',
            value: '',
          }
          this.$router.push({
            query: {},
          })
          break
      }
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
            this.$refs.table.refresh()
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
            this.$refs.table.refresh()
            window.toastr['success'](
              this.$tc('estimates.marked_as_rejected_message')
            )
          }
        }
      })
    },

    setFilters() {
      clearTimeout(this.timeout)
      this.timeout = setTimeout(() => {
        this.resetSelectedEstimates()
        this.$refs.table.refresh()
      }, 900)
    },

    clearFilter() {
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }

      this.filters = {
        customer: '',
        status: '',
        from_date: '',
        to_date: '',
        estimate_number: '',
        customcode: '',
      }

      this.activeTab = this.$t('general.all')
    },

    clearFilter() {
      if (this.filters.customer) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.customer
        )
      }

      this.filters = {
        customer: '',
        status: '',
        from_date: '',
        to_date: '',
        invoice_number: '',
        customcode: '',
      }

      this.activeTab = this.$t('general.all')
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

    async removeEstimate(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 1),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteEstimate({ ids: [this.id] })

          if (res.data.success) {
            this.$refs.table.refresh()
            this.resetSelectedEstimates()
            window.toastr['success'](this.$tc('estimates.deleted_message', 1))
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async convertInToinvoice(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_conversion'),
        icon: '/assets/icon/file-alt-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willConvertInToinvoice) => {
        if (willConvertInToinvoice) {
          let res = await this.convertToInvoice(id)
          if (res.data) {
            window.toastr['success'](this.$t('estimates.conversion_message'))
            this.$router.push(`invoices/${res.data.invoice.id}/edit`)
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async removeMultipleEstimates() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('estimates.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteMultipleEstimates()

          if (res.data.success) {
            this.$refs.table.refresh()
            this.resetSelectedEstimates()
            window.toastr['success'](this.$tc('estimates.deleted_message', 2))
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

    async onMarkAsSent(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$t('estimates.confirm_mark_as_sent'),
        icon: '/assets/icon/check-circle-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willMarkAsSent) => {
        if (willMarkAsSent) {
          const data = {
            id: id,
            status: 'SENT',
          }

          let response = await this.markAsSent(data)
          this.refreshTable()

          if (response.data) {
            window.toastr['success'](
              this.$tc('estimates.mark_as_sent_successfully')
            )
          }
        }
      })
    },
    async sendEstimate(estimate) {
      this.openModal({
        title: this.$t('estimates.send_estimate'),
        componentName: 'SendEstimateModal',
        id: estimate.id,
        data: estimate,
        variant: 'lg',
      })
    },

    async sendSMSEstimate(estimate) {
      this.openModal({
        title: this.$t('estimates.sendsms_estimate_title'),
        componentName: 'SendEstimateSMSModal',
        id: estimate.id,
        data: estimate,
        variant: 'lg',
      })
    },
    //
    setActiveTab(val) {
      switch (val.value) {
        case 'DRAFT':
          this.activeTab = this.$t('general.draft')
          this.$router.push({
            query: {
              status: 'DRAFT',
            },
          })
          break
        case 'SENT':
          this.activeTab = this.$t('general.sent')
          this.$router.push({
            query: {
              status: 'SENT',
            },
          })
          break
        case 'VIEWED':
          this.activeTab = this.$t('general.viewed')
          this.$router.push({
            query: {
              status: 'VIEWED',
            },
          })
          break
        case 'EXPIRED':
          this.activeTab = this.$t('general.expired')
          this.$router.push({
            query: {
              status: 'EXPIRED',
            },
          })
          break
        case 'ACCEPTED':
          this.activeTab = this.$t('general.accepted')
          this.$router.push({
            query: {
              status: 'ACCEPTED',
            },
          })
          break
        case 'REJECTED':
          this.activeTab = this.$t('general.rejected')
          this.$router.push({
            query: {
              status: 'REJECTED',
            },
          })
          break
        default:
          this.activeTab = this.$t('general.all')
          this.$router.shift()
          break
      }
    },
    setFiltersUrlQuery() {
      if (this.$route.query.status && this.$route.query.status == 'DRAFT') {
        this.setStatusFilter({ title: this.$t('general.draft') })
        this.setActiveTab({
          name: 'DRAFT',
          value: 'DRAFT',
        })
      }
      if (this.$route.query.status && this.$route.query.status == 'SENT') {
        this.setStatusFilter({ title: this.$t('general.sent') })
        this.setActiveTab({
          name: 'SENT',
          value: 'SENT',
        })
      }
      if (this.$route.query.status && this.$route.query.status == 'VIEWED') {
        this.setStatusFilter({ title: this.$t('general.viewed') })
        this.setActiveTab({
          name: 'VIEWED',
          value: 'VIEWED',
        })
      }
      if (this.$route.query.status && this.$route.query.status == 'EXPIRED') {
        this.setStatusFilter({ title: this.$t('general.expired') })
        this.setActiveTab({
          name: 'EXPIRED',
          value: 'EXPIRED',
        })
      }
      if (this.$route.query.status && this.$route.query.status == 'ACCEPTED') {
        this.setStatusFilter({ title: this.$t('general.accepted') })
        this.setActiveTab({
          name: 'ACCEPTED',
          value: 'ACCEPTED',
        })
      }
      if (this.$route.query.status && this.$route.query.status == 'REJECTED') {
        this.setStatusFilter({ title: this.$t('general.rejected') })
        this.setActiveTab({
          name: 'REJECTED',
          value: 'REJECTED',
        })
      }
    },

async permissionsUserModule(){
    const data = {
       module: "estimates"
    }
    const permissions = await this.getUserModules(data)
    // valida que el usuario tenga permiso para ingresar al modulo
    if(permissions.super_admin == false){
      if(permissions.exist == false ){
        this.$router.push('/admin/dashboard')
      }else {
       const modulePermissions = permissions.permissions[0]
       if(modulePermissions == null){
            this.$router.push('/admin/dashboard')
          } else
        if(modulePermissions.access == 0){
          this.$router.push('/admin/dashboard')
        }
      }
    }

    // valida que el usuario tenga el permiso create, read, delete, update
    if(permissions.super_admin == true){
      this.permissionModule.create = true
      this.permissionModule.createInvoice = true
      this.permissionModule.update = true
      this.permissionModule.delete = true
      this.permissionModule.read = true
    }else if(permissions.exist == true && permissions.permissions[0] != null){
      const modulePermissions = permissions.permissions[0]
      if(modulePermissions.create == 1){
          this.permissionModule.create = true
      }
      if(modulePermissions.update == 1){
          this.permissionModule.update = true
      }
      if(modulePermissions.delete == 1){
          this.permissionModule.delete = true
      }
      if(modulePermissions.read == 1){
          this.permissionModule.read = true
      }
    }

    const dataInvoices = {
       module: "invoices"
    }
    const permissionsInvoices = await this.getUserModules(dataInvoices)
    if(permissionsInvoices.super_admin == true){
      this.permissionModule.createInvoice = true
    }else if(permissionsInvoices.exist == true ){
      const modulePermissions = permissionsInvoices.permissions[0]
      if(modulePermissions == null){
        this.permissionModule.createInvoice = false
      } else if(modulePermissions.create == 1){
        this.permissionModule.createInvoice = true
      }
    }
    },
  },
}
</script>