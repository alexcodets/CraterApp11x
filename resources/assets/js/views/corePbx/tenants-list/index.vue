<template>
  <base-page v-if="isSuperAdmin" class="items">
    <sw-page-header :title="$t('corePbx.menu_title.tenants_list')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('corePbx.menu_title.tenants_list')"
          active
        />
      </sw-breadcrumb>
    </sw-page-header>

    <div class="flex flex-wrap items-center justify-end md:-mt-12 mb-5 gap-3">
      <sw-button
        variant="primary-outline"
        size="lg"
        class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        @click="toggleFilter"
      >
        {{ $t('general.filter') }}
        <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
      </sw-button>

      <sw-button
        tag-name="router-link"
        to="tenants-list/create"
        variant="primary"
        size="lg"
        class="w-full md:w-auto mb-2 md:mb-0"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('corePbx.tenants.add') }}
      </sw-button>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group :label="$tc('general.name')" class="flex-1 mt-2 mr-4">
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
            @input="setFilters('name')"
          />
        </sw-input-group>
        <sw-input-group
          :label="$tc('customers.server')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.server"
            type="text"
            name="server"
            class="mt-2"
            autocomplete="off"
            @input="setFilters('server')"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('corePbx.tenants.code')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.code"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
            @input="setFilters('code')"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-gray-900 cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <div class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ tenantsList.length }}</b>
          {{ $t('general.of') }} <b>{{ totalTenants }}</b>
        </p>
      </div>
      <!-- <base-loader v-if="isRequestOngoing" /> -->

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
        class="-mt-10 md:mt-0"
      >
        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('general.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('general.name') }}</span>
            <router-link
              :to="{
                path: `/admin/corePBX/tenant/tenants-list/${row.id}/view`,
              }"
              class="font-medium text-primary-500 hover:text-blue-600"
              v-if="permissionModule.read"
            >
              {{ row.name }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.tenants.code')"
          show="tenant_code"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.tenants.code') }}</span>
            <p class="ml-3">
              {{ row.tenant_code }}
            </p>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('general.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('general.status') }}</span>
            <sw-badge
              v-if="row.status === 'Active/Completed'"
              :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
              :color="$utils.getBadgeStatusColor('COMPLETED').color"
              class="px-3 py-1"
            >
              {{ $t('corePbx.tenants.completed') }}
            </sw-badge>
            <sw-badge
              v-else
              :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
              :color="$utils.getBadgeStatusColor('OVERDUE').color"
              class="px-3 py-1"
            >
              {{ $t('general.inactive') }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('general.date')"
          show="created_at"
        >
          <template slot-scope="row">
            <span>{{ $t('general.date') }}</span>
            <p>
              {{ formatDate(row.created_at) }}
            </p>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('general.server')"
          show="server_name"
        >
          <template slot-scope="row">
            <span>{{ $t('general.server') }}</span>
            <p class="ml-5">
              {{ row.metadata.server_name }}
            </p>

            <span
              v-if="row && row.metadata && row.metadata.server_status === 'A'"
              class="text-success fs-6 text-center"
              style="font-size: 14px"
            >
              {{ $t('settings.customization.modules.server_online') }}
            </span>
            <span
              v-else
              class="text-danger fs-6 text-center"
              style="font-size: 10px"
            >
              {{ $t('settings.customization.modules.server_offline') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('general.use')"
          show="in_use"
        >
          <template slot-scope="row">
            <span>{{ $t('general.use') }}</span>
            <sw-badge
              v-if="row.metadata.in_use === true"
              :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
              :color="$utils.getBadgeStatusColor('COMPLETED').color"
              class="px-3 py-1"
            >
              {{ $t('general.yes') }}
            </sw-badge>
            <sw-badge
              v-else
              :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
              :color="$utils.getBadgeStatusColor('OVERDUE').color"
              class="px-3 py-1"
            >
              {{ $t('general.not') }}
            </sw-badge>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('packages.action') }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />
              <sw-dropdown-item
                tag-name="router-link"
                :to="`/admin/corePBX/tenant/tenants-list/${row.id}/view`"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                v-if="permissionModule.update && row.status === 'Incomplete'"
                @click="completedStatus(row.id)"
              >
                <check-circle-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('corePbx.tenants.completed') }}
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
import {
  FilterIcon,
  EyeIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
  CheckCircleIcon,
} from '@vue-hero-icons/solid'
import SatelliteIcon from '@/components/icon/SatelliteIcon.vue'
import moment from 'moment'

export default {
  components: {
    SatelliteIcon,
    FilterIcon,
    EyeIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    CheckCircleIcon,
  },

  data() {
    return {
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: false,
      name: '',
      pbxServer: '',
      serverPbxOptions: [],

      filters: {
        name: '',
        server: '',
        code: '',
      },
      totalTenants: 0,
      tenantsList: [],
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
      },
    }
  },
  filters: {
    formatDate(value) {
      return moment(value).format('DD/MM/YYYY HH:mm')
    },
  },

  computed: {
    ...mapGetters('user', ['currentUser']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },

  created() {
    this.permissionsUserModule()
  },
  methods: {
    ...mapActions('tenants', [
      'fetchPbxTenantsList',
      'fetchPbxServices',
      'statusCompletedTenants',
    ]),
    ...mapActions('user', ['getUserModules']),
    refreshTable() {
      this.$refs.table.refresh()
    },
    setFilters(filter) {
      if (filter == 'name' && this.filters.name?.length > 2) this.refreshTable()
      if (filter == 'server' && this.filters.server?.length > 2)
        this.refreshTable()
      if (filter == 'code' && this.filters.code?.length > 2) this.refreshTable()

      if (filter == 'name' && this.filters.name?.length == 0)
        this.refreshTable()
      if (filter == 'server' && this.filters.server?.length == 0)
        this.refreshTable()
      if (filter == 'code' && this.filters.code?.length == 0)
        this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
        server: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
        this.refreshTable()
      }
      this.showFilters = !this.showFilters
    },

    formatDate(value) {
      return moment(value).format('DD/MM/YYYY HH:mm')
    },

    // async PbxServer() {
    //   const params = { limit: 100000, status: 'A' }

    //   let res = await this.fetchPbxServices(params)
    //   if (res) {
    //     this.serverPbxOptions = res.pbxServers.data
    //   }
    // },

    // async selectServerPbxMetod(server) {
    // },

    async completedStatus(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        icon: 'warning',
        buttons: true,
        dangerMode: true,
      }).then(async (result) => {
        if (result) {
          let res = await this.statusCompletedTenants(id)
          if (res) {
            this.refreshTable()
          }
          //console.log(res)
        }
      })
    },
    /*CONSULTAR DATA*/
    async fetchData({ page, filter, sort }) {
      try {
        this.isRequestOngoing = true
        if (sort) {
          sort = `${sort.order === 'desc' ? '-' : ''}${sort.fieldName}`
        }
        let data = {
          name: this.filters.name !== null ? this.filters.name : '',
          server_name: this.filters.server !== null ? this.filters.server : '',
          code: this.filters.code !== null ? this.filters.code : '',
          sort,
          page,
          limit: 10,
        }
        let response = await this.fetchPbxTenantsList(data)
        this.totalTenants = response.meta.total
        this.tenantsList = response.data

        return {
          data: response.data || {},
          pagination: {
            totalPages: response.meta.last_page,
            currentPage: page,
            count: response.meta.total,
          },
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    async permissionsUserModule() {
      const data = {
        module: 'pbx_tenant',
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
