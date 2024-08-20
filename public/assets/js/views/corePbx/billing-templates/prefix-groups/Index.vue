<template>
  <div class="relative">
    <!-- Page Header -->
    <sw-page-header :title="$t('corePbx.prefix_groups.title')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('corePbx.corePbx')" to="/admin/corePBX"/>
        <sw-breadcrumb-item
          :title="$tc('corePbx.prefix_groups.prefix_group', 2)"
          to="#"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
        <sw-button
          v-show="totalPrefixGroups"
          size="lg"
          variant="primary-outline"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="h-4 ml-1 -mr-1 font-bold"/>
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/international-rate`"
          variant="primary-outline"
          size="lg"
          class="flex justify-center w-auto align-bottom ml-4"
        >
         
          {{ $t('corePbx.prefix_groups.custom_destination_button') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="prefix-groups/create"
          size="lg"
          variant="primary"
          class="ml-4"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold"/>
          {{ $t('corePbx.prefix_groups.new_prefix_group') }}
        </sw-button>
      </template>
    </sw-page-header>

    <!--   Fitros     -->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group
          :label="$t('corePbx.prefix_groups.name')"
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
          :label="$t('corePbx.prefix_groups.description')"
          class="flex-1 mt-2 ml-0 lg:ml-6"
        >
          <sw-input
            v-model="filters.description"
            type="text"
            name="description"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group>

        <label
          class="absolute text-sm leading-snug text-black cursor-pointer"
          style="top: 10px; right: 15px"
          @click="clearFilter"
        >
          {{ $t('general.clear_all') }}
        </label>
      </sw-filter-wrapper>
    </slide-y-up-transition>

    <!--   Si la tabla esta vacia     -->
    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('corePbx.prefix_groups.no_prefix_groups')"
      :description="$t('corePbx.prefix_groups.list_of_prefix_groups')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/corePBX/billing-templates/prefix-groups/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('corePbx.prefix_groups.add_new_prefixes_group') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <!--   Si hay informacion para la tabla     -->
    <div v-show="!showEmptyScreen" class="relative table-container">
      <!-- Fila de utilidades -->
      <div class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid">
        <!-- Informacion de paginacion -->
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ prefixGroups.length }}</b>
          {{ $t('general.of') }} <b>{{ totalPrefixGroups }}</b>
        </p>

        <!-- Dropdown para eliminar multiples grupos -->
        <sw-transition type="fade">
          <sw-dropdown v-if="selectedPrefixGroups.length">
            <span
              slot="activator"
              class="flex block text-sm font-medium cursor-pointer select-none text-primary-400"
            >
                {{ $t('general.actions') }}
                <chevron-down-icon class="h-5" />
            </span>

            <sw-dropdown-item @click="'removeMultipleItemGroups'">
              <trash-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.delete') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </sw-transition>
      </div>

      <!-- Seleccionar todos los elementos -->
      <!--<div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="'selectAllItemGroups'"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="'selectAllItemGroups'"
        />
      </div>-->

      <!-------------------------- Tabla -------------------------->
      <base-loader v-if="isLoadingDelete"/>
      <sw-table-component
        ref="table"
        :show-filter="false"
        :data="fetchData"
        table-class="table"
      >
      <!--<sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="no-click"
        >
          <div slot-scope="row" class="relative block">
            <sw-checkbox
              :id="row.id"
              :value="row.id"
              variant="primary"
              size="sm"
            />
          </div>
        </sw-table-column>-->

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.prefix_groups.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.prefix_groups.name') }}</span>
            <router-link
              :to="{ path: `prefix-groups/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.name }}
            </router-link>
          </template>
        </sw-table-column>

              <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.prefix_groups.prefix_groups_type')"
          show="type"
        >
          <template slot-scope="row">
            <span> Type</span>
            {{ row.type }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :filterable="true"
          :label="$t('corePbx.prefix_groups.description')"
          show="description"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.prefix_groups.description') }}</span>
            <span v-html="row.description ? row.description : $t('item_groups.empty')"/>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('customers.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`prefix-groups/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`prefix-groups/${row.id}/copy`"
                tag-name="router-link"
              >
                <plus-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('packages.copy') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`prefix-groups/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removePrefixGroup(row.id)">
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

import {mapActions, mapGetters} from 'vuex'
import {PlusSmIcon, ArrowLeftIcon} from '@vue-hero-icons/solid'
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  TrashIcon,
  PencilIcon,
  PlusIcon,
  EyeIcon,
} from '@vue-hero-icons/solid'
import AstronautIcon from '../../../../components/icon/AstronautIcon'
import BaseLoader from '../../../../components/base/BaseLoader.vue'

export default {
  components: {
    PlusSmIcon,
    ArrowLeftIcon,
    FilterIcon,
    XIcon,
    ChevronDownIcon,
    TrashIcon,
    PlusIcon,
    PencilIcon,
    EyeIcon,
    AstronautIcon,
    BaseLoader
  },
  data() {
    return {
      showFilters: false,
      isRequestOngoing: false,
      isLoadingDelete: false,
      filters: {
        name: '',
        description: '',
      },
    }
  },
  computed: {
    ...mapGetters('prefixGroup', [
      'prefixGroups',
      'totalPrefixGroups',
      'selectedPrefixGroups',
      'selectAllField'
    ]),

    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    showEmptyScreen() {
      return !this.totalPrefixGroups && !this.isRequestOngoing
    },
    selectAllFieldStatus: {
      get: function () {
        return this.selectAllField
      },
      set: function (val) {
        this.setSelectAllState(val)
      },
    }
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('prefixGroup', [
      'fetchPrefixGroups',
      'deletePrefixGroup',
      'selectAllPrefixGroups',
      'setSelectAllState'
    ]),

    async fetchData({page, filter, sort}) {

      let data = {
        name: this.filters.name,
        description: this.filters.description,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchPrefixGroups(data)
      this.isRequestOngoing = false

      return {
        data: response.data.prefixGroups.data,
        pagination: {
          totalPages: response.data.prefixGroups.last_page,
          currentPage: page,
        },
      }

    },

    refreshTable() {
      this.$refs.table.refresh()
    },

    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
        description: ''
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },

    async removePrefixGroup(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.prefix_groups.confirm_delete', 1),
        icon: 'error',
        iconHtml: `<svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-red-600"fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>`,
        buttons: {
          cancel: true,
          confirm: true,
        }
      }).then(async (result) => {
        try{
          if (result) {
            this.isLoadingDelete = true
            await this.deletePrefixGroup({ids: [id]})
            window.toastr['success'](this.$tc('corePbx.prefix_groups.deleted_message', 1))
            this.$refs.table.refresh()
          }
        }catch(e){
          window.toastr['error'](e.message)
        }finally{
          this.isLoadingDelete = false
        }
      })
    },

  }
}
</script>

<style>

</style>