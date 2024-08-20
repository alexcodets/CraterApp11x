<template>
  <base-page v-if="isSuperAdmin" class="items">
    <sw-page-header :title="$t('packages.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="#" :title="$tc('corePbx.menu_title.packages', 2)" active />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalPackages"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="packages/create"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('packages.add_new_package') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">

        <sw-input-group
          :label="$tc('packages.filter.package_id')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.packages_number"
            type="text"
            name="packages_number"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('packages.filter.name')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.pbx_package_name"
            type="text"
            name="pbx_package_name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>
        <sw-input-group
          :label="$t('packages.item.type_service')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-select
            v-model="filters.status_payment"
            :options="categories"
            :searchable="true"
            :show-labels="false"
            :placeholder="$t('expenses.categories.select_a_category')"
            label="name"
            class="mt-2"
            @click="filter = !filter"
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
      :title="$t('packages.no_packages')"
      :description="$t('packages.list_of_packages')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="packages/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('packages.add_new_package') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
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
          {{ $t('general.showing') }}: <b>{{ packages.length }}</b>

          {{ $t('general.of') }}

          <b>{{ totalPackages }}</b>
        </p>

        <sw-transition type="fade">
          <sw-dropdown v-if="selectedPackages.length">
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

            <sw-dropdown-item @click="removeMultiplePackages">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllPackages"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllPackages"
        />
      </div>

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
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
          :label="$t('packages.item.package_id')"
          show="packages_number"
        >
          <template slot-scope="row">
            <span>{{ $t('packages.item.package_id') }}</span>
            <router-link
              :to="{ path: `packages/${row.id}/edit` }"
              class="font-medium text-primary-500"
            >
              {{ row.packages_number}}
            </router-link>
          </template>
        </sw-table-column>


        <sw-table-column
          :sortable="true"
          :label="$t('packages.item.name')"
          show="pbx_package_name"
        />

   
         <sw-table-column
          :sortable="true"
          :label="$t('packages.item.type_service')"
          show="status_payment">
          <template slot-scope="row">
            
            <div style="text-transform: capitalize;" >{{ row.status_payment}}</div>
          
          </template>
          </sw-table-column>


        <sw-table-column
          :sortable="true"
          :label="$t('packages.item.status')"
          show="status"
        >
          <template slot-scope="row">
            
            <div v-if="row.status == 'A' " >Active</div>
            <div v-if="row.status == 'I' " >Inactive </div>
            <div v-if="row.status == 'R' " >Restrictive</div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('packages.item.qty')"
          show="qty_available"
        />

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
                :to="`packages/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

          
              <sw-dropdown-item
                tag-name="router-link"
                :to="`packages/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`packages/${row.id}/copy`"
              >
                <plus-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('packages.copy') }}
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
      categories: [{"name":"Prepaid", "value":"prepaid"},{"name":"Postpaid", "value":"postpaid"}],
      filters: {
        pbx_package_name: '',
        packages_number: '',
        status_payment: '',
      },
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('pbx', [
      'selectedPackages',
      'totalPackages',
      'packages',
      'selectAllField',
    ]),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.totalPackages && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    selectField: {
      get: function () {
        return this.selectedPackages
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
      this.selectAllPackages()
    }
  },
  methods: {
    ...mapActions('pbx', [
      'fetchPackages',
      'fetchPackages2',
      'selectPackage',
      'resetSelectedPackages',
      'selectAllPackages',
      'deletePackage',
      'clonePackage',
    ]),
    refreshTable() {
      this.$refs.table.refresh()
    },
    async fetchData({ page, filter, sort }) {
    
      let data = {
        pbx_package_name: this.filters.pbx_package_name !== null ? this.filters.pbx_package_name : '',
        packages_number: this.filters.packages_number !== null ? this.filters.packages_number : '',
        status_payment: this.filters.status_payment !== null ? this.filters.status_payment.value : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true;
      
      let response = await this.fetchPackages2(data);     
      this.isRequestOngoing = false;

      return {
        data: response.data.pbxPackages.data || {},
        pagination: {
          totalPages: response.data.pbxPackages.last_page,
          currentPage: page,
          count: response.data.pbxPackages.total
        },
      }
    },
    setFilters() {
      
      this.refreshTable()
    },
    mounted(){
       this.categories = [{"name":"Prepaid", "value":"prepaid"},{"name":"Postpaid", "value":"postpaid"}];
    
    },
    clearFilter() {
      this.filters = {
        pbx_package_name: '',
        packages_number: '',
        status_payment: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },
    async removePackage(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('packages.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePackage(id)
          if (res.data.success) {
            window.toastr['success'](this.$tc('packages.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }
          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('packages.user_attached_message'),
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