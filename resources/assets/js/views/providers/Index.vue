<template>
  <base-page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('providers.title')">
          <sw-breadcrumb slot="breadcrumbs">
            <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
            <sw-breadcrumb-item
              :title="$tc('providers.provider', 2)"
              to="#"
              active
            />
          </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalProviders"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="providers/create"
          size="lg"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('providers.new_provider') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('providers.prov_number')"
          class="flex-1 mt-2 mr-4"
        >         
          <sw-input v-model="filters.providers_number"
            type="text"
            name="providers_number"
            class="mt-2"
            autocomplete="off"
          >
            <hashtag-icon slot="leftIcon" class="h-5 ml-1 text-gray-500" />
          </sw-input>
        </sw-input-group>

        <sw-input-group
          :label="$t('providers.titl')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.title"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>
       
        <sw-input-group
          :label="$t('providers.phone')"
          class="flex-1 mt-2 mr-4"
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
          :label="$t('providers.email')"
          class="flex-1 mt-2"
        >
          <sw-input
            v-model="filters.email"
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
      :title="$t('providers.no_providers')"
      :description="$t('providers.list_of_providers')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/providers/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('providers.add_new_provider') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="flex justify-between mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ providers.length }}</b>
          {{ $t('general.of') }} <b>{{ totalProviders }}</b>
        </p>      
      </div>

      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
        class="-mt-12 md:mt-0"
      >   

          <sw-table-column
          :sortable="true"
          :label="$t('providers.prov_number')"
          show="providers_number"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.prov_number') }}</span>
            <span> {{ row.providers_number }}
            </span>
          </template>
        </sw-table-column>   

        <sw-table-column
          :sortable="true"
          :label="$t('providers.titl')"
          show="title"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.titl') }}</span>
            <router-link
              :to="{ path: `providers/${row.id}/view` }"
              class="font-medium text-primary-500"
              v-if="permissionModule.read"
              >
              {{ row.title }}
            </router-link>
            <span v-else >{{ row.title }}</span>
          </template>
        </sw-table-column>
        
        <sw-table-column
          :sortable="true"
          :label="$t('providers.phone')"
          show="phone"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.phone') }}</span>
            <span> {{ row.phone }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('providers.email')"
          show="email"
        >
          <template slot-scope="row">
            <span>{{ $t('providers.email') }}</span>
            <span class="boxtext"> {{ row.email }}
            </span>
          </template>
        </sw-table-column>      

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('providers.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`providers/${row.id}/edit`"
                tag-name="router-link"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`providers/${row.id}/view`"
                tag-name="router-link"
                v-if="permissionModule.read"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeProvider(row.id)" v-if="permissionModule.delete">
                <trash-icon class="h-5 mr-3 text-gray-600"
                 />
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
import { PlusSmIcon } from '@vue-hero-icons/solid'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  EyeIcon,
  HashtagIcon
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
    HashtagIcon
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: true,
      filters: {
        providers_number: '',
        title: '',
        phone: '',
        email: '',
      },
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false
      },
    }
  },
  mounted(){
    this.permissionsUserModule()
  },
  computed: {
    showEmptyScreen() {
      return !this.totalProviders && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('providers', [
      'providers',
      'totalProviders',
    ]),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('providers', [
      'fetchProviders',
      'deleteProvider',
    ]),
    ...mapActions('notification', ['showNotification']),
    ...mapActions('user', ['getUserModules']),
    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        providers_number: this.filters.providers_number,
        title: this.filters.title,        
        phone: this.filters.phone,
        email: this.filters.email,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchProviders(data)
      this.isRequestOngoing = false

      return {
        data: response.data.providers.data,
        pagination: {
          totalPages: response.data.providers.last_page,
          currentPage: page,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {        
        providers_number: '',
        title: '',
        phone: '',
        email: ''
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeProvider(id) {
      this.id = id      
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('providers.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteProvider({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('providers.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }else if (!res.data.success) {
            window.toastr['error'](this.$tc('providers.provider_associated', 1))
            //this.$refs.table.refresh()
            return
          }else{
            window.toastr['error'](res.data.message)
            return true
          }          
        }
      })
    },
    async permissionsUserModule(){
      const data = {
         module: "providers" 
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
