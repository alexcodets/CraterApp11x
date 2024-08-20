<template >
  <!-- <base-page v-if="isSuperAdmin" > -->
  <div :class="{ 'xl:pl-64': showSideBar }">
    <sw-page-header :title="$t('tickets.menu_title.departaments')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('tickets.menu_title.departaments', 2)"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/packages`"
          class="mr-3"
          variant="primary-outline"
        >
          {{ $t('general.go_back') }}
        </sw-button>


        <div class="mr-3 hidden xl:block">
          <sw-button
            class=""
            variant="primary-outline"
            @click="toggleListCustomers"
          >
            {{ $t('tickets.departaments.menu') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
        </div>

        <!-- <sw-button
          v-show="totalDepartaments"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button> -->

        <sw-button
          tag-name="router-link"
          to="/admin/corePBX/reports/departaments/create"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('tickets.departaments.add_new_departament') }}
        </sw-button>
      </template>
    </sw-page-header>

    <slide-x-left-transition>
      <Sidebart-departaments v-show="showSideBar" />
    </slide-x-left-transition>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group
          :label="$tc('packages.filter.name')"
          class="flex-1 mt-2 mr-4"
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
      :title="$t('tickets.departaments.no_packages')"
      :description="$t('tickets.departaments.list_of_departaments')"
    >
      <astronaut-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="departaments/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('tickets.departaments.add_new_departament') }}
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
          {{ $t('general.showing') }}: <b>{{ departaments.length }}</b>

          {{ $t('general.of') }}

          <b>{{ totalDepartaments }}</b>
        </p>
      </div>

      <sw-table-component
        ref="table"
        :data="fetchData"
        :show-filter="false"
        table-class="table"
      >
        <sw-table-column
          :sortable="true"
          :label="$t('tickets.departaments.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('tickets.departaments.name') }}</span>

            <router-link
              :to="`/admin/corePBX/reports/departaments/${row.id}/edit`"
              class="font-medium text-primary-500"
            >
              {{ row.name }}
            </router-link>
          </template>
        </sw-table-column>
        

        <sw-table-column
          :sortable="false"
          :label="$t('tickets.departaments.creator')"
          show="creator"
        >
          <template slot-scope="row">
            <span>{{ $t('tickets.departaments.creator') }}</span>

            <router-link
              :to="`/admin/customers/${row.create_user.id}/view`"
              class="font-medium text-primary-500"
              v-if="row.create_user.role == 'customer'"
            >
            {{ $t('general.customer') }}: {{ row.create_user.customcode }}
            </router-link>

            <router-link
              :to="`/admin/users/${row.create_user.id}/view`"
              class="font-medium text-primary-500"
              v-else
            >
            {{ $t('general.user') }}: {{ row.create_user.name }}
            </router-link>

          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('tickets.departaments.description')"
          show="description"
        >
          <template v-if="row.description !== null" slot-scope="row">
            {{ row.description.replace(/(<([^>]+)>)/gi, '') }}
          </template>
        </sw-table-column>


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
                :to="`/admin/corePBX/reports/departaments/${row.id}/edit`"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeDepartament(row.id)">
                <trash-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.delete') }}
              </sw-dropdown-item>
            </sw-dropdown>
          </template>
        </sw-table-column>
      </sw-table-component>
    </div>
  </div>
  <!-- </base-page> -->
</template>

<script>
import { mapActions, mapGetters } from 'vuex'
import SidebartDepartaments from '../SidebartTickets'
import AstronautIcon from '@/components/icon/AstronautIcon'
import {
  EyeIcon,
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  ClipboardListIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
export default {
  components: {
    EyeIcon,
    AstronautIcon,
    FilterIcon,
    SidebartDepartaments,
    XIcon,
    ChevronDownIcon,
    ClipboardListIcon,
    PencilIcon,
    TrashIcon,
    PlusIcon,
  },
  data() {
    return {
      id: null,
      contenido: false,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      filters: {
        name: '',
        module: '',
        qty: '',
      },
      showSideBar: true,
      totalDepartaments: 0,
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('ticketDepartament', [
      'selectedDepartaments',
      'departaments',
    ]),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },

    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },
    showEmptyScreen() {
      return this.totalDepartaments == 0
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

  methods: {
    ...mapActions('customSearch', [
      'fetchCustomSearch',
      'deleteCustomSearch',
    ]),
    refreshTable() {
      this.$refs.table.refresh()
    },
    async fetchData({ page, filter, sort }) {

      if(sort.fieldName == 'creator'){
        sort.fieldName = 'user.name'
      }

      let data = {
        name: this.filters.name !== null ? this.filters.name : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchCustomSearch(data)
      this.isRequestOngoing = false
      //console.log(response.data.customSearches.total)
      this.totalDepartaments = response.data.customSearches.total

      return {
        data: response.data.customSearches.data|| {},
        pagination: {
          currentPage: page,
          totalPages: response.data.customSearches.last_page,
          count: response.data.customSearches.total,
        },
      }
    },
    setFilters() {
      this.refreshTable()
    },
    clearFilter() {
      this.filters = {
        name: '',
        email: '',
        phone: '',
      }
    },
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },
    async removeDepartament(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('tickets.departaments.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          let res = await this.deleteCustomSearch(id)
          if (res.data.success) {
            window.toastr['success'](
              this.$tc('tickets.departaments.deleted_message', 1)
            )
            this.$refs.table.refresh()
            return true
          }
          window.toastr['error'](res.data.message)
          return true
        }
      })
    },
    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
      /*   this.isRefresh = true
      setTimeout(() => (this.isRefresh = false), 300) */
    },
  },
}
</script>