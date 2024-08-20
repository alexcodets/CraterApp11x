<template>
  <sw-card variant="setting-card">
  <base-page class="customer-create">
    <sw-page-header :title="$t('tax_groups.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
        <sw-breadcrumb-item
          :title="$tc('tax_groups.tax_group', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalTaxGroups"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="tax-groups/create"
          size="lg"
          variant="primary"
          class="ml-4"
          v-if="permissionModule.create"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('tax_groups.new_tax_group') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('tax_groups.name')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>
        
        <sw-input-group
          :label="$t('tax_groups.country')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.country"
            type="text"
            name="country"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('tax_groups.state')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.state"
            type="text"
            name="state"
            class="mt-2"
            autocomplete="off"
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
      :title="$t('tax_groups.no_tax_groups')"
      :description="$t('tax_groups.list_of_tax_groups')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/settings/tax-groups/create"
        size="lg"
        variant="primary-outline"
        v-if="permissionModule.create"
      >
        {{ $t('tax_groups.add_new_tax_group') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ taxGroups.length }}</b>
          {{ $t('general.of') }} <b>{{ totalTaxGroups }}</b>
        </p>      
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('tax_groups.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('tax_groups.name') }}</span>
            <router-link
              :to="{ path: `tax-groups/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
            >
              {{ row.name }}
            </router-link>
            <span v-else>
              {{ row.name }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('tax_groups.description')"
          show="description"
        >
          <template slot-scope="row">
            <span>{{ $t('tax_groups.description') }}</span>
            <span v-if="row.description" v-html="row.description">
            </span>
            <span v-else>
            {{ $t('tax_groups.empty') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('tax_groups.country')"
          show="country"
        >
          <template slot-scope="row">
            <span>{{ $t('tax_groups.country') }}</span>
            <span v-if="row.country_name" v-html="row.country_name">
            </span>
            <span v-else>
            {{ $t('tax_groups.not_selected') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('tax_groups.state')"
          show="state"
        >
          <template slot-scope="row">
            <span>{{ $t('tax_groups.state') }}</span>
            <span v-if="row.state_name" v-html="row.state_name">
            </span>
            <span v-else>
            {{ $t('tax_groups.not_selected') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('tax_groups.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`tax-groups/${row.id}/edit`"
                tag-name="router-link"
                v-if="permissionModule.update"
                >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>
              
              <sw-dropdown-item
              :to="`tax-groups/${row.id}/view`"
              tag-name="router-link"
              v-if="permissionModule.read"
              >
              <eye-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.view') }}
            </sw-dropdown-item>
            
            <sw-dropdown-item @click="removeTaxGroup(row.id)"
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
  </sw-card>
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
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      filters: {
        name: '',
        country: '',
        state: ''
      },
      permissionModule: {
        create: false,
        read: false,
        delete: false,
        update: false
      }
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalTaxGroups && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('taxGroups', [
      'taxGroups',
      'totalTaxGroups',
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  created(){
    this.permissionsUserModule()
  },
  methods: {
    ...mapActions('taxGroups', [
      'fetchTaxGroups',
      'selectAllTaxGroups',
      'selectTaxGroup',
      'deleteTaxGroup',
    ]),
    ...mapActions('notification', ['showNotification']),
    ...mapActions('user', ['getUserModules']),
    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name,
        country: this.filters.country,
        state: this.filters.state,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchTaxGroups(data)
      this.isRequestOngoing = false

      return {
        data: response.data.tax_groups.data,
        pagination: {
          totalPages: response.data.tax_groups.last_page,
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

    async removeTaxGroup(id) {
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteTaxGroup({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('tax_groups.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },

    async permissionsUserModule(){
      const data = {
         module: "tax_Groups" 
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
          }else if(modulePermissions.access == 0 ){
            this.$router.push('/admin/dashboard')
          }
        }
      }

      // valida que el usuario tenga el permiso create, read, delete, update
      if(permissions.super_admin == true){
        this.permissionModule.create = true
        this.permissionModule.update = true
        this.permissionModule.delete = true
        this.permissionModule.read = true
      }else if(permissions.exist == true ){
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

    }
  },
}
</script>
