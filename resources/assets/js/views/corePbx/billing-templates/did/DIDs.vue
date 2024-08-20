<template>
  <base-page v-if="isSuperAdmin" class="items">
    <!-- Page Header -->

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('corePbx.menu_title.templates')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item
            to="#"
            :title="$tc('corePbx.menu_title.did', 2)"
            active
          />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalDID"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="did/create"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('corePbx.did.add_did') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group :label="$tc('corePbx.did.id')" class="flex-1 mt-2 mr-4">
          <sw-input
            v-model="filters.did_number"
            type="text"
            name="did_number"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('corePbx.did.name')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
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

    <!-- EMPTY LIST -->

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('corePbx.no_did')"
      :description="$t('corePbx.list_of_did')"
    >
      <satellite-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/corePBX/billing-templates/did/create"
        size="lg"
        variant="primary-outline"
        v-if="permissionModule.create"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('corePbx.did.add_new_did') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ did.length }}</b>
          {{ $t('general.of') }} <b>{{ totalDID }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedDID.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
              {{ $t('general.actions') }}
              <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item
              @click="removeMultipleItems"
              v-if="permissionModule.delete"
            >
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div
        class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12"
      ></div>
      <base-loader v-if="isLoadingDelete" />
      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.did.id')"
          show="did_number"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.did.id') }}</span>
            <router-link
              :to="{ path: `did/${row.id}/edit` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.did_number }}
            </router-link>
            <span v-else>
              {{ row.did_number }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.did.name')"
          show="name"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('did.item.status')"
          show="status"
        >
          <template slot-scope="row">
            {{ statusOptions[row.status] }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('did.item.rate')"
          show="did_rate"
        >
          <template slot-scope="row">
            {{ defaultCurrency.symbol + ' ' + row.did_rate.toFixed(2) }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('packages.action') }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`did/${row.id}/edit`"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`did/${row.id}/copy`"
                v-if="permissionModule.create"
              >
                <plus-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('packages.copy') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                @click="removeDID(row.id)"
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
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
import SatelliteIcon from '../../../../components/icon/SatelliteIcon.vue'
export default {
  components: {
    SatelliteIcon,
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
  },
  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      isLoadingDelete: false,
      filters: {
        name: '',
        did_number: '',
      },
      statusOptions: {
        A: this.$t('general.active'),
        I: this.$t('general.inactive'),
      },
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false,
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('did', ['did', 'totalDID', 'selectedDID', 'selectAllField']),
    ...mapGetters('company', ['defaultCurrency']),
    selectField: {
      get: function () {
        return this.selectedDID
      },
      set: function (val) {
        this.selectDID(val)
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
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.totalDID && !this.isRequestOngoing
    },
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
  destroyed() {
    if (this.selectAllField) {
      this.selectAllDID()
    }
  },

  created() {
    this.permissionsUserModule()
  },
  methods: {
    ...mapActions('did', [
      'deleteMultipleDID',
      'deleteDID',
      'selectDID',
      'fetchDIDs',
      'selectAllDID',
      'setSelectAllState',
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
        did_number: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },
    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name !== null ? this.filters.name : '',
        did_number:
          this.filters.did_number !== null ? this.filters.did_number : '',
        orderByField: sort.fieldName || 'did_number',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchDIDs(data)

      this.isRequestOngoing = false
      return {
        data: response.data.profileDID.data || {},
        pagination: {
          currentPage: page,
          totalPages: response.data.profileDID.last_page,
          count: response.data.profileDID.total,
        },
      }
    },
    async removeDID(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('did.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        try {
          if (willDelete) {
            this.isLoadingDelete = true
            await this.deleteDID(id)
            window.toastr['success'](this.$t('corePbx.did.did_delete_message'))
            this.$refs.table.refresh()
          }
        } catch (error) {
          window.toastr['error'](
            this.$t('corePbx.did.did_delete_message_error')
          )
        } finally {
          this.isLoadingDelete = false
        }
      })
    },
    async removeMultipleItems() {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete', 2),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          try {
            this.isLoadingDelete = true
            await this.deleteMultipleDID()
            window.toastr['success'](this.$t('corePbx.did.did_delete_message'))
            this.$refs.table.refresh()
          } catch (error) {
            window.toastr['error'](
              this.$t('corePbx.did.did_delete_message_error')
            )
          } finally {
            this.isLoadingDelete = false
          }
        }
      })
    },

    async permissionsUserModule() {
      const data = {
        module: 'pbx_did',
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