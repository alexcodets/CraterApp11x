<template>
  <base-page v-if="isSuperAdmin" class="items">

   <!-- Page Header -->

   <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <sw-page-header :title="$t('corePbx.menu_title.templates')">
      <sw-breadcrumb slot="breadcrumbs">
        <sw-breadcrumb-item
          to="#"
          :title="$tc('corePbx.menu_title.Internacional', 2)"
          active
        />
      </sw-breadcrumb>

    </sw-page-header>
    <div class="flex flex-wrap items-center justify-end">
        <div class="float-left mr-4 w-full md:w-auto md:ml-4 mb-2 md:mb-0">
          <select
            class="border border-dark"
            style="
              text-align: center;
              border: solid 1px black;
              width: 70px;
              height: 43.5px;
              border-radius: 10%;
            "
            v-model="selected"
            @change="refreshTable()"
          >
            <option
              style="text-align: center"
              v-for="option in options"
              :value="option.value"
            >
              {{ option.name }}
            </option>
          </select>
        </div>

        <sw-button
          v-show="totalInternacionals"
          variant="primary-outline"
          size="lg"
          @click="toggleFilter"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          {{ $t('general.filter') }}
          <component :is="filterIcon" class="w-4 h-4 ml-2 -mr-1" />
        </sw-button>

        <div class="float-right ml-4 w-full md:w-auto md:ml-4 mb-2 md:mb-0">
          <sw-dropdown
            style="
              padding-top: 7px;
              text-align: center;
              border: solid 1px black;
              width: 80px;
              height: 42px;
              border-radius: 10%;
            "
          >
            <span
              slot="activator"
              class="text-sm font-medium cursor-pointer select-none"
              style="font-size: 16px !important"
            >
              {{ $t('general.actions') }}
            </span>
            <sw-dropdown-item @click="modifySelected">
              <pencil-icon class="h-5 mr-3 text-gray-600" />
              {{ $t('general.modify_select') }}
            </sw-dropdown-item>
          </sw-dropdown>
        </div>

        <!-- $t('general.filter') -->
        <sw-button
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
          @click="addPaymentMode"
        >
        {{ $t('expenses.import') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          :to="`/admin/corePBX/billing-templates/prefix-groups`"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
          variant="primary-outline"
        >
          {{ $t('corePbx.internacional.prefix_groups') }}
        </sw-button>

        <sw-button
          tag-name="router-link"
          to="international-rate/create"
          variant="primary"
          size="lg"
          class="w-full md:w-auto md:ml-4 mb-2 md:mb-0"
        >
          <plus-icon class="w-6 h-6 mr-1 -ml-2" />
          {{ $t('corePbx.internacional.add_internacional') }}
        </sw-button>
      </div>
    </div>

    <!-- Filtros -->
    <div>
      <slide-y-up-transition>
        <sw-filter-wrapper
          v-show="showFilters"
          class="relative grid grid-flow-col grid-rows"
        >
          <!-- 1ERA COLUMNA-->

          <div class="w-50" style="margin-left: 1em; margin-right: 1em">
            <div style="min-width: 275px">
              <sw-input-group :label="$t('general.type')" class="mt-2">
                <sw-select
                  v-model="filters.prefix_type"
                  :options="prefix_type"
                  :group-select="false"
                  :searchable="true"
                  :show-labels="false"
                  :placeholder="'Select a prefix type'"
                  :allow-empty="false"
                  group-values="options"
                  group-label="label"
                  track-by="name"
                  label="name"
                  @remove="clearStatusSearch()"
                />
              </sw-input-group>
            </div>

            <sw-input-group
              :label="'Name'"
              class="mt-2"
              style="min-width: 275px"
            >
              <sw-input v-model="filters.name"> </sw-input>
            </sw-input-group>
          </div>

          <!-- 2da COLUMNA-->

          <div class="w-50" style="margin-left: 1em; margin-right: 1em">
            <sw-input-group
              v-if="filters.prefix_type.value == 'P'"
              :label="$t('corePbx.custom_did_groups.prefix')"
              class="mt-2"
              style="min-width: 275px"
            >
              <sw-input v-model="filters.prefix"> </sw-input>
            </sw-input-group>

            <div
              v-if="filters.prefix_type.value == ''"
              class="mt-9 not-italic font-normal leading-tight text-left outline-none rounded-md input-field box-border-2 placeholder-gray-400 text-black w-full h-10 px-3 py-2 text-sm"
            ></div>

            <sw-input-group
              v-if="filters.prefix_type.value == 'FT'"
              :label="'From'"
              class="mt-2"
              style="min-width: 275px"
            >
              <sw-input v-model="filters.from"> </sw-input>
            </sw-input-group>

            <sw-input-group :label="$t('CorePbx.internacional.country')" class="mt-2">
              <sw-select
                v-model="filters.country_id"
                :options="countries"
                :searchable="true"
                :show-labels="false"
                :allow-empty="false"
                :placeholder="$t('general.select_country')"
                label="name"
                track-by="id"
              />
            </sw-input-group>
          </div>

          <!-- 3ERA COLUMNA-->

          <div class="w-50" style="margin-left: 1em; margin-right: 1em">
            <div
              v-if="
                filters.prefix_type.value == '' ||
                filters.prefix_type.value == 'P'
              "
              class="mt-9 not-italic font-normal leading-tight text-left outline-none rounded-md input-field box-border-2 placeholder-gray-400 text-black w-full h-10 px-3 py-2 text-sm"
            ></div>

            <sw-input-group
              v-if="filters.prefix_type.value == 'FT'"
              :label="'To'"
              class="mt-2"
              style="min-width: 275px"
            >
              <sw-input v-model="filters.to"> </sw-input>
            </sw-input-group>

            <div style="min-width: 275px">
              <sw-input-group :label="'Custom Destination Group'" class="mt-2">
                <sw-select
                  v-model="filters.prefixrate_groups_id"
                  :options="prefixrate_groups"
                  :searchable="true"
                  :show-labels="false"
                  :allow-empty="true"
                  :placeholder="'Select Group'"
                  :multiple="true"
                  class="mt-2"
                  label="name"
                  track-by="id"
                />
              </sw-input-group>
            </div>
          </div>

          <label
            class="absolute text-sm leading-snug text-black cursor-pointer"
            @click="clearFilter"
            style="top: 20px; right: 30px"
            >{{ $t('general.clear_all') }}</label
          >
        </sw-filter-wrapper>
      </slide-y-up-transition>
    </div>

    <!--
    <slide-y-up-transition>
      <sw-filter-wrapper v-show="showFilters" class="mt-3">

        <sw-input-group
          :label="$tc('didFree.item.prefijo')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.prefix"
            :placeholder="$t('didFree.item.prefijo')"
            type="text"
            name="prefix"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group> 

        <sw-input-group
          :label="$tc('didFree.item.name')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-input
            v-model="filters.name"
            :placeholder="$t('didFree.item.name')"
            type="text"
            name="name"
            class="mt-2"
            autocomplete="off"
          />
        </sw-input-group> 

        <sw-input-group
          :label="$tc('settings.company_info.country')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-select
            v-model="filters.country_id"
            :options="countryNone"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_country')"
            class="mt-2"
            label="name"
            track-by="id"
          />
        </sw-input-group>

        <sw-input-group
          :label="$tc('didFree.item.custom_destination')"
          class="flex-1 mt-2 mr-4"
        >
          <sw-select
            v-model="filters.prefixrate_groups_id"
            :options="prefixrate_groups"
            :searchable="true"
            :show-labels="false"
            :allow-empty="false"
            :placeholder="$t('general.select_country')"
            class="mt-2"
            label="name"
            track-by="id"
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
    -->

    <!-- EMPTY LIST -->
    <sw-empty-table-placeholder
      v-show="showEmptyScreen"
      :title="$t('corePbx.internacional.no_rate')"
      :description="$t('corePbx.internacional.list_of_rate')"
    >
      <satellite-icon class="mt-5 mb-4" />

      <sw-button
        slot="actions"
        tag-name="router-link"
        to="/admin/corePBX/billing-templates/international-rate/create"
        size="lg"
        variant="primary-outline"
      >
        <plus-icon class="w-6 h-6 mr-1 -ml-2" />
        {{ $t('corePbx.internacional.add_new_rate') }}
      </sw-button>
    </sw-empty-table-placeholder>

    <div class="relative table-container" v-show="!showEmptyScreen">
      <div
        class="relative flex items-center justify-between h-10 mt-5 list-none border-b-2 border-gray-200 border-solid"
      >
        <p class="text-sm">
          {{ $t('general.showing') }}: <b>{{ internacionals.length }}</b>
          {{ $t('general.of') }} <b>{{ totalInternacionals }}</b>
        </p>
      </div>

      <div class="absolute z-10 items-center pl-4 mt-2 select-none md:mt-12">
        <sw-checkbox
          v-model="selectAllFieldStatus"
          variant="primary"
          size="sm"
          class="hidden md:inline"
          @change="selectAllInternacionals"
        />

        <sw-checkbox
          v-model="selectAllFieldStatus"
          :label="$t('general.select_all')"
          variant="primary"
          size="sm"
          class="md:hidden"
          @change="selectAllInternacionals"
        />
      </div>

      <base-loader v-if="isLoadingDelete" />
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

        <!-- Nueva Columna -->
        <!-- <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.number')"
          show="extensions_number"
        >
          <template slot-scope="row">
            <span> {{ $t('corePbx.extensions.extension_id') }} </span>

            <div v-html="row.extensions_number" />
          </template>
        </sw-table-column> -->
        <!-- Nueva Columna -->

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.internacional.name')"
          show="name"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.name') }}</span>

            <router-link
              :to="{ path: `international-rate/${row.id}/edit` }"
              class="font-medium text-primary-500"
            >
              {{ row.name }}
            </router-link>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.internacional.type')"
          show="typecustom"
        >
          <template slot-scope="row">
            <span class="whitespace-nowrap">{{
              $t('corePbx.internacional.type')
            }}</span>
            <span class="whitespace-nowrap">{{
              row.typecustom == 'P' ? 'Prefix' : 'From / To'
            }}</span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.internacional.prefix')"
          show="prefix"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.prefix') }}</span>
            {{ row.prefix }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.internacional.fromto')"
          show="from"
        >
          <template slot-scope="row">
            <span class="whitespace-nowrap">{{
              $t('corePbx.internacional.fromto')
            }}</span>
            <span class="whitespace-nowrap">{{
              `${row.from || ''}${row.to ? ' / ' : ''}${row.to || ''}`
            }}</span>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.internacional.country')"
          show="country_id"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.country') }}</span>
            {{ getCountry(row.country_id ? row.country_id : 0) }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.internacional.rate_per_minute')"
          show="rate_per_minute"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.rate_per_minute') }}</span>
            {{ defaultCurrency.symbol + ' ' + row.rate_per_minute }}
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="true"
          :label="$t('corePbx.internacional.status')"
          show="status"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.internacional.status') }}</span>
            <div v-if="row.status == 'A'">
              <sw-badge
                :bg-color="$utils.getBadgeStatusColor('COMPLETED').bgColor"
                :color="$utils.getBadgeStatusColor('COMPLETED').color"
                class="px-3 py-1"
              >
                {{ $t('general.active') }}
              </sw-badge>
            </div>
            <div v-if="row.status == 'I'">
              <sw-badge
                :bg-color="$utils.getBadgeStatusColor('OVERDUE').bgColor"
                :color="$utils.getBadgeStatusColor('OVERDUE').color"
                class="px-3 py-1"
              >
                {{ $t('general.inactive') }}
              </sw-badge>
            </div>
          </template>
        </sw-table-column>

        <sw-table-column
          :sortable="false"
          :label="$t('general.groups_column')"
          show="prefixGroups"
        >
          <template slot-scope="row">
            <span>{{ $t('general.groups_column') }}</span>

            <div v-html="row.FormattedPrefixGroups"></div>
          </template>
        </sw-table-column>

        <!--  <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.prepaidpostpaid')"
          show="status_payment"
        >
          <template slot-scope="row">
            <span>{{ $t('corePbx.extensions.prepaidpostpaid') }}</span>

            <span>
              {{ row.status_payment ? row.status_payment : 'Not selected' }}
            </span>
          </template>
        </sw-table-column> -->

        <!--  <sw-table-column
          :sortable="true"
          :label="$t('corePbx.extensions.description')"
          show="description"
        >
          <template slot-scope="row">
            <span> {{ $t('corePbx.extensions.description') }} </span>

            <div v-html="row.description" />
          </template>
        </sw-table-column> -->

        <!--  <sw-table-column
          :sortable="true"
          :label="$t('items.added_on')"
          sort-as="created_at_no_timezone"
          show="created_at_no_timezone"
        >
          <template slot-scope="row">
            <span>{{ $t('items.added_on') }}</span>
            <span>
              {{
                row.created_at_no_timezone
                  ? row.created_at_no_timezone
                  : 'Not selected'
              }}
            </span>
          </template>
        </sw-table-column> -->

        <sw-table-column
          :sortable="true"
          :filterable="false"
          cell-class="action-dropdown"
        >
          <template slot-scope="row">
            <span> {{ $t('items.action') }} </span>

            <sw-dropdown>
              <dot-icon slot="activator" />

              <sw-dropdown-item
                :to="`international-rate/${row.id}/edit`"
                tag-name="router-link"
              >
                <pencil-icon class="h-5 mr-3 text-gray-600" />
                {{ $t('general.edit') }}
              </sw-dropdown-item>

              <sw-dropdown-item @click="removeExtension(row.id)">
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
import {
  FilterIcon,
  XIcon,
  ChevronDownIcon,
  PencilIcon,
  TrashIcon,
  PlusIcon,
} from '@vue-hero-icons/solid'
import SatelliteIcon from '../../../../components/icon/SatelliteIcon.vue'

export default {
  components: {
    SatelliteIcon,
    FilterIcon,
    XIcon,
    PlusIcon,
    ChevronDownIcon,
    PencilIcon,
    TrashIcon,
  },

  data() {
    return {
      id: null,
      showFilters: false,
      sortedBy: 'created_at',
      isRequestOngoing: true,
      isLoadingDelete: false,
      countryNone: [{ code: '', id: 999999, name: 'None', phonecode: 0 }],
      country: null,
      prefixGroup: {
        prefixes: [],
        status: null,
        prefix_group: [],
      },
      //
      prefixrate_groups: [],
      showFilters: false,
      isRequestOngoing: true,
      timeout: null,
      filters: {
        category: { name: '', value: '' },
        name: '',
        country_id: '',
        prefix_type: { name: '', value: '' },
        prefix: '',
        from: '',
        to: '',
        prefixrate_groups_id: '',
      },
      prefix_type: [
        {
          label: 'Type',
          isDisable: true,
          options: [
            { name: 'Prefix', value: 'P' },
            { name: 'From / To', value: 'FT' },
          ],
        },
      ],
      categories: [
        {
          label: 'Category',
          isDisable: true,
          options: [
            { name: 'Custom', value: 'C' },
            { name: 'International', value: 'I' },
            { name: 'Toll Free', value: 'T' },
          ],
        },
      ],
      selected: 10,
      options: [
        { name: '10', value: 10 },
        { name: '20', value: 20 },
        { name: '50', value: 50 },
        { name: '100', value: 100 },
      ],
      //
    }
  },

  computed: {
    ...mapGetters(['countries']),
    ...mapGetters('user', ['currentUser']),
    ...mapGetters('extensions', [
      'totalExtensions',
      'extensions',
      'selectedExtensions',
    ]),

    ...mapGetters('internacionalrate', [
      'totalInternacionals',
      'internacionals',
      'selectedInternacionals',
      'selectAllField',
    ]),

    ...mapGetters('company', ['defaultCurrency']),

    selectField: {
      get: function () {
        return this.selectedInternacionals
      },
      set: function (val) {
        this.selectInternational(val)
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

    isSuperAdmin() {
      return this.currentUser.role == 'super admin'
    },
    showEmptyScreen() {
      return !this.totalInternacionals && !this.isRequestOngoing
    },
    filterIcon() {
      return this.showFilters ? 'x-icon' : 'filter-icon'
    },
  },

  async mounted() {
    this.countryNone = [...this.countryNone, ...this.countries]
    let res = await this.CargarCustomDestination()
    let no_group = { name: 'No Group', value: 0, id: 0 }
    this.prefixrate_groups = [no_group, ...res.data.internacional]
  },

  watch: {
    filters: {
      handler: 'setFilters',
      deep: true,
    },
  },

  destroyed() {
    if (this.selectAllField) {
      this.selectAllInternacionals()
    }
  },

  methods: {
    ...mapActions('modal', ['openModal']),
    ...mapActions('internacionalrate', [
      'fetchInternacionals',
      'deleteInternacional',
      'CargarCustomDestination',
      'setSelectedState',
      'setSelectAllState',
      'selectAllInternacionals',
      'selectInternational',
    ]),
    ...mapActions('extensions', [
      'fetchExtensions',
      'selectExtension',
      'deleteExtension',
    ]),

    refreshTable() {
      this.$refs.table.refresh()
    },
    setFilters() {
      this.refreshTable()
    },

    clearFilter() {
      this.filters = {
        name: '',
        country_id: '',
        prefix_type: { name: '', value: '' },
        prefix: '',
        from: '',
        to: '',
        prefixrate_groups_id: '',
      }
    },

    toggleFilter() {
      if (this.showFilters) {
        this.clearFilter()
      }
      this.showFilters = !this.showFilters
    },

    /*CONSULTAR DATA*/
    async fetchData({ page, filter, sort }) {
      let data = {
        name: this.filters.name,
        country_id: this.filters.country_id.id || '',
        prefix_type: this.filters.prefix_type.value,
        prefix: this.filters.prefix,
        from: this.filters.from,
        to: this.filters.to,
        prefix_type: this.filters.prefix_type.value,
        prefixrate_groups_id:
          this.filters.prefixrate_groups_id.length > 0
            ? this.filters.prefixrate_groups_id.map((prefix) => {
                return prefix.id
              })
            : '',
        orderByField: sort.fieldName || 'id',
        orderBy: sort.order || 'desc',
        limit: this.selected,
        page,
      }

      this.isRequestOngoing = true
      let response = await this.fetchInternacionals(data)
      this.isRequestOngoing = false
      return {
        data: response.data.internacionals.data || {},
        pagination: {
          totalPages: response.data.internacionals.last_page,
          currentPage: page,
          count: response.data.internacionals.total,
        },
      }
    },

    async modifySelected() {
      if (this.selectField.length > 0) {
        //console.log(this.selectField)
        let data = {
          selectedField: this.selectecField,
          type: 'All'
        }
        this.openModal({
          title: this.$tc('general.modify_select'),
          componentName: 'PrefixModify',
          refreshData: this.$refs.table.refresh,
          data: this.selectField,
        })
      } else {
        window.toastr['error'](this.$tc('general.actions_modify_selected'))
      }
    },

    /* ELIMINAR*/
    async removeExtension(id) {
      swal({
        title: this.$t('general.are_you_sure'),
        text: this.$tc('corePbx.internacional.confirm_delete'),
        icon: '/assets/icon/trash-solid.svg',
        buttons: true,
        dangerMode: true,
      }).then(async (willDelete) => {
        if (willDelete) {
          try {
            this.isLoadingDelete = true
            await this.deleteInternacional(id)
            window.toastr['success'](
              this.$tc('corePbx.internacional.deleted_message', 1)
            )
            this.$refs.table.refresh()
          } catch (error) {
            if (error.message === 'user_attached') {
              window.toastr['error'](
                this.$tc('packages.user_attached_message'),
                this.$t('general.action_failed')
              )
            } else {
              window.toastr['error'](this.$t('general.action_failed_message'))
            }
          } finally {
            this.isLoadingDelete = false
          }
        }
      })
    },

    getStatus(status) {
      if (status === 'A') {
        return 'Active'
      }
      if (status === 'I') {
        return 'Inactive'
      }
    },
    getCountry(id) {
      let country = this.countries.filter((country) => country.id == id)
      return country.length > 0 ? country[0].name : 'None'
    },
    addPaymentMode() {
      this.openModal({
        title: this.$tc('general.import_custom_destination'),
        componentName: 'IndexCsv',
        refreshData: this.$refs.table.refresh,
      })
    },
  },
}
</script>