<template>
  <base-page class="customer-create">
    <!------------------- Cabecera  ----------------->

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item :title="$t('general.home')" to="dashboard" />
        <sw-breadcrumb-item :title="$tc('groups.group', 2)" to="#" active />
      </sw-breadcrumb>

      <div class="flex flex-wrap items-center justify-end"> 
        <sw-button
          
        class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
          type="button"
          
          @click="goBack()"
        >
          {{ $t('general.go_back') }}
        </sw-button>

        <sw-button
          v-show="totalGroups"
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
          to="groups/create"
          size="lg"
          variant="primary"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          <plus-sm-icon class="h-6 mr-1 -ml-2 font-bold" />
          {{ $t('groups.new_package_group') }}
        </sw-button>
      </div>
    </div>

    <!--   Fitros     -->
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters">
        <sw-input-group :label="$t('groups.name')" class="flex-1 mt-2">
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
      :title="$t('groups.no_groups')"
      :description="$t('groups.list_of_groups')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/groups/create"
        size="lg"
        variant="primary-outline"
      >
        {{ $t('groups.add_new_group') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div v-show="!showEmptyScreen" class="relative table-container">
      <div
        class="relative flex items-center justify-between h-10 mt-5 border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ groups.length }}</b>
          {{ $t('general.of') }} <b>{{ totalGroups }}</b>
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
          :label="$t('groups.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('groups.name') }}</span>
            <router-link
              :to="{ path: `groups/${row.id}/view` }"
              class="font-medium text-primary-500"
            >
              {{ row.name }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('groups.description')"
          show="description"
        >
          <template slot-scope="row">
            <span>{{ $t('groups.description') }}</span>
            <span v-if="row.description" v-html="row.description"> </span>
            <span v-else>
              {{ $t('groups.empty') }}
            </span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('groups.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`groups/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                :to="`groups/${row.id}/view`"
                tag-name="router-link"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeGroup(row.id)">
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
      },
    }
  },
  computed: {
    showEmptyScreen() {
      return !this.totalGroups && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    ...mapGetters('group', ['groups', 'totalGroups']),
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },
  methods: {
    ...mapActions('group', [
      'fetchGroups',
      'selectAllGroups',
      'selectGroup',
      'deleteGroup',
    ]),
    ...mapActions('notification', ['showNotification']),

    refreshTable() {
      this.$refs.table.refresh()
    },

    goBack() {
      this.$router.push(`/admin/packages`)
    },

    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name,
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchGroups(data)
      this.isRequestOngoing = false

      return {
        data: response.data.groups.data,
        pagination: {
          totalPages: response.data.groups.last_page,
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

    async removeGroup(id) {
      this.id = id
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('items.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteGroup({ ids: [id] })

          if (res.data.success) {
            window.toastr['success'](this.$tc('groups.deleted_message', 1))
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
