<template>
  <!-- <base-page v-if="isSuperAdmin" class="items"> -->
  <div :class="{ 'xl:pl-96': showSideBar }">
    <sw-page-header :title="$t('corePbx.menu_title.tenants')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('corePbx.menu_title.tenants')"
          active
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/packages`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <div class="mr-3 hidden xl:block">
          <sw-button
            class=""
            variant="primary-outline"
            @click="toggleListCustomers"
          >
            {{ $t('tickets.departaments.menu') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
        </div>
      </template>
    </sw-page-header>

    <slide-x-left-transition>
      <Sidebart-departaments v-show="showSideBar" />
    </slide-x-left-transition>

    <!-- EMPTY LIST -->
    <sw-empty-table-placeholder
      v-show="showEmptyScreen && tenantsList.length === 0"
      :title="$t('corePbx.no_custom_app_rate')"
      :description="$t('corePbx.list_of_custom_app_rate')"
    >
      <satellite-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/corePBX/billing-templates/custom-app-rate/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('corePbx.add_custom_app_rate') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ tenantsList.length }}</b>
          {{ $t('general.of') }} <b>{{ totalTenants }}</b>
        </p>
      </div>
      <base-loader v-if="isRequestOngoing" />

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('general.name')"
          show="tenantid"
        >
          <template slot-scope="row">
            <span>{{ $t('general.name') }}</span>
            <p class="font-medium text-primary-500">
              {{ row.tenant.name }} ({{ row.code }})
            </p>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('users.added_on')"
          show="created_at"
        >
          <template slot-scope="row">
            <span>{{ $t('users.added_on') }}</span>
            <p>
              {{ row.created_at | formatDate }}
            </p>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('users.last_updated')"
          show="updated_at"
        >
          <template slot-scope="row">
            <span>{{ $t('users.last_updated') }}</span>
            <p>
              {{ row.updated_at | formatDate }}
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
            <div class="flex items-center">
              <sw-checkbox
                v-model="row.status"
                :true-value="1"
                :false-value="0"
                @change="updateStatus(row.id, row.status)"
                class="mr-1"
                v-if="permissionModule.update"
              />
              <span>{{
                row.status ? $t('general.recording') : $t('general.suspended')
              }}</span>
              <div v-if="permissionModule.read">
                <sw-button
                  v-if="row.status"
                  variant="primary-outline"
                  size="sm"
                  @click="clickImport(row.id)"
                  class="ml-2"
                >
                  {{ $t('general.import') }}
                </sw-button>
              </div>
            </div>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </div>
  <!-- </base-page> -->
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import SidebartDepartaments from '../SidebartTickets'
import {
  FilterIcon,
  EyeIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
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
    SidebartDepartaments
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      isLoadingDelete: false,
      name: '',
      status_payment: '',
      description: '',

      filters: {
        name: '',
      },
      showSideBar: true,
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
    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  created() {
    this.permissionsUserModule()
  },
  methods: {
    ...mapActions('pbxService', [
      'fetchTenantAll',
      'updateStatusTenant',
      'tenantImportCdr',
      'enableTenant',
      'disableTenant',
    ]),
    ...mapActions('user', ['getUserModules']),
    refreshTable() {
      this.$refs.table.refresh()
    },
    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
      }
    },

    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },
    /*CONSULTAR DATA*/
    async fetchData({ page, filter, sort }) {
      try {
        this.isRequestOngoing = false
        let data = {
          name: this.filters.name !== null ? this.filters.name : '',
          orderByField: sort.fieldName || 'updated_at',
          orderBy: sort.order || 'desc',
          page,
          limit: 10,
        }
        let response = await await this.fetchTenantAll(data)
        this.totalTenants = response.data.tenants.total
        this.tenantsList = response.data.tenants.data
        return {
          data: response.data.tenants.data || {},
          pagination: {
            // currentPage: page,
            totalPages: response.data.tenants.last_page,
            currentPage: page,
            count: response.data.tenants.total,
          },
        }
      } catch (e) {
        console.log(e)
      } finally {
        this.isRequestOngoing = false
      }
    },

    /* ELIMINAR*/
    async updateStatus(id, status) {
      //console.log(id, status)
      try {
        this.isLoadingDelete = true
        // await this.updateStatusTenant({id, status})
        if (status) {
          await this.enableTenant(id)
          await this.tenantImportCdr({ id, days: 1 })
        } else {
          await this.disableTenant(id)
        }

        window.toastr['success'](this.$t('general.status_updated'))
        this.$refs.table.refresh()
      } catch (error) {
        if (error.response.status !== 405) {
          window.toastr['error'](
            this.$t('corePbx.packages.delete_custom_app_rate_error')
          )
        }
      } finally {
        this.isLoadingDelete = false
      }
    },
    async clickImport(id) {
      try {
        swal({
          title: this.$t('general.are_you_sure'),
          text: this.$tc(
            'general.this_operation_can_take_several_hours_and_slow_down_the_process_of_downloading_calls_for_the_report'
          ),
          buttons: true,
          dangerMode: true,
        })
          .then(async (validUser) => {
            /*console.log(
              '🚀 ~ file: index.vue ~ line 385 ~ clickImport ~ validUser',
              validUser
            )*/
            if (!validUser) {
              return
            }
            this.isLoadingImport = true
            await this.tenantImportCdr({ id, days: 95 })
            window.toastr['success'](this.$t('general.status_updated'))
            this.$refs.table.refresh()
          })
          .catch((e) => {
            console.log(e)
          })
          .finally(() => {
            this.isLoadingImport = false
          })
      } catch (e) {
        console.log(e)
      } finally {
        this.isLoadingImport = false
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
