<template>
  <div>
    <sw-page-header :title="$t('general.tables')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item to="#" :title="$tc('general.tables', 2)" active />
      </sw-breadcrumb>

      <template slot="actions">

        <sw-button @click="toggleFilter" size="lg" type="button" variant="primary-outline">
          {{ $t("general.filter") }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold" />
        </sw-button>

        <sw-button tag-name="router-link" to="create-table" variant="primary" size="lg" class="ml-4">
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('core_pos.tables.add_table') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group :label="$t('core_pos.tables.table_name')" class="flex-1 mt-2">
          <sw-input v-model="filters.name" class="mt-2" type="text" autocomplete="off" />
        </sw-input-group>

        <sw-input-group :label="$t('core_pos.tables.user')" class="flex-1 mt-2 ml-0 lg:ml-6">
          <sw-input v-model="filters.user_id" type="text" class="mt-2" autocomplete="off" />
        </sw-input-group>

        <label class="absolute text-sm leading-snug text-black cursor-pointer" style="top: 10px; right: 15px"
          @click="clearFilter">{{ $t("general.clear_all") }}</label>
      </sw-filter-wrapper>
    </slide-y-up-transition>
    <!-- Table -->

    <div class="relative table-container">
      <div class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid">
        <p class="text-sm"></p>
      </div>

      <sw-table-component ref="table" :data="fetchData" :show-filter="false" table-class="table">

        <sw-table-column :sortable="true" :label="$t('core_pos.tables.table_name')" sort-as="name" show="name">
          <template slot-scope="row">
            <span>{{ $t('core_pos.tables.table_name') }}</span>
            {{ row.name }}
          </template>
        </sw-table-column>
        <sw-table-column :sortable="true" :label="$t('core_pos.tables.user')" sort-as="user_id" show="user.name" />
        <sw-table-column :sortable="true" :label="$t('core_pos.cash_registers')" sort-as="cash_register">
          <template slot-scope="row">
            <div v-for="item in row.cash_register" :key="item.id"> - {{ item.name }}</div>
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
                :to="`table/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeTable(row)">
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
        user_id: "",
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

    async fetchData({ page, filter, sort }) {

      let data = {
        name: this.filters.name,
        user_id: this.filters.user_id,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await window.axios.post('/api/v1/core-pos/get-tables', data)
      this.isRequestOngoing = false
      return {
        data: response.data.tables.data,
        pagination: {
          totalPages: response.data.tables.last_page,
          count: response.data.tables.total,
          currentPage: page,
        },
      }

    },
    clearFilter() {
      this.filters = {
        name: "",
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

    async removeTable(row) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('settings.core_pos.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {

          let res = await window.axios.delete(`/api/v1/core-pos/tables/${row.id}`)
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
  