<template>
  <base-page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-page-header :title="$t('items.title')">
        <sw-breadcrumb slot="breadcrumbs">
          <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
          <sw-breadcrumb-item :title="$tc('items.item', 2)" to="#" active />
        </sw-breadcrumb>
      </sw-page-header>

      <div class="flex flex-wrap items-center justify-end">
        <sw-button
          v-show="totalItems"
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
          :to="`/admin/item-groups`"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
        >
          {{ $t('navigation.items_group') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="items/create"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          v-if="permissionModule.create"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('items.add_item') }}
        </sw-button>
      </div>
    </div>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group :label="$tc('items.name')" class="flex-1 mt-2 ml-0">
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
            v-if="permissionModule.read"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('items.unit')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-select
            v-model="filters.unit"
            :options="itemUnits"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('items.select_a_unit')"
            class="mt-2"
            label="name"
            autocomplete="off"
          />
        </sw-input-group>

        <!--
        <sw-input-group
          :label="$tc('items.price')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.price"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>
        -->
        
        <sw-input-group
          :label="$tc('items.avalara')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
          v-if="isAvalaraAvailable"
        >
          <sw-select
            v-model="filters.avalara"
            :options="avalaraFilterOptions"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('items.avalara')"
            class="mt-2"
            label="text"
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
      :title="$t('items.no_items')"
      :description="$t('items.list_of_items')"
    >
      <satellite-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/items/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('items.add_new_item') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="
          relative
          flex
          items-center
          justify-between
          h-10
          mt-5
          list-none
          border-b-2 border-gray-200 border-solid
        "
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ items.length }}</b>
          {{ $t('general.of') }} <b>{{ totalItems }}</b>
        </p>

        <sw-transition>
          <sw-dropdown v-if="selectedItems.length">
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

            <sw-dropdown-item @click="removeMultipleItems"
            v-if="permissionModule.delete" >
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-7">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllItems"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden mt-2"
          @change="selectAllItems"
        />
      </div>

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
        class="-mt-10 md:mt-0"
      >
        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="custom-control custom-checkbox">
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
          :label="$t('items.item_number')"
          show="item_number"
        >
          <template slot-scope="row">
            <span>{{ $t('items.item_number') }}</span>
            <span> {{ row.item_number }}</span>
          </template>
        </sw-table-column>

        <sw-table-column :sortable="true" :label="$t('items.name')" show="name">
          <template slot-scope="row">
            <span>{{ $t('items.name') }}</span>
            <router-link
              :to="{ path: `items/${row.id}/edit` }"
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
          :label="$t('items.unit')"
          show="unit_name"
        >
          <template slot-scope="row">
            <span>{{ $t('items.unit') }}</span>

            <span>
              {{ row.unit_name ? row.unit_name : 'Not selected' }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('items.price')"
          show="price"
        >
          <template slot-scope="row">
            <span> {{ $t('items.price') }} </span>

            <div v-html="$utils.formatMoney(row.price, defaultCurrency)" />
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('items.added_on')"
          sort-as="created_at"
          show="formattedCreatedAt"
        />

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('items.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`items/${row.id}/edit`"
                tag-name="router-link"
                v-if="permissionModule.update"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeItems(row.id)"
              v-if="permissionModule.delete" >
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
import SatelliteIcon from '../../components/icon/SatelliteIcon'

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
      avalaraFilterOptions: [
        {
          value: null,
          text: 'ALL',
        },
        {
          value: true,
          text: "Active",
        },
        {
          value: false,
          text: 'Not Active',
        },
      ],

      filters: {
        name: '',
        unit: '',
        price: '',
        avalara: '',
      },
      permissionModule: {
        create: false,
        update: false,
        delete: false,
        read: false
      },
      isAvalaraAvailable: false
    }
  },

  computed: {
    ...mapGetters('item', [
      'items',
      'selectedItems',
      'totalItems',
      'selectAllField',
      'itemUnits',
    ]),

    ...mapGetters('company', ['defaultCurrency']),

    showEmptyScreen() {
      return !this.totalItems && !this.isRequestOngoing
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    selectField: {
      get: function () {
        return this.selectedItems
      },
      set: function (val) {
        this.selectItem(val)
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

  mounted() {
    this.fetchModuleAccess()
    this.permissionsUserModule()
    this.fetchItemUnits({ limit: 'all' })
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllItems()
    }
  },

  methods: {
    ...mapActions('item', [
      'fetchItems',
      'selectAllItems',
      'selectItem',
      'deleteItem',
      'deleteMultipleItems',
      'setSelectAllState',
      'fetchItemUnits',
    ]),
    ...mapActions('user', ['getUserModules']),
    ...mapActions('modules', ['getModules']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        search: this.filters.name !== null ? this.filters.name : '',
        unit_id: this.filters.unit !== null ? this.filters.unit.id : '',
        avalara_bool: this.filters.avalara !== null ? this.filters.avalara.value : '',
        price: Math.round(this.filters.price * 100),
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      // console.log("Lista para mostrar",data);
      this.isRequestOngoing = true
      let response = await this.fetchItems(data)
      this.isRequestOngoing = false
      
      return {
        data: response.data.items.data,
        pagination: {
          totalPages: response.data.items.last_page,
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
        unit: '',
        price: '',
        avalara: null,
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeItems(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          try {
            await this.deleteItem({ ids: [id] })
             this.$refs.table.refresh() 
             window.toastr['success'](this.$tc('items.deleted_message', 1))           
          } catch (error) {
            const objectErrors = error.response.data.errors
            if (objectErrors) {
              Object.keys(objectErrors).map((key) => {
                objectErrors[key].map((error) => {
                  window.toastr['error'](error)
                })
              })
            }
          }
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
             await this.deleteMultipleItems()
             this.$refs.table.refresh()
           } catch (error) {
             const objectErrors = error.response.data.errors
              if (objectErrors) {
                Object.keys(objectErrors).map((key) => {
                  objectErrors[key].map((error) => {
                    window.toastr['error'](error)
                  })
                })
              }        
           }
        }
      })
    },

    async fetchModuleAccess() {
      const modules = ['Avalara']
      const modulesArray = await this.getModules(modules)
 
      const moduleAvalara = modulesArray.modules.find(
        (element) => element.name === 'Avalara'
      )

      if (moduleAvalara && moduleAvalara.status == 'A') {
        this.isAvalaraAvailable = true
      }
    },
    
    async permissionsUserModule(){
      const data = {
        module: "items" 
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
