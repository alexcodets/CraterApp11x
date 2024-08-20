<template>
  <base-page v-if="isSuperAdmin" class="items">
    <sw-page-header :title="$t('roles.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="#" :title="$tc('roles.title', 2)" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="roles/create"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('roles.add_new_role') }}
        </sw-button>
      </template>
    </sw-page-header>

    
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group :label="$tc('roles.filter.name')" class="flex-1 mt-2 mr-4">
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

    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('roles.no_roles')"
      :description="$t('roles.list_of_roles')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/roles/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('roles.add_new_role') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ lengthRolesMin  }}</b>

          {{ $t('general.of') }}

          <b>{{ totalRoles }}</b>
        </p>
      </div>
      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >

        <sw-table-column
          :sortable="true"
          :label="$t('roles.name')"
          show="name"
        />

        <sw-table-column
          :sortable="true"
          :label="$t('roles.description')"
          show="description"
        />

<!--        <sw-table-column :sortable="true" :label="$t('roles.permissions')">
          <template slot-scope="row">
            <span>{{ $t('roles.permissions') }}</span>
            {{ totalpermissions(row.permissions) }}
          </template>
        </sw-table-column>-->

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.action') }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />

               <sw-dropdown-item tag-name="router-link" :to="`roles/${row.id}/user`">
                <user-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('roles.assign_role') }}
              </sw-dropdown-item>

              <sw-dropdown-item tag-name="router-link" :to="`roles/${row.id}/view`">
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item tag-name="router-link" :to="`roles/${row.id}/edit`">
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removePackage(row.id)">
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
import AstronautIcon from '@/components/icon/AstronautIcon'
import {
  UserIcon,
  EyeIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'

export default {
  components: {
    UserIcon,
    EyeIcon,
    AstronautIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
  },
  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      totalRoles: 0,
      filters: {
        name: '',
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('roles', ['roles']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.roles && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedRoles
      },
      set: function (val) {
        this.selectedUser(val)
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
  created() {
    if (!this.isSuperAdmin) {
      this.$router.push('/admin/dashboard')
    }
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllRoles()
    }
  },

  methods: {
    totalpermissions(arr){
      return(arr.length)
    },
    ...mapActions('roles', [
      'fetchRoles', 
      'showRoleWithPermissions', 
      'deleteRole'
    ]),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        all: false,
        name: this.filters.name !== null ? this.filters.name : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true

      let response = await this.fetchRoles(data)

      this.lengthRoles = response.data.roles.total

      this.lengthRolesMin = response.data.roles.from

      this.isRequestOngoing = false

      this.totalRoles = response.data.roles.total
     
      return {
        data: response.data.roles.data,
        pagination: {
          totalPages: response.data.roles.last_page,
          currentPage: page,
        },
      }
    },

    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removePackage(roleId) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('roles.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteRole(roleId)

          if (res.data) {
            window.toastr['success'](this.$tc('roles.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }

          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('roles.user_attached_message'),
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
