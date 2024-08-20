<template>
  <sw-card variant="setting-card">
  <base-page class="customer-create">
    <sw-page-header :title="$t('settings.retentions.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <!-- <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" /> -->
        <sw-breadcrumb-item
          :title="$tc('settings.retentions.retention', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalRetentions"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="retentions/create"
          size="lg"
          variant="primary"
          class="ml-4"
          v-if="permissionModule.create"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('settings.retentions.new_retention') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('settings.retentions.concept')"
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
      :title="$t('settings.retentions.no_retentions')"
      :description="$t('settings.retentions.list_of_retentions')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/settings/retentions/create"
        size="lg"
        variant="primary-outline"
        v-if="permissionModule.create"
      >
        {{ $t('settings.retentions.new_retention') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ retentions.length }}</b>
          {{ $t('general.of') }} <b>{{ totalRetentions }}</b>
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
          :label="$t('settings.retentions.concept')"
          show="concept"
        >
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.retentions.type')"
          show="type_of_minimium_base_in_currency"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.retentions.type') }}</span>
            <span v-if="row.type_of_minimium_base_in_currency" v-html="row.type_of_minimium_base_in_currency">
            </span>
            <span v-else>
            {{ $t('settings.retentions.empty') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('settings.retentions.percentage')"
          show="percentage"
        >
          <template slot-scope="row">
            <span>{{ $t('settings.retentions.percentage') }}</span>
            <span v-if="row.percentage" v-html="row.percentage">
            </span>
            <span v-else>
            {{ $t('settings.retentions.not_selected') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('settings.retentions.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`retentions/${row.id}/edit`"
                tag-name="router-link"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <!-- <sw-dropdown-item
                :to="`retentions/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item> -->

              <sw-dropdown-item @click="removeRetention(row.id)" v-if="permissionModule.delete">
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
// import AstronautIcon from '../../components/icon/AstronautIcon'

export default {
  components: {
    // AstronautIcon,
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
      },
      permissionModule:{
        create: false, 
        read: false, 
        update: false, 
        delete: false, 
      }
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalRetentions && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('retentions', [
      'retentions',
      'totalRetentions',
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
    ...mapActions('retentions', [
      'fetchRetentions',
      /* 'selectAllTaxGroups',
      'selectTaxGroup',*/
      'deleteRetention',
    ]),
    ...mapActions('notification', ['showNotification']),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('company', ['fetchCompanySettings']),
    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchRetentions(data)
      this.isRequestOngoing = false

      //console.log('res: ', response);

      return {
        data: response.data.retentions.data,
        pagination: {
          totalPages: response.data.retentions.last_page,
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

    async removeRetention(id) {
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteRetention(id)
          //console.log("🚀 ~ file: Index.vue ~ line 286 ~ removeRetention ~ res", res)

          if (res.data.success) {
            window.toastr['success'](this.$tc('settings.retentions.deleted_message'))
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
       module: "retentions" 
    }
    const permissions = await this.getUserModules(data)
    let response = await this.fetchCompanySettings(['retention_platform_active'])
    let retentionActive = true
  
    if(response.data.retention_platform_active != undefined){
      retentionActive = response.data.retention_platform_active == "YES" ? false : true
    }
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
        } else if(retentionActive){
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
