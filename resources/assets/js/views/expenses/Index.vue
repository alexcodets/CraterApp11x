<template>
  <base-page>
    <!-- Page Header -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('expenses.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />

          <sw-breadcrumb-item
            to="#"
            :title="$tc('expenses.expense', 2)"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalExpenses"
          size="lg"
          variant="primary-outline"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="expenses/create"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          size="lg"
          variant="primary"
          v-if="permissionModule.create"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('expenses.add_expense') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="expenses-template"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          size="lg"
          variant="primary"
          v-if="permissionModule.create"
        >
          {{ $t('expenses.template_expenses') }}
        </sw-button>

        <sw-button
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          size="lg"
          variant="primary"
          @click="importExpense"
          v-if="permissionModule.create"
        >
          <upload-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('expenses.import') }}
        </sw-button>
      </div>
    </div>

    <!--Filter Wrapper -->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <!-- COLUMN 1 -->
        <div class="w-25" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group
            :label="$t('expenses.subject')"
            class="flex-1 mt-2"
            style="min-width: 200px"
          >
            <sw-input v-model="filters.subject"> </sw-input>
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.expense_number')"
            class="flex-1 mt-2"
            style="min-width: 200px"
          >
            <sw-input v-model="filters.expense_number">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>
        <!-- COLUMN 2 -->
        <div class="w-25" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group
            :label="$t('expenses.customer')"
            class="flex-1 mt-3"
            style="min-width: 300px"
          >
            <base-customer-select
              ref="customerSelect"
              @select="onSelectCustomer"
              @deselect="clearCustomerSearch"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('customers.customcode')"
            class="flex-1 mt-2"
            style="min-width: 300px"
          >
            <sw-input v-model="filters.customcode">
              <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
            </sw-input>
          </sw-input-group>
        </div>

        <!-- COLUMN 3 -->
        <div class="w-25" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group
            :label="$t('expenses.provider')"
            class="flex-1 mt-2"
            style="min-width: 300px"
          >
            <sw-select
              v-model="filters.provider"
              :options="providers"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.provider_select')"
              label="title"
              class="mt-2"
              @click="filter = !filter"
            />
          </sw-input-group>

          <sw-input-group
            :label="$t('expenses.category')"
            class="flex-1 mt-2"
            style="min-width: 300px"
          >
            <sw-select
              v-model="filters.category"
              :options="categories"
              :searchable="true"
              :show-labels="false"
              :placeholder="$t('expenses.categories.select_a_category')"
              label="name"
              class="mt-2"
              @click="filter = !filter"
            />
          </sw-input-group>
        </div>

        <!-- COLUMN 4 -->
        <div class="w-25" style="margin-left: 1em; margin-right: 1em">
          <sw-input-group :label="$t('expenses.from_date')" class="flex-1 mt-2">
            <base-date-picker
              v-model="filters.from_date"
              :calendar-button="true"
              class="mt-2"
              calendar-button-icon="calendar"
            />
          </sw-input-group>

          <sw-input-group :label="$t('expenses.to_date')" class="flex-1 mt-2">
            <base-date-picker
              v-model="filters.to_date"
              :calendar-button="true"
              class="mt-2"
              calendar-button-icon="calendar"
            />
          </sw-input-group>
        </div>

        <!--
        <sw-input-group
          :label="$t('expenses.category')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-select
            v-model="filters.provider"
            :options="providers"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('expenses.categories.select_a_category')"
            label="name"
            class="mt-2"
            @click="filter = !filter"
          />
        </sw-input-group>
         -->

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <!-- Empty Table Placeholder -->
    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('expenses.no_expenses')"
      :description="$t('expenses.list_of_expenses')"
    >
      <observatory-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/expenses/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('expenses.add_new_expense') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ expenses.length }}</b>

          {{ $t('general.of') }} <b>{{ totalExpenses }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedExpenses.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleExpenses">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <sw-tabs
        @update="setStatusFilter"
        ref="tabsStatusPayments"
        class="hidden md:inline"
      >
        <!-- :active-tab="activeTab" -->
        <!-- filters: All, Active(Process) y Pending -->
        <sw-tab-item :title="$t('general.all')" filter="ALL" />
        <sw-tab-item :title="$t('general.processed')" filter="ACTIVE" />
        <sw-tab-item :title="$t('general.pending')" filter="PENDING" />
        <sw-tab-item :title="$t('general.next_due')" filter="NEXTDUE" />
      </sw-tabs>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-7">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllExpenses"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden mt-2"
          @change="selectAllExpenses"
        />
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        class="-mt-10 md:mt-0"
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

        <!-- <sw-table-column
          :sortable="true"
          :label="$t('expenses.date')"
          sort-as="expense_date"
          show="formattedExpenseDate"
        /> -->

        <sw-table-column :sortable="true" :label="$t('expenses.date')">
          <template slot-scope="row">
            <span>{{ $t('expenses.date') }}</span>
            <sw-badge
              v-if="daysDueDate(row)"
              :bg-color="$utils.getBadgeStatusColor('EXPIRED').bgColor"
              :color="$utils.getBadgeStatusColor('EXPIRED').color"
            >
              {{ row.expense_date }}
            </sw-badge>
            <div v-else>
              {{ row.expense_date }}
            </div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('expenses.expense_number')"
          show="expense_number"
        >
          <template slot-scope="row">
            <span>{{ $t('expenses.expense_number') }}</span>
            <router-link
              :to="{ path: `expenses/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.expense_number }}
            </router-link>
            <span v-else>
              {{ row.expense_number }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$tc('expenses.categories.category', 1)"
          sort-as="name"
          show="category.name"
        >
          <template slot-scope="row">
            <span>{{ $tc('expenses.categories.category', 1) }}</span>
            <span> {{ row.category.name }} </span>
            <!-- <router-link
              :to="{ path: `expenses/${row.id}/edit` }"
              class="font-medium text-primary-500"
            >
              {{ row.category.name }}
            </router-link> -->
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('expenses.customer')"
          sort-as="user_name"
          show="user_name"
        >
          <template slot-scope="row">
            <div v-if="permissionModule.read">
              <div v-if="row.user != null">
                <router-link
                  :to="{ path: `customers/${row.user_id}/view` }"
                  class="font-medium text-primary-500"
                >
                  {{ row.user_name }}
                </router-link>
                <!-- <p style="font-size: 15px;"> {{ row.user.customcode }}</p> -->
              </div>
              <div v-else>
                {{ $t('expenses.customer_no_selected') }}
              </div>
            </div>
            <div v-else>
              {{
                row.user != null
                  ? row.user_name
                  : $t('expenses.customer_no_selected')
              }}
            </div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('expenses.provider')"
          sort-as="provider_title"
          show="provider_title"
        >
          <template slot-scope="row">
            <div v-if="permissionModule.read">
              <div v-if="row.provider != null">
                <router-link
                  :to="{ path: `providers/${row.providers_id}/view` }"
                  class="font-medium text-primary-500"
                >
                  {{ row.provider_title }}
                </router-link>
                <p style="font-size: 15px">
                  {{ row.provider.providers_number }}
                </p>
              </div>
              <div v-else>
                {{ $t('expenses.provider_no_selected') }}
              </div>
            </div>
            <div v-else>
              {{
                row.provider != null
                  ? row.provider_title
                  : $t('expenses.provider_no_selected')
              }}
              <p style="font-size: 15px">{{ row.provider.providers_number }}</p>
            </div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('expenses.amount')"
          sort-as="amount"
          show="category.amount"
        >
          <template slot-scope="row">
            <span>{{ $t('expenses.amount') }}</span>
            <div v-html="$utils.formatMoney(row.amount, defaultCurrency)" />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('general.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('general.status') }}</span>
            <sw-badge
              :bg-color="$utils.getBadgeStatusExpenseColor(row.status).bgColor"
              :color="$utils.getBadgeStatusExpenseColor(row.status).color"
            >
              {{
                row.status == 'Active' ? $t('general.processed') : row.status
              }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('expenses.subject')"
          sort-as="subject"
        >
          <template slot-scope="row">
            <span>{{ $t('expenses.subject') }}</span>
            <span v-html="row.subject ? row.subject : $t('item_groups.empty')">
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown no-click"
        >
          <template slot-scope="row">
            <span>{{ $t('expenses.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`expenses/${row.id}/edit`"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`expenses/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('invoices.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`expenses/${row.id}/docs`"
              >
                <document-text-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.docs') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="removeExpense(row.id)"
                v-if="permissionModule.delete"
              >
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
import ObservatoryIcon from '../../components/icon/ObservatoryIcon'
// import moment, { invalid } from 'moment'
import {
  PencilIcon,
  TrashIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PlusIcon,
  HashtagIcon,
  UploadIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'

import { DocumentTextIcon } from '@vue-hero-icons/outline'

export default {
  components: {
    ObservatoryIcon,
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    HashtagIcon,
    UploadIcon,
    EyeIcon,
    DocumentTextIcon,
  },

  data() {
    return {
      days_due_date: null,
      showFilters: false,
      isRequestOngoing: true,
      filters: {
        category: null,
        from_date: '',
        to_date: '',
        user: '',
        provider: '',
        expense_number: '',
        customcode: '',
        status: 'All',
        subject: '',
      },
      providers: [],
      timeout: null,
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false,
      },
    }
  },

  computed: {
    showEmptyScreen() {
      return !this.totalExpenses && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    ...mapGetters('category', ['categories']),

    ...mapGetters('expense', [
      'selectedExpenses',
      'totalExpenses',
      'expenses',
      'selectAllField',
    ]),

    ...mapGetters('company', ['defaultCurrency']),

    ...mapGetters('customer', ['customers']),

    // ...mapGetters('providers', ['providers']),

    selectField: {
      get: function () {
        return this.selectedExpenses
      },
      set: function (val) {
        this.selectExpense(val)
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
  mounted() {
    this.permissionsUserModule()
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllExpenses()
    }
  },

  created() {
    this.fetchCategories({ limit: 'all' })
    this.loadData()
    this.fetchConfig()
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('expense', [
      'fetchExpenses',
      'selectExpense',
      'deleteExpense',
      'deleteMultipleExpenses',
      'selectAllExpenses',
      'setSelectAllState',
    ]),

    ...mapActions('category', ['fetchCategories']),
    ...mapActions('providers', ['fetchProviders']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('company', ['fetchCompanySettings']),

    async fetchConfig() {
      let response = await this.fetchCompanySettings([
        'warning_before_due_date',
      ])
      this.days_due_date = response.data.warning_before_due_date
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        user_id: this.filters.user ? this.filters.user.id : null,
        providers_id: this.filters.provider ? this.filters.provider.id : null,
        expense_category_id:
          this.filters.category !== null ? this.filters.category.id : '',

        from_date:
          this.filters.from_date === ''
            ? this.filters.from_date
            : this.filters.from_date,

        to_date:
          this.filters.to_date === ''
            ? this.filters.to_date
            : this.filters.to_date,

        expense_number: this.filters.expense_number,
        customcode: this.filters.customcode,

        orderByField: sort.fieldName || 'created_at',

        orderBy: sort.order || 'desc',
        status: this.filters.status,
        subject: this.filters.subject,
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchExpenses(data)
      this.isRequestOngoing = false

      return {
        data: response.data.expenses.data,

        pagination: {
          totalPages: response.data.expenses.last_page,
          currentPage: page,
          count: response.data.expenses.count,
        },
      }
    },
    setStatusFilter(val) {
      switch (val.title) {
        case this.$t('general.all'):
          this.filters.status = 'All'
          break
        case this.$t('general.processed'):
          this.filters.status = 'Active'
          break
        case this.$t('general.pending'):
          this.filters.status = 'Pending'
          break
        case this.$t('general.next_due'):
          this.filters.status = 'NextDue'
          break
      }

      this.setFilters()
    },

    async loadData() {
      let objeto = await this.fetchProviders({ limit: 'all' })
      this.providers = objeto.data.providers.data
    },

    onSelectCustomer(customer) {
      this.filters.user = customer
    },

    /*  onSelectProvider(provider) {
      this.filters.provider = provider
    }, */

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
      if (this.filters.user) {
        this.$refs.customerSelect.$refs.baseSelect.removeElement(
          this.filters.user
        )
      }

      this.filters = {
        category: null,
        from_date: '',
        to_date: '',
        user: null,
        provider: null,
        expense_number: '',
        customcode: '',
        subject: '',
      }
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.user = ''
      this.refreshTable()
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeExpense(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteExpense({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 1))
            this.refreshTable()
            return true
          } else if (res.data.error) {
            window.toastr['error'](res.data.message)
          }
        }
      })
    },

    async removeMultipleExpenses() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('expenses.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let request = await this.deleteMultipleExpenses()

          if (request.data.success) {
            window.toastr['success'](this.$tc('expenses.deleted_message', 2))
            this.$refs.table.refresh()
          } else if (request.data.error) {
            window.toastr['error'](request.data.message)
          }
        }
      })
    },

    async permissionsUserModule() {
      const data = {
        module: 'expenses',
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
    importExpense() {
      this.openModal({
        title: 'Import Expenses',
        componentName: 'ImportExpenseCsvModal',
        refreshData: this.$refs.table.refresh,
      })
    },
    daysDueDate(expense) {
      let fechaObj = new Date(expense.expense_date)
      // let expenseDue = fechaObj.toISOString().substring(0, 10)
      const today = new Date()
      today.setDate(today.getDate() + parseInt(this.days_due_date))

      if (today >= fechaObj && expense.status != 'Active') {
        return true
      } else {
        return false
      }
    },
  },
}
</script>
