<template>
  <base-page class="customer-create">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('customers.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
          <sw-breadcrumb-item
            :title="$tc('customers.customer', 2)"
            to="#"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalCustomers"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          v-show="totalCustomers"
          size="lg"
          tag-name="router-link"
          to="customers/disable-list"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          @click="toggleFilter"
        >
          {{ $t('general.disable_list') }}
          <eye-off-icon class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="customers/create"
          size="lg"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('customers.add_new_customer') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('customers.display_nametable')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.display_name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.contact_name')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.contact_name"
            type="text"
            name="address_name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.phone')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.phone"
            type="text"
            name="phone"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('customers.status')"
          color="black-light"
          class="flex-1 mt-2 ml-0 lg:ml-6"
          hidden
        >
          <base-status-select
            ref="statusSelect"
            @select="onSelectStatus"
            @deselect="clearStatusSearch"
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
      :title="$t('customers.no_customers')"
      :description="$t('customers.list_of_customers')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/customers/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('customers.add_new_customer') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative">
      <div
        class="relative flex items-center justify-between h-5 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ customers.length }}</b>
          {{ $t('general.of') }} <b>{{ totalCustomers }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedCustomers.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="removeMultipleCustomers">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('customers.deletecustomer') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <sw-tabs
        :active-tab="activeTab"
        @update="setStatusFilter"
        ref="tabsStatusPayments"
        class="hidden md:inline"
      >
        <!-- :active-tab="activeTab" -->
        <!-- filters: All, Active(Process) y Pending -->
        <sw-tab-item :title="$t('general.all')" filter="ALL" />
        <sw-tab-item
          :title="$t('customers.customer_prepaid')"
          filter="prepaid"
        />
        <sw-tab-item
          :title="$t('customers.customer_postpaid')"
          filter="postpaid"
        />
      </sw-tabs>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-7">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllCustomers"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllCustomers"
        />
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
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

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('customers.customer_number')"
          show="customcode"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.customer_number') }}</span>
            <router-link
              :to="{ path: `customers/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.customcode }}
            </router-link>
            <span class="font-medium text-black-500" v-else>{{
              row.customcode
            }}</span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('customers.display_nametable')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.display_nametable') }}</span>
            <router-link
              :to="{ path: `customers/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.name }}
            </router-link>
            <span class="font-medium text-black-500" v-else>{{
              row.name
            }}</span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.contact_name')"
          show="contact_name"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.contact_name') }}</span>
            <span>
              {{
                row.contact_name
                  ? row.contact_name
                  : $t('customers.no_contact_name')
              }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.phone')"
          show="phone"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.phone') }}</span>
            <span>
              {{ row.phone ? row.phone : $t('customers.no_contact') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.amount_due')"
          show="due_amount"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.amount_due') }} </span>
            <div v-html="$utils.formatMoney(row.due_amount, row.currency)" />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('customers.added_on')"
          sort-as="created_at"
          show="formattedCreatedAt"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('customers.type')"
          show="status_payment"
        >
          <template slot-scope="row">
            <span>{{ $t('customers.type') }}</span>
            <span>
              {{ row.status_payment }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`customers/${row.id}/edit`"
                tag-name="router-link"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`customers/${row.id}/view`"
                tag-name="router-link"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="permissionModule.delete"
                @click="removeCustomer(row.id)"
              >
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('customers.deletecustomer') }}
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
  EyeOffIcon,
  CreditCardIcon,
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
    EyeOffIcon,
    CreditCardIcon,
  },
  mounted() {
    this.permissionsUserModule()
  },
  data() {
    return {
      activeTab: this.$t('general.all'),
      showFilters: false,
      isRequestOngoing: true,
      filters: {
        display_name: '',
        contact_name: '',
        phone: '',
        status: '',
      },
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
      return !this.totalCustomers && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('customer', [
      'customers',
      'selectedCustomers',
      'totalCustomers',
      'selectAllField',
    ]),
    selectField: {
      get: function () {
        return this.selectedCustomers
      },
      set: function (val) {
        this.selectCustomer(val)
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
      this.selectAllCustomers()
    }
  },
  // created(){
  //   this.permissionsUserModule()
  // },
  methods: {
    ...mapActions('customer', [
      'fetchCustomers',
      'selectAllCustomers',
      'selectCustomer',
      'deleteCustomer',
      'deleteMultipleCustomers',
      'setSelectAllState',
    ]),
    ...mapActions('notification', ['showNotification']),
    ...mapActions('user', ['getUserModules']),
    refreshTable() {
      this.$refs.table.refresh()
    },
    async fetchData({ page, filter, sort }) {
      //console.log(this.filters)
      let data = {
        display_name: this.filters.display_name,
        contact_name: this.filters.contact_name,
        phone: this.filters.phone,
        status_customer: this.filters.status ? this.filters.status.value : '',
        status_payment: this.filters.status_payment
          ? this.filters.status_payment
          : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchCustomers(data)

      this.isRequestOngoing = false

      return {
        data: response.data.customers.data,
        pagination: {
          totalPages: response.data.customers.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        display_name: '',
        contact_name: '',
        phone: '',
        status: '',
      }
      if (this.filters.status) {
        this.$refs.statusSelect.$refs.baseSelect.removeElement(
          this.filters.status
        )
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },
    onSelectStatus(status) {
      this.filters.status = status
    },
    async clearStatusSearch(removedOption, id) {
      this.filters.status = ''
      this.refreshTable()
    },
    async removeCustomer(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
      }).then(async (result) => {
        if (result) {
          let res = await this.deleteCustomer({ ids: [id] })

          if (res.data.type === 'success') {
            window.toastr['success'](this.$tc('customers.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          window.toastr[res.data.type](res.data.message)
          return true
        }
      })
    },

    async removeMultipleCustomers() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('customers.confirm_delete', 2),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        showCancelButton: true,
        showConfirmButton: true,
      }).then(async (result) => {
        if (result.value) {
          let request = await this.deleteMultipleCustomers()
          if (request.data.success) {
            this.showNotification({
              type: 'success',
              message: this.$tc('customers.deleted_message', 2),
            })
            this.refreshTable()
          } else if (request.data.error) {
            this.showNotification({
              type: 'error',
              message: request.data.message,
            })
          }
        }
      })
    },

    async permissionsUserModule() {
      const data = {
        module: 'customers',
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
    },

    setStatusFilter(val) {
      if (this.activeTab == val.title) {
        return true
      }
      this.activeTab = val.title
      switch (val.title) {
        case this.$t('general.all'):
          this.filters.status_payment = ''
          break
        case this.$t('customers.customer_prepaid'):
          this.filters.status_payment = 'prepaid'
          break
        case this.$t('customers.customer_postpaid'):
          this.filters.status_payment = 'postpaid'
          break
      }

      this.setFilters()
    },
  },
}
</script>
