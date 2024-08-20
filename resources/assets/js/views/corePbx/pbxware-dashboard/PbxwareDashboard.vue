<template>
  <base-page :class="{ 'xl:pl-96': showSideBar }">
    <sw-page-header :title="$t('corePbx.title')">
      <template slot="actions">
        <div class="mr-3 hidden xl:block" style="margin-bottom: 10px;">
          <sw-button
            class=""
            variant="primary-outline"
            @click="togglehidenmenu"
          >
            {{ $t('corePbx.menu') }}
            <component :is="listIcon" class="w-4 h-4 ml-2 -mr-1" />
          </sw-button>
        </div>
        </template>
    </sw-page-header>
  <!-- sidebar -->
    <slide-x-left-transition>
      <customer-view-sidebar v-show="showSideBar" />
    </slide-x-left-transition>

    <!-- Chart
    <customer-chart :refresh="isRefresh" /> -->
     
     <pbx-stats />

    <!-- Extencions -->

    <div
        class="tabs mb-5 grid col-span-12 border-t-2 border-solid pt-6"
        style="border-top-color: #f9fbff"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck1"
            />
            <header
              class="
                col-span-5
                flex
                justify-between
                items-center
                py-3
                cursor-pointer
                select-none
                tab-label
              "
              for="chck1"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('corePbx.dashboard.extencions') }}
              </span>
              <div
                class="
                  rounded-full
                  border border-grey
                  w-7
                  h-7
                  flex
                  items-center
                  justify-center
                  test
                "
              >
                <!-- icon by feathericons.com -->
                <svg
                  aria-hidden="true"
                  class=""
                  data-reactid="266"
                  fill="none"
                  height="24"
                  stroke="#606F7B"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewbox="0 0 24 24"
                  width="24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content">
              
              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchPackagesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.dashboard.name')"
                  show="code"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.total')"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

    <!-- International Calls -->
       <div
        class="tabs mb-5 grid col-span-12 border-t-2 border-solid pt-6"
        style="border-top-color: #f9fbff"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck1"
            />
            <header
              class="
                col-span-5
                flex
                justify-between
                items-center
                py-3
                cursor-pointer
                select-none
                tab-label
              "
              for="chck1"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('corePbx.dashboard.international_calls') }}
              </span>
              <div
                class="
                  rounded-full
                  border border-grey
                  w-7
                  h-7
                  flex
                  items-center
                  justify-center
                  test
                "
              >
                <!-- icon by feathericons.com -->
                <svg
                  aria-hidden="true"
                  class=""
                  data-reactid="266"
                  fill="none"
                  height="24"
                  stroke="#606F7B"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewbox="0 0 24 24"
                  width="24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content">
              
              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchPackagesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.dashboard.from')"
                  show="code"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.to')"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.date')"
                  show="package.name"
                />

                 <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.totald')"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

    <!-- Call History -->
    <div
        class="tabs mb-5 grid col-span-12 border-t-2 border-solid pt-6"
        style="border-top-color: #f9fbff"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck1"
            />
            <header
              class="
                col-span-5
                flex
                justify-between
                items-center
                py-3
                cursor-pointer
                select-none
                tab-label
              "
              for="chck1"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('corePbx.dashboard.call_history') }}
              </span>
              <div
                class="
                  rounded-full
                  border border-grey
                  w-7
                  h-7
                  flex
                  items-center
                  justify-center
                  test
                "
              >
                <!-- icon by feathericons.com -->
                <svg
                  aria-hidden="true"
                  class=""
                  data-reactid="266"
                  fill="none"
                  height="24"
                  stroke="#606F7B"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewbox="0 0 24 24"
                  width="24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content">
              
              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchPackagesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.dashboard.from')"
                  show="code"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.to')"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.date')"
                  show="package.name"
                />

                 <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.totald')"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

   <!-- Did -->
      <div
        class="tabs mb-5 grid col-span-12 border-t-2 border-solid pt-6"
        style="border-top-color: #f9fbff"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck1"
            />
            <header
              class="
                col-span-5
                flex
                justify-between
                items-center
                py-3
                cursor-pointer
                select-none
                tab-label
              "
              for="chck1"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('corePbx.dashboard.did') }}
              </span>
              <div
                class="
                  rounded-full
                  border border-grey
                  w-7
                  h-7
                  flex
                  items-center
                  justify-center
                  test
                "
              >
                <!-- icon by feathericons.com -->
                <svg
                  aria-hidden="true"
                  class=""
                  data-reactid="266"
                  fill="none"
                  height="24"
                  stroke="#606F7B"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewbox="0 0 24 24"
                  width="24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content">
              
              <sw-table-component
                ref="table"
                :show-filter="false"
                :data="fetchPackagesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('corePbx.dashboard.did_channel')"
                  show="code"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('corePbx.dashboard.provider')"
                  show="package.name"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

    <!------------ SERVICES PBX ------------>

      <div
        class="tabs mb-5 grid col-span-12 pt-6"
      >
        <div class="border-b tab">
          <div class="relative">
            <input
              class="w-full absolute z-10 cursor-pointer opacity-0 h-5 top-4"
              type="checkbox"
              id="chck4"
            />
            <header
              class="
                col-span-5
                flex
                justify-between
                items-center
                py-3
                cursor-pointer
                select-none
                tab-label
              "
              for="chck4"
            >
              <span class="text-gray-500 uppercase sw-section-title">
                {{ $t('customers.services_pbx') }}
              </span>
              <div
                class="
                  rounded-full
                  border border-grey
                  w-7
                  h-7
                  flex
                  items-center
                  justify-center
                  test
                "
              >
                <!-- icon by feathericons.com -->
                <svg
                  aria-hidden="true"
                  class=""
                  data-reactid="266"
                  fill="none"
                  height="24"
                  stroke="#606F7B"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  viewbox="0 0 24 24"
                  width="24"
                  xmlns="http://www.w3.org/2000/svg"
                >
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </div>
            </header>
            <div class="tab-content">

              <sw-table-component
                ref="services_pbx_table"
                :show-filter="false"
                :data="fetchPbxServicesData"
                table-class="table"
              >
                <sw-table-column
                  :sortable="true"
                  :label="$t('services.service_number')"
                  show="code"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$tc('packages.package', 1)"
                  show="pbxPackage.pbx_package_name"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.amount')"
                  sort-as="total"
                >
                  <template slot-scope="row">
                    <span>{{ $t('customers.amount') }}</span>
                    <div
                      v-html="$utils.formatMoney(row.total, row.user.currency)"
                    />
                  </template>
                </sw-table-column>

                <sw-table-column :sortable="true" :label="$t('customers.term')">
                  <template slot-scope="row">
                    <span>{{ $t('customers.term') }}</span>
                    <span>{{ capitalizeFirstLetter(row.term) }}</span>
                  </template>
                </sw-table-column>

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.activation_date')"
                  sort-as="date_begin"
                  show="date_begin"
                />

                <sw-table-column
                  :sortable="true"
                  :label="$t('customers.renewal_date')"
                  show="renewal_date"
                />

                <sw-table-column
                  :sortable="false"
                  :filterable="false"
                  cell-class="action-dropdown no-click"
                >
                  <template slot-scope="row">
                    <span>{{ $t('general.actions') }}</span>

                    <sw-dropdown>
                      <dot-icon slot="activator" />

                      <sw-dropdown-item
                        :to="``"
                        tag-name="router-link"
                      >
                        <calculator-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('invoices.new_invoice') }}
                      </sw-dropdown-item>

                      <sw-dropdown-item
                        :to="``"
                        tag-name="router-link"
                      >
                        <cog-icon class="h-5 mr-3 text-gray-600" />
                        {{ $t('general.manage') }}
                      </sw-dropdown-item>
                    </sw-dropdown>
                  </template>
                </sw-table-column>
              </sw-table-component>
            </div>
          </div>
        </div>
      </div>

   </base-page>
</template>


<script>
import PbxStats from './partials/PbxStats'
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
  ClipboardListIcon,
} from '@vue-hero-icons/solid'
import CustomerViewSidebar from './partials/PbxwareViewSidebar'
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
    ClipboardListIcon,
    CustomerViewSidebar,
    PbxStats,
  },
  data() {
    return {
      showSideBar: false,
      isRefresh: false,
    }
  },
  computed: {
    ...mapGetters('user', ['currentUser']),
    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
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
  destroyed() {
    if (this.selectAllField) {
      this.selectAllPackages()
    }
  },
  methods: {
    refreshTable() {
      this.$refs.table.refresh()
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
    togglehidenmenu() {
      this.showSideBar = !this.showSideBar
      this.isRefresh = true
      setTimeout(() => (this.isRefresh = false), 300)
    },
  },
}
</script>