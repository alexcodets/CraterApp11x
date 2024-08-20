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
    </sw-page-header>

      <div class="flex flex-wrap items-center justify-end md:-mt-16">
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

        <sw-button
          v-show="totalDepartaments"
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
          to="departaments/create"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('tickets.departaments.add_new_departament') }}
        </sw-button>
      </div>

    <slide-x-left-transition>
      <Sidebart-departaments v-show="showSideBar" />
    </slide-x-left-transition>

    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">
        <sw-input-group
          :label="$tc('tickets.menu_title.departaments')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.name"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />

          <p class="text-xs mt-1 text-rose-600">
                {{ $t('tickets.warning_filter_departamente') }}
              </p>
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
          :label="$t('tickets.departaments.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('tickets.departaments.name') }}</span>

            <router-link
              :to="`departaments/${row.id}/view`"
              class="font-medium text-primary-500"
            >
              {{ row.name }}
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
          :label="$t('tickets.departaments.email')"
          show="email"
        />
        <sw-table-column
          v-if="contenido"
          :sortable="true"
          :label="$t('tickets.departaments.default_priority')"
          show="status"
        >
          <template slot-scope="row">
            {{
              row.default_priority == null
                ? ''
                : row.default_priority == 'E'
                ? 'Emergency'
                : row.default_priority == 'C'
                ? 'Critical'
                : row.default_priority == 'H'
                ? 'High'
                : row.default_priority == 'M'
                ? 'Medium'
                : row.default_priority == 'L'
                ? 'Low'
                : ''
            }}
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
                :to="`departaments/${row.id}/view`"
              >
                <eye-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.view') }}
              </sw-dropdown-item>

              <sw-dropdown-item
                tag-name="router-link"
                :to="`departaments/${row.id}/edit`"
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
    ...mapGetters('ticketDepartament', [
      'selectedDepartaments',
      'totalDepartaments',
      'departaments',
      'selectAllField',
    ]),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.totalDepartaments && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
    selectField: {
      get: function () {
        return this.selectedDepartaments
      },
    },

    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
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
    ...mapActions('ticketDepartament', [
      'fetchDepartaments',
      'deleteDepartament',
    ]),
    refreshTable() {
      this.$refs.table.refresh()
    },
    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name !== null ? this.filters.name : '',
        orderByField: sort.fieldName || 'created_at',
        orderBy: sort.order || 'desc',
        page,
      }
      this.isRequestOngoing = true
      let response = await this.fetchDepartaments(data)
      this.isRequestOngoing = false

      return {
        data: response.data.departaments.data || {},
        pagination: {
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
          let res = await this.deleteDepartament(id)
          
          if (res.data.success){
            window.toastr['success'](this.$tc('tickets.departaments.deleted_message', 1))
            this.$refs.table.refresh()
            return true
          }else{
            window.toastr['error'](res.data.error)
            //this.$refs.table.refresh()
            return true
          }
          /*
          if (res.data.error === 'user_attached') {
            window.toastr['error'](
              this.$tc('tickets.departaments.user_attached_message'),
              this.$t('general.action_failed')
            )
            return true
          }
          window.toastr['error'](res.data.message)
          return true
          */
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