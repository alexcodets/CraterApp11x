<template >
  <!-- <base-page  > -->
  <div :class="{ 'xl:pl-96': showSideBar }">
    <sw-page-header :title="$t('bandwidth.e911management')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('bandwidth.e911management')"
          active
        />
      </sw-breadcrumb>

      <template slot="actions">
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
          tag-name="router-link"
          to="/customer/reports/departaments/create"
          variant="primary"
          size="lg"
          class="ml-4"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('tickets.departaments.add_new_departament') }}
        </sw-button> -->
      </template>
    </sw-page-header>

    <slide-x-left-transition>
      <Sidebart-departaments v-show="showSideBar" />
    </slide-x-left-transition>

  <!-- FILTER DESCOMMENT -->
    <!-- <slide-y-up-transition>
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
    </slide-y-up-transition> -->

    <!-- <sw-empty-table-placeholder
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
    </sw-empty-table-placeholder> -->


    <h1>Coming Soon</h1>
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
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      showSideBar: true,
      totalDepartaments: 0,
    }
  },
  computed: {
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },

    listIcon() {
      return this.showSideBar ? 'x-icon' : 'clipboard-list-icon'
    },
    showEmptyScreen() {
      return this.totalDepartaments == 0
    },
  },
  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  methods: {
    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },
    toggleListCustomers() {
      this.showSideBar = !this.showSideBar
    },
  },
}
</script>