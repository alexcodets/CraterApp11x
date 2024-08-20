<template>
  <!-- <base-page v-if="isSuperAdmin" > -->
  <div>
    <sw-page-header :title="$t('core_pos.index_store')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('core_pos.index_store', 2)"
          active
        />
      </sw-breadcrumb>
      <template slot="actions">
        <sw-button
          @click="toggleFilter"
          size="lg"
          type="button"
          variant="primary-outline"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="store/create"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('core_pos.add_store') }}
        </sw-button>
      </template>
    </sw-page-header>

    <!-- Filters 27 Jul Alejo-->

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group :label="$t('core_pos.store_name')" class="flex-1 mt-2">
          <sw-input
            v-model="filters.name"
            class="mt-2"
            type="text"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('core_pos.store_description')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.description"
            type="text"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <sw-input-group
          :label="$t('core_pos.store_company')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
        
        <base-customer-select ref="customerSelect" @select="onSelectCustomer" @deselect="clearCustomerSearch" />
        </sw-input-group>
<!-- 
        <sw-input-group :label="$tc('customers.customer', 1)" class="mt-2" style="min-width: 300px">
          </sw-input-group> -->

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
          >{{ $t('general.clear_all') }}</label
        >
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <!-- /Filters 27 Jul Alejo-->

    <!-- Table -->

    <div class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm"></p>
      </div>

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="true"
          :label="$t('core_pos.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('core_pos.name') }}</span>
            <router-link
              :to="{
                path: `/admin/corePOS/store/edit/${row.id}`,
              }"
              class="font-medium text-primary-500"
            >
              {{ row.name }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('core_pos.description')"
          show="description"
        >
          <template slot-scope="row">
            <span>{{ $t('core_pos.description') }}</span>
            {{ row.description }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('core_pos.company')"
          show="company_name"
        >
          <template slot-scope="row">
            <span>{{ $t('core_pos.company') }}</span>
            {{ row.company_name }}
          </template>
        </sw-table-column>

        <!-- Actions -->

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span>{{ $t('payments.action') }}</span>
            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                tag-name="router-link"
                :to="`store/edit/${row.id}`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeStore(row)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </div>
</template>

<script>
import {
  PlusIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  EyeIcon,
  ClipboardListIcon,
  PencilIcon,
  TrashIcon,
} from '@vue-hero-icons/solid'

import { mapActions } from 'vuex'

export default {
  components: {
    PlusIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    ClipboardListIcon,

    EyeIcon,
    PencilIcon,
    TrashIcon,
  },
  data() {
    return {
      isRequestOgoing: true,
      totalStores: null,
      showFilters: false,
      filters: {
        name: '',
        description: '',
        company_name: '',
        customer: ''
      },
    }
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  computed: {
    showEmptyScreen() {
      return !this.totalStores && !this.totalStores
    },

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },

  methods: {
    ...mapActions('corePos', ['fetchStores', 'deleteStore']),

    onSelectCustomer(customer) {
      this.filters.company_name = customer.name
    },

    async clearCustomerSearch(removedOption, id) {
      this.filters.customer = ''
      this.refreshTable()
    },

    async fetchData({ page, filter, sort }) {
      try {
        let data = {
          company_name: this.filters.company_name,
          description: this.filters.description,
          name: this.filters.name,
          orderByField: sort.fieldName || 'created_at',
          orderBy: sort.order || 'desc',
          page,
        }
        this.isRequestOngoing = true
        const response = await this.fetchStores(data)
        this.isRequestOngoing = false
        /*return */
        return {
          data: response.data.stores.data,
          pagination: {
            totalPages: response.data.stores.last_page,
            currentPage: page,
          },
        }
      } catch (e) {
        console.log(e)
      }
    },
    clearFilter() {
      this.filters = {
        name: '',
        description: '',
        company_name: '',
      }
    },

    setFilters() {
      this.refreshTable()
    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }

      this.showFilters = !this.showFilters
    },

    async removeStore(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('settings.core_pos.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteStore({ id: row.id })

          if (res.data.success) {
            window.toastr['success'](
              this.$tc('settings.core_pos.deleted_message', 1)
            )
            this.$refs.table.refresh()
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
