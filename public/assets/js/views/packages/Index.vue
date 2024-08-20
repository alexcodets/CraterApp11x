<template>
  <base-page v-if="isSuperAdmin" class="items">
    <sw-page-header :title="$t('packages.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="dashboard" :title="$t('general.home')" />
        <sw-breadcrumb-item to="#" :title="$tc('packages.title', 2)" active />
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
          :to="`/admin/groups`"
          size="lg"
          class="ml-4"
          variant="primary-outline"
        >
          {{ $t('navigation.package_group') }}
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
        <!-- FILTER PACKAGES NUMBER -->
        <sw-input-group :label="$tc('packages.filter.package_id')" class="mt-2" >
          <sw-input
            v-model="filters.package_number"
            type="text"
            name="name"
            autocomplete="off"
          />
        </sw-input-group>
        
        <!-- FILTER PACKAGES NAME -->
        <sw-input-group :label="$tc('packages.filter.name')" class="mt-2 md:px-2">
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            autocomplete="off"
          />
        </sw-input-group>

       <!-- FILTER PACKAGES STATUS PAYMENT -->
        <sw-input-group :label="$t('packages.filter.packages_type')" class="mt-2">
          <sw-select
            v-model.trim="filters.status_payment"
            :options="status_paymentOptions"
            label="text"
            track-by="value"
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
        to="/admin/packages/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('packages.add_new_package') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
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
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
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

        <sw-table-column :sortable="true" :label="$t('packages.item.package_id')" show="package_number">
          <template slot-scope="row">
            <span>{{ $t('packages.item.package_id') }}</span>
            <router-link
              :to="{ path: `packages/${row.id}/edit` }"
              class="font-medium text-primary-500"
            >
              {{ row.package_number }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('packages.item.name')"
          show="name"
        />


         <sw-table-column
          :sortable="true"
          :label="$t('packages.item.type_service')"
          show="status_payment">
          <template slot-scope="row">
            
            <div v-if="row.status_payment == 'O' " >{{$t('packages.item.postpaid')}}</div>
            <div v-if="row.status_payment == 'R' " >{{$t('packages.item.prepaid')}} </div>
          </template>
          </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('packages.item.status')"
          show="status">
          <template slot-scope="row">
           

            <div v-if="row.status == 'A' " >Active</div>
            <div v-if="row.status == 'I' " >Inactive </div>
            <div v-if="row.status == 'R' " >Restrictive</div>
          </template>
          </sw-table-column>

           <sw-table-column
          :sortable="true"
          :label="$t('packages.item.qty')"
          show="qty"/>

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('packages.action') }} </span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item tag-name="router-link" :to="`packages/${row.id}/view`">
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item tag-name="router-link" :to="`packages/${row.id}/edit`">
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item tag-name="router-link" :to="`packages/${row.id}/copy`">
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
      filters: {
        name: '',
        package_number: '',
        status_payment: '',
        module: '',
        qty: '',
      },
      status_paymentOptions: [
        { value: 'O', text: 'Prepaid' },
        { value: 'R', text: 'Postpaid' },
      ],
      timeClear: null,
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('pack', [
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
      handler(val){
        // delay to avoid multiple request
        if (this.timeClear) clearTimeout(this.timeClear)

        this.timeClear = setTimeout(() => {
          this.setFilters()
        }, 800)
      },
      deep: true,
    },
  },
  destroyed() {
    if (this.selectAllField) {
      this.selectAllPackages()
    }
  },
  methods: {
    ...mapActions('pack', [
      'fetchPackages',
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
        name: this.filters.name !== null ? this.filters.name : '',
        package_number: this.filters.package_number !== null ? this.filters.package_number : '',
        status_payment: this.filters.status_payment !== null ? this.filters.status_payment.value : '',
        module: this.filters.module !== null ? this.filters.module : '',
        // qty: this.filters.qty !== null ? this.filters.qty : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
     
      this.isRequestOngoing = true
      let response = await this.fetchPackages(data)
      this.isRequestOngoing = false
      return {
        data: response.data.packages.data,
        pagination: {
          totalPages: response.data.packages.last_page,
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
        package_number: '',
        status_payment: '',
        module: '',
        qty: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },
    async removePackage(id) {
      let pack = []
      pack.push(id)
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('packages.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deletePackage(pack)
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